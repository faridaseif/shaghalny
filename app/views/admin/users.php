<?php ob_start(); ?>

<!-- Alerts -->
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php 
            if ($_GET['success'] == 'updated') echo 'User updated successfully.';
            elseif ($_GET['success'] == 'deleted') echo 'User deleted successfully.';
            else echo 'Action completed successfully.';
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?php 
            if ($_GET['error'] == 'self_action_prevented') echo 'Security: You cannot block yourself or demote your own admin role.';
            else echo 'An error occurred. Please try again.';
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Page Controls -->
<div class="card mb-4 shadow-sm">
    <div class="card-header border-0 bg-white py-3">
        <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-3">
            <h5 class="mb-0 fw-bold text-dark">Users Management</h5>
            
            <form method="GET" action="/shaghalny8/shaghalny/public/index.php" class="d-flex gap-2 flex-wrap align-items-center">
                <input type="hidden" name="controller" value="admin">
                <input type="hidden" name="action" value="users">
                
                <div class="input-group input-group-sm" style="min-width: 250px;">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0" name="search" placeholder="Search name or emails..." value="<?php echo htmlspecialchars($filters['search']); ?>">
                </div>

                <select class="form-select form-select-sm" name="role" style="width: 120px;">
                    <option value="">Role: All</option>
                    <option value="user" <?php echo $filters['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo $filters['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>

                <select class="form-select form-select-sm" name="status" style="width: 120px;">
                    <option value="">Status: All</option>
                    <option value="active" <?php echo $filters['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="blocked" <?php echo $filters['status'] === 'blocked' ? 'selected' : ''; ?>>Blocked</option>
                </select>

                <button type="submit" class="btn btn-sm btn-primary px-3">Filter</button>
                <a href="/shaghalny8/shaghalny/public/index.php?controller=admin&action=users" class="btn btn-sm btn-light border">Reset</a>
            </form>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover" style="min-width: 900px;">
            <thead class="bg-light text-secondary small text-uppercase">
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 30%;">User</th>
                    <th style="width: 10%;">Role</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 15%;">Joined</th>
                    <th style="width: 30%; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="fw-bold text-muted">#<?php echo $u['user_id']; ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex justify-content-center align-items-center me-3 flex-shrink-0" 
                                     style="width: 40px; height: 40px; background-color: #eff6ff; color: var(--blue-main); font-weight: 700;">
                                    <?php echo strtoupper(substr($u['first_name'], 0, 1)); ?>
                                </div>
                                <div style="min-width: 0;">
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 200px;">
                                        <?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?>
                                    </div>
                                    <div class="text-muted small text-truncate" style="max-width: 200px;">
                                        <?php echo htmlspecialchars($u['email']); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if($u['role'] === 'admin'): ?>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-light text-dark border rounded-pill">User</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(($u['status'] ?? 'active') === 'active'): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Blocked</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-secondary small">
                            <?php echo date('M d, Y', strtotime($u['created_at'])); ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm shadow-sm rounded">
                                <button type="button" class="btn btn-white border" onclick="viewUser(<?php echo $u['user_id']; ?>)" title="View Details">
                                    <i class="fas fa-eye text-primary"></i>
                                </button>
                                <button type="button" class="btn btn-white border" onclick="editUser(<?php echo $u['user_id']; ?>)" title="Edit User">
                                    <i class="fas fa-edit text-warning"></i>
                                </button>
                                <button type="button" class="btn btn-white border" onclick="deleteUser(<?php echo $u['user_id']; ?>)" title="Delete User">
                                    <i class="fas fa-trash-alt text-danger"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-users-slash fa-2x mb-3 text-secondary-light"></i>
                            <p>No users found matching your filters.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">User Profile Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-light" id="viewUserBody">
                <!-- Content loaded via JS -->
            </div>
            <div class="modal-footer bg-white">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/shaghalny8/shaghalny/public/index.php?controller=admin&action=updateUser">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit User Role & Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    
                    <div class="d-flex align-items-center mb-4 p-3 bg-light rounded">
                        <div class="rounded-circle bg-white d-flex justify-content-center align-items-center me-3 border" 
                             style="width: 50px; height: 50px; font-weight: bold; color: var(--blue-main);">
                            <span id="edit_initial">U</span>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold" id="edit_full_name">User Name</h6>
                            <div class="text-muted small" id="edit_display_email">email@example.com</div>
                            <!-- Hidden inputs to retain current values if controller expects them, though normally update should be sparse. 
                                 However, current controller expects first_name/last_name/email to update.
                                 Since admin shouldn't edit them, we pass existing values back.
                            -->
                            <input type="hidden" name="first_name" id="edit_first_name_hidden">
                            <input type="hidden" name="last_name" id="edit_last_name_hidden">
                            <input type="hidden" name="email" id="edit_email_hidden">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Role</label>
                        <select class="form-select" name="role" id="edit_role">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                        <div class="form-text">Admins have full access to this portal.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Account Status</label>
                        <select class="form-select" name="status" id="edit_status">
                            <option value="active">Active</option>
                            <option value="blocked">Blocked</option>
                        </select>
                        <div class="form-text">Blocked users cannot log in or apply for jobs.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-white border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger-subtle text-danger border-bottom-0">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i> Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4 text-center">
                <p class="lead mb-1">Are you sure you want to delete this user?</p>
                <p class="text-secondary small">This action will remove their account and all associated data permanently.</p>
                
                <form method="POST" action="/shaghalny8/shaghalny/public/index.php?controller=admin&action=deleteUser" id="deleteForm" class="mt-4">
                    <input type="hidden" name="user_id" id="delete_user_id">
                    <button type="button" class="btn btn-light border px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4">Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Review Delete Form (Hidden) -->
<form id="deleteReviewForm" method="POST" action="/shaghalny8/shaghalny/public/index.php?controller=admin&action=deleteReview" style="display:none;">
    <input type="hidden" name="review_id" id="delete_review_id">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const userModal = new bootstrap.Modal(document.getElementById('viewUserModal'));
    const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));

    function viewUser(id) {
        userModal.show();
        const body = document.getElementById('viewUserBody');
        body.innerHTML = '<div class="d-flex justify-content-center align-items-center" style="height:300px;"><div class="spinner-border text-primary"></div></div>';
        
        fetch(`/shaghalny8/shaghalny/public/index.php?controller=admin&action=getUserDetails&id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    body.innerHTML = `<div class="p-4 text-center text-danger"><i class="fas fa-exclamation-triangle fa-2x mb-2"></i><p>Error: ${data.error}</p></div>`;
                    return;
                }
                const u = data.user || null;
                if (!u) {
                    body.innerHTML = `<div class="p-4 text-center text-secondary"><p>User not found.</p></div>`;
                    return;
                }
                const edu = data.education || null;
                const exp = data.experience || [];
                const skills = data.skills || [];
                const languages = data.languages || [];
                const cv = data.cv || null;
                const career = data.career || {};
                const reviews = data.reviews || [];
                const achievements = data.achievements || [];

                // Helpers
                const val = (v) => v ? v : '<span class="text-muted fst-italic">Not set</span>';
                
                let expHtml = '<p class="text-muted small">No experience added.</p>';
                if(exp.length > 0) {
                    expHtml = exp.map(e => `
                        <div class="mb-3 border-bottom pb-2">
                            <div class="fw-bold text-dark">${e.job_title} <span class="badge bg-light text-dark border ms-2">${e.experience_type || 'Full Time'}</span></div>
                            <div class="text-primary small">${e.company_name}</div>
                            <div class="text-muted small">${e.start_date} - ${e.working == 1 ? 'Present' : e.end_date}</div>
                        </div>
                    `).join('');
                }

                let reviewsHtml = '<p class="text-muted small">No reviews yet.</p>';
                if(reviews.length > 0) {
                    reviewsHtml = reviews.map(r => `
                        <div class="card mb-2 border-0 shadow-sm">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <div class="fw-bold small">${r.first_name ? r.first_name + ' ' + r.last_name : 'Unknown User'}</div>
                                    <div class="text-warning small"><i class="fas fa-star"></i> ${r.rating}</div>
                                </div>
                                <p class="mb-2 small text-secondary">"${r.review_text}"</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted" style="font-size:0.75rem;">${new Date(r.created_at).toLocaleDateString()}</small>
                                    <button class="btn btn-xs btn-outline-danger py-0 px-2" style="font-size:0.75rem;" onclick="deleteReview(${r.review_id})">Delete</button>
                                </div>
                            </div>
                        </div>
                    `).join('');
                }

                body.innerHTML = `
                    <!-- Header -->
                    <div class="bg-white p-4 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex justify-content-center align-items-center me-3 shadow-sm border" 
                                 style="width: 70px; height: 70px; background: #f8f9fa; font-size: 1.75rem; color: var(--blue-main); font-weight:700;">
                                ${u.first_name ? u.first_name[0] : 'U'}
                            </div>
                            <div class="flex-grow-1">
                                <h4 class="mb-0 fw-bold text-dark">${u.first_name} ${u.last_name}</h4>
                                <div class="text-muted small mb-1">ID: #${u.user_id} &bull; ${u.email}</div>
                                <div>
                                    <span class="badge ${u.status === 'active' ? 'bg-success' : 'bg-danger'} rounded-pill me-1">${u.status}</span>
                                    <span class="badge bg-secondary rounded-pill me-1">${u.role}</span>
                                    <span class="badge bg-light text-dark border rounded-pill"> <i class="fas fa-star text-warning"></i> ${u.average_rating || 0} (${u.total_reviews || 0} reviews)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <h6 class="text-uppercase text-secondary small fw-bold mb-3">Sections</h6>
                        
                        <div class="accordion" id="userProfileAccordion">
                            
                            <!-- Personal Info -->
                            <div class="accordion-item shadow-sm mb-3 border-0 overflow-hidden rounded">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold text-dark bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePersonal">
                                        <i class="fas fa-user-circle me-2 text-primary"></i> Personal Information
                                    </button>
                                </h2>
                                <div id="collapsePersonal" class="accordion-collapse collapse" data-bs-parent="#userProfileAccordion">
                                    <div class="accordion-body bg-white border-top">
                                        <div class="row g-3">
                                            <div class="col-md-6"><small class="d-block text-muted">Gender</small> <span class="fw-medium">${val(u.gender)}</span></div>
                                            <div class="col-md-6"><small class="d-block text-muted">Birthdate</small> <span class="fw-medium">${val(u.birthdate)}</span></div>
                                            <div class="col-md-6"><small class="d-block text-muted">Nationality</small> <span class="fw-medium">${val(u.nationality)}</span></div>
                                            <div class="col-md-6"><small class="d-block text-muted">Location</small> <span class="fw-medium">${val(u.city)}, ${val(u.country)}</span></div>
                                            <div class="col-12"><small class="d-block text-muted">Phone</small> <span class="fw-medium">${val(u.phone)}</span></div>
                                            <div class="col-12"><small class="d-block text-muted">About Me</small> <p class="mb-0 small text-secondary">${val(u.about_me)}</p></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Education -->
                            <div class="accordion-item shadow-sm mb-3 border-0 overflow-hidden rounded">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold text-dark bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEdu">
                                        <i class="fas fa-graduation-cap me-2 text-success"></i> Education
                                    </button>
                                </h2>
                                <div id="collapseEdu" class="accordion-collapse collapse" data-bs-parent="#userProfileAccordion">
                                    <div class="accordion-body bg-white border-top">
                                        ${edu ? `
                                            <div class="fw-bold">${edu.school_name}</div>
                                            <div class="text-primary small">${edu.education_level}</div>
                                            <div class="small">Graduated: ${edu.graduation_year} &bull; Grade: ${edu.grade}</div>
                                            <div class="small text-muted">Language: ${edu.language_of_study}</div>
                                        ` : '<p class="text-muted small mb-0">No education info.</p>'}
                                    </div>
                                </div>
                            </div>

                            <!-- Experience -->
                            <div class="accordion-item shadow-sm mb-3 border-0 overflow-hidden rounded">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold text-dark bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExp">
                                        <i class="fas fa-briefcase me-2 text-warning"></i> Experience
                                    </button>
                                </h2>
                                <div id="collapseExp" class="accordion-collapse collapse" data-bs-parent="#userProfileAccordion">
                                    <div class="accordion-body bg-white border-top">
                                        ${expHtml}
                                    </div>
                                </div>
                            </div>

                            <!-- Skills -->
                            <div class="accordion-item shadow-sm mb-3 border-0 overflow-hidden rounded">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold text-dark bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSkills">
                                        <i class="fas fa-tools me-2 text-secondary"></i> Skills & Languages
                                    </button>
                                </h2>
                                <div id="collapseSkills" class="accordion-collapse collapse" data-bs-parent="#userProfileAccordion">
                                    <div class="accordion-body bg-white border-top">
                                        <h6 class="small fw-bold text-muted">Skills</h6>
                                        <div class="mb-3">
                                            ${skills.length ? skills.map(s => `<span class="badge bg-light text-dark border me-1">${s}</span>`).join('') : '<span class="text-muted small">None</span>'}
                                        </div>
                                        <h6 class="small fw-bold text-muted">Languages</h6>
                                        <div>
                                            ${languages.length ? languages.map(l => `<span class="badge bg-info-subtle text-info-emphasis border me-1">${l.language_name} (${l.proficiency})</span>`).join('') : '<span class="text-muted small">None</span>'}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CV & Career -->
                            <div class="accordion-item shadow-sm mb-3 border-0 overflow-hidden rounded">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold text-dark bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCareer">
                                        <i class="fas fa-file-alt me-2 text-danger"></i> CV & Career Interest
                                    </button>
                                </h2>
                                <div id="collapseCareer" class="accordion-collapse collapse" data-bs-parent="#userProfileAccordion">
                                    <div class="accordion-body bg-white border-top">
                                        <div class="mb-3">
                                            <strong class="d-block small">CV File</strong>
                                            ${cv ? `<a href="/shaghalny8/shaghalny/public/uploads/cvs/${cv.cv_filename}" target="_blank" class="btn btn-sm btn-outline-primary mt-1"><i class="fas fa-download"></i> Download CV</a> <div class="small text-muted mt-1">Uploaded: ${new Date(cv.uploaded_at).toLocaleDateString()}</div>` : '<span class="text-muted small">No CV uploaded.</span>'}
                                        </div>
                                        <div class="border-top pt-2">
                                            <div class="row g-2 small">
                                                <div class="col-6"><span class="text-muted">Career Level:</span> ${val(career.career_level)}</div>
                                                <div class="col-6"><span class="text-muted">Job Type:</span> ${val(career.job_type)}</div>
                                                <div class="col-6"><span class="text-muted">Expected Salary:</span> ${val(career.expected_salary)}</div>
                                                <div class="col-6"><span class="text-muted">Workplace:</span> ${val(career.workplace)}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                             <!-- Reviews (Admin Manage) -->
                            <div class="accordion-item shadow-sm mb-3 border-0 overflow-hidden rounded">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold text-dark bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReviews">
                                        <i class="fas fa-star me-2 text-warning"></i> Reviews & Management
                                    </button>
                                </h2>
                                <div id="collapseReviews" class="accordion-collapse collapse" data-bs-parent="#userProfileAccordion">
                                    <div class="accordion-body bg-white border-top bg-light">
                                        ${reviewsHtml}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                `;
            });
    }

    function editUser(id) {
        editModal.show();
        fetch(`/shaghalny8/shaghalny/public/index.php?controller=admin&action=getUserDetails&id=${id}`)
            .then(res => res.json())
            .then(data => {
                const u = data.user;
                document.getElementById('edit_user_id').value = u.user_id;
                document.getElementById('edit_full_name').textContent = u.first_name + ' ' + u.last_name;
                document.getElementById('edit_display_email').textContent = u.email;
                document.getElementById('edit_initial').textContent = u.first_name[0];
                
                // Set hidden inputs (required for controller validation, though mostly unchanged)
                document.getElementById('edit_first_name_hidden').value = u.first_name;
                document.getElementById('edit_last_name_hidden').value = u.last_name;
                document.getElementById('edit_email_hidden').value = u.email;

                document.getElementById('edit_role').value = u.role;
                document.getElementById('edit_status').value = u.status || 'active';
            });
    }

    function deleteUser(id) {
        document.getElementById('delete_user_id').value = id;
        deleteModal.show();
    }

    function deleteReview(id) {
        if(confirm('Are you sure you want to delete this review?')) {
            const form = document.getElementById('deleteReviewForm');
            document.getElementById('delete_review_id').value = id;
            form.submit();
        }
    }
</script>

<?php 
$content = ob_get_clean();
include 'layout.php';
?>
