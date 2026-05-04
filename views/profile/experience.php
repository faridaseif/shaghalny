<?php
$data = $viewData ?? [];
$errors = $viewErrors ?? [];
$firstExp = $data['experiences'][0] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Experience Info Form</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/form.css">
</head>
<body>
<div class="container">
  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $e): ?>
        <div><?php echo htmlspecialchars($e); ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <form id="experienceForm" method="POST" action="index.php?action=experience">
    <div class="section">
      <h3>Work Experience</h3>

      <!-- Years of Experience -->
      <label for="years-experience">How many years of experience do you have? <span class="required">*</span></label>
      <select id="years-experience" name="years-experience">
        <option value="">Select</option>
        <option value="No">No Experience</option>
        <option value="Less than 1">Less than 1 year</option>
        <option value="1">1 year</option>
        <option value="2">2 years</option>
        <option value="3">3 years</option>
        <option value="4">4 years</option>
        <option value="5+">5+ years</option>
      </select>

      <!-- Experience Details (hidden if no experience) -->
      <div id="experience-details" style="display:none; margin-top:15px;">
        <label for="job-title">Job Title <span class="required">*</span></label>
        <input type="text" id="job-title" name="job-title" placeholder="Enter job title" value="<?php echo htmlspecialchars($firstExp['job_title'] ?? ''); ?>">

        <label for="company">Company / Organization <span class="required">*</span></label>
        <input type="text" id="company" name="company" placeholder="Enter company/organization name" value="<?php echo htmlspecialchars($firstExp['company'] ?? ''); ?>">

        <label for="job-category">Job Category <span class="required">*</span></label>
        <select id="job-category" name="job-category">
          <option value="">Select Category</option>
          <option>IT / Software</option>
          <option>Business / Management</option>
          <option>Marketing / Sales</option>
          <option>Engineering</option>
          <option>Healthcare</option>
          <option>Education</option>
        </select>

        <label for="experience-type">Experience Type <span class="required">*</span></label>
        <select id="experience-type" name="experience-type">
          <option value="">Select Type</option>
          <option>Full Time</option>
          <option>Part Time</option>
          <option>Freelance</option>
          <option>Intern</option>
          <option>Volunteer</option>
        </select>

        <label for="start-date">Starting From <span class="required">*</span></label>
        <input type="month" id="start-date" name="start-date" value="<?php echo htmlspecialchars($firstExp['start_date'] ?? ''); ?>">

        <label for="end-date">Ending In <span class="required">*</span></label>
        <input type="month" id="end-date" name="end-date" value="<?php echo htmlspecialchars($firstExp['end_date'] ?? ''); ?>">
        <div class="form-check">
         <input class="form-check-input" type="checkbox" id="publicProfile" <?php echo !empty($firstExp['working']) ? 'checked' : ''; ?>><span>
         <label class="form-check-label" for="publicProfile">Currently working</span></label>
        </div>

       

        <div style="margin-top:15px;">
          <button type="button" id="save-experience" class="btn btn-primary" style="margin-right:20px;">Save</button>
          <button type="button" id="add-experience" class="btn btn-secondary" style="margin-left:20px;">Add More</button>
        </div>
        
      </div>

      <!-- Container to show added experiences -->
      <div id="experience-list" style="margin-top:20px;"></div>

    </div>
      <button type="submit">Continue ➔</button>
  </form>
</div>

<script src="assets/js/form.js?v=<?php echo time(); ?>"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const setVal = (id, val) => { const el = document.getElementById(id); if (el && val) el.value = val; };
    setVal('years-experience', '<?php echo addslashes($firstExp['years_experience'] ?? ''); ?>');
    setVal('job-category', '<?php echo addslashes($firstExp['job_category'] ?? ''); ?>');
    setVal('experience-type', '<?php echo addslashes($firstExp['experience_type'] ?? ''); ?>');
    if ('<?php echo addslashes($firstExp['years_experience'] ?? ''); ?>' && '<?php echo addslashes($firstExp['years_experience'] ?? ''); ?>' !== 'No') {
      const details = document.getElementById('experience-details');
      if (details) details.style.display = 'block';
    }
  });
</script>


<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="mb-3">
                <div class="success-icon-circle mx-auto d-flex align-items-center justify-content-center bg-success text-white rounded-circle" style="width: 60px; height: 60px; font-size: 30px;">
                    <i class="fas fa-check"></i>
                </div>
            </div>
            <h4 class="mb-2">Success!</h4>
            <p class="text-muted">Your changes have been saved successfully.</p>
            <button type="button" class="btn btn-primary w-100" id="successContinueBtn">Continue</button>
        </div>
    </div>
</div>

<?php if (!empty($viewSuccess)): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('successModal'));
        document.getElementById('successContinueBtn').addEventListener('click', function() {
            window.location.href = 'index.php?action=expertise';
        });
        myModal.show();
    });
</script>
<?php endif; ?>
</html>
