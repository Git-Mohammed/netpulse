<?php

/**
 * Database & Repository Test Script
 * 
 * Verifies the database singleton connection and tests retrieving tickets 
 * from the TicketRepository layer.
 * 
 * @author Mohammed Bin Fares
 * @version 1.0.0
 */

// Include required core and repository classes (adjust paths if needed based on your directory structure)
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../src/Models/Ticket.php';
require_once __DIR__ . '/../src/Repositories/TicketRepository.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h2>NetPulse Portal - Database Connection Test</h2>";

try {
    // 1. Test Database Singleton Connection
    $db = Database::getInstance();
    echo "<p style='color: green;'>✅ <strong>Database connection established successfully via Singleton!</strong></p>";

    // 2. Test TicketRepository and Object Mapping
    $ticketRepo = new TicketRepository();
    $tickets = $ticketRepo->getAllTickets();

    echo "<p>📊 Total tickets fetched: <strong>" . count($tickets) . "</strong></p>";

    if (!empty($tickets)) {
        echo "<h3>Recent Tickets Preview:</h3>";
        echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%; font-family: sans-serif;'>";
        echo "<tr style='background: #f2f2f2;'><th>ID</th><th>Ticket Number</th><th>Title</th><th>Priority</th><th>Status</th><th>Created At</th></tr>";
        
        // Loop through the first 5 tickets as a preview
        $counter = 0;
        foreach ($tickets as $ticket) {
            if ($counter++ >= 5) break; // Show top 5 only
            echo "<tr>";
            echo "<td>" . htmlspecialchars($ticket->ticketId) . "</td>";
            echo "<td>" . htmlspecialchars($ticket->ticketNumber) . "</td>";
            echo "<td>" . htmlspecialchars($ticket->title) . "</td>";
            echo "<td>" . htmlspecialchars($ticket->priority) . "</td>";
            echo "<td>" . htmlspecialchars($ticket->status) . "</td>";
            echo "<td>" . htmlspecialchars($ticket->createdAt) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠️ No tickets found in the database. Please run the seed data script.</p>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ <strong>Connection or Query Failed:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}