<!-- Page Header Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white border-0 shadow-sm">
            <div class="card-body py-4 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="card-title mb-1 fw-700">
                        <i class="fas fa-calendar-alt me-2"></i>Timetable Management
                    </h2>
                    <p class="card-text opacity-75 fs-5 mb-0">Create and manage academic schedules and events</p>
                </div>
                <button type="button" class="btn btn-light btn-lg fw-600 px-4" data-bs-toggle="modal" data-bs-target="#createEventModal">
                    <i class="fas fa-plus me-2 text-primary"></i>Create Event
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
                <h5 class="mb-0 fw-700"><i class="fas fa-search me-2 text-primary"></i>Filter Events</h5>
            </div>
            <div class="card-body pt-0">
                <form method="GET" action="timetable.php" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label small fw-600 text-muted uppercase">Search Event</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0 bg-light" id="search" name="search" value="<?php echo escape($search ?? ''); ?>" placeholder="Title or description...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="date" class="form-label small fw-600 text-muted uppercase">Filter by Date</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-calendar-day text-muted"></i></span>
                            <input type="date" class="form-control border-start-0 ps-0 bg-light" id="date" name="date" value="<?php echo escape($date_filter ?? ''); ?>">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-600">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <a href="timetable.php" class="btn btn-outline-secondary w-100 py-2 fw-600">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Timetable Table -->
<div class="card border-0 shadow-sm list-card">
    <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-700"><i class="fas fa-list me-2 text-primary"></i>Event List</h5>
        <?php if (isset($pagination['total_records'])): ?>
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-600"><?php echo $pagination['total_records']; ?> Events Found</span>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0 text-muted small fw-600 text-uppercase">Date & Time</th>
                        <th class="py-3 border-0 text-muted small fw-600 text-uppercase">Event Details</th>
                        <th class="py-3 border-0 text-muted small fw-600 text-uppercase">Subject & Class</th>
                        <th class="py-3 border-0 text-muted small fw-600 text-uppercase">Room</th>
                        <th class="py-3 border-0 text-muted small fw-600 text-uppercase">Teacher</th>
                        <th class="text-end pe-4 py-3 border-0 text-muted small fw-600 text-uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (empty($events)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-calendar-times fa-3x text-light mb-3 d-block"></i>
                                <p class="text-muted fw-500">No events found matching your criteria.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td class="ps-4 align-middle">
                                    <div class="fw-700 text-dark"><?php echo formatDate($event['event_date']); ?></div>
                                    <small class="text-primary fw-600"><?php echo formatTime($event['start_time']) . ' - ' . formatTime($event['end_time']); ?></small>
                                </td>
                                <td class="align-middle">
                                    <div class="fw-700 text-dark"><?php echo escape($event['title']); ?></div>
                                    <span class="badge rounded-pill bg-info bg-opacity-10 text-info px-2 py-1" style="font-size: 0.75rem; font-weight: 600;">
                                        <?php echo strtoupper($event['event_type']); ?>
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <div class="fw-600"><?php echo escape($event['subject_name'] ?? 'N/A'); ?></div>
                                    <small class="text-muted"><?php echo escape($event['class_name'] ?? 'N/A'); ?></small>
                                </td>
                                <td class="align-middle">
                                    <div><i class="fas fa-door-open me-2 text-muted"></i><?php echo escape($event['room_name'] ?? 'N/A'); ?></div>
                                    <?php if (!empty($event['location'])): ?>
                                        <small class="text-muted d-block ms-4"><?php echo escape($event['location']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                            <i class="fas fa-user-tie text-muted"></i>
                                        </div>
                                        <div class="fw-600"><?php echo escape($event['teacher_first_name'] . ' ' . $event['teacher_last_name']); ?></div>
                                    </div>
                                </td>
                                <td class="text-end pe-4 align-middle">
                                    <div class="btn-group shadow-sm rounded">
                                        <button type="button" class="btn btn-white btn-sm edit-event-btn border-0 px-3" 
                                                data-bs-toggle="modal" data-bs-target="#editEventModal" 
                                                data-event='<?php echo json_encode($event); ?>'
                                                title="Edit Event">
                                            <i class="fas fa-edit text-primary"></i>
                                        </button>
                                        <button type="button" class="btn btn-white btn-sm delete-event-btn border-0 px-3"
                                                data-bs-target="#deleteEventModal"
                                                data-bs-toggle="modal"
                                                data-event-id="<?php echo $event['id']; ?>"
                                                title="Delete Event">
                                            <i class="fas fa-trash text-danger"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (!empty($events)): ?>
    <div class="card-footer bg-white border-0 py-4 px-4">
        <?php include __DIR__ . '/../../includes/pagination.php'; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Create Event Modal -->
<div class="modal fade" id="createEventModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="timetable.php">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title fw-700"><i class="fas fa-plus-circle me-2"></i>New Timetable Event</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label for="title" class="form-label small fw-700 text-uppercase tracking-wider">Event Title</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0 px-3 py-2 fs-6" id="title" name="title" required placeholder="e.g., Mathematics Lecture">
                        </div>
                        <div class="col-md-6">
                            <label for="event_type" class="form-label small fw-700 text-uppercase tracking-wider">Event Type</label>
                            <select class="form-select bg-light border-0 px-3 py-2 fs-6" id="event_type" name="event_type" required>
                                <option value="class">🎓 Class</option>
                                <option value="exam">📝 Exam</option>
                                <option value="meeting">🤝 Meeting</option>
                                <option value="break">☕ Break</option>
                                <option value="other">⭐ Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="subject_id" class="form-label small fw-700 text-uppercase tracking-wider">Subject</label>
                            <select class="form-select bg-light border-0 px-3 py-2 fs-6" id="subject_id" name="subject_id">
                                <option value="">None / Other</option>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?php echo $subject['id']; ?>"><?php echo escape($subject['subject_code'] . ' - ' . $subject['subject_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="class_id" class="form-label small fw-700 text-uppercase tracking-wider">Class/Section</label>
                            <select class="form-select bg-light border-0 px-3 py-2 fs-6" id="class_id" name="class_id">
                                <option value="">None / Other</option>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?php echo $class['id']; ?>"><?php echo escape($class['class_code'] . ' - ' . $class['class_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="teacher_id" class="form-label small fw-700 text-uppercase tracking-wider">Teacher</label>
                            <select class="form-select bg-light border-0 px-3 py-2 fs-6" id="teacher_id" name="teacher_id" <?php echo isTeacher() ? 'disabled' : ''; ?>>
                                <?php if (isTeacher()): ?>
                                    <option value="<?php echo $current_user['id']; ?>" selected><?php echo escape($current_user['first_name'] . ' ' . $current_user['last_name']); ?></option>
                                <?php else: ?>
                                    <option value="">Select Teacher</option>
                                    <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?php echo $teacher['id']; ?>"><?php echo escape($teacher['first_name'] . ' ' . $teacher['last_name']); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="room_id" class="form-label small fw-700 text-uppercase tracking-wider">Room</label>
                            <select class="form-select bg-light border-0 px-3 py-2 fs-6" id="room_id" name="room_id">
                                <option value="">None / Other</option>
                                <?php foreach ($rooms as $room): ?>
                                    <option value="<?php echo $room['id']; ?>"><?php echo escape($room['room_code'] . ' - ' . $room['room_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="event_date" class="form-label small fw-700 text-uppercase tracking-wider">Event Date</label>
                            <input type="date" class="form-control bg-light border-0 px-3 py-2 fs-6" id="event_date" name="event_date" required value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="time_slot_id" class="form-label small fw-700 text-uppercase tracking-wider">Time Slot</label>
                            <select class="form-select bg-light border-0 px-3 py-2 fs-6" id="time_slot_id" name="time_slot_id" required>
                                <option value="">Select Time Slot</option>
                                <?php foreach ($timeSlots as $slot): ?>
                                    <option value="<?php echo $slot['id']; ?>"><?php echo escape($slot['slot_name'] . ' (' . formatTime($slot['start_time']) . ')'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label small fw-700 text-uppercase tracking-wider">Description (Optional)</label>
                            <textarea class="form-control bg-light border-0 px-3 py-2 fs-6" id="description" name="description" rows="3" placeholder="Additional details about the event..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-white fw-600 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-600 px-5 shadow-sm">Save Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Event Modal -->
<div class="modal fade" id="editEventModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="timetable.php">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="event_id" id="edit_event_id">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title fw-700"><i class="fas fa-edit me-2"></i>Edit Timetable Event</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <!-- Same fields as create modal but with 'edit_' IDs -->
                         <div class="col-md-12">
                            <label for="edit_title" class="form-label small fw-700 text-uppercase tracking-wider">Event Title</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0 px-3 py-2 fs-6" id="edit_title" name="title" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_event_type" class="form-label small fw-700 text-uppercase tracking-wider">Event Type</label>
                            <select class="form-select bg-light border-0 px-3 py-2 fs-6" id="edit_event_type" name="event_type" required>
                                <option value="class">🎓 Class</option>
                                <option value="exam">📝 Exam</option>
                                <option value="meeting">🤝 Meeting</option>
                                <option value="break">☕ Break</option>
                                <option value="other">⭐ Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_subject_id" class="form-label small fw-700 text-uppercase tracking-wider">Subject</label>
                            <select class="form-select bg-light border-0 px-3 py-2 fs-6" id="edit_subject_id" name="subject_id">
                                <option value="">None / Other</option>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?php echo $subject['id']; ?>"><?php echo escape($subject['subject_code'] . ' - ' . $subject['subject_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_class_id" class="form-label small fw-700 text-uppercase tracking-wider">Class/Section</label>
                            <select class="form-select bg-light border-0 px-3 py-2 fs-6" id="edit_class_id" name="class_id">
                                <option value="">None / Other</option>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?php echo $class['id']; ?>"><?php echo escape($class['class_code'] . ' - ' . $class['class_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_teacher_id" class="form-label small fw-700 text-uppercase tracking-wider">Teacher</label>
                            <select class="form-select bg-light border-0 px-3 py-2 fs-6" id="edit_teacher_id" name="teacher_id" <?php echo isTeacher() ? 'disabled' : ''; ?>>
                                <?php if (isTeacher()): ?>
                                    <option value="<?php echo $current_user['id']; ?>" selected><?php echo escape($current_user['first_name'] . ' ' . $current_user['last_name']); ?></option>
                                <?php else: ?>
                                    <option value="">Select Teacher</option>
                                    <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?php echo $teacher['id']; ?>"><?php echo escape($teacher['first_name'] . ' ' . $teacher['last_name']); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_room_id" class="form-label small fw-700 text-uppercase tracking-wider">Room</label>
                            <select class="form-select bg-light border-0 px-3 py-2 fs-6" id="edit_room_id" name="room_id">
                                <option value="">None / Other</option>
                                <?php foreach ($rooms as $room): ?>
                                    <option value="<?php echo $room['id']; ?>"><?php echo escape($room['room_code'] . ' - ' . $room['room_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_event_date" class="form-label small fw-700 text-uppercase tracking-wider">Event Date</label>
                            <input type="date" class="form-control bg-light border-0 px-3 py-2 fs-6" id="edit_event_date" name="event_date" required>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_time_slot_id" class="form-label small fw-700 text-uppercase tracking-wider">Time Slot</label>
                            <select class="form-select bg-light border-0 px-3 py-2 fs-6" id="edit_time_slot_id" name="time_slot_id" required>
                                <option value="">Select Time Slot</option>
                                <?php foreach ($timeSlots as $slot): ?>
                                    <option value="<?php echo $slot['id']; ?>"><?php echo escape($slot['slot_name'] . ' (' . formatTime($slot['start_time']) . ')'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="edit_description" class="form-label small fw-700 text-uppercase tracking-wider">Description (Optional)</label>
                            <textarea class="form-control bg-light border-0 px-3 py-2 fs-6" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-white fw-600 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-600 px-5 shadow-sm">Update Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Event Modal -->
<div class="modal fade" id="deleteEventModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="timetable.php">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="event_id" id="delete_event_id">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <div class="modal-header bg-danger text-white border-0 py-3">
                    <h5 class="modal-title fw-700"><i class="fas fa-trash-alt me-2"></i>Delete Event</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle fa-4x text-danger opacity-25"></i>
                    </div>
                    <h5 class="fw-700 mb-2">Are you sure?</h5>
                    <p class="text-muted mb-0">This action cannot be undone and will permanently remove this event from the schedule.</p>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3 justify-content-center">
                    <button type="button" class="btn btn-white fw-600 px-4" data-bs-dismiss="modal">No, Keep it</button>
                    <button type="submit" class="btn btn-danger fw-600 px-4 shadow-sm">Yes, Delete Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Handle Edit Modal
    const editEventModal = document.getElementById('editEventModal');
    if (editEventModal) {
        editEventModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const data = JSON.parse(button.getAttribute('data-event'));
            
            document.getElementById('edit_event_id').value = data.id;
            document.getElementById('edit_title').value = data.title;
            document.getElementById('edit_event_type').value = data.event_type;
            document.getElementById('edit_subject_id').value = data.subject_id || '';
            document.getElementById('edit_class_id').value = data.class_id || '';
            document.getElementById('edit_teacher_id').value = data.teacher_id;
            document.getElementById('edit_room_id').value = data.room_id || '';
            document.getElementById('edit_event_date').value = data.event_date;
            document.getElementById('edit_time_slot_id').value = data.time_slot_id;
            document.getElementById('edit_description').value = data.description || '';
        });
    }

    // Handle Delete Modal
    const deleteEventModal = document.getElementById('deleteEventModal');
    if (deleteEventModal) {
        deleteEventModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const eventId = button.getAttribute('data-event-id');
            document.getElementById('delete_event_id').value = eventId;
        });
    }
</script>

<style>
    .tracking-wider { letter-spacing: 0.05em; }
    .uppercase { text-transform: uppercase; }
    .btn-white { background-color: #fff; border: 1px solid #e2e8f0; color: #4a5568; }
    .btn-white:hover { background-color: #f7fafc; color: #2d3748; }
    .avatar-sm { line-height: 1; }
    .fw-700 { font-weight: 700; }
</style>
