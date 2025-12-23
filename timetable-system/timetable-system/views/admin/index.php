<!-- Admin Console Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-dark text-white border-0 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;">
            <div class="card-body p-5">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="display-5 fw-800 mb-2">Admin Console</h1>
                        <p class="lead opacity-75 mb-0">System-wide governance, resource management and activity auditing.</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-4 mt-md-0">
                        <div class="d-inline-flex align-items-center bg-white bg-opacity-10 rounded-pill px-4 py-2 border border-white border-opacity-10">
                            <i class="fas fa-shield-check text-success me-3"></i>
                            <div class="text-start">
                                <div class="small opacity-50 text-uppercase fw-700" style="font-size: 0.6rem;">Security Level</div>
                                <div class="fw-700">Highest (Admin)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="row mb-5">
    <?php
    $cards = [
        ['label' => 'Total Users', 'value' => $stats['users'], 'icon' => 'fa-users', 'color' => '#4361ee', 'link' => 'users.php'],
        ['label' => 'Resources (Rooms)', 'value' => $stats['rooms'], 'icon' => 'fa-door-open', 'color' => '#3a0ca3', 'link' => 'rooms.php'],
        ['label' => 'Active Subjects', 'value' => $stats['subjects'], 'icon' => 'fa-book', 'color' => '#7209b7', 'link' => 'subjects.php'],
        ['label' => 'Student Cohorts', 'value' => $stats['classes'], 'icon' => 'fa-graduation-cap', 'color' => '#f72585', 'link' => 'classes.php'],
        ['label' => 'Total Events', 'value' => $stats['events'], 'icon' => 'fa-calendar-check', 'color' => '#4cc9f0', 'link' => '../timetable.php'],
    ];
    foreach ($cards as $c): ?>
    <div class="col-md-4 col-lg-2-4 mb-4" style="flex: 0 0 auto; width: 20%;">
        <div class="card border-0 shadow-sm h-100 card-hover overflow-hidden">
            <div class="card-body p-4 text-center">
                <div class="icon-circle mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: <?php echo $c['color']; ?>1a; color: <?php echo $c['color']; ?>; border-radius: 12px;">
                    <i class="fas <?php echo $c['icon']; ?> fs-5"></i>
                </div>
                <div class="h3 fw-800 mb-1" style="color: #1e293b;"><?php echo $c['value']; ?></div>
                <div class="small fw-700 text-muted text-uppercase opacity-75" style="font-size: 0.65rem;"><?php echo $c['label']; ?></div>
                <a href="<?php echo $c['link']; ?>" class="stretched-link"></a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Command Center Layout -->
<div class="row">
    <!-- Main Management Area -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 py-4 px-4">
                <h5 class="mb-0 fw-700"><i class="fas fa-terminal me-2 text-primary"></i>Command Center</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-4 bg-light rounded-4 border border-white h-100 transition-all hover-translate">
                            <h6 class="fw-700 mb-3"><i class="fas fa-user-plus me-2 text-primary"></i>Onboarding</h6>
                            <p class="small text-muted mb-4">Add new faculty, students, or administrative staff to the system.</p>
                            <a href="users.php#create" class="btn btn-primary rounded-pill px-4 fw-700 btn-sm">Provision User</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-4 bg-light rounded-4 border border-white h-100 transition-all hover-translate">
                            <h6 class="fw-700 mb-3"><i class="fas fa-file-invoice me-2 text-success"></i>Auditing</h6>
                            <p class="small text-muted mb-4">Generate institutional reports and verify resource utilization.</p>
                            <a href="reports.php" class="btn btn-success text-white rounded-pill px-4 fw-700 btn-sm">System Reports</a>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-4 bg-light rounded-4 border border-primary border-opacity-10 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3"><i class="fas fa-magic text-primary"></i></div>
                                <div>
                                    <h6 class="fw-700 mb-1 text-primary">Timetable Dispatch</h6>
                                    <p class="small text-muted mb-0">Navigate to the master scheduler to manage all events.</p>
                                </div>
                            </div>
                            <a href="../timetable.php" class="btn btn-dark rounded-pill px-4 fw-700">Open Dispatch</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity & Info Sidebar -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm bg-dark text-white mb-4 overflow-hidden" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%) !important;">
            <div class="card-body p-4">
                <div class="position-relative" style="z-index: 2;">
                    <h5 class="fw-700 mb-4">System Health</h5>
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <span class="small opacity-75">Database Status</span>
                        <span class="badge bg-white bg-opacity-10 text-white rounded-pill px-3 py-1 fw-700 border border-white border-opacity-20 flex-shrink-0">
                            <i class="fas fa-circle text-success me-1 small"></i> Online
                        </span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <span class="small opacity-75">Auth Engine</span>
                        <span class="badge bg-white bg-opacity-10 text-white rounded-pill px-3 py-1 fw-700 border border-white border-opacity-20 flex-shrink-0">
                            <i class="fas fa-circle text-success me-1 small"></i> Active
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small opacity-75">Conflict Logic</span>
                        <span class="badge bg-white bg-opacity-10 text-white rounded-pill px-3 py-1 fw-700 border border-white border-opacity-20 flex-shrink-0">
                            <i class="fas fa-circle text-success me-1 small"></i> Polling
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header border-0 py-4 px-4 text-white" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
                <h5 class="mb-0 fw-700">Resource Summary</h5>
            </div>
            <div class="card-body p-4 pt-4">
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-700 text-muted text-uppercase">Room Saturation</span>
                        <span class="small fw-700">~64%</span>
                    </div>
                    <div class="progress" style="height: 6px; border-radius: 10px;">
                        <div class="progress-bar bg-info" style="width: 64%"></div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-700 text-muted text-uppercase">Faculty Booking</span>
                        <span class="small fw-700">~42%</span>
                    </div>
                    <div class="progress" style="height: 6px; border-radius: 10px;">
                        <div class="progress-bar bg-warning" style="width: 42%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .col-lg-2-4 { width: 20%; flex: 0 0 auto; }
    @media (max-width: 991px) { .col-lg-2-4 { width: 33.333%; } }
    @media (max-width: 767px) { .col-lg-2-4 { width: 50%; } }
    
    .hover-translate:hover { transform: translateY(-5px); transition: all 0.3s; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
    .card-hover:hover { transform: scale(1.05); transition: all 0.3s; z-index: 10; box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important; }
    .display-5 { font-size: 2.5rem; }
    .fw-800 { font-weight: 800; }
</style>
