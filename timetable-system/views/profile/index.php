<!-- Profile Header Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-dark text-white border-0 shadow-sm overflow-hidden">
            <div class="card-body p-0">
                <div class="d-md-flex">
                    <div class="p-5 text-center bg-primary" style="min-width: 250px;">
                        <div class="avatar-lg bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-user-circle fa-4x text-white"></i>
                        </div>
                        <h4 class="fw-700 mb-1"><?php echo escape($user->first_name . ' ' . $user->last_name); ?></h4>
                        <span class="badge rounded-pill bg-white text-primary px-3 py-1 fw-700 uppercase" style="font-size: 0.7rem;">
                            <?php echo escape($user->role); ?>
                        </span>
                    </div>
                    <div class="p-5 flex-grow-1 d-flex flex-column justify-content-center">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="small opacity-50 text-uppercase fw-700 tracking-wider">Username</div>
                                <div class="h5 fw-600">@<?php echo escape($user->username); ?></div>
                            </div>
                            <div class="col-md-4">
                                <div class="small opacity-50 text-uppercase fw-700 tracking-wider">Email</div>
                                <div class="h5 fw-600"><?php echo escape($user->email); ?></div>
                            </div>
                            <div class="col-md-4">
                                <div class="small opacity-50 text-uppercase fw-700 tracking-wider">Active Since</div>
                                <div class="h5 fw-600"><?php echo date('M Y', strtotime($user->created_at ?? date('Y-m-d'))); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Account Settings -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 py-3">
                <h5 class="mb-0 fw-700"><i class="fas fa-user-edit me-2 text-primary"></i>Personal Settings</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="profile.php">
                    <input type="hidden" name="action" value="update_profile">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase text-muted">First Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-address-card text-muted"></i></span>
                                <input type="text" class="form-control bg-light border-0 px-3 py-2" name="first_name" value="<?php echo escape($user->first_name); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase text-muted">Last Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-address-card text-muted"></i></span>
                                <input type="text" class="form-control bg-light border-0 px-3 py-2" name="last_name" value="<?php echo escape($user->last_name); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase text-muted">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-at text-muted"></i></span>
                                <input type="text" class="form-control bg-light border-0 px-3 py-2" name="username" value="<?php echo escape($user->username); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase text-muted">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" class="form-control bg-light border-0 px-3 py-2" name="email" value="<?php echo escape($user->email); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="text-end border-top pt-4">
                        <button type="submit" class="btn btn-primary fw-700 px-5 py-2 shadow-sm">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm border-start border-4 border-danger">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-700 mb-1">Danger Zone</h6>
                    <p class="text-muted small mb-0">Once you delete your account, there is no going back. Please be certain.</p>
                </div>
                <button class="btn btn-outline-danger btn-sm fw-700" disabled>Deactivate Account</button>
            </div>
        </div>
    </div>

    <!-- Security Sidebar -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 py-3">
                <h5 class="mb-0 fw-700"><i class="fas fa-shield-alt me-2 text-warning"></i>Security Settings</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="profile.php">
                    <input type="hidden" name="action" value="change_password">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="mb-4">
                        <label class="form-label small fw-700 text-uppercase text-muted">Current Password</label>
                        <input type="password" class="form-control bg-light border-0 px-3 py-2" name="current_password" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label small fw-700 text-uppercase text-muted">New Password</label>
                        <input type="password" class="form-control bg-light border-0 px-3 py-2" name="new_password" required placeholder="Min 6 characters">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label small fw-700 text-uppercase text-muted">Confirm New Password</label>
                        <input type="password" class="form-control bg-light border-0 px-3 py-2" name="confirm_password" required>
                    </div>

                    <button type="submit" class="btn btn-warning w-100 fw-700 py-2 shadow-sm">Update Password</button>
                </form>
            </div>
        </div>

        <!-- Activity Summary -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3">
                <h5 class="mb-0 fw-700"><i class="fas fa-history me-2 text-info"></i>Quick Activity</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="d-flex align-items-center mb-3">
                    <div class="small text-muted me-auto">Last Login</div>
                    <div class="small fw-700 text-dark"><?php echo $user->last_login ? formatDateTime($user->last_login) : 'Never'; ?></div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="small text-muted me-auto">Password last changed</div>
                    <div class="small fw-700 text-dark">N/A</div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="small text-muted me-auto">Session Status</div>
                    <div class="small"><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">Active</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.uppercase { text-transform: uppercase; }
.tracking-wider { letter-spacing: 0.05em; }
.fw-700 { font-weight: 700; }
.fw-600 { font-weight: 600; }
.avatar-lg { border: 4px solid rgba(255,255,255,0.2); }
</style>
