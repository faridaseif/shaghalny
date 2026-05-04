<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
        <a href="index.php?controller=Job&action=myJobs" class="sec">Jobs I Posted</a>
<a href="index.php?controller=Job&action=jobsIAppliedFor" class="sec sec-active">Jobs I Applied For</a>
        <a href="index.php?controller=Application&action=workHistory" class="sec">Work History</a>
      </div>
    </section>
    
    <!-- Jobs I Applied For -->
    <section class="jobs-section" data-section="applied">
  <?php if (empty($jobsIappliedFor)): ?>
      <p class='no-applicants'>You haven't applied to any jobs yet.</p>
  <?php else: ?>
  <?php foreach ($jobsIappliedFor as $job): ?>
      <article class="applied-card">
        <header class="applied-head">
          <div class="applied-main">
            <div class="applied-icon-round" style="display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.1rem; color: #1d4ed8;">
              <?= strtoupper(substr($job['first_name'], 0, 1)) ?>
            </div>
            <div>
              <h2 class="applied-title"><?php echo $job['title']; ?></h2>
              <p class="applied-by"><span style="color: #870606f7;">Posted by: </span><?php echo $job['first_name']." ".$job['last_name']; ?></p>
            </div>
          </div>
          <div class="applied-side">
            <span class="applied-time">Applied <?php echo $job['application_time']; ?></span>
            <span class="applied-badge applied-badge-ok">Status: <?php echo $job['application_status']; ?></span>
          </div>
        </header>

        <p class="applied-text">
          <?php echo $job['description']; ?>
        </p>

        <div class="applied-row">
          <span class="applied-pay"><?php echo $job['payment']." EGP"; ?></span>
        </div>

        <div class="applied-note applied-note-ok">
          <p class="applied-note-main">
            <?php
            if($job['application_status'] == 'accepted'){
            echo "🎉 Congratulations! You've been selected for this job.";
            }
            elseif($job['application_status'] == 'pending'){
            echo "📅 Your application is under review.";
            }
            elseif($job['application_status'] == 'rejected'){
            echo "🚫 Unfortunately, your application was not selected for this job.";
            }
            ?>
          </p>
          <p class="applied-note-sub">
            Start date: <?php echo $job['date']; ?>
          </p>
        </div>
      </article>
  <?php endforeach; ?>
  <?php endif; ?>
    </section>
    </section>
    </main>
    <?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
</body>
</html>