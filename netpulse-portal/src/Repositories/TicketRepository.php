<?php

/**
 * Class TicketRepository
 * 
 * Handles all database persistence operations related to tickets, 
 * utilizing PDO Prepared Statements and mapping results directly to Ticket domain models.
 * 
 * @package NetPulse\Repositories
 * @author Mohammed Bin Fares
 * @version 1.1.0
 */
class TicketRepository {

    /**
     * @var PDO Holds the database connection instance.
     */
    private PDO $db;

    /**
     * TicketRepository constructor.
     * 
     * Initializes the repository with a single active database connection 
     * retrieved from the Database Singleton class.
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Retrieves all tickets from the database, mapped into an array of Ticket objects.
     * 
     * @return Ticket[] Returns an array of Ticket domain model objects.
     */
    public function getAllTickets(): array {
        $sql = "SELECT * FROM TICKET ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $rawRecords = $stmt->fetchAll();
        $tickets = [];

        // Map each raw database record into a Ticket object
        foreach ($rawRecords as $record) {
            $tickets[] = new Ticket($record);
        }

        return $tickets;
    }

    /**
     * Finds a specific ticket by its unique internal identifier and returns it as a Ticket object.
     * 
     * @param int $ticketId The internal primary key ID of the ticket.
     * @return Ticket|null Returns a Ticket object if found, or null otherwise.
     */
    public function getTicketById(int $ticketId): ?Ticket {
        $sql = "SELECT * FROM TICKET WHERE ticket_id = :ticket_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ticket_id' => $ticketId]);
        
        $record = $stmt->fetch();

        if (!$record) {
            return null;
        }

        return new Ticket($record);
    }

    /**
     * Creates a new ticket record in the database.
     * 
     * @param array $data Associative array containing ticket details.
     * @return int Returns the last inserted primary key ID of the new ticket.
     */
    public function createTicket(array $data): int {
        $sql = "INSERT INTO TICKET (ticket_number, incident_id, title, description, priority, status, created_at) 
                VALUES (:ticket_number, :incident_id, :title, :description, :priority, 'OPEN', NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'ticket_number' => $data['ticket_number'],
            'incident_id'   => $data['incident_id'] ?? null,
            'title'         => $data['title'],
            'description'   => $data['description'],
            'priority'      => $data['priority'] ?? 'MEDIUM'
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Updates the status and operational state of an existing ticket.
     * 
     * @param int $ticketId The internal ID of the ticket to update.
     * @param string $newStatus The target status (e.g., IN_PROGRESS, RESOLVED, CLOSED).
     * @return bool Returns true on success or false on failure.
     */
    public function updateTicketStatus(int $ticketId, string $newStatus): bool {
        $sql = "UPDATE TICKET SET status = :status, updated_at = NOW() WHERE ticket_id = :ticket_id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            'status'    => $newStatus,
            'ticket_id' => $ticketId
        ]);
    }
}