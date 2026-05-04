<?php
// app/controllers/PostController.php
require_once APP_ROOT . '/app/models/Post.php';
require_once APP_ROOT . '/config/database.php';

class PostController
{
    // Display the social feed page (accessible without login)
    public function feed()
    {
        try {
            // Allow viewing without login, but track if user is logged in
            $currentUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
            
            // Try to get posts, but handle errors gracefully
            $posts = [];
            try {
                global $pdo;
                if (!$pdo) {
                    error_log("ERROR: PDO connection is null");
                    throw new Exception('Database connection not available');
                }
                
                // Test query first
                $testQuery = $pdo->query("SELECT COUNT(*) as count FROM posts");
                $testResult = $testQuery->fetch(PDO::FETCH_ASSOC);
                error_log("DEBUG: Found " . ($testResult['count'] ?? 0) . " posts in database");
                
                $posts = Post::getAll($currentUserId);
                error_log("DEBUG: Post::getAll() returned " . count($posts) . " posts");
            } catch (PDOException $e) {
                error_log("ERROR - Database error loading posts: " . $e->getMessage());
                error_log("ERROR - SQL State: " . $e->getCode());
                error_log("ERROR - File: " . $e->getFile() . " Line: " . $e->getLine());
                // Show error in development mode
                if (defined('APP_ENV') && APP_ENV === 'development') {
                    $posts = []; // Still show empty, but error is logged
                } else {
                    $posts = [];
                }
            } catch (Exception $e) {
                error_log("ERROR - Exception loading posts: " . $e->getMessage());
                error_log("ERROR - File: " . $e->getFile() . " Line: " . $e->getLine());
                error_log("ERROR - Stack trace: " . $e->getTraceAsString());
                $posts = [];
            }
            
            // Get user info for the header (if logged in)
            $currentUser = null;
            if ($currentUserId) {
                try {
                    global $pdo;
                    if ($pdo) {
                        $stmt = $pdo->prepare("SELECT user_id, CONCAT(first_name, ' ', last_name) as name, profile_picture 
                                               FROM users WHERE user_id = :user_id");
                        $stmt->execute(['user_id' => $currentUserId]);
                        $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
                    }
                } catch (Exception $e) {
                    error_log("Error loading user info: " . $e->getMessage());
                    // Continue without user info
                }
            }
            
            require_once APP_ROOT . '/app/views/posts/feed.php';
        } catch (Exception $e) {
            error_log("Fatal error in feed(): " . $e->getMessage());
            // Show a basic error page
            echo "<!DOCTYPE html><html><head><title>Error</title></head><body>";
            echo "<h1>Error Loading Feed</h1>";
            echo "<p>Please check that the database tables are created.</p>";
            echo "<p>Run the SQL in: <code>app/database/social_feed_schema.sql</code></p>";
            if (defined('APP_ENV') && APP_ENV === 'development') {
                echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            echo "</body></html>";
        }
    }

    // AJAX: Create a new post (with file upload support)
    public function create()
    {
        header('Content-Type: application/json');
        
        try {
            if (empty($_SESSION['user_id'])) {
                throw new Exception('Unauthorized', 403);
            }

            $userId = (int)$_SESSION['user_id'];
            $content = '';
            $postType = 'text';
            $imageUrl = null;

            // Handle file upload
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $postType = 'photo';
                $imageUrl = $this->handleFileUpload($_FILES['photo']);
                if (!$imageUrl) {
                    throw new Exception('Failed to upload image', 400);
                }
            }

            // Get content from POST or JSON
            if (isset($_POST['content'])) {
                $content = trim($_POST['content']);
                $postType = $_POST['post_type'] ?? $postType;
            } else {
                $input = json_decode(file_get_contents('php://input'), true) ?? [];
                $content = trim($input['content'] ?? '');
                $postType = $input['post_type'] ?? $postType;
                $imageUrl = $input['image_url'] ?? $imageUrl;
            }

            if (empty($content) && empty($imageUrl)) {
                 throw new Exception('Content or image is required', 400);
            }

            $postId = Post::create($userId, $content, $postType, $imageUrl);
            $post = Post::getById($postId, $userId);

            echo json_encode(['success' => true, 'post' => $post]);

        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // Handle file upload
    private function handleFileUpload($file)
    {
        $uploadDir = APP_ROOT . '/app/public/uploads/posts/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = $file['type'];
        if (!in_array($fileType, $allowedTypes)) {
            return false;
        }

        // Validate file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            return false;
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('post_', true) . '.' . $extension;
        $filepath = $uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Return relative URL
            return '/uploads/posts/' . $filename;
        }

        return false;
    }

    // AJAX: Toggle like on a post
    public function toggleLike()
    {
        header('Content-Type: application/json');
        
        if (empty($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $postId = isset($input['post_id']) ? (int)$input['post_id'] : 0;
        $userId = (int)$_SESSION['user_id'];

        if (!$postId) {
            http_response_code(400);
            echo json_encode(['error' => 'Post ID is required']);
            exit;
        }

        $result = Post::toggleLike($postId, $userId);
        
        // Get updated like count
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) as likes_count FROM post_likes WHERE post_id = :post_id");
        $stmt->execute(['post_id' => $postId]);
        $likesCount = $stmt->fetch(PDO::FETCH_ASSOC)['likes_count'];

        echo json_encode([
            'success' => true,
            'liked' => $result['liked'],
            'likes_count' => (int)$likesCount
        ]);
    }

    // AJAX: Add a comment
    public function addComment()
    {
        header('Content-Type: application/json');
        
        if (empty($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $postId = isset($input['post_id']) ? (int)$input['post_id'] : 0;
        $content = trim($input['content'] ?? '');
        $userId = (int)$_SESSION['user_id'];

        if (!$postId || empty($content)) {
            http_response_code(400);
            echo json_encode(['error' => 'Post ID and content are required']);
            exit;
        }

        $comment = Post::addComment($postId, $userId, $content);
        
        // Get updated comments count
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) as comments_count FROM post_comments WHERE post_id = :post_id");
        $stmt->execute(['post_id' => $postId]);
        $commentsCount = $stmt->fetch(PDO::FETCH_ASSOC)['comments_count'];

        echo json_encode([
            'success' => true,
            'comment' => $comment,
            'comments_count' => (int)$commentsCount
        ]);
    }

    // AJAX: Get comments for a post
    public function getComments()
    {
        // Ensure we output JSON and catch all errors
        header('Content-Type: application/json');
        
        // Suppress any output that might interfere with JSON
        ob_start();
        
        try {
            // Get post_id from query string
            $postId = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;
            
            if (!$postId) {
                ob_end_clean();
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Post ID is required', 'debug' => ['received_post_id' => $_GET['post_id'] ?? 'not set']]);
                exit;
            }

            // Check if database connection exists
            global $pdo;
            if (!$pdo) {
                throw new Exception('Database connection not available');
            }

            $comments = Post::getComments($postId);
            
            // Ensure comments is always an array
            if (!is_array($comments)) {
                $comments = [];
            }
            
            ob_end_clean();
            echo json_encode(['success' => true, 'comments' => $comments]);
            exit;
        } catch (PDOException $e) {
            ob_end_clean();
            error_log("PDO Error in getComments: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'error' => 'Database error',
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            exit;
        } catch (Exception $e) {
            ob_end_clean();
            error_log("Error in getComments: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'error' => 'Failed to load comments',
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    // AJAX: Update a post
    public function update()
    {
        header('Content-Type: application/json');
        
        if (empty($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $userId = (int)$_SESSION['user_id'];
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $postId = isset($input['post_id']) ? (int)$input['post_id'] : 0;
        $content = trim($input['content'] ?? '');

        if (!$postId || empty($content)) {
            http_response_code(400);
            echo json_encode(['error' => 'Post ID and content are required']);
            exit;
        }

        $updated = Post::update($postId, $userId, $content);
        
        if ($updated) {
            $post = Post::getById($postId, $userId);
            echo json_encode(['success' => true, 'post' => $post]);
        } else {
            http_response_code(403);
            echo json_encode(['error' => 'Post not found or you do not have permission to edit it']);
        }
    }

    // AJAX: Delete a post
    public function delete()
    {
        header('Content-Type: application/json');
        
        if (empty($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $postId = isset($input['post_id']) ? (int)$input['post_id'] : 0;
        $userId = (int)$_SESSION['user_id'];

        if (!$postId) {
            http_response_code(400);
            echo json_encode(['error' => 'Post ID is required']);
            exit;
        }

        $deleted = Post::delete($postId, $userId);
        
        if ($deleted) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(403);
            echo json_encode(['error' => 'Post not found or you do not have permission to delete it']);
        }
    }
}
?>

