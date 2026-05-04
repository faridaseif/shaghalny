<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/Model.php';

class Job extends Model
{
    protected $table = "job";

    public function createJob(array $post): bool
    {
        $user_id = $_SESSION['user_id'] ?? 1;

        $sql = "INSERT INTO {$this->table}
            (title, category, payment, description, location, date, time, duration, visibility,user_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?)";

        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            $post['job_title'],
            $post['category'],
            $post['payment'],
            $post['description'],
            $post['location'],
            $post['date'],
            $post['time'],
            $post['duration'],
            $post['visibility'],
            $user_id
        ]);
        return $result;
    }


    function getJobsByUserId($user_id):array{
        $sql = "SELECT j.*, COUNT(a.application_id) as num_of_apps 
                FROM {$this->table} j 
                LEFT JOIN application a ON j.job_id = a.job_id 
                WHERE j.user_id = ? 
                GROUP BY j.job_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getJobsIappliedFor($user_id):array{
        $sql="SELECT job.title, users.first_name, users.last_name, application.application_time, 
              application.status as application_status, job.description, job.payment, job.date
              FROM {$this->table} job
              INNER JOIN application ON job.job_id = application.job_id
              INNER JOIN users ON job.user_id = users.user_id
              WHERE application.applicant_id = ?";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function changeStatus($job_id, $status = 'pending') {
        try {
            $sql = "UPDATE {$this->table} SET status = ? WHERE job_id = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$status, $job_id]);
        } catch (PDOException $e) {
            // Suppress error if column 'status' doesn't exist to prevent crash
            if (strpos($e->getMessage(), 'Unknown column') !== false) {
                return false; 
            }
            throw $e;
        }
    }

    public function checkAndCloseExpiredJobs() {
        $closedJobs = [];
        
        // 1. Select expired jobs with their accepted applicants
        $sql = "SELECT j.job_id, j.payment, j.title, a.applicant_id, u.first_name, u.last_name 
                FROM {$this->table} j
                INNER JOIN application a ON j.job_id = a.job_id
                INNER JOIN users u ON a.applicant_id = u.user_id
                WHERE j.status = 'pending' 
                AND a.status = 'accepted'
                AND DATE_ADD(a.accepted_at, INTERVAL j.duration MINUTE) < NOW()";
        
        $stmt = $this->conn->query($sql);
        
        // PDO query returns statement on success, convert result check
        if ($stmt && $stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $job_id = $row['job_id'];
                $applicant_id = $row['applicant_id'];
                $payment = $row['payment'];

                // 2. Update user_public_info (jobs_completed)
                // 2. Update user_public_info (jobs_completed, total_jobs, recent_activity)
                $updatePublic = "UPDATE user_public_info 
                                 SET jobs_completed = jobs_completed + 1, 
                                     total_jobs = total_jobs + 1,
                                     recent_activity = ?
                                 WHERE user_id = ?";
                $stmt1 = $this->conn->prepare($updatePublic);
                $stmt1->execute([$row['title'], $applicant_id]);

                // 3. Update users table (total_earnings)
                $updateUsers = "UPDATE users SET total_earnings = total_earnings + ? WHERE user_id = ?";
                $stmt2 = $this->conn->prepare($updateUsers);
                $stmt2->execute([$payment, $applicant_id]);

                // 4. Close the job
                $updateJob = "UPDATE {$this->table} SET status = 'closed' WHERE job_id = ?";
                $stmt3 = $this->conn->prepare($updateJob);
                $stmt3->execute([$job_id]);

                // 5. Add to closed jobs list
                $closedJobs[] = $row;
            }
        }
        return $closedJobs;
    }

    function SetJobNewValues($job_id, $new_time, $new_payment) {
        $sql = "
          UPDATE job
          SET time = ?, payment = ?
          WHERE job_id = ?";
    
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$new_time, $new_payment, $job_id]);
    }

    function getJobStatus($job_id){
        $sql = "SELECT status FROM {$this->table} WHERE job_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$job_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public static function getAllJobs($filters = [])
    {
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT j.*, u.first_name, u.last_name, u.email 
                FROM job j 
                JOIN users u ON j.user_id = u.user_id 
                WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (j.title LIKE ? OR j.description LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
            $term = "%" . $filters['search'] . "%";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($filters['status'])) {
            $sql .= " AND j.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['category'])) {
            $sql .= " AND j.category = ?";
            $params[] = $filters['category'];
        }

        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'oldest':
                $sql .= " ORDER BY j.job_id ASC";
                break;
            case 'highest_pay':
                $sql .= " ORDER BY j.payment DESC";
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY j.job_id DESC";
                break;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function countAll() {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT COUNT(*) FROM job";
        return $db->query($sql)->fetchColumn();
    }

    public static function countNewThisMonth() {
        $db = Database::getInstance()->getConnection();
        // job table has 'date' column which stores job date, assuming we want to track by creation/id ideally timestamp?
        // Checking schema: 'date' is date type. Let's use it for now as creation date proxy if no created_at
        // Actually job_id is auto_increment, but best to use date if available.
        // Assuming 'date' is creating date or similar.
        $sql = "SELECT COUNT(*) FROM job WHERE MONTH(date) = MONTH(CURRENT_DATE()) AND YEAR(date) = YEAR(CURRENT_DATE())";
        return $db->query($sql)->fetchColumn();
    }
}