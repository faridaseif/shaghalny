<?php
// namespace App\Controllers;

require_once __DIR__ . '/../../utilities/Controller.php';

use App\Utilities\Controller;

class HomeController extends Controller {

    public function index() {
        // 1. Check Authentication (Simulated middleware)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $firstName = $_SESSION['user_first_name'] ?? 'Guest';
        $userId = $_SESSION['user_id'] ?? null;

        // Initialize DB connection
        require_once __DIR__ . '/../../config/database.php';
        $db = \Database::getInstance()->getConnection();

        // Data Containers
        $allJobs = [];
        $recentMessages = [];
        $isGuest = false;

        if ($userId) {
            // --- LOGGED IN USER (FUNCTIONAL DASHBOARD) ---
            
            // Fetch User Name
            $stmt = $db->prepare("SELECT first_name FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($user) $firstName = $user['first_name'];

            // Fetch Real Messages
            try {
                // Fixed Query: Use 'location' instead of lat/lng which don't exist
                $jobsStmt = $db->query("SELECT job_id as id, title, payment as price, location as dist, description as `desc`, category FROM job LIMIT 5");
                $allJobs = $jobsStmt->fetchAll(\PDO::FETCH_ASSOC);
                
                // Add mock lat/lng since DB doesn't have them yet, to prevent JS errors
                foreach ($allJobs as &$job) {
                    $job['lat'] = 30.0444 + (mt_rand(-10, 10) / 100);
                    $job['lng'] = 31.2357 + (mt_rand(-10, 10) / 100);
                    $job['price'] = $job['price'] . ' EGP'; // Format price
                }
            } catch (\Exception $e) {
                $allJobs = []; 
                error_log("HomeController Error: " . $e->getMessage());
            }

            try {
                $msgsStmt = $db->query("SELECT sender_name as name, message as msg, time, initial, color_theme as color FROM messages LIMIT 5");
                $recentMessages = $msgsStmt->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\Exception $e) {
                // Fallback or empty if messages table issue
                $recentMessages = [];
            }

        } else {
            // --- GUEST USER (SMART LANDING PAGE) ---
            // "Visual Mirroring": Serve the exact same Dashboard but with Mock Data.
            $isGuest = true;
            
            // Mock Data for "Preview" Experience
            $allJobs = [
                ['id'=>1, 'title'=>'Dog Walking', 'price'=>'$20/hr', 'dist'=>'0.5 miles', 'desc'=>'Walk my golden retriever.', 'lat'=>30.0444, 'lng'=>31.2357, 'category'=>'Pet Care'],
                ['id'=>2, 'title'=>'Math Tutor', 'price'=>'$30/hr', 'dist'=>'1.2 miles', 'desc'=>'Help with 8th grade math.', 'lat'=>30.05, 'lng'=>31.24, 'category'=>'Tutoring'],
                ['id'=>3, 'title'=>'Lawn Mowing', 'price'=>'$40', 'dist'=>'2.0 miles', 'desc'=>'Mow the front yard.', 'lat'=>30.04, 'lng'=>31.23, 'category'=>'Yard Work'],
                ['id'=>4, 'title'=>'Delivery Driver', 'price'=>'$15/hr', 'dist'=>'1.5 miles', 'desc'=>'Deliver local packages.', 'lat'=>30.06, 'lng'=>31.25, 'category'=>'Delivery'],
            ];
            $recentMessages = [
                ['name'=>'System', 'msg'=>'Login to see messages!', 'time'=>'Now', 'initial'=>'S', 'color'=>'blue']
            ];
        }

        // Format Jobs for Map Pins (Shared Logic)
        $mapPins = array_map(function($job) {
            return [
                'id' => $job['id'],
                'lat' => (float)$job['lat'],
                'lng' => (float)$job['lng'],
                'icon' => '📍', 
                'price' => $job['price'],
                'title' => $job['title'] 
            ];
        }, $allJobs);

        $dashboardData = [
            'user_name' => $firstName,
            'selectedJob' => !empty($allJobs) ? $allJobs[0] : null,
            'mapPins' => $mapPins,
            'recommendedJobs' => $allJobs,
            'recentMessages' => $recentMessages
        ];

        // 3. Load The "Smart" View (Index.php acts as both Dashboard & Landing)
        include __DIR__ . '/../views/home/home.php';
    }
    public function help() {
        // Simple view render for Help
        include __DIR__ . '/../views/home/help.php';
    }

    public function about() {
        // Simple view render for About Us
        include __DIR__ . '/../views/home/about_us.php';
    }
}
