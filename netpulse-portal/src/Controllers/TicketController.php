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
     * Handles the creation and storage of a new operational ticket initiated by a human user.
     * 
     * This method validates the incoming HTTP POST request, verifies the user's active session 
     * and role authorization, builds the payload safely, and delegates the creation process 
     * to the TicketService layer before redirecting the user accordingly.
     *
     * @return void
     * @throws \Exception If the database insertion or service processing fails.
     */
    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Ensure the user is authenticated and has a valid active session
            $currentUserId = $_SESSION['user_id'] ?? null;
            $currentUserRole =strtoupper($_SESSION['role'] ?? '');

            if (!$currentUserId || !$currentUserRole) {
                header("Location: index.php?page=login");
                exit;
            }

            $payload = [
                'title'       => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'priority'    => $_POST['priority'] ?? 'MEDIUM',
                'incident_id' => !empty($_POST['incident_id']) ? (int)$_POST['incident_id'] : null,
                'assigned_to' => $_POST['assigned_to'] ?? null // Assignment value coming from the admin selection list (if applicable)
            ];

            try {
                // Invoke human ticket creation passing the payload along with the current user context
                $newTicketId = $this->ticketService->createHumanTicket($payload, $currentUserId, $currentUserRole);
                
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
     * Handles operational ticket status update requests (HTTP POST Request).
     * 
     * This handler applies security best practices by:
     * 1. Verifying authentication status.
     * 2. Enforcing access control policies (RBAC & ABAC): Only an ADMIN 
     *    or the Assigned Engineer is allowed to make modifications.
     * 3. Delegating business logic and audit logging to the Service layer.
     *
     * @return void
     */
    public function updateStatus(): void {
        // Restrict requests to POST method only to prevent basic CSRF attacks and URL tampering
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // 1. Receive and secure inputs (Input Sanitization & Casting)
            $ticketId  = isset($_POST['ticket_id']) ? (int)$_POST['ticket_id'] : 0;
            $newStatus = trim($_POST['status'] ?? '');
            $note      = isset($_POST['note']) ? trim($_POST['note']) : null;

            // 2. Retrieve secure session data
            $currentUserId = (int)($_SESSION['user_id'] ?? 0);
            $userRole      = strtoupper($_SESSION['role'] ?? '');

            // Authentication Check
            if ($currentUserId === 0) {
                header('Location: index.php?page=login');
                exit;
            }

            try {
                // 3. Fetch ticket data to verify its existence and engineer ownership
                $ticket = $this->ticketService->getTicketDetails($ticketId);

                if (!$ticket) {
                    throw new Exception('The ticket does not exist or has been deleted.');
                }

                // 4. Authorization Validation
                $isAdmin = ($userRole === 'ADMIN');
                
                // Dynamically extract the engineer ID to handle naming conventions (CamelCase vs Snake_case) or nested objects
                $assignedEngineerId = $ticket->assigned_to 
                    ?? $ticket->assignedTo 
                    ?? ($ticket->assignedEngineer->userId ?? $ticket->assignedEngineer->id ?? 0);
                
                $isAssignedEngineer = ((int)$assignedEngineerId === $currentUserId) && ($currentUserId > 0);

                // Reject the operation if the user is neither an admin nor the assigned engineer
                if (!$isAdmin && !$isAssignedEngineer) {
                    throw new Exception('Action denied: You are not authorized to modify the status of this ticket as it is not assigned to you.');
                }

                // 5. Service Layer Delegation
                // Pass the currentUserId to record it in the Audit Trail as the executor of the change
                $this->ticketService->changeTicketStatus($ticketId, $newStatus, $currentUserId, $note);
                
                // 6. Redirect on success (PRG Pattern: Post/Redirect/Get)
                header("Location: index.php?page=tickets-show&id={$ticketId}&success=1");
                exit;
                
            } catch (Exception $e) {
                // Catch errors and pass them safely to the UI
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