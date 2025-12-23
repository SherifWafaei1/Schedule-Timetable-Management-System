<!-- Page Header Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white border-0 shadow-sm">
            <div class="card-body py-4 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="card-title mb-1 fw-700">
                        <i class="fas fa-door-open me-2"></i>Room Management
                    </h2>
                    <p class="card-text opacity-75 fs-5 mb-0">Manage classrooms, labs, and facility resources</p>
                </div>
                <button type="button" class="btn btn-light btn-lg fw-600 px-4" data-bs-toggle="modal" data-bs-target="#createRoomModal">
                    <i class="fas fa-plus me-2 text-primary"></i>Add Room
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Rooms Table Card -->
<div class="card border-0 shadow-sm list-card">
    <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-700"><i class="fas fa-list me-2 text-primary"></i>All Rooms (<?php echo $total_rooms; ?>)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0 text-muted small fw-600 text-uppercase">Room Details</th>
                        <th class="py-3 border-0 text-muted small fw-600 text-uppercase">Type</th>
                        <th class="py-3 border-0 text-muted small fw-600 text-uppercase">Capacity</th>
                        <th class="py-3 border-0 text-muted small fw-600 text-uppercase">Status</th>
                        <th class="text-end pe-4 py-3 border-0 text-muted small fw-600 text-uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (empty($rooms)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-door-closed fa-3x text-light mb-3 d-block"></i>
                                <p class="text-muted fw-500">No rooms registered in the system.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rooms as $room): ?>
                            <tr>
                                <td class="ps-4 align-middle">
                                    <div class="fw-700 text-dark"><?php echo escape($room['room_name']); ?></div>
                                    <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i><?php echo escape($room['location'] ?: 'No location set'); ?> • <?php echo escape($room['room_code']); ?></small>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-light text-primary fw-600 border px-3 py-2 text-uppercase" style="font-size: 0.7rem;">
                                        <?php echo escape($room['room_type']); ?>
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-users text-muted me-2"></i>
                                        <span class="fw-600"><?php echo escape($room['capacity']); ?></span>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <?php if ($room['is_active']): ?>
                                        <span class="text-success small fw-700"><i class="fas fa-check-circle me-1"></i>Active</span>
                                    <?php else: ?>
                                        <span class="text-muted small fw-700"><i class="fas fa-times-circle me-1"></i>Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4 align-middle">
                                    <div class="btn-group shadow-sm rounded border">
                                        <button type="button" class="btn btn-white btn-sm edit-room-btn border-0 px-3" 
                                                data-bs-toggle="modal" data-bs-target="#editRoomModal" 
                                                data-room='<?php echo json_encode($room); ?>'
                                                title="Edit Room">
                                            <i class="fas fa-edit text-primary"></i>
                                        </button>
                                        <button type="button" class="btn btn-white btn-sm delete-room-btn border-0 px-3"
                                                data-bs-target="#deleteRoomModal"
                                                data-bs-toggle="modal"
                                                data-room-id="<?php echo $room['id']; ?>"
                                                data-room-name="<?php echo escape($room['room_name']); ?>"
                                                title="Delete Room">
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
    <?php if (!empty($rooms)): ?>
    <div class="card-footer bg-white border-0 py-4 px-4">
        <?php include __DIR__ . '/../../../includes/pagination.php'; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modals -->
<!-- Create Room Modal -->
<div class="modal fade" id="createRoomModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="rooms.php">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title fw-700"><i class="fas fa-plus-circle me-2"></i>Register New Room</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Room Code</label>
                            <input type="text" class="form-control bg-light border-0 px-3 py-2" name="room_code" required placeholder="e.g. R101">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Room Name</label>
                            <input type="text" class="form-control bg-light border-0 px-3 py-2" name="room_name" required placeholder="e.g. Science Lab A">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Capacity</label>
                            <input type="number" class="form-control bg-light border-0 px-3 py-2" name="capacity" value="30" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Room Type</label>
                            <select class="form-select bg-light border-0 px-3 py-2" name="room_type" required>
                                <option value="classroom">Classroom</option>
                                <option value="lab">Laboratory</option>
                                <option value="auditorium">Auditorium</option>
                                <option value="meeting_room">Meeting Room</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Location / Building</label>
                            <input type="text" class="form-control bg-light border-0 px-3 py-2" name="location" placeholder="e.g. Building B, 2nd Floor">
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="roomActive" checked>
                                <label class="form-check-label fw-600" for="roomActive">Room is available for scheduling</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-white fw-600" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-600 px-4 shadow-sm">Save Room</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Room Modal -->
<div class="modal fade" id="editRoomModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="rooms.php">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="room_id" id="edit_room_id">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title fw-700"><i class="fas fa-edit me-2"></i>Modify Room Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Room Code</label>
                            <input type="text" class="form-control bg-light border-0 px-3 py-2" id="edit_room_code" name="room_code" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Room Name</label>
                            <input type="text" class="form-control bg-light border-0 px-3 py-2" id="edit_room_name" name="room_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Capacity</label>
                            <input type="number" class="form-control bg-light border-0 px-3 py-2" id="edit_capacity" name="capacity" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Room Type</label>
                            <select class="form-select bg-light border-0 px-3 py-2" id="edit_room_type" name="room_type" required>
                                <option value="classroom">Classroom</option>
                                <option value="lab">Laboratory</option>
                                <option value="auditorium">Auditorium</option>
                                <option value="meeting_room">Meeting Room</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Location / Building</label>
                            <input type="text" class="form-control bg-light border-0 px-3 py-2" id="edit_location" name="location">
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active">
                                <label class="form-check-label fw-600" for="edit_is_active">Room is active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-white fw-600" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-600 px-4 shadow-sm">Update Room</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Room Modal -->
<div class="modal fade" id="deleteRoomModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="rooms.php">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="room_id" id="delete_room_id">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <div class="modal-header bg-danger text-white border-0 py-3">
                    <h5 class="modal-title fw-700"><i class="fas fa-trash-alt me-2"></i>Delete Room</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle fa-4x text-danger opacity-25"></i>
                    </div>
                    <h5 class="fw-700 mb-2">Are you sure?</h5>
                    <p class="text-muted mb-0 fw-500">Removing <span id="delete_label_room_name" class="fw-700 text-dark"></span> may affect scheduled events.</p>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3 justify-content-center">
                    <button type="button" class="btn btn-white fw-600 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-600 px-4 shadow-sm">Confirm Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Edit Logic
        const editRoomModal = document.getElementById('editRoomModal');
        if (editRoomModal) {
            editRoomModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const room = JSON.parse(button.getAttribute('data-room'));
                
                document.getElementById('edit_room_id').value = room.id;
                document.getElementById('edit_room_code').value = room.room_code;
                document.getElementById('edit_room_name').value = room.room_name;
                document.getElementById('edit_capacity').value = room.capacity;
                document.getElementById('edit_room_type').value = room.room_type;
                document.getElementById('edit_location').value = room.location || '';
                document.getElementById('edit_is_active').checked = parseInt(room.is_active) === 1;
            });
        }

        // Delete Logic
        const deleteRoomModal = document.getElementById('deleteRoomModal');
        if (deleteRoomModal) {
            deleteRoomModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                document.getElementById('delete_room_id').value = button.getAttribute('data-room-id');
                document.getElementById('delete_label_room_name').textContent = button.getAttribute('data-room-name');
            });
        }
    });
</script>

<style>
    .tracking-wider { letter-spacing: 0.05em; }
    .btn-white { background-color: #fff; border: 1px solid #e2e8f0; color: #4a5568; }
    .btn-white:hover { background-color: #f7fafc; color: #2d3748; }
    .fw-700 { font-weight: 700; }
</style>
