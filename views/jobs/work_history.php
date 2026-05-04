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
<a href="index.php?controller=Job&action=jobsIAppliedFor" class="sec">Jobs I Applied For</a>
        <a href="index.php?controller=Application&action=workHistory" class="sec sec-active">Work History</a>
       </div>
    </section>

<?php if (empty($workHistory)): ?>
    <section class="jobs-section" data-section="history">
        <p class='no-applicants'>No completed work history yet.</p>
    </section>
<?php else: ?>
<?php foreach ($workHistory as $job): ?>
    <section class="jobs-section" data-section="history">
      <article class="hist-card">
        <header class="hist-head">
          <div class="hist-main">
            <div class="hist-icon" style="display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.1rem; color: #1d4ed8;">
              <?= strtoupper(substr($job['first_name'], 0, 1)) ?>
            </div>
            <div>
              <h2 class="hist-title"><?php echo $job['title']; ?></h2>
              <p class="hist-by"><span style="color: #144878ff;">Employer: </span><?php echo $job['first_name'] . ' ' . $job['last_name']; ?></p>
            </div>
          </div>
          <div class="hist-side">
            <span class="hist-pay"><?php echo $job['payment']." EGP"; ?></span>
            <span class="hist-when">Accepted at <?php echo $job['accepted_at']; ?></span>
          </div>
        </header>

        <div class="hist-meta">
          <div>
            <span class="hist-meta-label"><span style="color: #870606f7;">Duration: </span></span>
            <span class="hist-meta-value"><?php echo $job['duration']." minutes"; ?></span>
          </div>
        </div>
        <div class="hist-box hist-box-ok">
          <p class="hist-box-title">Review from <?php echo $job['first_name']; ?>:</p>
          <p class="hist-box-text">
            <?php echo $job['review_text']; ?>
          </p>
        </div>
      </article>
    </section>
<?php endforeach; ?>
<?php endif; ?>
</main>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
</body>
</html>