<?php
$user = $viewData['user'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile Completed</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
  <div class="container mt-5">
    <div class="card">
      <div class="card-body">
        <h3 class="card-title">Profile Completed</h3>
        <p class="card-text">
          Thanks <?php echo htmlspecialchars($user['first_name'] ?? ''); ?>! Your profile steps are saved.
        </p>
        <a href="/shaghalny/" class="btn btn-primary">Go to landing</a>
      </div>
    </div>
  </div>
</body>
</html>

