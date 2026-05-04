<?php
$data = $viewData ?? [];
$errors = $viewErrors ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Education Info Form</title>

  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

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
  <form id="educationForm" method="POST" action="index.php?action=education">
    <div class="section">
      <h3>Education Information</h3>

      <label for="edu-level">Current Educational Level <span class="required">*</span></label>
      <select id="edu-level" name="edu-level">
        <option value="">Select Level</option>
        <option value="Highschool">High School</option>
        <option value="Bachelor">Bachelor's Degree</option>
        <option value="Master">Master's Degree</option>
        <option value="Doctorate">Doctorate</option>
      </select>

      <!-- Degree Details (hidden for Highschool) -->
      <div id="degree-details" style="display:none;">
       <label for="field-study">Field(s) of Study <span class="required">*</span></label>
<select id="field-study" name="field-study" >
  <option value="Computer Science">Computer Science</option>
  <option value="Business">Business</option>
  <option value="Engineering">Engineering</option>
  <option value="Medicine">Medicine</option>
  <option value="Arts">Arts</option>
  <option value="Law">Law</option>
</select>


        <label for="university">University / Institution <span class="required">*</span></label>
        <input type="text" id="university" name="university" placeholder="Enter University / Institution" value="<?php echo htmlspecialchars($data['school_name'] ?? ''); ?>">

        <label for="degree-year">Year Obtained / Expected <span class="required">*</span></label>
        <select id="degree-year" name="degree-year">
          <option value="">Select Year</option>
        </select>

        <label for="degree-grade">Grade / GPA <span class="required">*</span></label>
        <input type="text" id="degree-grade" name="degree-grade" placeholder="Enter Grade / GPA" value="<?php echo htmlspecialchars($data['grade'] ?? ''); ?>">
      </div>

      <!-- Highschool Fields -->
      <div id="highschool-details" style="display:none;">
        <label for="school-name">School Name <span class="required">*</span></label>
        <input type="text" id="school-name" name="school-name" placeholder="Enter School Name" value="<?php echo htmlspecialchars($data['school_name'] ?? ''); ?>">

        <label for="certificate">Certificate Name <span class="required">*</span></label>
        <select id="certificate" name="certificate">
          <option value="">Select Certificate</option>
          <option>Thanaweya Amma</option>
          <option>International Baccalaureate</option>
          <option>IGCSE</option>
          <option>American Diploma</option>
        </select>

        <label for="language">Language of Study <span class="required">*</span></label>
        <select id="language" name="language">
          <option value="">Select Language</option>
          <option>Arabic</option>
          <option>English</option>
          <option>French</option>
        </select>

        <label for="grad-year">Graduation Year <span class="required">*</span></label>
        <select id="grad-year" name="grad-year">
          <option value="">Select Year</option>
        </select>

        <label for="school-grade">Grade / GPA <span class="required">*</span></label>
        <input type="text" id="school-grade" name="school-grade" placeholder="Enter Grade / GPA" value="<?php echo htmlspecialchars($data['grade'] ?? ''); ?>">
      </div>

    </div>

    <button type="submit">Continue ➔</button>
  </form>
</div>

<script src="assets/js/form.js?v=<?php echo time(); ?>"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const setVal = (id, val) => { const el = document.getElementById(id); if (el && val) el.value = val; };
    setVal('edu-level', '<?php echo addslashes($data['education_level'] ?? ''); ?>');
    setVal('field-study', '<?php echo addslashes($data['language_of_study'] ?? ''); ?>');
    setVal('university', '<?php echo addslashes($data['school_name'] ?? ''); ?>');
    setVal('degree-year', '<?php echo addslashes($data['graduation_year'] ?? ''); ?>');
    setVal('degree-grade', '<?php echo addslashes($data['grade'] ?? ''); ?>');
    setVal('school-name', '<?php echo addslashes($data['school_name'] ?? ''); ?>');
    setVal('language', '<?php echo addslashes($data['language_of_study'] ?? ''); ?>');
    setVal('grad-year', '<?php echo addslashes($data['graduation_year'] ?? ''); ?>');
    setVal('school-grade', '<?php echo addslashes($data['grade'] ?? ''); ?>');
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
            window.location.href = 'index.php?action=experience';
        });
        myModal.show();
    });
</script>
<?php endif; ?>
</body>
</html>
