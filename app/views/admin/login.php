<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Shaghalny</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/shaghalny8/shaghalny/public/assets/css/admin_login.css">
</head>
<body>

    <div class="bg-blob blob-1"></div>
    <div class="bg-blob blob-2"></div>

    <div class="login-container">
        <div class="logo-area">
            <h1>Admin Portal</h1>
            <p>Enter your credentials to access the dashboard</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="/shaghalny8/shaghalny/public/index.php?controller=admin&action=authenticate" method="POST">
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="admin@shaghalny.com" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-login">Sign In</button>
        </form>
    </div>

</body>
</html>
