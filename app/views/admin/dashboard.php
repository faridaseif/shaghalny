<?php ob_start(); ?>

<!-- Dashboard Metrics -->
<div class="dashboard-metrics">
    <!-- Users -->
    <a href="/shaghalny8/shaghalny/public/index.php?controller=admin&action=users" style="text-decoration: none; color: inherit;">
        <div class="metric-card" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='';">
            <div class="metric-info">
                <h3>Total Users</h3>
                <div class="value"><?php echo number_format($stats['users']); ?></div>
                <div class="trend up">
                    <i class="fas fa-arrow-up"></i> <?php echo $stats['users_growth']; ?> new this month
                </div>
            </div>
            <div class="metric-icon icon-bg-blue">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </a>

    <!-- Jobs -->
    <a href="/shaghalny8/shaghalny/public/index.php?controller=admin&action=jobs" style="text-decoration: none; color: inherit;">
        <div class="metric-card" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='';">
            <div class="metric-info">
                <h3>Total Job Posts</h3>
                <div class="value"><?php echo number_format($stats['jobs']); ?></div>
                <div class="trend up">
                    <i class="fas fa-arrow-up"></i> <?php echo $stats['jobs_growth']; ?> new this month
                </div>
            </div>
            <div class="metric-icon icon-bg-purple">
                <i class="fas fa-briefcase"></i>
            </div>
        </div>
    </a>

    <!-- Applications -->
    <a href="/shaghalny8/shaghalny/public/index.php?controller=admin&action=applications" style="text-decoration: none; color: inherit;">
        <div class="metric-card" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='';">
            <div class="metric-info">
                <h3>Applications</h3>
                <div class="value"><?php echo number_format($stats['applications']); ?></div>
                <div class="trend up">
                    <i class="fas fa-file-powerpoint"></i> Total applications
                </div>
            </div>
            <div class="metric-icon icon-bg-green">
                <i class="fas fa-file-alt"></i>
            </div>
        </div>
    </a>

    <!-- Pending -->
    <a href="/shaghalny8/shaghalny/public/index.php?controller=admin&action=applications" style="text-decoration: none; color: inherit;">
        <div class="metric-card" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='';">
            <div class="metric-info">
                <h3>Pending Approvals</h3>
                <div class="value"><?php echo number_format($stats['pending_apps']); ?></div>
                <div class="trend up">
                    <span style="color:#f59e0b">Needs Review</span>
                </div>
            </div>
            <div class="metric-icon icon-bg-orange">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </a>
</div>

<!-- Charts & Quick Links Layer -->
<div class="grid-2-1">
    <!-- Chart -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">Job Applications Overview</div>
            <select style="border:1px solid #ddd; padding:4px 8px; border-radius:6px">
                <option>This Year</option>
                <option>Last Year</option>
            </select>
        </div>
        <canvas id="applicationsChart" height="250"></canvas>
    </div>

    <!-- Quick Links -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">Quick Actions</div>
        </div>
        <div class="quick-links">

            <a href="#" class="quick-link-btn">
                <i class="fas fa-user-plus fa-2x" style="color:var(--green-main)"></i>
                <span>Approve Users</span>
            </a>
            <a href="#" class="quick-link-btn">
                <i class="fas fa-file-contract fa-2x" style="color:#a855f7"></i>
                <span>Reports</span>
            </a>
            <a href="#" class="quick-link-btn">
                <i class="fas fa-envelope fa-2x" style="color:#f97316"></i>
                <span>Broadcast</span>
            </a>
        </div>
    </div>
</div>

<!-- Recent Activity & Popular Categories -->
<div class="grid-2-1">
    <!-- Recent Activity Table -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">Recent Activity</div>
            <a href="#" style="color:var(--blue-main); font-size:0.9rem; font-weight:500">View All</a>
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>User / Entity</th>
                        <th>Action</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="display:flex; align-items:center; gap:10px">
                            <div style="width:30px; height:30px; background:#e0e7ff; border-radius:50%; display:flex; align-items:center; justify-content:center; color:var(--blue-main)">JD</div>
                            John Doe
                        </td>
                        <td>Applied for <strong>Senior Developer</strong></td>
                        <td>2 mins ago</td>
                        <td><span class="status-badge status-active">Active</span></td>
                    </tr>
                    <tr>
                        <td style="display:flex; align-items:center; gap:10px">
                            <div style="width:30px; height:30px; background:#fce7f3; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#db2777">AS</div>
                            Alice Smith
                        </td>
                        <td>Updated Profile</td>
                        <td>15 mins ago</td>
                        <td><span class="status-badge status-active">Verified</span></td>
                    </tr>
                    <tr>
                        <td style="display:flex; align-items:center; gap:10px">
                            <div style="width:30px; height:30px; background:#fef3c7; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#d97706">Tech</div>
                            Tech Solutions
                        </td>
                        <td>Posted new job <strong>UX Designer</strong></td>
                        <td>1 hour ago</td>
                        <td><span class="status-badge status-pending">Pending</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Stats Radar/Pie -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">User Demographics</div>
        </div>
        <canvas id="userChart" height="200"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Application Chart
    const ctxApp = document.getElementById('applicationsChart').getContext('2d');
    new Chart(ctxApp, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
            datasets: [{
                label: 'Applications',
                data: [65, 59, 80, 81, 56, 55, 40, 75],
                fill: true,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { borderDash: [2, 4] } }, x: { grid: { display: false } } }
        }
    });

    // User Chart
    const ctxUser = document.getElementById('userChart').getContext('2d');
    new Chart(ctxUser, {
        type: 'doughnut',
        data: {
            labels: ['Job Seekers', 'Employers', 'Admins'],
            datasets: [{
                data: [70, 25, 5],
                backgroundColor: ['#4f46e5', '#10b981', '#f59e0b'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            cutout: '70%'
        }
    });
});
</script>

<?php 
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
