<?php

class Admin
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllUsers($search = '', $role = '', $status = '', $sort = 'newest')
    {
        $sql = "SELECT * FROM users WHERE 1=1";
        $params = [];

        if ($search) {
            $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
            $term = "%$search%";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if ($role) {
            $sql .= " AND role = ?";
            $params[] = $role;
        }

        if ($status) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

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

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id)
    {
        $sql = "SELECT u.*, 
                       upi.average_rating, upi.total_reviews, upi.jobs_completed,
                       up.birthdate, up.gender, up.nationality, up.country, up.city, up.about_me
                  FROM users u
             LEFT JOIN user_public_info upi ON u.user_id = upi.user_id
             LEFT JOIN user_personal_info up ON u.user_id = up.user_id
                 WHERE u.user_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updatePublicInfo($id, $data)
    {
        // Check existence
        $stmt = $this->conn->prepare("SELECT id FROM user_public_info WHERE user_id = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
             $this->conn->prepare("INSERT INTO user_public_info (user_id) VALUES (?)")->execute([$id]);
        }

        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }
        $params[] = $id;

        $sql = "UPDATE user_public_info SET " . implode(', ', $fields) . " WHERE user_id = ?";
        return $this->conn->prepare($sql)->execute($params);
    }

    public function updateUser($id, $data)
    {
        // Dynamic update
        $fields = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }
        
        $params[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE user_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteUser($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE user_id = ?");
        return $stmt->execute([$id]);
    }

    // Extended Getters for Full Profile View
    public function getEducation($id) {
        $stmt = $this->conn->prepare("SELECT * FROM user_education WHERE user_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getExperience($id) {
        $stmt = $this->conn->prepare("SELECT * FROM user_experience WHERE user_id = ? ORDER BY start_date DESC");
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSkills($id) {
        $sql = "SELECT s.skill_name FROM user_skills us JOIN skills s ON us.skill_id = s.skill_id WHERE us.user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getLanguages($id) {
        $sql = "SELECT l.language_name, ul.proficiency FROM user_languages ul JOIN languages l ON ul.language_id = l.language_id WHERE ul.user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCV($id) {
        $stmt = $this->conn->prepare("SELECT * FROM user_cv WHERE user_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCareerInterest($id) {
        $stmt = $this->conn->prepare("SELECT * FROM user_career_interest WHERE user_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getAchievements($id) {
        $sql = "SELECT a.achievement_name FROM user_achievements ua JOIN achievements a ON ua.achievement_id = a.achievement_id WHERE ua.user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getReviews($id) {
         // Reviews ABOUT the user
        $stmt = $this->conn->prepare("SELECT ur.*, r.first_name, r.last_name FROM user_reviews ur LEFT JOIN users r ON ur.reviewer_id = r.user_id WHERE ur.reviewed_user_id = ? ORDER BY ur.created_at DESC");
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteReview($reviewId) {
        $stmt = $this->conn->prepare("DELETE FROM user_reviews WHERE review_id = ?");
        return $stmt->execute([$reviewId]);
    }

    public function getAllJobs($search = '', $status = '', $sort = 'newest')
    {
        $sql = "SELECT j.*, u.first_name, u.last_name, u.email 
                FROM job j 
                JOIN users u ON j.user_id = u.user_id 
                WHERE 1=1";
        $params = [];

        if ($search) {
            $sql .= " AND (j.title LIKE ? OR j.description LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
            $term = "%$search%";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if ($status) {
            $sql .= " AND j.status = ?";
            $params[] = $status;
        }

        switch ($sort) {
            case 'oldest':
                $sql .= " ORDER BY j.job_id ASC";
                break;
            case 'highest_pay':
                $sql .= " ORDER BY j.payment DESC";
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY j.job_id DESC"; // Assuming job_id increments with time, or use created_at if available
                break;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
