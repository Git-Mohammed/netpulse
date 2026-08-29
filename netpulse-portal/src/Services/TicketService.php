<?php

/**
 * Class TicketService
 * 
 * Encapsulates business logic, transaction management, and coordinates 
 * between repositories and audit history logging.
 * 
 * @package NetPulse\Services
 * @author Mohammed Bin Fares
 * @version 1.0.0
 */

require_once __DIR__ . '/../Repositories/TicketRepository.php';
require_once __DIR__ . '/../../core/Database.php';

class TicketService {

    private TicketRepository $ticketRepository;
    private PDO $db;

    public function __construct() {
        $this->ticketRepository = new TicketRepository();
        $this->db = Database::getInstance();
    }

    /**
     * Retrieves all system tickets.
     * 
     * @return Ticket[]
     */
    public function listAllTickets(): array {
        return $this->ticketRepository->getAllTickets();
    }

    /**
     * Retrieves tickets filtered by status and/or priority.
     * 
     * @param string|null $status
     * @param string|null $priority
     * @return Ticket[]
     */
    public function getFilteredTickets(?string $status, ?string $priority): array {
        return $this->ticketRepository->getFilteredTickets($status, $priority);
    }

    /**
     * Retrieves a specific ticket details by ID.
     * 
     * @param int $ticketId
     * @return Ticket
     * @throws Exception if ticket not found
     */
    public function getTicketDetails(int $ticketId): Ticket {
        $ticket = $this->ticketRepository->findById($ticketId);
        if (!$ticket) {
            throw new Exception("التذكرة برقم ID رقم {$ticketId} غير موجودة في النظام.");
        }
        return $ticket;
    }

    /**
     * Updates a ticket's status and automatically logs an immutable audit trail 
     * inside a strict database transaction block.
     * 
     * @param int $ticketId
     * @param string $newStatus
     * @param int $changedBy User ID performing the action
     * @param string|null $note Reason or remarks for the state mutation
     * @return bool
     * @throws Exception
     */
    public function changeTicketStatus(int $ticketId, string $newStatus, int $changedBy, ?string $note = null): bool {
        // 1. Fetch current ticket state
        $ticket = $this->getTicketDetails($ticketId);
        $oldStatus = $ticket->status;

        // If status is identical, no action needed
        if ($oldStatus === $newStatus) {
            return true;
        }

        // 2. Execute within an Atomic Transaction
        try {
            $this->db->beginTransaction();

            // Step A: Update ticket status
            $isUpdated = $this->ticketRepository->updateStatus($ticketId, $newStatus);
            if (!$isUpdated) {
                throw new Exception("فشل تحديث حالة التذكرة في قاعدة البيانات.");
            }

            // Step B: Insert immutable log into TICKET_HISTORY
            $historySql = "INSERT INTO TICKET_HISTORY (ticket_id, changed_by, old_status, new_status, change_note, changed_at) 
                           VALUES (:ticket_id, :changed_by, :old_status, :new_status, :change_note, NOW())";
            
            $stmt = $this->db->prepare($historySql);
            $stmt->execute([
                'ticket_id'   => $ticketId,
                'changed_by'  => $changedBy,
                'old_status'  => $oldStatus,
                'new_status'  => $newStatus,
                'change_note' => $note
            ]);

            // Commit transaction if all steps succeed
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            // Rollback changes if any error occurs
            $this->db->rollBack();
            throw new Exception("خطأ أثناء تنفيذ معاملة التحديث: " . $e->getMessage());
        }
    }
    public function generateAutomatedTicket(array $payload): string {
        $ticketNumber =$this->generateTicketNumber();

        $ticketData = [
            'ticket_number' => $ticketNumber,
            'incident_id'   => $payload['incident_id'] ?? null,
            'title'         => $payload['title'],
            'description'   => $payload['description'],
            'priority'      => $payload['priority'] ?? 'CRITICAL'
        ];

        return $this->ticketRepository->create($ticketData);
    }
 
    /**
     * Creates a new operational ticket initiated by a human user with secure role-based assignment.
     * 
     * This method generates a unique human-readable ticket tracking number, enforces backend security 
     * validation regarding ticket ownership based on the user's role (forcing self-assignment 
     * for ENGINEERS or allowing selection input for ADMINS), and delegates the final database insertion 
     * to the repository layer.
     *
     * @param array $payload An associative array containing ticket details ('title', 'description', 'priority', 'incident_id', 'assigned_to').
     * @param int $currentUserId The unique identifier of the authenticated user performing the action.
     * @param string $currentUserRole The role authorization level of the user (e.g., 'ENGINEER', 'ADMIN').
     * @return string Returns the newly created ticket's primary key ID.
     * @throws \Exception If the database operation fails.
     */
    public function createHumanTicket(array $payload, int $currentUserId, string $currentUserRole): string {
        $ticketNumber = $this->generateTicketNumber();

        // Enforce backend security validation and role-based ticket assignment routing
        $assignedTo = null;
        if ($currentUserRole === 'ENGINEER') {
            // Force-assign the ticket to the active engineer, ignoring any incoming form input tampering
            $assignedTo = $currentUserId;
        } elseif ($currentUserRole === 'ADMIN') {
            // Permit administrators to assign the ticket using the provided selection input
            $assignedTo = !empty($payload['assigned_to']) ? (int)$payload['assigned_to'] : null;
        }

        $ticketData = [
            'ticket_number' => $ticketNumber,
            'incident_id'   => $payload['incident_id'] ?? null,
            'title'         => $payload['title'],
            'description'   => $payload['description'],
            'priority'      => $payload['priority'] ?? 'MEDIUM',
            'assigned_to'   => $assignedTo
        ];

        return $this->ticketRepository->create($ticketData);
    }

    /**
     * Assigns or re-assigns a ticket to a specific engineer.
     * 
     * @param int $ticketId
     * @param int|null $engineerId
     * @return bool
     * @throws Exception
     */
    public function assignEngineer(int $ticketId, ?int $engineerId): bool {
        // التحقق من وجود التذكرة أولاً
        $ticket = $this->getTicketDetails($ticketId);
        
        $isUpdated = $this->ticketRepository->updateAssignedEngineer($ticketId, $engineerId);
        if (!$isUpdated) {
            throw new Exception("فشل تعيين المهندس المسؤول للتذكرة.");
        }
        return true;
    }

    /**
     * Generates a unique, human-readable serial ticket tracking number based on the current year 
     * and a random 4-digit sequence.
     *
     * @return string Returns the formatted ticket tracking number (e.g., TKT-2026-XXXX).
     */
    private function generateTicketNumber(): string {
        $year = date('Y');
        $randomNumber = rand(1000, 9999);
        
        return "TKT-{$year}-{$randomNumber}";
    }
}