<?php

/**
 * Class User
 * 
 * Represents a User data entity/transfer object, encapsulating system users 
 * (Admins and Engineers) within the NetPulse portal.
 * 
 * @package NetPulse\Models
 * @author Mohammed Bin Fares
 * @version 1.0.0
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
}