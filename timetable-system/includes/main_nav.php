<?php
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-3" style="background: #0f172a !important; border-bottom: 1px solid rgba(255,255,255,0.05);">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-800 fs-4 d-flex align-items-center" href="<?php echo APP_URL; ?>/admin/dashboard.php">
            <div class="bg-primary rounded-pill p-1 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                <i class="fas fa-bolt text-white fs-6"></i>
            </div>
            <span class="ls-tight"><?php echo APP_NAME; ?></span>
        </a>
        
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto ms-lg-4 gap-2">
                <?php if (isLoggedIn()): ?>
                <li class="nav-item">
                    <a class="nav-link px-3 rounded-pill fw-600 <?php echo $current_page == 'dashboard.php' ? 'active bg-primary' : ''; ?>" href="<?php echo APP_URL; ?>/admin/dashboard.php">
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 rounded-pill fw-600 <?php echo $current_page == 'calendar.php' ? 'active bg-primary' : ''; ?>" href="<?php echo APP_URL; ?>/calendar.php">
                        Calendar
                    </a>
                </li>
                <?php if (isAdmin() || isTeacher()): ?>
                <li class="nav-item">
                    <a class="nav-link px-3 rounded-pill fw-600 <?php echo $current_page == 'timetable.php' ? 'active bg-primary' : ''; ?>" href="<?php echo APP_URL; ?>/timetable.php">
                        Timetable
                    </a>
                </li>
                <?php endif; ?>
                <?php if (isAdmin()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link px-3 rounded-pill fw-600 dropdown-toggle <?php echo ($current_dir == 'admin' && $current_page != 'dashboard.php') ? 'active bg-white bg-opacity-10' : ''; ?>" href="#" data-bs-toggle="dropdown">
                            Administration
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark border-0 shadow-lg mt-2 py-2">
                            <li><a class="dropdown-item py-2 px-3 fw-600" href="<?php echo APP_URL; ?>/admin/index.php"><i class="fas fa-terminal me-2 opacity-50"></i>Admin Console</a></li>
                            <li><hr class="dropdown-divider opacity-10"></li>
                            <li><a class="dropdown-item py-2 px-3 fw-600" href="<?php echo APP_URL; ?>/admin/users.php"><i class="fas fa-users me-2 opacity-50"></i>User Management</a></li>
                            <li><a class="dropdown-item py-2 px-3 fw-600" href="<?php echo APP_URL; ?>/admin/subjects.php"><i class="fas fa-book me-2 opacity-50"></i>Subject List</a></li>
                            <li><a class="dropdown-item py-2 px-3 fw-600" href="<?php echo APP_URL; ?>/admin/rooms.php"><i class="fas fa-door-open me-2 opacity-50"></i>Facility Rooms</a></li>
                            <li><a class="dropdown-item py-2 px-3 fw-600" href="<?php echo APP_URL; ?>/admin/classes.php"><i class="fas fa-graduation-cap me-2 opacity-50"></i>Class Cohorts</a></li>
                            <li><hr class="dropdown-divider opacity-10"></li>
                            <li><a class="dropdown-item py-2 px-3 fw-600" href="<?php echo APP_URL; ?>/admin/reports.php"><i class="fas fa-chart-line me-2 opacity-50"></i>System Reports</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php endif; ?>
            </ul>
            
            <ul class="navbar-nav align-items-center gap-2">
                <?php if (isLoggedIn()): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link p-1 d-flex align-items-center dropdown-toggle no-caret" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                            <span class="fw-700 text-white" style="font-size: 0.75rem;">
                                <?php echo substr($current_user['first_name'] ?? 'U', 0, 1) . substr($current_user['last_name'] ?? 'S', 0, 1); ?>
                            </span>
                        </div>
                        <div class="d-none d-xl-block">
                            <div class="fw-700 text-white" style="font-size: 0.85rem; line-height: 1;"><?php echo escape(($current_user['first_name'] ?? '') . ' ' . ($current_user['last_name'] ?? '')); ?></div>
                            <small class="text-muted text-uppercase fw-700" style="font-size: 0.65rem;"><?php echo escape($_SESSION['role'] ?? ''); ?></small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark border-0 shadow-lg mt-2 py-2">
                        <li><a class="dropdown-item py-2 px-3 fw-600" href="<?php echo APP_URL; ?>/profile.php"><i class="fas fa-user-circle me-2 opacity-50"></i>Account Settings</a></li>
                        <li><hr class="dropdown-divider opacity-10"></li>
                        <li><a class="dropdown-item py-2 px-3 fw-600 text-danger" href="<?php echo APP_URL; ?>/logout.php"><i class="fas fa-power-off me-2 opacity-50"></i>Log Out</a></li>
                    </ul>
                </li>
                <?php elseif ($current_page != 'login.php'): ?>
                <li class="nav-item">
                    <a class="btn btn-primary rounded-pill px-4 fw-700 btn-sm" href="<?php echo APP_URL; ?>/login.php">Sign In</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<style>
    .fw-800 { font-weight: 800; }
    .fw-700 { font-weight: 700; }
    .fw-600 { font-weight: 600; }
    .ls-tight { letter-spacing: -0.025em; }
    .no-caret::after { display: none !important; }
    .dropdown-menu-dark { background: #1e293b; }
    .dropdown-item:hover { background: #334155; }
    .nav-link.active.bg-primary { color: #fff !important; }
</style>