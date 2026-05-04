<?php
if (!isset($jobs)) {
    header("Location: /shaghalny/index.php?controller=Job&action=myJobs");
    exit;
}
require_once __DIR__ . '/../../controllers/ApplicationController.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Jobs | Shaghalny</title>
  <!-- Absolute URL path from web root (htdocs) -->
<link rel="stylesheet" href="assets/css/my_jobs.css?v=<?php echo time(); ?>" />
  <link rel="stylesheet" href="assets/css/Header.css?v=<?php echo time(); ?>" />
  <link rel="stylesheet" href="assets/css/footer.css?v=<?php echo time(); ?>" />
  <!-- React (CDNJS) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/react/18.2.0/umd/react.development.js" crossorigin></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/react-dom/18.2.0/umd/react-dom.development.js" crossorigin></script>
  <!-- Framer Motion -->
  <script src="https://unpkg.com/framer-motion@10.16.4/dist/framer-motion.js"></script>

  <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
</head>
<body>
  <?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>
  <main class="main">
    <!-- Dashboard header + filters -->
    <section class="myjobs-dashboard">
    <h1 class="title">
  <iconify-icon icon="noto:handbag" width="30" height="30" style="vertical-align: text-bottom; margin-right: 0.5rem;"></iconify-icon>
  My Jobs Dashboard
</h1>
      <div class="sections" role="tablist">
        <a href="index.php?controller=Job&action=myJobs" class="sec sec-active">Jobs I Posted</a>
<a href="index.php?controller=Job&action=jobsIAppliedFor" class="sec">Jobs I Applied For</a>
        <a href="index.php?controller=Application&action=workHistory" class="sec">Work History</a>
      </div>
    </section>

<section class="jobs-section" data-section="posted">
  <div class="jobs-grid">
    <?php foreach ($jobs as $job): ?>
    <article class="job-card" data-job-id="<?= $job['job_id'] ?>" data-payment="<?= $job['payment'] ?>" data-time="<?= $job['time'] ?>">
      <header class="job-card-header">
        <div class="job-title-row">
          <h2 class="job-title"><?= htmlspecialchars($job['title']) ?></h2>
          <status class="status"><?= htmlspecialchars($job['status'] ?? 'Open') ?></span>
        </div>
      </header>

      <div class="card">
        <p class="job"><?= htmlspecialchars($job['description']) ?></p>
      </div>

      <div class="job-grid">
        <div class="money">
          <span class="amount"><?= htmlspecialchars($job['payment']) ?></span>
          <span class="amount-label">EGP total</span>
        </div>
        <div class="date">
          <span><?= htmlspecialchars($job['posted_time']) ?></span>
        </div>
      </div>

      <footer class="job-footer">
        <div class="job-extra">
          <span class="job-extra-label">Applications:</span>
          <span class="job-extra-value"><?= htmlspecialchars($job['num_of_apps']) ?></span>
        </div>
        <div class="job-actions">
        <a href="index.php?controller=Application&action=Applications&id=<?= $job['job_id'] ?>" 
   class="view-link">
   View Applications
</a>
          <button type="button" class="edit edit-job">Edit</button>
          <a href="index.php?controller=Job&action=jobClose&id=<?= $job['job_id'] ?>" 
             class="close">Close</a>
  </div>
      </footer>
    </article>
    <?php endforeach; ?>
  </div>
</section>

        <!-- End job card examples -->

      </div>
  </section>

    <!-- Applications modal (hidden by default; content to be filled by backend later) -->
    <section class="applications-modal" id="applications-modal" aria-hidden="true">
      <div class="applications-backdrop" data-close-modal></div>

      <div class="applications-dialog" role="dialog" aria-modal="true" aria-labelledby="applications-title">

        <div class="applications-list">
        <?php $applicants = $applicants ?? []; ?>
        <!-- $acceptedApp is already set by the controller -->

        <?php if ($acceptedApp): ?>
            <!-- Show only the accepted applicant --->
            <article class="application-item accepted-view-card">
                <header class="accepted-header">
                    <div class="accepted-profile">
                        <div class="profile-avatar">
                             <?= strtoupper(substr($acceptedApp['first_name'], 0, 1)) ?>
                        </div>
                        <div class="profile-info">
                            <h3 class="profile-name">
                                <?= htmlspecialchars($acceptedApp['first_name']) ?>
                                <?php if (isset($currentJob) && $currentJob['status'] === 'closed'): ?>
                                    <span class="badge-on-job modal-badge" style="color: #4338ca; background: #e0e7ff; border-color: #c7d2fe;">
                                        <iconify-icon icon="lucide:check-circle-2" width="14" style="vertical-align: text-bottom;"></iconify-icon>
                                        Completed the job
                                    </span>
                                <?php else: ?>
                                    <span class="badge-on-job modal-badge">
                                        <iconify-icon icon="lucide:check-circle" width="14" style="vertical-align: text-bottom;"></iconify-icon>
                                        On the Job
                                    </span>
                                <?php endif; ?>
                            </h3>
                            <p class="profile-sub"><?= (isset($currentJob) && $currentJob['status'] === 'closed') ? 'Job successfully finished' : 'Hired for this task' ?></p>
                        </div>
                    </div>
                    
                    <!-- New Message Button aligned right -->
                    <button type="button" class="btn primary btn-message-action" onclick="window.location.href='index.php?controller=Message&action=inbox&recipient_id=<?= $acceptedApp['applicant_id'] ?>'">
                        <iconify-icon icon="lucide:message-square" width="16" style="margin-right: 4px; vertical-align: middle;"></iconify-icon>
                        Message
                    </button>
                </header>

                <div class="accepted-stats-grid">
                    <div class="stat-box">
                        <span class="stat-label">Rating</span>
                        <div class="stat-value">
                            <iconify-icon icon="ph:star-fill" class="star-icon"></iconify-icon>
                            <?= number_format($acceptedApp['average_rating'], 1) ?>
                        </div>
                        <span class="stat-sub"><?= $acceptedApp['total_reviews'] ?> reviews</span>
                    </div>
                    
                    <div class="stat-box">
                        <div class="stat-value"><?= $acceptedApp['jobs_completed'] ?></div>
                        <span class="stat-label">Jobs Done</span>
                    </div>

                    <div class="stat-box activity-box">
                        <span class="stat-label">Recent Activity</span>
                        <p class="stat-text">
                            "<?= htmlspecialchars($acceptedApp['recent_activity'] ?? 'No recent activity') ?>"
                        </p>
                    </div>
                </div>

                <!-- Script removed (Using Success Modal instead) -->

            </article>

        <?php elseif (empty($applicants)): ?>
            <p class="no-applicants">No applications found for this job yet.</p>
        <?php else: ?>
            <!-- Show list of pending applicants -->
            <?php foreach ($applicants as $app): ?>
              <article class="application-item">
                <div class="application-left">
                  <div class="applicant-main">
                    <div class="applicant-top-row">
                      <div>
                        <div class="applicant-name-row">
                          <h3 class="applicant-name"><?= htmlspecialchars($app['first_name']) ?></h3>
                        </div>
                        <p class="applicant-meta"><?= htmlspecialchars($app['average_rating']).htmlspecialchars($app['jobs_completed']) ?></p>
                      </div>
                      <span class="applicant-time"><?= htmlspecialchars($app['application_time']) ?></span>
                    </div>
                    <p class="applicant-message">
                    <?= htmlspecialchars($app['about_me']) ?>
                    </p>
                  </div>
                </div>
                <div class="application-actions">
                <a href="index.php?controller=Application&action=acceptApplication&app=<?= $app['application_id'] ?>"
                class="btn accept">Accept</a>
              <a href="index.php?controller=Application&action=declineApplication&app=<?= $app['application_id'] ?>"
               class="btn decline">Decline</a>
               <button type="button" class="btn primary">Message</button>
                </div>
              </article>
           <?php endforeach; ?>
        <?php endif; ?>

        <footer class="applications-footer">
          <button type="button" class="btn close-modal" data-close-modal>Close</button>
        </footer>
      </div>
    </section>

    <!-- Contact modal (hidden by default; content to be filled by backend later) -->
    <section class="applications-modal contact-modal" id="contact-modal" aria-hidden="true">
      <div class="applications-backdrop" data-close-contact></div>

      <div class="applications-dialog" role="dialog" aria-modal="true" aria-labelledby="contact-title">
      <div class="header-row">  
      <header class="applications-header">
          <h2 class="applications-title" id="contact-title">
            Contact <span class="contact-worker-name">Marcus T.</span>
          </h2>
          <p class="contact-job-label">
            For job: <span class="contact-job-title">Package Delivery</span>
          </p>
        </header>
        <button id="mess-but">Message</button>
        </div>
        <div class="applications-list contact-body">
          <!--
            Example structure – later replace with PHP:
            <?php // Contact info for the assigned worker ?>
          -->

          <div class="contact-row">
            <div class="contact-label">Phone</div>
            <div class="contact-value">+1 (555) 123‑4567</div>
          </div>

          <div class="contact-row">
            <div class="contact-label">Email</div>
            <div class="contact-value">marcus@example.com</div>
          </div>

          <p class="contact-note">
            Only share sensitive information (like exact addresses) once you’re comfortable with the worker.
          </p>
        </div>

        <footer class="applications-footer">
          <button type="button" class="btn close-modal" data-close-contact>Close</button>
        </footer>
      </div>
    </section>

            </section>

    <!-- Edit Job Modal -->
    <section class="applications-modal edit-job-modal" id="edit-job-modal" aria-hidden="true">
      <div class="applications-backdrop" data-close-edit></div>

      <div class="applications-dialog" role="dialog" aria-modal="true" aria-labelledby="edit-job-title">
        <header class="applications-header">
          <h2 class="applications-title" id="edit-job-title">
            Edit Job: <span class="edit-job-name"></span>
          </h2>
        </header>

        <div class="applications-list contact-body">
        <form id="edit-job-form" action="index.php?controller=Job&action=editJob" method="POST">
            <input type="hidden" id="edit-job-id" name="job_id">
            
            <div class="form-group">
              <label for="edit-payment" class="form-label">Payment (EGP)</label>
              <input type="number" id="edit-payment" name="payment" class="form-input" required>
            </div>

            <div class="form-group">
              <label for="edit-time" class="form-label">Time</label>
              <input type="time" id="edit-time" name="time" class="form-input" required>
            </div>
            
        <footer class="applications-footer">
          <button type="button" class="btn" data-close-edit>Cancel</button>
          <button type="submit" id="save-job-btn" class="btn primary">Save Changes</button>        </footer>
          </form>
        </div>
      </div>
    </section>

    <!-- Rate Applicant Modal -->
    <section class="applications-modal rate-modal" id="rate-modal" aria-hidden="true">
      <div class="applications-backdrop" data-close-rate></div>

      <div class="applications-dialog" role="dialog" aria-modal="true" aria-labelledby="rate-title">
        <header class="applications-header">
          <h2 class="applications-title" id="rate-title">
            Rate Applicant: <span class="rate-applicant-name"></span>
          </h2>
          <p class="rate-job-label">Job: <span class="rate-job-title"></span></p>
        </header>

        <div class="applications-list contact-body">
          <form id="rate-form" action="index.php?controller=Application&action=submitReview" method="POST">
            <input type="hidden" id="rate-job-id" name="job_id">
            <input type="hidden" id="rate-applicant-id" name="applicant_id">
            
            <div class="form-group">
              <label class="form-label">Rating</label>
              <div class="star-rating">
                <!-- Simple radio inputs for stars -->
                <input type="radio" id="star5" name="rating" value="5" /><label for="star5" title="5 stars">★</label>
                <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="4 stars">★</label>
                <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="3 stars">★</label>
                <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="2 stars">★</label>
                <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="1 star">★</label>
              </div>
            </div>

            <div class="form-group">
              <label for="review-text" class="form-label">Review</label>
              <textarea id="review-text" name="review_text" class="form-input" rows="4" placeholder="Share your experience..."></textarea>
            </div>
            
            <footer class="applications-footer">
              <button type="button" class="btn" data-close-rate>Cancel</button>
              <button type="submit" class="btn primary">Submit Review</button>
            </footer>
          </form>
        </div>
      </div>
    </section>
  </main>

  <script>
    window.jobsToRate = <?php echo json_encode($jobsToRate ?? []); ?>;
  </script>
  <script>
    window.jobsToRate = <?php echo json_encode($jobsToRate ?? []); ?>;
  </script>
  <script src="assets/js/my_jobs.js"></script>
  
  <?php if (isset($open_modal) && $open_modal): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Clean URL to prevent re-opening on refresh
        const cleanUrl = 'index.php?controller=Job&action=myJobs';
        window.history.replaceState({}, document.title, cleanUrl);

        // Custom Modal Opening Logic
        const modal = document.getElementById('applications-modal');
        if (modal) {
            modal.classList.add('is-open'); 
            modal.setAttribute('aria-hidden', 'false');
        }
    });
  </script>
  <?php endif; ?>
  </div> <!-- ensure main container is closed if needed, but it seems closed above -->
  <?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
</body>
</html>