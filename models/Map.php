<?php
class Map {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo; 
    }

    public function getAllJobs() {
        try {
            // Select all jobs that are NOT closed
            $stmt = $this->db->prepare("SELECT * FROM job WHERE status != 'closed'");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); 
        } catch (PDOException $e) {
            // Fallback if 'status' column is missing: Select all jobs
            $stmt = $this->db->prepare("SELECT * FROM job");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); 
        } 
    }
}
?>