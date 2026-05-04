<?php
// Date is now passed from HomeController as $dashboardData
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Shaغalny</title>
    
    <!-- React (CDNJS) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/18.2.0/umd/react.development.js" crossorigin></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react-dom/18.2.0/umd/react-dom.development.js" crossorigin></script>
    <!-- Babel Excluded (Using Plain JS) -->
    <!-- Framer Motion -->
    <script src="https://unpkg.com/framer-motion@10.16.4/dist/framer-motion.js"></script>
    
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Leaflet Configuration -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Local Styles (Relative Paths) -->
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="assets/css/Header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
</head>
<body class="bg-slate-50">
    <!-- Header Included from Layouts -->
    <?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <!-- ROOT CONTAINER FOR REACT DASHBOARD -->
    <div id="root" style="min-height: 80vh;">
        <!-- Loading State (Fallback) -->
        <div class="flex items-center justify-center h-screen text-gray-400 animate-pulse">
            Loading Dashboard...
        </div>
    </div>

<!-- Pure JS Dashboard (Bypass Babel) -->
    <!-- Safe Data Injection -->
    <div id="server-data-payload" style="display:none;" data-json="<?php echo htmlspecialchars(json_encode($dashboardData ?? []), ENT_QUOTES, 'UTF-8'); ?>" data-is-guest="<?php echo isset($isGuest) && $isGuest ? 'true' : 'false'; ?>"></div>
    
    <script>
        // Init Globals Safely
        let SERVER_DATA = {};
        let IS_GUEST = false;
        let USER_NAME = "User";

        try {
            const el = document.getElementById('server-data-payload');
            if (el) {
                const raw = el.getAttribute('data-json');
                if (raw) SERVER_DATA = JSON.parse(raw);
                IS_GUEST = (el.getAttribute('data-is-guest') === 'true');
            }
        } catch(e) {
            console.error("Data Parse Error", e);
        }

        // Fallback Defaults
        if (!SERVER_DATA || Array.isArray(SERVER_DATA)) SERVER_DATA = {}; // Ensure object
        if (!SERVER_DATA.mapPins) SERVER_DATA.mapPins = [];
        if (!SERVER_DATA.recommendedJobs) SERVER_DATA.recommendedJobs = [];
        if (!SERVER_DATA.recentMessages) SERVER_DATA.recentMessages = [];
        
        USER_NAME = SERVER_DATA.user_name || "User";
    </script>
    <script src="assets/js/components/Dashboard.js?v=<?php echo time(); ?>"></script>

    <!-- Unified Footer from PHP -->
    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
