<?php ob_start(); ?>

<!-- Filters & Search -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <div class="card-title">Manage Jobs</div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; padding-top: 1rem;">
        <div>
            <input type="text" id="searchJobs" placeholder="Search jobs..." 
                   style="width: 100%; padding: 10px; border: 1px solid var(--admin-border); border-radius: 8px;">
        </div>
        <div>
            <select id="filterStatus" style="width: 100%; padding: 10px; border: 1px solid var(--admin-border); border-radius: 8px;">
                <option value="">All Status</option>
                <option value="open">Open</option>
                <option value="closed">Closed</option>
                <option value="pending">Pending</option>
            </select>
        </div>
        <div>
            <select id="filterCategory" style="width: 100%; padding: 10px; border: 1px solid var(--admin-border); border-radius: 8px;">
                <option value="">All Categories</option>
                <option value="pet care">Pet Care</option>
                <option value="tutoring">Tutoring</option>
                <option value="yard work">Yard Work</option>
                <option value="errands">Errands</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div>
            <select id="sortJobs" style="width: 100%; padding: 10px; border: 1px solid var(--admin-border); border-radius: 8px;">
                <option value="highest_pay">Highest Pay</option>
                <option value="lowest_pay">Lowest Pay</option>
            </select>
        </div>
    </div>
</div>

<!-- Jobs Table -->
<div class="card">
    <div class="table-responsive">
        <table class="admin-table" id="jobsTable">
            <thead>
                <tr>
                    <th>Job ID</th>
                    <th>Title</th>
                    <th>Employer</th>
                    <th>Category</th>
                    <th>Payment</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Posted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jobs as $j): ?>
                <tr>
                    <td>#<?php echo str_pad($j['job_id'], 3, '0', STR_PAD_LEFT); ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($j['title']); ?></strong><br>
                        <small style="color: var(--admin-text-light);"><?php echo htmlspecialchars(substr($j['description'], 0, 50)) . (strlen($j['description']) > 50 ? '...' : ''); ?></small>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div class="user-avatar-small"><?php echo strtoupper(substr($j['first_name'], 0, 1) . substr($j['last_name'], 0, 1)); ?></div>
                            <?php echo htmlspecialchars($j['first_name'] . ' ' . $j['last_name']); ?>
                        </div>
                    </td>
                    <td><span class="category-badge badge-<?php echo str_replace(' ', '-', strtolower($j['category'])); ?>"><?php echo ucfirst($j['category']); ?></span></td>
                    <td><strong>$<?php echo number_format($j['payment'], 2); ?></strong></td>
                    <td><?php echo htmlspecialchars($j['duration']); ?> mins</td>
                    <td><span class="status-badge status-<?php echo strtolower($j['status']); ?>"><?php echo ucfirst($j['status']); ?></span></td>
                    <td><?php echo date('M d, Y', strtotime($j['date'])); ?></td>
                    <td>
                        <div class="action-buttons">
                            <?php if ($j['status'] !== 'closed'): ?>
                            <button class="action-btn btn-delete" onclick="closeJob(<?php echo $j['job_id']; ?>)" title="Close Job">
                                <i class="fas fa-times"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($jobs)): ?>
                <tr>
                    <td colspan="9" style="text-center; padding: 2rem; color: var(--admin-text-light);">No jobs found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="pagination-container">
        <div class="pagination-info">Showing <?php echo count($jobs); ?> jobs</div>
    </div>
</div>



<!-- Confirm Close Job Modal -->
<div id="closeJobModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Close Job</h3>
            <button class="modal-close" onclick="closeModal('closeJobModal')">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to close this job? This action will:</p>
            <ul style="margin: 1rem 0; padding-left: 1.5rem;">
                <li>Mark the job as closed</li>
                <li>Prevent new applications</li>
                <li>Notify the employer</li>
            </ul>
            <p><strong>Job ID:</strong> <span id="closeJobId"></span></p>
        </div>
        <div class="modal-footer">
            <button class="banner-btn banner-btn-outline" style="border-radius: 5px; padding: 10px;" onclick="closeModal('closeJobModal')">Cancel</button>
            <button class="banner-btn banner-btn-red" style="border-radius: 5px; padding: 10px;" onclick="confirmCloseJob()">Close Job</button>
        </div>
    </div>
</div>

<script>
// Global variable to store current job ID for actions
let currentJobId = null;

// Close Job
function closeJob(jobId) {
    currentJobId = jobId;
    document.getElementById('closeJobId').textContent = '#' + jobId.toString().padStart(3, '0');
    document.getElementById('closeJobModal').style.display = 'flex';
}

// Confirm Close Job
function confirmCloseJob() {
    fetch('/shaghalny8/shaghalny/public/index.php?controller=admin&action=closeJob', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ job_id: currentJobId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to close job.');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('An error occurred.');
    });
    
    closeModal('closeJobModal');
}

// Close Modal
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

// Search and Filter functionality
// Function to apply filters and reload page
function applyFilters() {
    const search = document.getElementById('searchJobs').value;
    const status = document.getElementById('filterStatus').value;
    const category = document.getElementById('filterCategory').value;
    const sort = document.getElementById('sortJobs').value;
    
    // Build URL with query parameters
    const params = new URLSearchParams();
    params.append('controller', 'admin');
    params.append('action', 'searchJobs');
    
    if (search) params.append('search', search);
    if (status) params.append('status', status);
    if (category) params.append('category', category);
    if (sort) params.append('sort', sort);
    
    // Reload page with new parameters
    window.location.href = '/shaghalny8/shaghalny/public/index.php?' + params.toString();
}

// Search on Enter key
document.getElementById('searchJobs')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});

// Filter by status - immediate reload
document.getElementById('filterStatus')?.addEventListener('change', function(e) {
    applyFilters();
});

// Filter by category - immediate reload
document.getElementById('filterCategory')?.addEventListener('change', function(e) {
    applyFilters();
});

// Sort - immediate reload
document.getElementById('sortJobs')?.addEventListener('change', function(e) {
    applyFilters();
});

// Set current filter values from URL on page load
window.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    const search = urlParams.get('search');
    const status = urlParams.get('status');
    const category = urlParams.get('category');
    const sort = urlParams.get('sort');
    
    if (search) document.getElementById('searchJobs').value = search;
    if (status) document.getElementById('filterStatus').value = status;
    if (category) document.getElementById('filterCategory').value = category;
    if (sort) document.getElementById('sortJobs').value = sort;
});
</script>

<?php 
$content = ob_get_clean();
include 'layout.php';
?>
