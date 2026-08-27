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
     * Ensures a default admin user exists in the database.
     * If no users are found, creates a default admin account.
     */
    public function ensureDefaultAdminExists(): void {
        // التحقق مما إذا كان هناك أي مستخدم مسجل مسبقاً
        $stmt = $this->db->query("SELECT COUNT(*) FROM WEB_USER");
        $count = (int) $stmt->fetchColumn();

        if ($count === 0) {
            // بيانات الحساب الافتراضي مع تضمين البريد الإلكتروني لتجنب خطأ 1364
            $username = 'admin';
            $email = 'admin@netpulse.local'; // يمكنك تعديل الإيميل حسب رغبتك
            $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
            $role = 'ADMIN';

            $insertStmt = $this->db->prepare(
                "INSERT INTO WEB_USER (username, email, password_hash, role) VALUES (?, ?, ?, ?)"
            );
            $insertStmt->execute([$username, $email, $passwordHash, $role]);
        }
    }

/**
     * Creates a new user in the database.
     */
    public function createUser(array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO WEB_USER (username, email, password_hash, role) VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'] ?? 'SUPPORT_ENGINEER'
        ]);
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

    /**
     * Retrieves all users from the database.
     * 
     * @return User[]
     */
    public function getAllUsers(): array {
        $sql = "SELECT * FROM WEB_USER ORDER BY user_id DESC";
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