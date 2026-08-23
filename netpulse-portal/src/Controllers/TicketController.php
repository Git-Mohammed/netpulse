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

public function updateStatus(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ticketId = isset($_POST['ticket_id']) ? (int)$_POST['ticket_id'] : 0;
        $newStatus = $_POST['status'] ?? '';
        $note = $_POST['note'] ?? null;
        $changedBy = 1; // معرف المستخدم الافتراضي (المشرف أو المهندس الحالي)

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
   public function index() {
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