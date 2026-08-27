<?php

/**
 * Class Database
 * 
 * Provides a centralized database connection mechanism using the Singleton design pattern
 * and PHP Data Objects (PDO) to ensure secure, efficient, and single-instance communication 
 * with the MySQL database.
 * 
 * @package NetPulse\Core
 * @author Mohammed Bin Fares
 * @version 1.0.0
 */
class Database {
    
    /**
     * @var PDO|null Holds the single active instance of the PDO database connection.
     */
    private static ?PDO $instance = null;

    /**
     * Database constructor.
     * 
     * Private constructor to prevent direct instantiation via the 'new' operator,
     * enforcing the Singleton design pattern.
     */
    private function __construct() {}

    /**
     * Prevents cloning of the Database instance.
     * 
     * @return void
     */
    private function __clone() {}

    /**
     * Retrieves the single active instance of the database connection.
     * 
     * If the connection does not exist, it initializes a new secure PDO connection
     * using configuration parameters and strict error/security modes. Subsequent calls
     * will return the existing cached instance to optimize resource utilization.
     * 
     * @return PDO Returns the active PDO connection instance.
     * @throws PDOException Throws an exception if the connection fails.
     */
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                // Connection configuration parameters
                $host     = '127.0.0.1';
                $db_name  = 'netpulse_portal';
                $username = 'root';
                $password = '';
                $port     = '3306';

                // Data Source Name (DSN) with UTF-8mb4 character set enforcement
                $dsn = "mysql:host=$host;port=$port;dbname=$db_name;charset=utf8mb4";
                
                // Initialize PDO with strict security and performance attributes
                self::$instance = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on database errors
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch results as associative arrays
                    PDO::ATTR_EMULATE_PREPARES   => false,                  // Disable emulation to use true native prepared statements (SQL Injection prevention)
                ]);

            } catch (PDOException $e) {
                // Handle connection failure gracefully and securely
                http_response_code(500);
                exit("Database Connection Failed: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}