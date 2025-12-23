<?php
// View Helper Functions
if (!function_exists('getEventsForDate')) {
    function getEventsForDate($events, $date) {
        return array_filter($events, function($event) use ($date) {
            return $event['event_date'] === $date;
        });
    }
}

if (!function_exists('getNavigationUrl')) {
    function getNavigationUrl($view, $year, $month, $date, $direction) {
        $url = "calendar.php?view=$view";
        switch ($direction) {
            case 'prev':
                switch ($view) {
                    case 'day': return $url . "&date=" . date('Y-m-d', strtotime($date . ' -1 day'));
                    case 'week': return $url . "&date=" . date('Y-m-d', strtotime($date . ' -1 week'));
                    case 'month':
                        $newMonth = $month == 1 ? 12 : $month - 1;
                        $newYear = $month == 1 ? $year - 1 : $year;
                        return $url . "&year=$newYear&month=$newMonth";
                }
                break;
            case 'next':
                switch ($view) {
                    case 'day': return $url . "&date=" . date('Y-m-d', strtotime($date . ' +1 day'));
                    case 'week': return $url . "&date=" . date('Y-m-d', strtotime($date . ' +1 week'));
                    case 'month':
                        $newMonth = $month == 12 ? 1 : $month + 1;
                        $newYear = $month == 12 ? $year + 1 : $year;
                        return $url . "&year=$newYear&month=$newMonth";
                }
                break;
        }
        return $url;
    }
}
?>

<!-- Calendar Header Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-body p-0">
                <div class="d-md-flex">
                    <!-- Left: Navigation Controls -->
                    <div class="p-4 bg-primary text-white d-flex flex-column justify-content-between" style="min-width: 300px;">
                        <div>
                            <h4 class="fw-700 mb-0">
                                <?php
                                switch ($view) {
                                    case 'day': echo date('D, M j, Y', strtotime($date)); break;
                                    case 'week': echo date('M j', strtotime($startDate)) . " - " . date('M j, Y', strtotime($endDate)); break;
                                    case 'month': echo date('F Y', mktime(0, 0, 0, $month, 1, $year)); break;
                                }
                                ?>
                            </h4>
                            <p class="opacity-75 small">System Academic Calendar</p>
                        </div>
                        <div class="d-flex mt-4 gap-2">
                            <a href="<?php echo getNavigationUrl($view, $year, $month, $date, 'prev'); ?>" class="btn btn-white btn-sm px-3 border-0 shadow-sm"><i class="fas fa-chevron-left"></i></a>
                            <a href="calendar.php?view=<?php echo $view; ?>&date=<?php echo date('Y-m-d'); ?>" class="btn btn-white btn-sm px-3 fw-600 border-0 shadow-sm">Today</a>
                            <a href="<?php echo getNavigationUrl($view, $year, $month, $date, 'next'); ?>" class="btn btn-white btn-sm px-3 border-0 shadow-sm"><i class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <!-- Right: View Selector -->
                    <div class="p-4 bg-white flex-grow-1 d-flex align-items-center justify-content-between">
                        <div class="btn-group p-1 bg-light rounded-pill shadow-inner">
                            <a href="calendar.php?view=day&date=<?php echo $date; ?>" class="btn rounded-pill px-4 fw-600 <?php echo $view === 'day' ? 'btn-primary shadow-sm' : 'btn-light border-0'; ?>">Day</a>
                            <a href="calendar.php?view=week&date=<?php echo $date; ?>" class="btn rounded-pill px-4 fw-600 <?php echo $view === 'week' ? 'btn-primary shadow-sm' : 'btn-light border-0'; ?>">Week</a>
                            <a href="calendar.php?view=month&year=<?php echo $year; ?>&month=<?php echo $month; ?>" class="btn rounded-pill px-4 fw-600 <?php echo $view === 'month' ? 'btn-primary shadow-sm' : 'btn-light border-0'; ?>">Month</a>
                        </div>
                        <?php if (isAdmin()): ?>
                        <a href="timetable.php#create" class="btn btn-outline-primary fw-600 rounded-pill px-4">
                            <i class="fas fa-plus me-2"></i>Schedule Event
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Calendar Grid Content -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if ($view === 'month'): ?>
            <div class="calendar-month-grid">
                <div class="row g-0 border-bottom bg-light">
                    <?php foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName): ?>
                        <div class="col text-center py-3 small fw-700 text-uppercase text-muted"><?php echo $dayName; ?></div>
                    <?php endforeach; ?>
                </div>
                
                <?php
                $firstDayOfMonth = date('w', mktime(0, 0, 0, $month, 1, $year));
                $daysInMonth = date('t', mktime(0, 0, 0, $month, 1, $year));
                
                echo '<div class="row g-0">';
                // Lead-in days
                for ($i = 0; $i < $firstDayOfMonth; $i++) {
                    echo '<div class="col calendar-day other-month"></div>';
                }
                
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $currDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
                    $isToday = $currDate === date('Y-m-d');
                    $dayEvents = getEventsForDate($events, $currDate);
                    
                    echo '<div class="col calendar-day' . ($isToday ? ' bg-primary bg-opacity-10' : '') . ' border-start border-bottom">';
                    echo '<div class="d-flex justify-content-between align-items-center mb-2">';
                    echo '<span class="day-number ' . ($isToday ? 'bg-primary text-white rounded-circle d-flex align-items-center justify-content-center' : 'text-muted') . '" style="' . ($isToday ? 'width: 24px; height: 24px;' : '') . '">' . $day . '</span>';
                    echo '</div>';
                    
                    echo '<div class="calendar-events-container">';
                    foreach ($dayEvents as $event) {
                        $typeClass = match($event['event_type'] ?? '') {
                            'lecture' => 'bg-info',
                            'lab' => 'bg-success',
                            'exam' => 'bg-danger',
                            default => 'bg-primary'
                        };
                        echo '<div class="cal-event-pill ' . $typeClass . ' text-white mb-1 rounded px-2 py-1 small fw-600 ripple" 
                                   data-bs-toggle="modal" data-bs-target="#eventModal" 
                                   data-event=\'' . htmlspecialchars(json_encode($event)) . '\' style="cursor:pointer; font-size: 0.7rem;">';
                        echo '<i class="fas fa-clock me-1 opacity-75"></i>' . escape($event['title']);
                        echo '</div>';
                    }
                    echo '</div>';
                    echo '</div>';
                    
                    if (($firstDayOfMonth + $day) % 7 == 0 && $day < $daysInMonth) {
                        echo '</div><div class="row g-0">';
                    }
                }
                
                // Tail-out days
                $remaining = 7 - (($firstDayOfMonth + $daysInMonth) % 7);
                if ($remaining < 7) {
                    for ($i = 0; $i < $remaining; $i++) {
                        echo '<div class="col calendar-day other-month border-start border-bottom"></div>';
                    }
                }
                echo '</div>';
                ?>
            </div>

        <?php elseif ($view === 'week'): ?>
            <div class="calendar-week-grid">
                <div class="row g-0 border-bottom bg-light sticky-top" style="z-index: 10;">
                    <div class="col-1 border-end py-3 text-center small fw-700 text-muted">Time</div>
                    <?php for ($i = 0; $i < 7; $i++): $wDate = date('Y-m-d', strtotime($startDate . " +$i days")); ?>
                        <div class="col text-center py-2 border-end <?php echo $wDate === date('Y-m-d') ? 'bg-primary bg-opacity-10' : ''; ?>">
                            <div class="small fw-700 text-uppercase <?php echo $wDate === date('Y-m-d') ? 'text-primary' : 'text-muted'; ?>"><?php echo date('D', strtotime($wDate)); ?></div>
                            <div class="h5 mb-0 fw-700"><?php echo date('j', strtotime($wDate)); ?></div>
                        </div>
                    <?php endfor; ?>
                </div>

                <?php 
                $timeSlots = [['08:00', '09:30'], ['09:45', '11:15'], ['11:30', '13:00'], ['14:00', '15:30'], ['15:45', '17:15'], ['17:30', '19:00']];
                foreach ($timeSlots as $slot): ?>
                    <div class="row g-0 border-bottom">
                        <div class="col-1 border-end py-4 text-center small fw-600 bg-light text-muted">
                            <?php echo formatTime($slot[0]); ?>
                        </div>
                        <?php for ($i = 0; $i < 7; $i++): $wDate = date('Y-m-d', strtotime($startDate . " +$i days")); ?>
                            <div class="col border-end p-2 position-relative" style="min-height: 100px;">
                                <?php
                                $dEvents = getEventsForDate($events, $wDate);
                                foreach ($dEvents as $event) {
                                    if ($event['start_time'] >= $slot[0] && $event['start_time'] < $slot[1]) {
                                        echo '<div class="cal-event-card p-2 rounded shadow-sm bg-primary bg-opacity-10 border-start border-4 border-primary mb-2 mb-1 cursor-pointer" 
                                                   data-bs-toggle="modal" data-bs-target="#eventModal" data-event=\'' . htmlspecialchars(json_encode($event)) . '\'>';
                                        echo '<div class="fw-700 small">' . escape($event['title']) . '</div>';
                                        echo '<div class="text-muted" style="font-size: 0.65rem;"><i class="fas fa-map-marker-alt me-1"></i>' . escape($event['room_name'] ?? 'TBA') . '</div>';
                                        echo '</div>';
                                    }
                                }
                                ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: // Day View ?>
            <div class="row g-0">
                <div class="col-md-9 border-end">
                    <div class="p-4">
                        <?php 
                        $timeSlots = [['08:00', '09:30'], ['09:45', '11:15'], ['11:30', '13:00'], ['14:00', '15:30'], ['15:45', '17:15'], ['17:30', '19:00']];
                        $dEvents = getEventsForDate($events, $date);
                        foreach ($timeSlots as $slot): ?>
                            <div class="d-flex mb-4">
                                <div class="text-muted fw-600 small pt-1" style="width: 80px;"><?php echo formatTime($slot[0]); ?></div>
                                <div class="flex-grow-1 border-top pt-3 ps-3">
                                    <?php
                                    $found = false;
                                    foreach ($dEvents as $event) {
                                        if ($event['start_time'] >= $slot[0] && $event['start_time'] < $slot[1]) {
                                            $found = true;
                                            echo '<div class="card border-0 shadow-sm bg-light mb-3 card-hover" data-bs-toggle="modal" data-bs-target="#eventModal" data-event=\'' . htmlspecialchars(json_encode($event)) . '\' style="cursor:pointer">';
                                            echo '<div class="card-body py-3 d-flex align-items-center">';
                                            echo '<div class="rounded-circle bg-primary text-white me-3 d-flex align-items-center justify-content-center" style="width:40px; height:40px;"><i class="fas fa-calendar-check"></i></div>';
                                            echo '<div>';
                                            echo '<h6 class="mb-0 fw-700">' . escape($event['title']) . '</h6>';
                                            echo '<small class="text-muted">' . escape($event['subject_name'] ?? 'General') . ' • ' . (escape($event['room_name'] ?? 'TBA')) . '</small>';
                                            echo '</div>';
                                            echo '<div class="ms-auto"><span class="badge bg-white text-primary border">' . formatTime($event['start_time']) . '</span></div>';
                                            echo '</div></div>';
                                        }
                                    }
                                    if (!$found) echo '<div class="text-muted small italic py-2 opacity-50">No events scheduled</div>';
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-md-3 bg-light">
                    <div class="p-4">
                        <h6 class="fw-700 mb-4 text-uppercase small text-muted">Daily Overview</h6>
                        <div class="text-center py-4 px-3 bg-white rounded shadow-sm mb-4">
                            <div class="display-4 fw-700 text-primary mb-0"><?php echo count($dEvents); ?></div>
                            <div class="fw-600 text-muted">Events Today</div>
                        </div>
                        <div class="small fw-600 text-muted mb-2">Quick Stats</div>
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                            <span>Lectures</span>
                            <span class="fw-700"><?php echo count(array_filter($dEvents, fn($e) => ($e['event_type']??'') == 'lecture')); ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Exams / Labs</span>
                            <span class="fw-700"><?php echo count(array_filter($dEvents, fn($e) => ($e['event_type']??'') != 'lecture')); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-700"><i class="fas fa-info-circle me-2"></i>Event Information</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="modalContent">
                <!-- Dynamically Populated -->
            </div>
            <div class="modal-footer bg-light border-0 py-3">
                <button type="button" class="btn btn-white fw-600 shadow-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const eventModal = document.getElementById('eventModal');
    if (eventModal) {
        eventModal.addEventListener('show.bs.modal', function(e) {
            const data = JSON.parse(e.relatedTarget.getAttribute('data-event'));
            const body = document.getElementById('modalContent');
            
            let html = `
                <div class="text-center mb-4">
                    <div class="h4 fw-700 mb-1">${data.title}</div>
                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-2 fw-600 uppercase" style="font-size:0.75rem">${data.event_type || 'General'}</span>
                </div>
                <div class="row g-4 mb-4">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded text-center">
                            <div class="small text-muted mb-1 text-uppercase fw-700" style="font-size:0.6rem">Date</div>
                            <div class="fw-700">${data.event_date}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded text-center">
                            <div class="small text-muted mb-1 text-uppercase fw-700" style="font-size:0.6rem">Time</div>
                            <div class="fw-700">${formatTime(data.start_time)} - ${formatTime(data.end_time)}</div>
                        </div>
                    </div>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0 border-0 d-flex align-items-center">
                        <div class="icon-box bg-info bg-opacity-10 text-info me-3 rounded d-flex align-items-center justify-content-center" style="width:36px; height:36px;"><i class="fas fa-book"></i></div>
                        <div><small class="text-muted d-block small fw-700 text-uppercase">Subject</small><span class="fw-600">${data.subject_name || 'N/A'}</span></div>
                    </li>
                    <li class="list-group-item px-0 border-0 d-flex align-items-center mt-2">
                        <div class="icon-box bg-success bg-opacity-10 text-success me-3 rounded d-flex align-items-center justify-content-center" style="width:36px; height:36px;"><i class="fas fa-chalkboard-teacher"></i></div>
                        <div><small class="text-muted d-block small fw-700 text-uppercase">Teacher</small><span class="fw-600">${data.teacher_first_name} ${data.teacher_last_name}</span></div>
                    </li>
                    <li class="list-group-item px-0 border-0 d-flex align-items-center mt-2">
                        <div class="icon-box bg-warning bg-opacity-10 text-warning me-3 rounded d-flex align-items-center justify-content-center" style="width:36px; height:36px;"><i class="fas fa-users"></i></div>
                        <div><small class="text-muted d-block small fw-700 text-uppercase">Class</small><span class="fw-600">${data.class_name || 'N/A'}</span></div>
                    </li>
                    <li class="list-group-item px-0 border-0 d-flex align-items-center mt-2">
                        <div class="icon-box bg-danger bg-opacity-10 text-danger me-3 rounded d-flex align-items-center justify-content-center" style="width:36px; height:36px;"><i class="fas fa-map-marker-alt"></i></div>
                        <div><small class="text-muted d-block small fw-700 text-uppercase">Location</small><span class="fw-600">${data.room_name || 'TBA'}</span></div>
                    </li>
                </ul>
                ${data.description ? '<div class="mt-4 p-3 border rounded small text-muted bg-white shadow-inner">' + data.description + '</div>' : ''}
            `;
            body.innerHTML = html;
        });
    }
});

function formatTime(timeStr) {
    if (!timeStr) return '';
    const [h, m] = timeStr.split(':');
    const hh = parseInt(h);
    const ampm = hh >= 12 ? 'PM' : 'AM';
    return `${hh % 12 || 12}:${m} ${ampm}`;
}
</script>

<style>
.calendar-day { height: 140px; padding: 10px; transition: background 0.2s; position: relative; }
.calendar-day:hover { background-color: #f8fafc; }
.calendar-day.other-month { background-color: #fcfcfc; opacity: 0.5; }
.day-number { font-weight: 700; font-size: 0.9rem; }
.cal-event-pill { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; transition: transform 0.1s; }
.cal-event-pill:hover { transform: translateY(-1px); }
.shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); }
.btn-white { background-color: #fff; color: #333; }
.cursor-pointer { cursor: pointer; }
.card-hover:hover { transform: translateY(-3px); transition: all 0.3s; background-color: #f1f5f9 !important; }
</style>
