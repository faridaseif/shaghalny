<?php
// app/models/Post.php
require_once APP_ROOT . '/config/database.php';

class Post
{
    public $id;
    public $user_id;
    public $content;
    public $post_type;
    public $image_url;
    public $created_at;
    public $updated_at;

    // Create a new post
    public static function create($userId, $content, $postType = 'text', $imageUrl = null)
    {
        global $pdo;
        $sql = "INSERT INTO posts (user_id, content, post_type, image_url, created_at) 
                VALUES (:user_id, :content, :post_type, :image_url, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'content' => $content,
            'post_type' => $postType,
            'image_url' => $imageUrl
        ]);
        return $pdo->lastInsertId();
    }

    // Get all posts with user info, likes count, and comments count
    public static function getAll($currentUserId = null, $limit = 50, $offset = 0)
    {
        global $pdo;
        
        if (!$pdo) {
            throw new Exception('Database connection not available');
        }
        
        try {
            // First, check if posts table has any rows
            $countStmt = $pdo->query("SELECT COUNT(*) as count FROM posts");
            $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
            error_log("DEBUG: posts table has " . ($countResult['count'] ?? 0) . " rows");
            
            $sql = "
            SELECT p.*,
                   u.user_id,
                   CONCAT(u.first_name, ' ', u.last_name) as author_name,
                   u.profile_picture as author_avatar,
                   (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id) as likes_count,
                   (SELECT COUNT(*) FROM post_comments WHERE post_id = p.id) as comments_count,
                   " . ($currentUserId ? "(SELECT COUNT(*) FROM post_likes WHERE post_id = p.id AND user_id = :current_user_id) as is_liked" : "0 as is_liked") . "
            FROM posts p
            LEFT JOIN users u ON u.user_id = p.user_id
            ORDER BY p.created_at DESC
            LIMIT :limit OFFSET :offset
            ";
            error_log("DEBUG: Executing query with currentUserId: " . ($currentUserId ?? 'null'));
            $stmt = $pdo->prepare($sql);
            if ($currentUserId) {
                $stmt->bindValue(':current_user_id', $currentUserId, PDO::PARAM_INT);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("DEBUG: Post::getAll() returned " . count($result) . " posts");
            if (count($result) > 0) {
                error_log("DEBUG: First post data: " . json_encode($result[0]));
            }
            return $result;
        } catch (PDOException $e) {
            error_log("PDO Error in Post::getAll(): " . $e->getMessage());
            error_log("SQL State: " . $e->getCode());
            // If table doesn't exist, return empty array
            if (strpos($e->getMessage(), "doesn't exist") !== false || 
                strpos($e->getMessage(), "Unknown table") !== false) {
                error_log("Posts table doesn't exist yet. Please run the schema SQL.");
                return [];
            }
            // Re-throw so controller can handle it
            throw $e;
        }
    }

    // Get a single post by ID
    public static function getById($postId, $currentUserId = null)
    {
        global $pdo;
        $sql = "
        SELECT p.*,
               u.user_id,
               CONCAT(u.first_name, ' ', u.last_name) as author_name,
               u.profile_picture as author_avatar,
               (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id) as likes_count,
               (SELECT COUNT(*) FROM post_comments WHERE post_id = p.id) as comments_count,
               " . ($currentUserId ? "(SELECT COUNT(*) FROM post_likes WHERE post_id = p.id AND user_id = :current_user_id) as is_liked" : "0 as is_liked") . "
        FROM posts p
        JOIN users u ON u.user_id = p.user_id
        WHERE p.id = :post_id
        ";
        $stmt = $pdo->prepare($sql);
        if ($currentUserId) {
            $stmt->bindValue(':current_user_id', $currentUserId, PDO::PARAM_INT);
        }
        $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Toggle like on a post
    public static function toggleLike($postId, $userId)
    {
        global $pdo;
        
        // Check if already liked
        $check = $pdo->prepare("SELECT id FROM post_likes WHERE post_id = :post_id AND user_id = :user_id");
        $check->execute(['post_id' => $postId, 'user_id' => $userId]);
        $existing = $check->fetch();
        
        if ($existing) {
            // Unlike
            $stmt = $pdo->prepare("DELETE FROM post_likes WHERE post_id = :post_id AND user_id = :user_id");
            $stmt->execute(['post_id' => $postId, 'user_id' => $userId]);
            return ['action' => 'unliked', 'liked' => false];
        } else {
            // Like
            $stmt = $pdo->prepare("INSERT INTO post_likes (post_id, user_id, created_at) VALUES (:post_id, :user_id, NOW())");
            $stmt->execute(['post_id' => $postId, 'user_id' => $userId]);
            return ['action' => 'liked', 'liked' => true];
        }
    }

    // Add a comment to a post
    public static function addComment($postId, $userId, $content)
    {
        global $pdo;
        $sql = "INSERT INTO post_comments (post_id, user_id, content, created_at) 
                VALUES (:post_id, :user_id, :content, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'post_id' => $postId,
            'user_id' => $userId,
            'content' => $content
        ]);
        $commentId = $pdo->lastInsertId();
        
        // Return the comment with user info
        $sql = "SELECT c.*, 
                       CONCAT(u.first_name, ' ', u.last_name) as author_name,
                       u.profile_picture as author_avatar
                FROM post_comments c
                JOIN users u ON u.user_id = c.user_id
                WHERE c.id = :comment_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['comment_id' => $commentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get comments for a post
    public static function getComments($postId)
    {
        global $pdo;
        
        if (!$pdo) {
            throw new Exception('Database connection not available');
        }
        
        try {
            $sql = "SELECT c.*,
                           CONCAT(u.first_name, ' ', u.last_name) as author_name,
                           u.profile_picture as author_avatar
                    FROM post_comments c
                    LEFT JOIN users u ON u.user_id = c.user_id
                    WHERE c.post_id = :post_id
                    ORDER BY c.created_at ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['post_id' => $postId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result ? $result : [];
        } catch (PDOException $e) {
            error_log("Database error in getComments: " . $e->getMessage());
            // If table doesn't exist, return empty array instead of throwing
            if (strpos($e->getMessage(), "doesn't exist") !== false || 
                strpos($e->getMessage(), "Unknown table") !== false) {
                return [];
            }
            throw $e;
        }
    }

    // Update a post (only by owner)
    public static function update($postId, $userId, $content)
    {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE posts SET content = :content, updated_at = NOW() 
                              WHERE id = :post_id AND user_id = :user_id");
        $stmt->execute([
            'post_id' => $postId,
            'user_id' => $userId,
            'content' => $content
        ]);
        return $stmt->rowCount() > 0;
    }

    // Delete a post (only by owner)
    public static function delete($postId, $userId)
    {
        global $pdo;
        
        // Get post to delete image if exists
        $stmt = $pdo->prepare("SELECT image_url FROM posts WHERE id = :post_id AND user_id = :user_id");
        $stmt->execute(['post_id' => $postId, 'user_id' => $userId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($post) {
            // Delete the post
            $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :post_id AND user_id = :user_id");
            $stmt->execute(['post_id' => $postId, 'user_id' => $userId]);
            
            // Delete image file if exists
            if ($post['image_url']) {
                $imagePath = APP_ROOT . '/app/public' . $post['image_url'];
                if (file_exists($imagePath)) {
                    @unlink($imagePath);
                }
            }
            
            return true;
        }
        
        return false;
    }
    // Delete a post by ID (Admin override)
    public static function deleteById($postId)
    {
        global $pdo;
        
        // Get post to delete image if exists
        $stmt = $pdo->prepare("SELECT image_url FROM posts WHERE id = :post_id");
        $stmt->execute(['post_id' => $postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($post) {
            // Delete the post
            $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :post_id");
            $stmt->execute(['post_id' => $postId]);
            
            // Delete image file if exists
            if ($post['image_url']) {
                $imagePath = APP_ROOT . '/app/public' . $post['image_url'];
                if (file_exists($imagePath)) {
                    @unlink($imagePath);
                }
            }
            
            return true;
        }
        
        return false;
    }
    // Delete a comment (Admin or Owner)
    public static function deleteComment($commentId)
    {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM post_comments WHERE id = :comment_id");
        $stmt->execute(['comment_id' => $commentId]);
        return $stmt->rowCount() > 0;
    }
}
?>

