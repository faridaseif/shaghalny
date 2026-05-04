<?php
require_once __DIR__ . '/../../config/database.php';

/**
 * User Model
 * 
 * Handles all database interactions for the 'users' table.
 */
class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Find a user by their email address.
     *
     * @param string $email The email to search for.
     * @return mixed Returns user array if found, false otherwise.
     */
    public function findByEmail(string $email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Create a new user.
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string|null $phone
     * @param string $password
     * @return int|false The new user ID or false on failure.
     */
    public function create(string $firstName, string $lastName, string $email, ?string $phone, string $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("INSERT INTO users (first_name, last_name, email, phone, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$firstName, $lastName, $email, $phone, $hashedPassword]);
        
        $userId = (int)$this->db->lastInsertId();
        
        // Initialize user public info
        $stmt = $this->db->prepare("INSERT INTO user_public_info (user_id) VALUES (?)");
        $stmt->execute([$userId]);
        
        return $userId;
    }

    /**
     * Find a user by their unique ID.
     *
     * @param int $id The user ID.
     * @return mixed Returns user array if found, false otherwise.
     */
    public function findById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function delete($id) {
       $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = ?");
       return $stmt->execute([$id]);
    }

    public function update($id, $data) {
        $allowed = ['first_name', 'last_name', 'email', 'phone', 'role', 'status'];
        $sets = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $sets[] = "$key = ?";
                $params[] = $value;
            }
        }
        
        if (empty($sets)) {
            return false;
        }
        
        $params[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $sets) . " WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public static function getAllUsers($filters = [])
    {
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT * FROM users WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
            $term = "%" . $filters['search'] . "%";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($filters['role'])) {
            $sql .= " AND role = ?";
            $params[] = $filters['role'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
        }

        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'oldest':
                $sql .= " ORDER BY created_at ASC";
                break;
            case 'name_asc':
                $sql .= " ORDER BY first_name ASC, last_name ASC";
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY created_at DESC";
                break;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function countAll() {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT COUNT(*) FROM users";
        return $db->query($sql)->fetchColumn();
    }

    public static function countNewThisMonth() {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT COUNT(*) FROM users WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        return $db->query($sql)->fetchColumn();
    }
}
?>
