<?php

/**
 * Class TicketController
 * 
 * Handles HTTP requests, coordinates with TicketService, 
 * and prepares data for the presentation layer.
 * 
 * @package NetPulse\Controllers
 * @author Mohammed Bin Fares
 * @version 1.1.0
 */

require_once __DIR__ . '/../Services/TicketService.php';

class TicketController {

    private TicketService $ticketService;

    public function __construct() {
        $this->ticketService = new TicketService();
    }

    public function show(int $id): ?Ticket {
        return $this->ticketService->getTicketDetails($id);
    }

    /**
     * Handles the creation and storage of a new operational ticket.
     */
    public function store(): void {
                
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $payload = [
                'title'       => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'priority'    => $_POST['priority'] ?? 'MEDIUM',
                'incident_id' => !empty($_POST['incident_id']) ? (int)$_POST['incident_id'] : null
            ];

            try {
                $newTicketId = $this->ticketService->generateAutomatedTicket($payload);
                header("Location: index.php?page=tickets-show&id={$newTicketId}&success=created");
                exit;
            } catch (Exception $e) {
                $error = urlencode($e->getMessage());
                header("Location: index.php?page=tickets-create&error={$error}");
                exit;
            }
        }
    }

    /**
     * Handles assigning or changing the assigned engineer for a ticket.
     * (Admin Only)
     */
    public function assignEngineer(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ticketId   = isset($_POST['ticket_id']) ? (int)$_POST['ticket_id'] : 0;
            $assignedTo = !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;

            $userRole = strtoupper($_SESSION['role'] ?? '');

            // فحص الصلاحية: الأدمن فقط من يمكنه تعيين المهندسين
            if (!isset($_SESSION['user_id']) || $userRole !== 'ADMIN') {
                $error = urlencode('غير مصرح لك بإعادة تعيين التذاكر.');
                header("Location: index.php?page=tickets-show&id={$ticketId}&error={$error}");
                exit;
            }

            try {
                $this->ticketService->assignEngineer($ticketId, $assignedTo);
                header("Location: index.php?page=tickets-show&id={$ticketId}&success=assigned");
                exit;
            } catch (Exception $e) {
                $error = urlencode($e->getMessage());
                header("Location: index.php?page=tickets-show&id={$ticketId}&error={$error}");
                exit;
            }
        }
    }

    /**
     * Handles updating ticket status.
     * (Allowed for ADMIN or the assigned engineer only)
     */
    public function updateStatus(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ticketId  = isset($_POST['ticket_id']) ? (int)$_POST['ticket_id'] : 0;
            $newStatus = $_POST['status'] ?? '';
            $note      = $_POST['note'] ?? null;

            $currentUserId = (int)($_SESSION['user_id'] ?? 0);
            $userRole      = strtoupper($_SESSION['role'] ?? '');

            if ($currentUserId === 0) {
                header('Location: index.php?page=login');
                exit;
            }

            try {
                // جلب بيانات التذكرة للتحقق من المهندس المسؤول
                $ticket = $this->ticketService->getTicketDetails($ticketId);

                if (!$ticket) {
                    throw new Exception('التذكرة غير موجودة.');
                }

                $isAdmin = ($userRole === 'ADMIN');
                $isAssignedEngineer = (isset($ticket->assignedTo) && (int)$ticket->assignedTo === $currentUserId);

                // التحقق من صلاحية التعديل
                if (!$isAdmin && !$isAssignedEngineer) {
                    throw new Exception('غير مصرح لك بتعديل حالة هذه التذكرة.');
                }

                // تنفيذ التحديث وتحديد المستخدم الحالي كمنفذ للتغيير
                $this->ticketService->changeTicketStatus($ticketId, $newStatus, $currentUserId, $note);
                
                header("Location: index.php?page=tickets-show&id={$ticketId}&success=1");
                exit;
            } catch (Exception $e) {
                $error = urlencode($e->getMessage());
                header("Location: index.php?page=tickets-show&id={$ticketId}&error={$error}");
                exit;
            }
        }
    }

    /**
     * Renders the main tickets dashboard data.
     * 
     * @return array Returns array of tickets for the view.
     */
    public function index(): array {
        $status   = $_GET['status'] ?? null;
        $priority = $_GET['priority'] ?? null;

        $tickets = $this->ticketService->getFilteredTickets($status, $priority);

        return [
            'tickets'          => $tickets,
            'selectedStatus'   => $status,
            'selectedPriority' => $priority
        ];
    }
}