<?php

/**
 * Class TicketRepository
 * 
 * Handles all database persistence operations related to tickets, 
 * utilizing PDO Prepared Statements and mapping results directly to Ticket domain models.
 * 
 * @package NetPulse\Repositories
 * @author Mohammed Bin Fares
 * @version 1.3.0
 */

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../Models/Ticket.php';

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
     * Retrieves all tickets from the database, ordered by creation date descending, 
     * and maps them into an array of Ticket domain model objects.
     * 
     * @return Ticket[] Returns an array of Ticket domain objects.
     */
    public function getAllTickets(): array {
        $sql = "SELECT * FROM TICKET ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $rawRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tickets = [];

        foreach ($rawRecords as $record) {
            $tickets[] = new Ticket($record);
        }

        return $tickets;
    }

    /**
     * Retrieves tickets filtered by status and/or priority, ordered by creation date descending.
     * 
     * @param string|null $status The ticket status to filter by.
     * @param string|null $priority The ticket priority to filter by.
     * @return Ticket[] Returns an array of matching Ticket domain objects.
     */
    public function getFilteredTickets(?string $status, ?string $priority): array {
        $sql = "SELECT * FROM TICKET WHERE 1=1";
        $params = [];

        if (!empty($status)) {
            $sql .= " AND status = :status";
            $params['status'] = $status;
        }

        if (!empty($priority)) {
            $sql .= " AND priority = :priority";
            $params['priority'] = $priority;
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $rawRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tickets = [];

        foreach ($rawRecords as $record) {
            $tickets[] = new Ticket($record);
        }

        return $tickets;
    }

    /**
     * Finds a specific ticket by its unique internal identifier.
     * 
     * @param int $ticketId The internal primary key ID of the ticket.
     * @return Ticket|null Returns a Ticket object if found, or null otherwise.
     */
    public function findById(int $ticketId): ?Ticket {
        $sql = "SELECT * FROM TICKET WHERE ticket_id = :ticket_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ticket_id' => $ticketId]);
        
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

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
    public function createt(array $data): int {
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
     * Updates the status and operational state of an existing ticket,
     * while automatically refreshing the updated_at timestamp.
     * 
     * @param int $ticketId The internal ID of the ticket to update.
     * @param string $status The target status (e.g., IN_PROGRESS, RESOLVED, CLOSED).
     * @return bool Returns true on success or false on failure.
     */
    public function updateStatus(int $ticketId, string $status): bool {
        $sql = "UPDATE TICKET SET status = :status, updated_at = NOW() WHERE ticket_id = :ticket_id";
        $stmt = $this->db->prepare($sql);
        
        $stmt->execute([
            'status'    => $status,
            'ticket_id' => $ticketId
        ]);

        return $stmt->rowCount() > 0;
    }
}