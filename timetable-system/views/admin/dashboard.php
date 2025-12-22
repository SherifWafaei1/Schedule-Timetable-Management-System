<!-- Welcome Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm overflow-hidden position-relative" style="background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%) !important;">
            <div class="card-body py-5 position-relative" style="z-index: 2;">
                <i class="fas fa-calendar-check position-absolute opacity-10" style="font-size: 10rem; right: -2rem; bottom: -2rem; transform: rotate(-15deg);"></i>
                <div class="d-flex align-items-center">
                    <div>
                        <h1 class="display-6 fw-800 mb-2 text-white">
                            Welcome back, <?php echo escape($current_user['first_name']); ?>!
                        </h1>
                        <p class="lead opacity-75 mb-0 text-white">
                            <?php if (isAdmin()): ?>
                                Central hub for managing your academic schedules and resources.
                            <?php elseif (isTeacher()): ?>
                                Here is your teaching schedule and class overview for today.
                            <?php else: ?>
                                Track your academic progress and upcoming events.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <?php if (isAdmin()): ?>
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                        <h3 class="fw-700 mb-0"><?php echo number_format($stats['total_users'] ?? 0); ?></h3>
                    </div>
                    <p class="text-muted mb-0 fw-500">Total Registered Users</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                            <i class="fas fa-calendar-check fa-lg"></i>
                        </div>
                        <h3 class="fw-700 mb-0"><?php echo number_format($stats['total_events'] ?? 0); ?></h3>
                    </div>
                    <p class="text-muted mb-0 fw-500">Active Events</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info">
                            <i class="fas fa-door-open fa-lg"></i>
                        </div>
                        <h3 class="fw-700 mb-0"><?php echo number_format($stats['total_rooms'] ?? 0); ?></h3>
                    </div>
                    <p class="text-muted mb-0 fw-500">Managed Rooms</p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                            <i class="fas fa-calendar-alt fa-lg"></i>
                        </div>
                        <h3 class="fw-700 mb-0"><?php echo number_format($stats['my_events'] ?? 0); ?></h3>
                    </div>
                    <p class="text-muted mb-0 fw-500">My Total Events</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info">
                            <i class="fas fa-clock fa-lg"></i>
                        </div>
                        <h3 class="fw-700 mb-0"><?php echo number_format($stats['today_events'] ?? 0); ?></h3>
                    </div>
                    <p class="text-muted mb-0 fw-500">Today's Schedule</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="row">
    <!-- Today's Events -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3">
                <h5 class="card-title mb-0 fw-700">
                    <i class="fas fa-calendar-day me-2 text-primary"></i>Today's Events
                </h5>
            </div>
            <div class="card-body pt-0">
                <?php if (empty($today_events)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-check fa-3x text-light mb-3"></i>
                        <p class="text-muted mb-0">Free as a bird! Nothing scheduled for today.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush mt-2">
                        <?php foreach ($today_events as $event): ?>
                            <div class="list-group-item px-0 py-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 fw-700"><?php echo escape($event['title']); ?></h6>
                                    <span class="badge bg-light text-primary rounded-pill">
                                        <?php echo formatTime($event['start_time']); ?>
                                    </span>
                                </div>
                                <div class="d-flex align-items-center text-muted small">
                                    <span class="me-3"><i class="fas fa-book-open me-1"></i><?php echo escape($event['subject_name'] ?? 'General'); ?></span>
                                    <span><i class="fas fa-map-marker-alt me-1"></i><?php echo escape($event['room_name'] ?? 'Remote'); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Upcoming Events -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3">
                <h5 class="card-title mb-0 fw-700">
                    <i class="fas fa-calendar-week me-2 text-success"></i>Upcoming Week
                </h5>
            </div>
            <div class="card-body pt-0">
                <?php if (empty($upcoming_events)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-plus fa-3x text-light mb-3"></i>
                        <p class="text-muted mb-0">The next week looks clear.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush mt-2">
                        <?php foreach (array_slice($upcoming_events, 0, 5) as $event): ?>
                            <div class="list-group-item px-0 py-3 border-bottom-0">
                                <div class="d-flex align-items-center">
                                    <div class="date-badge me-3 text-center bg-light rounded px-2 py-1" style="min-width: 60px;">
                                        <div class="small fw-700 text-uppercase text-primary"><?php echo date('M', strtotime($event['event_date'])); ?></div>
                                        <div class="h5 mb-0 fw-700"><?php echo date('d', strtotime($event['event_date'])); ?></div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-700 text-dark"><?php echo escape($event['title']); ?></h6>
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i><?php echo formatTime($event['start_time']); ?> - <?php echo formatTime($event['end_time']); ?>
                                        </small>
                                    </div>
                                    <a href="<?php echo APP_URL; ?>/timetable.php?search=<?php echo urlencode($event['title']); ?>" class="btn btn-sm btn-light">
                                        <i class="fas fa-chevron-right text-muted"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3">
                <h5 class="card-title mb-0 fw-700">
                    <i class="fas fa-bolt me-2 text-warning"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body pt-0 pb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="<?php echo APP_URL; ?>/calendar.php" class="btn btn-light w-100 py-3 text-start border-0 shadow-hover">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                    <i class="fas fa-calendar text-primary"></i>
                                </div>
                                <div>
                                    <div class="fw-700">Calendar</div>
                                    <div class="small text-muted">View full schedule</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php if (isAdmin() || isTeacher()): ?>
                    <div class="col-md-3">
                        <a href="<?php echo APP_URL; ?>/timetable.php#create" class="btn btn-light w-100 py-3 text-start border-0 shadow-hover">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                                    <i class="fas fa-plus text-success"></i>
                                </div>
                                <div>
                                    <div class="fw-700">Create Event</div>
                                    <div class="small text-muted">Add new entry</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php if (isAdmin()): ?>
                    <div class="col-md-3">
                        <a href="<?php echo APP_URL; ?>/admin/users.php" class="btn btn-light w-100 py-3 text-start border-0 shadow-hover">
                            <div class="d-flex align-items-center">
                                <div class="bg-info bg-opacity-10 p-2 rounded me-3">
                                    <i class="fas fa-users text-info"></i>
                                </div>
                                <div>
                                    <div class="fw-700">Users</div>
                                    <div class="small text-muted">Manage access</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-3">
                        <a href="<?php echo APP_URL; ?>/profile.php" class="btn btn-light w-100 py-3 text-start border-0 shadow-hover">
                            <div class="d-flex align-items-center">
                                <div class="bg-secondary bg-opacity-10 p-2 rounded me-3">
                                    <i class="fas fa-user-edit text-secondary"></i>
                                </div>
                                <div>
                                    <div class="fw-700">Profile</div>
                                    <div class="small text-muted">Update info</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .shadow-hover:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
        transform: translateY(-2px);
        transition: all 0.2s ease;
    }
    .fw-700 { font-weight: 700; }
</style>
