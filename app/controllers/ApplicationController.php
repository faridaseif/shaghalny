<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Job.php';
require_once __DIR__ . '/../models/Application.php';
require_once __DIR__ . '/../../config/database.php';

class ApplicationController
{
    private Application $applicationModel;
    private Job $jobModel;

    public function __construct($db)
    {
        $this->applicationModel = new Application($db);
        $this->jobModel = new Job($db);
    }

    public function Applications()
    {
        // 1. Read job_id from URL
        $job_id = $_GET['id'] ?? null;

        if (!$job_id) {
            die("No job ID provided.");
        }

        // 2. Fetch jobs for the background view (Dashboard context)
        $user_id = $_SESSION['user_id'] ?? 1; // Fallback or handle auth properly
        $jobs = $this->jobModel->getJobsByUserId($user_id);

        
        foreach ($jobs as &$job) {
            $job['posted_time'] = $this->timeAgo($job['posted_time']);        
        }
        unset($job);

        // 3. Fetch applicants for this specific job
        $raw_applicants = $this->applicationModel->viewApplicants($job_id);

        $unique_applicants = [];

        foreach ($raw_applicants as $row) {
            $uid = $row['applicant_id'];
            if (!isset($unique_applicants[$uid])) {
                $unique_applicants[$uid] = $row;
            } else {
                // If we already have this user, but the new row is 'accepted', upgrade it.
                if (strtolower($row['application_status']) === 'accepted') {
                    $unique_applicants[$uid] = $row;
                }
            }
        }

        $applicants = []; // Pending list
        $acceptedApp = null; // Single accepted applicant

        foreach ($unique_applicants as $applicant) {
            $applicant['application_time'] = $this->timeAgo($applicant['application_time']);
            
            if (strtolower($applicant['application_status']) === 'accepted') {
                $acceptedApp = $applicant;
            } else {
                $applicants[] = $applicant;
            }
        }

        // 4. Fetch the specific job details for the modal context
        $currentJob = null;
        if ($job_id) {
            // Re-use loop or fetch fresh to ensure we have the correct single job
            foreach ($jobs as $j) {
                if ($j['job_id'] == $job_id) {
                    $currentJob = $j;
                    break;
                }
            }
        }

        // 5. Set flag to open modal automatically
        $open_modal = true;
        require APP_ROOT . "/app/views/jobs/my_jobs.php";
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

  function acceptApplication(){
    $applicationId = $_GET['app'] ?? null;
    if (!$applicationId) {
        die("No application ID provided.");
    }
    $this->applicationModel->acceptApp($applicationId);

    // Get job ID from application and update job status
    $jobId = $this->applicationModel->getJobId($applicationId);
    if ($jobId) {
        $this->jobModel->changeStatus($jobId, 'pending');
    }
    
    // Redirect back to the APPLICATIONS list with unique timestamp to preventing caching
    $url = "index.php?controller=Application&action=Applications&id=" . $jobId . "&t=" . time();
    header("Location: " . $url);
    exit;
  }

  function declineApplication(){
    $applicationId = $_GET['app'] ?? null;
    if (!$applicationId) {
        die("No application ID provided.");
    }
    $this->applicationModel->declineApp($applicationId); 
    
    $jobId = $this->applicationModel->getJobId($applicationId);
    $url = "index.php?controller=Application&action=Applications&id=" . $jobId . "&t=" . time();
    header("Location: " . $url);
    exit;
  }


  public function workHistory(){
    $user_id = $_SESSION['user_id'] ?? 1;
    $workHistory = $this->applicationModel->getWorkHistory($user_id);
    require APP_ROOT . "/app/views/jobs/work_history.php";
  }

  public function submitReview()
  {
      if ($_SERVER["REQUEST_METHOD"] !== "POST") {
          header("Location: index.php?controller=Job&action=myJobs");
          exit;
      }

      $reviewer_id = $_SESSION['user_id'] ?? 1;
      $reviewed_user_id = $_POST['applicant_id'] ?? null;
      $rating = $_POST['rating'] ?? null;
      $review_text = $_POST['review_text'] ?? '';
      $job_id = $_POST['job_id'] ?? null;

      if ($reviewed_user_id && $rating) {
          $this->applicationModel->submitReview($reviewer_id, $reviewed_user_id, $rating, $review_text);
          // Ideally, we could also mark the job/application as 'reviewed' to prevent duplicate popups, 
          // but for now we rely on the JS queue or page refresh clearing the list.
      }

      header("Location: index.php?controller=Job&action=myJobs");
      exit;
  }

     function apply(){
        try {
            if (isset($_GET['job_id'])) {
                $job_id = $_GET['job_id'];
                $applicant_id = $_SESSION['user_id'] ?? 1; // Fallback to 1 if session missing
                
                // Only increment if the application was successfully created
                $result = $this->applicationModel->applyForJob($job_id, $applicant_id);
                if ($result) {
                    $this->applicationModel->incrementNumOfApplicants($job_id);
                }
            }
        } catch (Throwable $e) {
            echo "<div style='padding: 20px; background: #ffebee; color: #b71c1c; border: 1px solid #ffcdd2; margin: 20px; font-family: sans-serif;'>";
            echo "<h3>⚠️ Application Error</h3>";
            echo "<p><strong>Error Message:</strong> " . $e->getMessage() . "</p>";
            echo "<p><strong>File:</strong> " . $e->getFile() . " on line " . $e->getLine() . "</p>";
            exit;
        } 
        
        // Success! Redirect back to map
        header("Location: index.php?controller=Map&action=map&applied=1");
        exit;

    }
}
