<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Post a Job | Shaghalny</title>
  <link rel="stylesheet" href="assets/css/post_jobs.css" />
  <link rel="stylesheet" href="assets/css/Header.css" />
  <link rel="stylesheet" href="assets/css/footer.css" />
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
    <section class="post-wrap">
      <header class="post-head">
        <h1 class="post-title">
          <iconify-icon icon="noto:spiral-notepad" width="30" height="30" style="vertical-align: text-bottom; margin-right: 0.5rem;"></iconify-icon>
          Post a New Job
        </h1>
        <p class="post-sub">Share a quick description so nearby teens can help out.</p>
      </header>

      <!-- Relative action so it works regardless of folder name -->
      <form class="post-form" action="index.php?controller=Job&action=postJob" method="post">          <input
            id="job_title"
            name="job_title"
            type="text"
            class="input"
            placeholder="e.g., Dog walking needed"
            required
          />
        </div>

        <div class="field field-row">
          <div class="field-half">
            <label for="category" class="label">Category</label>
            <div class="select-wrap">
              <select id="category" name="category" class="select" required>
                <!-- Example options; backend can generate real list -->
                <option value="" disabled selected hidden>Select a category</option>
                <option value="pet care">🐕 Pet Care</option>
                <option value="tutoring">📚 Tutoring</option>
                <option value="yard work">🌿 Yard Work</option>
                <option value="errands">🛒 Errands</option>
                <option value="other">other..</option>
              </select>
            </div>
          </div>

          <div class="field-half">
            <label for="payment" class="label">Payment</label>
            <div class="input-with-tag">
              <input
                id="payment"
                name="payment"
                type="text"
                class="input"
                required
              />
            </div>
            <p class="hint">in EGP</p>
          </div>
        </div>

        <div class="field">
          <label for="description" class="label">Description</label>
          <textarea
            id="description"
            name="description"
            class="textarea"
            rows="5"
            placeholder="Describe what you need help with..."
            maxlength="600"
            required
          ></textarea>
          <div class="count-row">
            <span class="hint">Include timing, expectations, and any special instructions.</span>
            <span class="count" data-count>0 / 600</span>
          </div>
        </div>

        <div class="field">
          <label for="location" class="label">Location</label>
          <input
            id="location"
            name="location"
            type="text"
            class="input"
            placeholder="Your address or nearby landmark"
            required
          />
          <p class="hint">You can share exact details with the worker after you accept them.</p>
        </div>

        <div class="field field-row">
          <div class="field-half">
            <label for="date" class="label">Date</label>
            <input id="date" name="date" type="date" class="input" />
          </div>
          <div class="field-half">
            <label for="time" class="label">Preferred Time</label>
            <input id="time" name="time" type="time" class="input" />
          </div>
        </div>

        <div class="field field-row">
          <div class="field-half">
            <label for="duration" class="label">Estimated Duration</label>
            <input
              id="duration"
              name="duration"
              type="number"
              step="0.01" 
              min="0"
              class="input"
              placeholder="in minutes"
            />
          </div>
          <div class="field-half">
            <label class="label">Visibility</label>
            <div class="chip-row">
              <label class="chip">
                <input type="radio" name="visibility" value="nearby" checked />
                <span>Nearby teens</span>
              </label>
              <label class="chip">
                <input type="radio" name="visibility" value="all" />
                <span>All teens</span>
              </label>
            </div>
          </div>
        </div>

        <footer class="post-footer">
          <button type="submit" class="btn-main">Post Job</button>
        </footer>
      </form>
    </section>
  </main>
  
  <!-- Success Modal -->
  <div class="modal-bg" id="successModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
    <div class="modal-content" style="background:white; padding:2rem; border-radius:12px; text-align:center; max-width:400px; width:90%; position:relative;">
        <div style="width:60px; height:60px; background:#10B981; color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem; font-size:30px;">
          <iconify-icon icon="fa-solid:check"></iconify-icon>
        </div>
        <h2 style="margin-bottom:0.5rem; color:#1F2937;">Success!</h2>
        <p style="color:#6B7280; margin-bottom:1.5rem;">Your job has been posted successfully.</p>
        <button id="goToHomeBtn" class="btn-main" style="width:100%;">Go to Homepage</button>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('success')) {
        const modal = document.getElementById('successModal');
        modal.style.display = 'flex';
        
        document.getElementById('goToHomeBtn').addEventListener('click', () => {
           window.location.href = 'index.php?page=home';
        });
      }
    });
  </script>

  <?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>

  <script src="assets/js/post_job.js"></script>
</body>
</html>


