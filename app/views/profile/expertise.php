<?php
$errors = $viewErrors ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Expertise Form</title>

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
  <form id="expertiseForm" method="POST" action="index.php?action=expertise" enctype="multipart/form-data">

    <!-- LANGUAGES SECTION -->
    <div class="section">
      <h3>Your Languages</h3>

      <label>Add a Language</label>
      <div class="multi-row">
        <select id="languageDD">
          <option value="">Select Language</option>
          <option>Arabic</option>
          <option>English</option>
          <option>French</option>
          <option>German</option>
          <option>Spanish</option>
        </select>

        <select id="proficiencyDD">
          <option value="">Proficiency</option>
          <option>Beginner</option>
          <option>Intermediate</option>
          <option>Advanced</option>
          <option>Fluent</option>
          <option>Native</option>
        </select>

        <button type="button" class="small-btn" id="addLanguageBtn">Add</button>
      </div>

      <div id="languageList" class="tag-container"></div>
    </div>

    <!-- SKILLS SECTION -->
    <div class="section">
      <h3>Your Skills, Tools & Technologies</h3>

      <label>Select Skill or Add New</label>
      <div class="multi-row">
        <select id="skillsDD">
          <option value="">Select Skill</option>
          <option>HTML</option>
          <option>CSS</option>
          <option>JavaScript</option>
          <option>Communication</option>
          <option>Project Management</option>
        </select>

        <input type="text" id="customSkill" placeholder="Type a new skill…">

        <button type="button" id="addSkillBtn" class="small-btn">Add</button>
      </div>

      <p class="hint">(You must add between 2 and 10 skills)</p>

      <div id="skillList" class="tag-container"></div>
    </div>

    <!-- CV UPLOAD -->
    <div class="section">
      <h3>Upload Your CV (optional)</h3>

      <div id="cvDropArea" class="drop-area">
        <p>Drag & Drop your CV here or Upload</p>
        
        <button type="button" id="browseCV" class="small-btn" style="background-color:grey;">Upload</button>
        <input type="file" id="cvFile" name="cvFile" accept=".pdf,.doc,.docx" hidden>
      </div>

      <p class="hint">Max file size: 5MB</p>

      <div id="cvFileName"></div>
    </div>

    <button type="submit">Confirm ✔</button>

  </form>
</div>

<script src="assets/js/form.js?v=<?php echo time(); ?>"></script>

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
            window.location.href = 'index.php?action=career_interest';
        });
        myModal.show();
    });
</script>
<?php endif; ?>
</html>
