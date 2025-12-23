<!-- Page Header Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-info text-white border-0 shadow-sm">
            <div class="card-body py-4 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="card-title mb-1 fw-700">
                        <i class="fas fa-users-cog me-2"></i>User Management
                    </h2>
                    <p class="card-text opacity-75 fs-5 mb-0">Manage system access, roles, and user accounts</p>
                </div>
                <button type="button" class="btn btn-light btn-lg fw-600 px-4" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="fas fa-user-plus me-2 text-info"></i>Create User
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3">
                <h5 class="mb-0 fw-700"><i class="fas fa-search me-2 text-info"></i>Filter Records</h5>
            </div>
            <div class="card-body pt-0">
                <form method="GET" action="users.php" class="row g-3">
                    <div class="col-md-5">
                        <label for="search" class="form-label small fw-600 text-muted uppercase">Search Users</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0 bg-light" id="search" name="search" value="<?php echo escape($search ?? ''); ?>" placeholder="Name, username or email...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="role" class="form-label small fw-600 text-muted uppercase">Filter by Role</label>
                        <select class="form-select bg-light border-0 px-3 py-2" id="role" name="role">
                            <option value="">All Roles</option>
                            <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="teacher" <?php echo $role_filter === 'teacher' ? 'selected' : ''; ?>>Teacher</option>
                            <option value="student" <?php echo $role_filter === 'student' ? 'selected' : ''; ?>>Student</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-info text-white w-100 py-2 fw-600">
                            <i class="fas fa-filter me-2"></i>Apply
                        </button>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <a href="users.php" class="btn btn-outline-secondary w-100 py-2 fw-600">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Users Table Card -->
<div class="card border-0 shadow-sm list-card">
    <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-700"><i class="fas fa-list me-2 text-info"></i>Users (<?php echo $total_users; ?>)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0 text-muted small fw-600 text-uppercase">User Info</th>
                        <th class="py-3 border-0 text-muted small fw-600 text-uppercase">Role</th>
                        <th class="py-3 border-0 text-muted small fw-600 text-uppercase">Status</th>
                        <th class="py-3 border-0 text-muted small fw-600 text-uppercase">Last Login</th>
                        <th class="text-end pe-4 py-3 border-0 text-muted small fw-600 text-uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-user-slash fa-3x text-light mb-3 d-block"></i>
                                <p class="text-muted fw-500">No users found matching your search.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="ps-4 align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-muted"></i>
                                        </div>
                                        <div>
                                            <div class="fw-700 text-dark"><?php echo escape($user['first_name'] . ' ' . $user['last_name']); ?></div>
                                            <small class="text-muted">@<?php echo escape($user['username']); ?> • <?php echo escape($user['email']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <span class="badge rounded-pill px-3 py-2 bg-<?php 
                                        echo match($user['role']) {
                                            'admin' => 'danger',
                                            'teacher' => 'warning',
                                            'student' => 'info',
                                            default => 'secondary'
                                        };
                                    ?> bg-opacity-10 text-<?php 
                                        echo match($user['role']) {
                                            'admin' => 'danger',
                                            'teacher' => 'warning',
                                            'student' => 'info',
                                            default => 'secondary'
                                        };
                                    ?> fw-600 uppercase" style="font-size: 0.75rem;">
                                        <?php echo escape($user['role']); ?>
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <?php if ($user['is_active']): ?>
                                        <span class="text-success small fw-700"><i class="fas fa-check-circle me-1"></i>Active</span>
                                    <?php else: ?>
                                        <span class="text-muted small fw-700"><i class="fas fa-times-circle me-1"></i>Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle">
                                    <small class="text-muted">
                                        <?php echo $user['last_login'] ? formatDateTime($user['last_login']) : 'Never'; ?>
                                    </small>
                                </td>
                                <td class="text-end pe-4 align-middle">
                                    <div class="btn-group shadow-sm rounded">
                                        <button type="button" class="btn btn-white btn-sm edit-user-btn border-0 px-3" 
                                                data-bs-toggle="modal" data-bs-target="#editUserModal" 
                                                data-user='<?php echo json_encode($user); ?>'
                                                title="Edit User">
                                            <i class="fas fa-edit text-info"></i>
                                        </button>
                                        <button type="button" class="btn btn-white btn-sm reset-password-btn border-0 px-3"
                                                data-bs-target="#resetPasswordModal"
                                                data-bs-toggle="modal"
                                                data-user-id="<?php echo $user['id']; ?>"
                                                data-username="<?php echo escape($user['username']); ?>"
                                                title="Reset Password">
                                            <i class="fas fa-key text-warning"></i>
                                        </button>
                                        <?php if ($user['id'] != $current_user['id']): ?>
                                            <button type="button" class="btn btn-white btn-sm delete-user-btn border-0 px-3"
                                                    data-bs-target="#deleteUserModal"
                                                    data-bs-toggle="modal"
                                                    data-user-id="<?php echo $user['id']; ?>"
                                                    data-username="<?php echo escape($user['username']); ?>"
                                                    title="Delete User">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (!empty($users)): ?>
    <div class="card-footer bg-white border-0 py-4 px-4">
        <?php include __DIR__ . '/../../../includes/pagination.php'; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modals (Create, Edit, Reset, Delete) - Centralized for clarity -->
<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="users.php">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <div class="modal-header bg-info text-white border-0 py-3">
                    <h5 class="modal-title fw-700"><i class="fas fa-user-plus me-2"></i>New System User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Username</label>
                            <input type="text" class="form-control bg-light border-0 px-3 py-2" name="username" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Email Address</label>
                            <input type="email" class="form-control bg-light border-0 px-3 py-2" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">First Name</label>
                            <input type="text" class="form-control bg-light border-0 px-3 py-2" name="first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Last Name</label>
                            <input type="text" class="form-control bg-light border-0 px-3 py-2" name="last_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Access Role</label>
                            <select class="form-select bg-light border-0 px-3 py-2" name="role" required>
                                <option value="student">Student</option>
                                <option value="teacher">Teacher</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Password</label>
                            <input type="password" class="form-control bg-light border-0 px-3 py-2" name="password" required>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="flexSwitchActive" checked>
                                <label class="form-check-label fw-600" for="flexSwitchActive">Account is active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-white fw-600" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white fw-600 px-4 shadow-sm">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="users.php">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="user_id" id="edit_user_id">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <div class="modal-header bg-info text-white border-0 py-3">
                    <h5 class="modal-title fw-700"><i class="fas fa-edit me-2"></i>Edit User Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Username</label>
                            <input type="text" class="form-control bg-light border-0 px-3 py-2" id="edit_username" name="username" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Email Address</label>
                            <input type="email" class="form-control bg-light border-0 px-3 py-2" id="edit_email" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">First Name</label>
                            <input type="text" class="form-control bg-light border-0 px-3 py-2" id="edit_first_name" name="first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Last Name</label>
                            <input type="text" class="form-control bg-light border-0 px-3 py-2" id="edit_last_name" name="last_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Access Role</label>
                            <select class="form-select bg-light border-0 px-3 py-2" id="edit_role" name="role" required>
                                <option value="student">Student</option>
                                <option value="teacher">Teacher</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active">
                                <label class="form-check-label fw-600" for="edit_is_active">Account is active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-white fw-600" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white fw-600 px-4 shadow-sm">Update Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="users.php">
                <input type="hidden" name="action" value="reset_password">
                <input type="hidden" name="user_id" id="reset_user_id">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <div class="modal-header bg-warning text-dark border-0 py-3">
                    <h5 class="modal-title fw-700"><i class="fas fa-key me-2"></i>Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted fw-500 mb-4">Setting a new password for <span id="reset_label_username" class="fw-700 text-dark"></span>.</p>
                    <label class="form-label small fw-700 text-uppercase tracking-wider">New Secure Password</label>
                    <input type="password" class="form-control bg-light border-0 px-3 py-2" name="new_password" required placeholder="Min 6 characters">
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-white fw-600" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning fw-600 px-4 shadow-sm">Save New Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="users.php">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="user_id" id="delete_user_id">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <div class="modal-header bg-danger text-white border-0 py-3">
                    <h5 class="modal-title fw-700"><i class="fas fa-user-minus me-2"></i>Delete Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle fa-4x text-danger opacity-25"></i>
                    </div>
                    <h5 class="fw-700 mb-2">Are you sure?</h5>
                    <p class="text-muted mb-0 fw-500">You are about to delete <span id="delete_label_username" class="fw-700 text-dark"></span>'s account. This action cannot be undone.</p>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3 justify-content-center">
                    <button type="button" class="btn btn-white fw-600 px-4" data-bs-dismiss="modal">Keep Account</button>
                    <button type="submit" class="btn btn-danger fw-600 px-4 shadow-sm text-uppercase tracking-wider" style="font-size: 0.85rem;">Permanently Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Edit User Logic
        const editUserModal = document.getElementById('editUserModal');
        if (editUserModal) {
            editUserModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const user = JSON.parse(button.getAttribute('data-user'));
                
                document.getElementById('edit_user_id').value = user.id;
                document.getElementById('edit_username').value = user.username;
                document.getElementById('edit_email').value = user.email;
                document.getElementById('edit_first_name').value = user.first_name;
                document.getElementById('edit_last_name').value = user.last_name;
                document.getElementById('edit_role').value = user.role;
                document.getElementById('edit_is_active').checked = parseInt(user.is_active) === 1;
            });
        }

        // Reset Password Logic
        const resetPasswordModal = document.getElementById('resetPasswordModal');
        if (resetPasswordModal) {
            resetPasswordModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                document.getElementById('reset_user_id').value = button.getAttribute('data-user-id');
                document.getElementById('reset_label_username').textContent = button.getAttribute('data-username');
            });
        }

        // Delete Logic
        const deleteUserModal = document.getElementById('deleteUserModal');
        if (deleteUserModal) {
            deleteUserModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                document.getElementById('delete_user_id').value = button.getAttribute('data-user-id');
                document.getElementById('delete_label_username').textContent = button.getAttribute('data-username');
            });
        }
    });
</script>

<style>
    .tracking-wider { letter-spacing: 0.05em; }
    .uppercase { text-transform: uppercase; }
    .btn-white { background-color: #fff; border: 1px solid #e2e8f0; color: #4a5568; }
    .btn-white:hover { background-color: #f7fafc; color: #2d3748; }
    .avatar-sm { line-height: 1; }
    .fw-700 { font-weight: 700; }
</style>
