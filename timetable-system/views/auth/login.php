<div class="card login-card shadow-lg border-0">
    <div class="card-header login-header text-center py-5 bg-primary text-white">
        <div class="mb-3">
            <i class="fas fa-calendar-alt fa-3x mb-3 opacity-80"></i>
        </div>
        <h3 class="mb-2"><?php echo APP_NAME; ?></h3>
        <p class="mb-0 opacity-75">Sign in to your account</p>
    </div>
    <div class="card-body p-5">
        <form method="POST" action="login.php" class="text-center">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div class="mb-4">
                <label for="username" class="form-label fw-600 mb-2">Username or Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0 text-center" id="username" name="username" 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required placeholder="Enter username">
                </div>
            </div>
            
            <div class="mb-4">
                <label for="password" class="form-label fw-600 mb-2">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" class="form-control border-start-0 ps-0 border-end-0 text-center" id="password" name="password" required placeholder="Enter password">
                    <button class="btn btn-outline-secondary border-start-0 bg-white" type="button" id="togglePassword">
                        <i class="fas fa-eye text-muted"></i>
                    </button>
                </div>
            </div>
            
            <div class="mb-4 d-flex justify-content-center align-items-center">
                <div class="form-check m-0">
                    <input type="checkbox" class="form-check-input" id="rememberMe" name="remember_me">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                </div>
                <a href="forgot-password.php" class="text-decoration-none small ms-3">Forgot password?</a>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-login py-3 fw-600">
                    Sign In
                </button>
            </div>
        </form>
        
        <div class="mt-5 pt-4 border-top text-center">
            <p class="small text-muted mb-3 fw-600">Demo Credentials</p>
            <div class="d-flex flex-column gap-2">
                <div class="badge bg-light text-dark p-2 border">Admin: admin / password</div>
                <div class="badge bg-light text-dark p-2 border">Teacher: teacher1 / password</div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo APP_URL; ?>/assets/js/login.js"></script>
