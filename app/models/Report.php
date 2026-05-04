<?php
// app/models/Report.php
require_once __DIR__ . '/../../config/database.php';

class Report
{
    public $id;
    public $user_id;
    public $post_id;
    public $report_type;
    public $title;
    public $description;
    public $status;
    public $priority;
    public $created_at;
    public $updated_at;
    public $resolved_at;
    public $admin_notes;

    // Create a new report
    public static function create($userId, $reportType, $title, $description, $priority = 'medium', $postId = null)
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "INSERT INTO reports (user_id, post_id, report_type, title, description, priority, status, created_at) 
                VALUES (:user_id, :post_id, :report_type, :title, :description, :priority, 'pending', NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'post_id' => $postId,
            'report_type' => $reportType,
            'title' => $title,
            'description' => $description,
            'priority' => $priority
        ]);
        return $pdo->lastInsertId();
    }

    // Get reports for a user (or all if admin)
    public static function getForUser($userId = null, $limit = 50)
    {
        $pdo = Database::getInstance()->getConnection();
        if ($userId) {
            $sql = "SELECT r.*, p.content as post_content, u.first_name as reporter_name 
                    FROM reports r 
                    LEFT JOIN posts p ON r.post_id = p.id 
                    LEFT JOIN users u ON r.user_id = u.user_id 
                    WHERE r.user_id = :user_id 
                    ORDER BY r.created_at DESC LIMIT :limit";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            // For anonymous or admin view
            $sql = "SELECT r.*, p.content as post_content, u.first_name as reporter_name 
                    FROM reports r 
                    LEFT JOIN posts p ON r.post_id = p.id 
                    LEFT JOIN users u ON r.user_id = u.user_id 
                    ORDER BY r.created_at DESC LIMIT :limit";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get a single report by ID
    public static function getById($reportId, $userId = null)
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM reports WHERE id = :id";
        if ($userId) {
            $sql .= " AND user_id = :user_id";
        }
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $reportId, PDO::PARAM_INT);
        if ($userId) {
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update report status
    public static function updateStatus($reportId, $status, $adminNotes = null)
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "UPDATE reports SET status = :status, updated_at = NOW()";
        if ($status === 'resolved') {
            $sql .= ", resolved_at = NOW()";
        }
        if ($adminNotes) {
            $sql .= ", admin_notes = :admin_notes";
        }
        $sql .= " WHERE id = :id";
        
        $params = ['id' => $reportId, 'status' => $status];
        if ($adminNotes) {
            $params['admin_notes'] = $adminNotes;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    // Delete a report
    public static function delete($reportId)
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "DELETE FROM reports WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $reportId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>
