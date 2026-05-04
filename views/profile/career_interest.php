<?php
$data = $viewData ?? [];
$errors = $viewErrors ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Career Interest Form</title>

  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

  <link rel="stylesheet" href="assets/css/form.css">
  
</head>

<body>

<div class="container">
  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $e): ?>
        <div><?php echo htmlspecialchars($e); ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <form id="careerInterestForm" method="POST" action="index.php?action=career_interest">

    <!-- CAREER LEVEL -->
    <div class="section">
      <h3>Current Career Level</h3>
      <select id="careerLevel" name="career_level">
        <option value="">Select Career Level</option>
        <option <?php echo (($data['career_level'] ?? '')==='Student')?'selected':''; ?>>Student</option>
        <option <?php echo (($data['career_level'] ?? '')==='Entry-level')?'selected':''; ?>>Entry-level</option>
        <option <?php echo (($data['career_level'] ?? '')==='Experienced')?'selected':''; ?>>Experienced</option>
        <option <?php echo (($data['career_level'] ?? '')==='Manager')?'selected':''; ?>>Manager</option>
        <option <?php echo (($data['career_level'] ?? '')==='Senior Management')?'selected':''; ?>>Senior Management</option>
        <option <?php echo (($data['career_level'] ?? '')==='Other')?'selected':''; ?>>Other</option>
      </select>
    </div>

    <!-- JOB TYPE -->
    <div class="section">
      <h3>Type of Jobs Interested In</h3>
      <select id="jobType" name="job_type">
        <option value="">Select Job Type</option>
        <option <?php echo (($data['job_type'] ?? '')==='Full time')?'selected':''; ?>>Full time</option>
        <option <?php echo (($data['job_type'] ?? '')==='Part Time')?'selected':''; ?>>Part Time</option>
        <option <?php echo (($data['job_type'] ?? '')==='Freelance')?'selected':''; ?>>Freelance</option>
        <option <?php echo (($data['job_type'] ?? '')==='Intern')?'selected':''; ?>>Intern</option>
        <option <?php echo (($data['job_type'] ?? '')==='Volunteer')?'selected':''; ?>>Volunteer</option>
      </select>
    </div>

    <!-- PREFERRED WORKPLACE -->
    <div class="section">
      <h3>Preferred Workplace</h3>
      <select id="workplace" name="workplace">
        <option value="">Select Workplace</option>
        <option>On-site</option>
        <option>Remote</option>
        <option>Hybrid</option>
        <option>Not specified</option>
      </select>
    </div>

    <!-- JOB CATEGORIES -->
    <div class="section">
      <h3>Job Categories You Are Looking For</h3>
      <div class="multi-row">
        <select id="jobCategoryDD">
          <option value="">Select Category</option>
          <option>IT / Software</option>
          <option>Marketing</option>
          <option>Finance</option>
          <option>Human Resources</option>
          <option>Sales</option>
          <option>Design</option>
          <option>Customer Service</option>
          <option>Education</option>
        </select>

        <input type="text" id="customCategory" placeholder="Type a new category…">
        <button type="button" id="addCategoryBtn" class="small-btn">Add</button>
      </div>

      <div id="categoryList" class="tag-container"></div>
    </div>

    

    <!-- CHECKBOXES -->
    <div class="section">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="acceptTerms" required>
        <label class="form-check-label" for="acceptTerms" id="termsLabel" style="cursor:pointer; text-decoration:underline">Accept Terms and Conditions</label>
      </div>
      <div  class="form-check">
        <input class="form-check-input" type="checkbox" id="publicProfile" name="public_profile" <?php echo !empty($_SESSION['public_profile']) ? 'checked' : ''; ?>>
        <label class="form-check-label" for="publicProfile">Allow profile to be public</label>
      </div>
      <div  class="form-check">
        <input class="form-check-input" type="checkbox" id="pushNotifications" name="push_notifications" <?php echo !empty($_SESSION['push_notifications']) ? 'checked' : ''; ?>>
        <label class="form-check-label" for="pushNotifications">Push notifications for new jobs</label>
      </div>
    </div>

    <button type="submit">Get Started</button>

  </form>
</div>

<!-- Terms & Conditions Modal -->
<div class="modal-bg" id="termsModal">
  <div class="modal-content">
    <h3>Terms and Conditions</h3>
    <p>
      Welcome to Shaghalny! By using this platform, you agree to provide accurate and up-to-date
      information about your career preferences. The platform may use your data to recommend
      job opportunities. You are responsible for the information you provide and agree to
      comply with all applicable laws and regulations. Shaghalny is not liable for the
      outcome of job applications. All users must respect others and refrain from posting
      misleading or offensive information.
    </p>
    <button id="closeTerms" class="small-btn">Close</button>
  </div>
</div>

<script src="assets/js/form.js?v=<?php echo time(); ?>"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const setVal = (id, val) => { const el = document.getElementById(id); if (el && val) el.value = val; };
    setVal('workplace', '<?php echo addslashes($data['workplace'] ?? ''); ?>');
  });
</script>

</body>

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
            <button type="button" class="btn btn-primary w-100" id="successContinueBtn">Go to Homepage</button>
        </div>
    </div>
</div>

<?php if (!empty($viewSuccess)): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('successModal'));
        document.getElementById('successContinueBtn').addEventListener('click', function() {
            window.location.href = 'index.php?page=home';
        });
        myModal.show();
    });
</script>
<?php endif; ?>
</html>
