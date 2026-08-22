<?php

/**
 * Class Ticket
 * 
 * Represents a Ticket data entity/transfer object, encapsulating the properties 
 * and attributes associated with an operational support ticket.
 * 
 * @package NetPulse\Models
 * @author Mohammed Bin Fares
 * @version 1.0.1
 */
class Ticket {

    /**
     * @var int The unique primary key identifier of the ticket.
     */
    public int $ticketId;

    /**
     * @var string The unique human-readable reference number (e.g., TKT-2026-0001).
     */
    public string $ticketNumber;

    /**
     * @var int|null The external reference ID pointing to the Oracle incident.
     */
    public ?int $incidentId;

    /**
     * @var string The title or brief summary of the issue.
     */
    public string $title;

    /**
     * @var string The detailed description of the operational network fault.
     */
    public string $description;

    /**
     * @var string The priority level (CRITICAL, HIGH, MEDIUM, LOW).
     */
    public string $priority;

    /**
     * @var string The current workflow status (OPEN, ASSIGNED, IN_PROGRESS, WAITING, RESOLVED, CLOSED).
     */
    public string $status;

    /**
     * @var int|null The user ID of the support engineer assigned to this ticket.
     */
    public ?int $assignedTo;

    /**
     * @var string Timestamp when the ticket was created.
     */
    public string $createdAt;

    /**
     * @var string|null Timestamp when the ticket was last updated.
     */
    public ?string $updatedAt;

    /**
     * @var string|null Timestamp when the ticket was officially closed.
     */
    public ?string $closedAt;

    /**
     * Ticket constructor.
     * 
     * Initializes a Ticket entity with optional data attributes (usually mapped from database records).
     * 
     * @param array $data Associative array of ticket attributes.
     */
    public function __construct(array $data = []) {
        if (!empty($data)) {
            $this->ticketId     = (int) ($data['ticket_id'] ?? 0);
            $this->ticketNumber = $data['ticket_number'] ?? '';
            $this->incidentId   = isset($data['incident_id']) ? (int) $data['incident_id'] : null;
            $this->title        = $data['title'] ?? '';
            $this->description  = $data['description'] ?? '';
            $this->priority     = $data['priority'] ?? 'MEDIUM';
            $this->status       = $data['status'] ?? 'OPEN';
            $this->assignedTo   = isset($data['assigned_to']) ? (int) $data['assigned_to'] : null;
            $this->createdAt    = $data['created_at'] ?? '';
            $this->updatedAt    = $data['updated_at'] ?? null;
            $this->closedAt     = $data['closed_at'] ?? null;
        }
    }
}