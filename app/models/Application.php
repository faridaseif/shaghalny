<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/Model.php';

Class Application extends Model{
protected $table="application";

    public function viewApplicants($job_id)
{
    $sql = "SELECT *, application.status as application_status
            FROM application
            LEFT JOIN users 
                ON application.applicant_id = users.user_id
            LEFT JOIN user_public_info 
                ON application.applicant_id = user_public_info.user_id
            LEFT JOIN user_personal_info 
                ON application.applicant_id = user_personal_info.user_id
            WHERE application.job_id = ? AND application.status != 'rejected'";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$job_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function acceptApp($applicationId){
    $status = 'accepted';
    $sql = "UPDATE application SET status = ?, accepted_at = NOW() WHERE application_id = ?";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([$status, $applicationId]);
}

public function getJobId($applicationId) {
    $sql = "SELECT job_id FROM application WHERE application_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$applicationId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['job_id'] : null;
}

function declineApp($applicationId){
    $status = 'rejected';
    $sql = "UPDATE application SET status = ? WHERE application_id = ?";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([$status, $applicationId]);
}

function getWorkHistory($user_id): array
{
    $sql = "
        SELECT 
            application.*, 
            job.*, 
            users.first_name, 
            users.last_name,
            user_reviews.review_text,
            user_reviews.rating
        FROM application
        LEFT JOIN job ON application.job_id = job.job_id
        LEFT JOIN users ON job.user_id = users.user_id
        LEFT JOIN user_reviews ON application.applicant_id = user_reviews.reviewed_user_id
        WHERE application.applicant_id = ?
          AND application.status = 'accepted'
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function submitReview($reviewer_id, $reviewed_user_id, $rating, $review_text)
    {
        $sql = "INSERT INTO user_reviews (reviewer_id, reviewed_user_id, rating, review_text) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([$reviewer_id, $reviewed_user_id, $rating, $review_text]);

        if ($result) {
            // Update Aggregates in user_public_info
            $aggSql = "
                UPDATE user_public_info 
                SET total_reviews = (SELECT COUNT(*) FROM user_reviews WHERE reviewed_user_id = ?),
                    average_rating = (SELECT AVG(rating) FROM user_reviews WHERE reviewed_user_id = ?)
                WHERE user_id = ?
            ";
            $aggStmt = $this->conn->prepare($aggSql);
            $aggStmt->execute([$reviewed_user_id, $reviewed_user_id, $reviewed_user_id]);
        }
        
        return $result;
    }
function applyForJob($job_id, $applicant_id){
    // Check if duplicate
    $checkSql = "SELECT COUNT(*) FROM application WHERE job_id = ? AND applicant_id = ?";
    $checkStmt = $this->conn->prepare($checkSql);
    $checkStmt->execute([$job_id, $applicant_id]);
    if ($checkStmt->fetchColumn() > 0) {
        return false; // Already applied
    }

    $sql="
    INSERT INTO application (job_id, applicant_id)
    VALUES (?, ?)
    ";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([$job_id, $applicant_id]);
}

function incrementNumOfApplicants($job_id){
    $sql = "UPDATE job SET num_of_apps = num_of_apps + 1 WHERE job_id = ?";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([$job_id]);
}

    public static function getAllApplications($limit = 50)
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT a.*, j.title as job_title, u.first_name, u.last_name, u.email as applicant_email
                FROM application a
                JOIN job j ON a.job_id = j.job_id
                JOIN users u ON a.applicant_id = u.user_id
                ORDER BY a.created_at DESC
                LIMIT ?";
        
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function countAll() {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT COUNT(*) FROM application";
        return $db->query($sql)->fetchColumn();
    }

    public static function countPending() {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT COUNT(*) FROM application WHERE status = 'pending'";
        return $db->query($sql)->fetchColumn();
    }
}
?>