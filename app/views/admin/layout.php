<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin Portal'; ?> - Shaghalny</title>
    <!-- Fonts & Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="/shaghalny8/shaghalny/public/assets/css/global.css?v=1.0.1">
    <link rel="stylesheet" href="/shaghalny8/shaghalny/public/assets/css/admin.css?v=1.0.1">
    <!-- Chart.js for analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="admin-wrapper">
    <!-- Sidebar -->
    <nav class="admin-sidebar" id="sidebar">
        <div class="admin-logo">
            <i class="fas fa-briefcase me-2"></i>     Shaghalny
        </div>

        <div class="admin-nav">
            <?php 
                $page = $_GET['action'] ?? 'dashboard'; 
                $navItems = [
                    'dashboard' => ['icon' => 'fa-home', 'label' => 'Dashboard'],
                    'users' => ['icon' => 'fa-users', 'label' => 'Manage Users'],
                    'jobs' => ['icon' => 'fa-briefcase', 'label' => 'Manage Jobs'],
                    'reports' => ['icon' => 'fa-chart-bar', 'label' => 'Reports / Analytics'],
                    'community' => ['icon' => 'fa-comments', 'label' => 'Community Feed'],
                ];
            ?>
            
            <?php foreach ($navItems as $action => $item): ?>
                <a href="index.php?controller=admin&action=<?php echo $action; ?>" 
                   class="nav-item <?php echo $page === $action ? 'active' : ''; ?>">
                    <i class="fas <?php echo $item['icon']; ?>"></i>
                    <span><?php echo $item['label']; ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Logout -->
        <div class="admin-user-profile">
            <div class="admin-avatar">
                <?php 
                    $name = $_SESSION['user_name'] ?? 'Admin';
                    echo strtoupper(substr($name, 0, 1)); 
                ?>
            </div>
            <div style="flex:1">
                <div style="font-weight:600; font-size:0.9rem"><?php echo htmlspecialchars($name); ?></div>
                <div style="font-size:0.75rem; color:var(--admin-text-light)"><?php echo htmlspecialchars($_SESSION['user_email'] ?? 'admin@shaghalny.com'); ?></div>
            </div>
            <a href="index.php?controller=auth&action=logout" title="Logout" style="color:red">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="admin-main">
        <!-- Top Header -->
        <header class="admin-header">
            <div style="display:flex; align-items:center; gap:1rem">
                <!-- Mobile Toggle -->
                <button id="sidebarToggle" class="icon-btn" style="display:none;">
                    <i class="fas fa-bars"></i>
                </button>
                <h2 style="font-size:1.25rem; font-weight:600"><?php echo $pageTitle ?? 'Dashboard'; ?></h2>
            </div>

            <div class="header-actions">
                <div class="admin-search">
                    <input type="text" placeholder="Search anything...">
                </div>
                <button class="icon-btn">
                    <i class="far fa-bell"></i>
                </button>
                <button class="icon-btn">
                    <i class="far fa-envelope"></i>
                </button>
            </div>
        </header>

        <!-- Page Content -->
        <div class="admin-content">
            <?php if (isset($content)) echo $content; ?>
        </div>
    </main>
</div>

<script>
    // Simple Sidebar Toggle for Mobile
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    
    if(toggle) {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
        // Check window resize
        if (window.innerWidth <= 768) {
             toggle.style.display = 'flex';
        }
    }
</script>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
