<?php

/**
 * Class TicketController
 * 
 * Handles HTTP requests, coordinates with TicketService, 
 * and prepares data for the presentation layer.
 * 
 * @package NetPulse\Controllers
 * @author Mohammed Bin Fares
 * @version 1.0.0
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
                'incident_id' => isset($_POST['incident_id']) && !empty($_POST['incident_id']) ? (int)$_POST['incident_id'] : null
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
     */
    public function assignEngineer(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ticketId = isset($_POST['ticket_id']) ? (int)$_POST['ticket_id'] : 0;
            $assignedTo = !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;

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

    public function updateStatus(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ticketId = isset($_POST['ticket_id']) ? (int)$_POST['ticket_id'] : 0;
            $newStatus = $_POST['status'] ?? '';
            $note = $_POST['note'] ?? null;
            $changedBy = 1;

            try {
                $this->ticketService->changeTicketStatus($ticketId, $newStatus, $changedBy, $note);
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
        $status = $_GET['status'] ?? null;
        $priority = $_GET['priority'] ?? null;

        $tickets = $this->ticketService->getFilteredTickets($status, $priority);

        return [
            'tickets' => $tickets,
            'selectedStatus' => $status,
            'selectedPriority' => $priority
        ];
    }
}