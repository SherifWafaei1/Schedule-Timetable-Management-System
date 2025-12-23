<!-- Page Header Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-warning text-dark border-0 shadow-sm">
            <div class="card-body py-4 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="card-title mb-1 fw-700">
                        <i class="fas fa-graduation-cap me-2"></i>Class Management
                    </h2>
                    <p class="card-text opacity-75 fs-5 mb-0">Define and manage student groups, cohorts and classes</p>
                </div>
                <button type="button" class="btn btn-dark btn-lg fw-600 px-4" data-bs-toggle="modal" data-bs-target="#createClassModal">
                    <i class="fas fa-plus me-2"></i>Add Class
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Classes Table Card -->
<div class="card border-0 shadow-sm list-card">
    <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-700"><i class="fas fa-list me-2 text-warning"></i>All Classes (<?php echo $total_classes; ?>)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0 text-muted small fw-600 text-uppercase">Class Info</th>
                        <th class="py-3 border-0 text-muted small fw-600 text-uppercase">Code</th>
                        <th class="py-3 border-0 text-muted small fw-600 text-uppercase">Status</th>
                        <th class="text-end pe-4 py-3 border-0 text-muted small fw-600 text-uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (empty($classes)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <i class="fas fa-users-slash fa-3x text-light mb-3 d-block"></i>
                                <p class="text-muted fw-500">No classes registered yet.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($classes as $class): ?>
                            <tr>
                                <td class="ps-4 align-middle">
                                    <div class="fw-700 text-dark"><?php echo escape($class['class_name']); ?></div>
                                    <small class="text-muted"><?php echo escape($class['description'] ?: 'No description provided'); ?></small>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-light text-dark fw-700 border"><?php echo escape($class['class_code']); ?></span>
                                </td>
                                <td class="align-middle">
                                    <?php if ($class['is_active']): ?>
                                        <span class="text-success small fw-700"><i class="fas fa-check-circle me-1"></i>Active</span>
                                    <?php else: ?>
                                        <span class="text-muted small fw-700"><i class="fas fa-times-circle me-1"></i>Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4 align-middle">
                                    <div class="btn-group shadow-sm rounded border">
                                        <button type="button" class="btn btn-white btn-sm edit-class-btn border-0 px-3" 
                                                data-bs-toggle="modal" data-bs-target="#editClassModal" 
                                                data-class='<?php echo json_encode($class); ?>'
                                                title="Edit Class">
                                            <i class="fas fa-edit text-warning"></i>
                                        </button>
                                        <button type="button" class="btn btn-white btn-sm delete-class-btn border-0 px-3"
                                                data-bs-target="#deleteClassModal"
                                                data-bs-toggle="modal"
                                                data-class-id="<?php echo $class['id']; ?>"
                                                data-class-name="<?php echo escape($class['class_name']); ?>"
                                                title="Delete Class">
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
    <?php if (!empty($classes)): ?>
    <div class="card-footer bg-white border-0 py-4 px-4">
        <?php include __DIR__ . '/../../../includes/pagination.php'; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modals -->
<!-- Create Class Modal -->
<div class="modal fade" id="createClassModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="classes.php">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <div class="modal-header bg-warning text-dark border-0 py-3">
                    <h5 class="modal-title fw-700"><i class="fas fa-plus-circle me-2"></i>Add New Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Class Code</label>
                            <input type="text" class="form-control bg-light border-0 px-3 py-2" name="class_code" required placeholder="e.g. GR10-A">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Class Name</label>
                            <input type="text" class="form-control bg-light border-0 px-3 py-2" name="class_name" required placeholder="e.g. Grade 10 Section A">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Description</label>
                            <textarea class="form-control bg-light border-0 px-3 py-2" name="description" rows="3" placeholder="Optional details about this class group..."></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="classActive" checked>
                                <label class="form-check-label fw-600" for="classActive">Class is active and enrollable</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-white fw-600" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning fw-600 px-4 shadow-sm text-dark">Save Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Class Modal -->
<div class="modal fade" id="editClassModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="classes.php">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="class_id" id="edit_class_id">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <div class="modal-header bg-warning text-dark border-0 py-3">
                    <h5 class="modal-title fw-700"><i class="fas fa-edit me-2"></i>Edit Class Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Class Code</label>
                            <input type="text" class="form-control bg-light border-0 px-3 py-2" id="edit_class_code" name="class_code" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Class Name</label>
                            <input type="text" class="form-control bg-light border-0 px-3 py-2" id="edit_class_name" name="class_name" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Description</label>
                            <textarea class="form-control bg-light border-0 px-3 py-2" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active">
                                <label class="form-check-label fw-600" for="edit_is_active">Class is active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-white fw-600" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning fw-600 px-4 shadow-sm text-dark">Update Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Class Modal -->
<div class="modal fade" id="deleteClassModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="classes.php">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="class_id" id="delete_class_id">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <div class="modal-header bg-danger text-white border-0 py-3">
                    <h5 class="modal-title fw-700"><i class="fas fa-trash-alt me-2"></i>Delete Class</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-circle fa-4x text-danger opacity-25"></i>
                    </div>
                    <h5 class="fw-700 mb-2">Are you sure?</h5>
                    <p class="text-muted mb-0 fw-500">Removing <span id="delete_label_class_name" class="fw-700 text-dark"></span> will affect all associated timetable records.</p>
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
        const editClassModal = document.getElementById('editClassModal');
        if (editClassModal) {
            editClassModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const classObj = JSON.parse(button.getAttribute('data-class'));
                
                document.getElementById('edit_class_id').value = classObj.id;
                document.getElementById('edit_class_code').value = classObj.class_code;
                document.getElementById('edit_class_name').value = classObj.class_name;
                document.getElementById('edit_description').value = classObj.description || '';
                document.getElementById('edit_is_active').checked = parseInt(classObj.is_active) === 1;
            });
        }

        // Delete Logic
        const deleteClassModal = document.getElementById('deleteClassModal');
        if (deleteClassModal) {
            deleteClassModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                document.getElementById('delete_class_id').value = button.getAttribute('data-class-id');
                document.getElementById('delete_label_class_name').textContent = button.getAttribute('data-class-name');
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
