<?php

/**
 * Class UserRepository
 * 
 * Handles database operations for system users and engineers.
 * 
 * @package NetPulse\Repositories
 * @author Mohammed Bin Fares
 * @version 1.0.0
 */

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../Models/User.php';

class UserRepository {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Finds a user by their unique ID.
     * 
     * @param int $userId
     * @return User|null
     */
    public function findById(int $userId): ?User {
        $sql = "SELECT * FROM WEB_USER WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$record) {
            return null;
        }

        return new User($record);
    }
    public function findByUsername(string $username): ?array {
            $stmt = $this->db->prepare("SELECT * FROM WEB_USER WHERE username = ?");
            $stmt->execute([$username]);
            return $stmt->fetch() ?: null;
        }
    /**
     * Retrieves all engineers available for assignment.
     * 
     * @return User[]
     */
    public function getAllEngineers(): array {
        $sql = "SELECT * FROM WEB_USER ORDER BY username ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];
        foreach ($records as $record) {
            $users[] = new User($record);
        }
        return $users;
    }
}