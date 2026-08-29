<?php

/**
 * Class User
 * 
 * Represents a User data entity/transfer object, encapsulating system users 
 * (Admins and Engineers) within the NetPulse portal.
 * 
 * @package NetPulse\Models
 * @author Mohammed Bin Fares
 * @version 1.1.0
 */
class User {

    /**
     * @var int Unique primary key identifier of the user.
     */
    public int $userId;

    /**
     * @var string Username for system access.
     */
    public string $username;

    /**
     * @var string User email address.
     */
    public string $email;

    /**
     * @var string User system role (ADMIN, ENGINEER).
     */
    public string $role;

    /**
     * @var string Timestamp when the user account was created.
     */
    public string $createdAt;

    /**
     * User constructor.
     * 
     * @param array $data Associative array of user attributes.
     */
    public function __construct(array $data = []) {
        if (!empty($data)) {
            $this->userId    = (int) ($data['user_id'] ?? 0);
            $this->username  = $data['username'] ?? '';
            $this->email     = $data['email'] ?? '';
            $this->role      = $data['role'] ?? 'ENGINEER';
            $this->createdAt = $data['created_at'] ?? '';
        }
    }

    // -------------------------------------------------------------------------
    // Getters & Accessors
    // -------------------------------------------------------------------------

    public function getId(): int {
        return $this->userId;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getRole(): string {
        return $this->role;
    }

    public function getCreatedAt(): string {
        return $this->createdAt;
    }

    // -------------------------------------------------------------------------
    // Helper Methods
    // -------------------------------------------------------------------------

    /**
     * Checks if the user is an Administrator.
     */
    public function isAdmin(): bool {
        return strtoupper($this->role) === 'ADMIN';
    }

    /**
     * Checks if the user is a Support Engineer.
     */
    public function isEngineer(): bool {
        return strtoupper($this->role) === 'ENGINEER' || strtoupper($this->role) === 'SUPPORT_ENGINEER';
    }

    /**
     * Converts the model properties to an associative array.
     */
    public function toArray(): array {
        return [
            'user_id'    => $this->userId,
            'username'   => $this->username,
            'email'      => $this->email,
            'role'       => $this->role,
            'created_at' => $this->createdAt,
        ];
    }
}