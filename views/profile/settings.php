<?php
// Settings View
$user = $viewData['user'] ?? [];
$career = $viewData['career'] ?? [];
$publicProfile = $career['public_profile'] ?? 0;
$push = $career['push_notifications'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Settings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/profile.css">
    <link rel="stylesheet" href="assets/css/Header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <!-- Scripts for Header are inside header.php now -->
    <script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
</head>
<body>
    <?php include __DIR__ . '/../layouts/header.php'; ?>

    <div class="container mt-5 pt-4" style="min-height: 50vh;">
        <h2 class="mb-4">Account Settings</h2>

        <?php if (!empty($viewErrors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($viewErrors as $e): ?><div><?php echo htmlspecialchars($e); ?></div><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-3">
                <div class="list-group">
                    <a href="#general" class="list-group-item list-group-item-action active" data-bs-toggle="list">General</a>
                    <a href="#security" class="list-group-item list-group-item-action" data-bs-toggle="list">Security</a>
                    <a href="#notifications" class="list-group-item list-group-item-action" data-bs-toggle="list">Notifications</a>
                    <a href="#danger" class="list-group-item list-group-item-action text-danger" data-bs-toggle="list">Delete Account</a>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="tab-content">
                    
                    <!-- General -->
                    <div class="tab-pane fade show active" id="general">
                        <div class="card shadow-sm p-4">
                            <h5>General Info</h5>
                            <form method="POST" action="index.php?action=update_email">
                                <div class="mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                                </div>
                                <button class="btn btn-primary">Update Email</button>
                            </form>
                            <hr>
                            <form method="POST" action="index.php?action=career_interest">
                                <input type="hidden" name="section" value="career"> 
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="public_profile" id="flexSwitchCheckDefault" <?php echo $publicProfile ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="flexSwitchCheckDefault">Make Profile Public</label>
                                </div>
                                <button class="btn btn-secondary btn-sm mt-2">Save Visibility</button>
                            </form>
                        </div>
                    </div>

                    <!-- Security -->
                    <div class="tab-pane fade" id="security">
                        <div class="card shadow-sm p-4">
                            <h5>Change Password</h5>
                            <form id="passwordForm" method="POST" action="index.php?action=change_password">
                                <div class="mb-3">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" class="form-control" name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" class="form-control" name="new_password" required minlength="6">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" name="confirm_password" required minlength="6">
                                </div>
                                <button type="button" class="btn btn-primary" id="triggerPasswordModal">Change Password</button>
                            </form>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <div class="tab-pane fade" id="notifications">
                        <div class="card shadow-sm p-4">
                            <h5>Notification Preferences</h5>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="pushNotif" <?php echo $push ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="pushNotif">Push Notifications</label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="emailNotif" checked>
                                <label class="form-check-label" for="emailNotif">Email Newsletters</label>
                            </div>
                            <button class="btn btn-primary">Save Preferences</button>
                        </div>
                    </div>

                    <!-- Danger Zone -->
                    <div class="tab-pane fade" id="danger">
                        <div class="card shadow-sm p-4 border-danger">
                            <h5 class="text-danger">Delete Account</h5>
                            <p class="text-muted">Once you delete your account, there is no going back. Please be certain.</p>
                            <form id="deleteForm" method="POST" action="index.php?action=delete_account">
                                <button type="button" class="btn btn-danger" id="triggerDeleteModal">Delete Account</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    
    <!-- Password Confirmation Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Password Change</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to change your password?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmPasswordBtn">Yes, Change It</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-danger">
                <div class="modal-header text-danger">
                    <h5 class="modal-title">Delete Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you absolutely sure you want to delete your account? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete My Account</button>
                </div>
            </div>
        </div>
    </div>

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
                <p class="text-muted">Updated successfully.</p>
                <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../layouts/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Password Modal
            const passBtn = document.getElementById('triggerPasswordModal');
            if(passBtn){
                passBtn.addEventListener('click', () => {
                    // Simple client-side check if empty
                    const form = document.getElementById('passwordForm');
                    if(form.reportValidity()){
                         new bootstrap.Modal(document.getElementById('passwordModal')).show();
                    }
                });
            }
            document.getElementById('confirmPasswordBtn')?.addEventListener('click', () => {
                document.getElementById('passwordForm').submit();
            });

            // Delete Modal
             const delBtn = document.getElementById('triggerDeleteModal');
            if(delBtn){
                delBtn.addEventListener('click', () => {
                     new bootstrap.Modal(document.getElementById('deleteModal')).show();
                });
            }
            document.getElementById('confirmDeleteBtn')?.addEventListener('click', () => {
                document.getElementById('deleteForm').submit();
            });

            // Success Modal Trigger (from PHP)
            <?php if (!empty($viewSuccess)): ?>
                new bootstrap.Modal(document.getElementById('successModal')).show();
            <?php endif; ?>
        });
    </script>
</body>
</html>
