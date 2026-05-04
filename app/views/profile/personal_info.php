<?php
$data = $viewData ?? [];
$errors = $viewErrors ?? [];
$birthDay = $birthMonth = $birthYear = '';
if (!empty($data['birthdate'])) {
    [$birthYear, $birthMonth, $birthDay] = explode('-', $data['birthdate']);
    $birthMonth = (int)$birthMonth;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Personal Info Form</title>

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
  <form id="personalForm" method="POST" action="index.php?action=personal_info">
    <!-- Personal Info -->
    <div class="section">
      <h3>Your Personal Info</h3>

      <label for="first-name">First Name<span class="required">*</span></label>
      <input type="text" id="first-name" name="first-name" placeholder="Enter first name" value="<?php echo htmlspecialchars($data['first_name'] ?? ''); ?>">

      <label for="last-name">Last Name<span class="required">*</span></label>
      <input type="text" id="last-name" name="last-name" placeholder="Enter last name" value="<?php echo htmlspecialchars($data['last_name'] ?? ''); ?>">

      <label for="birthdate">Birthdate<span class="required">*</span></label>
      <div class="birthdate-selects">
        <select name="day" id="day">
          <option value="">Day</option>
        </select>
        <select name="month" id="month">
          <option value="">Month</option>
        </select>
        <select name="year" id="year">
          <option value="">Year</option>
        </select>
      </div>

      <label>Gender<span class="required">*</span></label>
      <div class="radio-group">
        <label><input type="radio" name="gender" value="male" <?php echo (($data['gender'] ?? '') === 'male') ? 'checked' : ''; ?>> Male</label>
        <label><input type="radio" name="gender" value="female" <?php echo (($data['gender'] ?? '') === 'female') ? 'checked' : ''; ?>> Female</label>
      </div>

      <label for="nationality">Nationality<span class="required">*</span></label>
      <select id="nationality" name="nationality">
        <option value="">Select Nationality</option>
      </select>
    </div>

    <!-- Location -->
    <div class="section">
      <h3>Your Location</h3>

      <label for="country">Country<span class="required">*</span></label>
      <select id="country" name="country">
        <option value="">Select Country</option>
      </select>

      <label for="city">City<span class="required">*</span></label>
      <select id="city" name="city">
        <option value="">Select City</option>
      </select>

      <label for="area">Area<span class="required">*</span></label>
      <select id="area" name="area">
        <option value="">Select Area</option>
      </select>
    </div>

    <!-- Contact Info -->
    <div class="section">
      <h3>Contact Info</h3>

      <label for="mobile">Mobile Number<span class="required">*</span></label>
      <input type="text" id="mobile" name="mobile" placeholder="Enter mobile number" value="<?php echo htmlspecialchars($data['mobile'] ?? ''); ?>">
    </div>

    <button type="submit">Continue ➔</button>
  </form>
</div>
<script src="assets/js/form.js?v=<?php echo time(); ?>"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const setVal = (id, val) => {
      const el = document.getElementById(id);
      if (el && val) { el.value = val; }
    };
    setVal('day', '<?php echo $birthDay; ?>');
    setVal('month', '<?php echo $birthMonth; ?>');
    setVal('year', '<?php echo $birthYear; ?>');
    setVal('nationality', '<?php echo addslashes($data['nationality'] ?? ''); ?>');
    setVal('country', '<?php echo addslashes($data['country'] ?? ''); ?>');
    setVal('city', '<?php echo addslashes($data['city'] ?? ''); ?>');
    setVal('area', '<?php echo addslashes($data['area'] ?? ''); ?>');
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
            window.location.href = 'index.php?action=education';
        });
        myModal.show();
    });
</script>
<?php endif; ?>

</body>
</html>
