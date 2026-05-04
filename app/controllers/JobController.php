<?php


require_once __DIR__ . '/../models/Job.php';
require_once __DIR__ . '/../models/Application.php';
require_once __DIR__ . '/../../config/database.php';


class JobController
{
    private Job $jobModel;
    public function __construct($db)
    {
        $this->jobModel = new Job($db);
    }

    public function myJobs()
    {
        $user_id = $_SESSION['user_id'] ?? 1; // Default to 1 for testing if session not set
        try {
            $jobsToRate = $this->jobModel->checkAndCloseExpiredJobs();
        } catch (Exception $e) {
            // Log error or ignore if column is missing during development
            $jobsToRate = [];
            error_log("ExpiredJobs Check Failed: " . $e->getMessage());
        }
        $jobs = $this->jobModel->getJobsByUserId($user_id);
        
        foreach ($jobs as &$job) {
            $job['posted_time'] = $this->timeAgo($job['posted_time']);            
        }
        unset($job); // Fix: Break the reference to prevent view loop corruption
        
        // Initialize variables expected by the view
        $acceptedApp = null; 

        require __DIR__ . '/../views/jobs/my_jobs.php';
    }

    public function create(): void
    {
        require __DIR__ . '/../views/jobs/post_job.php';
    }

    public function postJob(): void
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        $result = $this->jobModel->createJob($_POST);

        if ($result) {
            $_SESSION['success_message'] = "Job posted successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to post job!";
        }

        header("Location: index.php?controller=Job&action=create&success=1");
        exit;
    }

    private function timeAgo($datetime)
    {
        $posted_timestamp = strtotime($datetime);
        $diff = time() - $posted_timestamp;

        if ($diff < 60) return "Just now";
        if ($diff < 3600) return floor($diff / 60) . " minutes ago";
        if ($diff < 86400) return floor($diff / 3600) . " hours ago";

        return floor($diff / 86400) . " days ago";
    }

public function jobsIAppliedFor()
{
    $user_id =$_SESSION['user_id'] ?? 1;
    $jobsIappliedFor = $this->jobModel->getJobsIappliedFor($user_id);
    require __DIR__ . '/../views/jobs/job_i_applied.php';
}


    function jobClose(){
        $job_id = $_GET['id'];
        $this->jobModel->changeStatus($job_id, 'closed');
        header("Location: index.php?controller=Job&action=myJobs");
        exit;
    }

    function editJob(){
$job_id = $_POST['job_id'];
$new_payment = $_POST['payment'];
$new_time = $_POST['time'];
$this->jobModel->SetJobNewValues($job_id, $new_time, $new_payment);
header("Location: index.php?controller=Job&action=myJobs");
exit;

    }

    public function searchJobs($job_title)
    {
       $job = $this->jobModel->searchJobs($job_title);
       return $job;
    }
}
