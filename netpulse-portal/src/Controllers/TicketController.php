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