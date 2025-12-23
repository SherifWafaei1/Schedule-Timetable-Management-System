<!-- Page Header Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-success text-white border-0 shadow-sm">
            <div class="card-body py-4 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="card-title mb-1 fw-700">
                        <i class="fas fa-book me-2"></i>Subject Management
                    </h2>
                    <p class="card-text opacity-75 fs-5 mb-0">Manage course catalogue, credits, and subject details</p>
                </div>
                <button type="button" class="btn btn-light btn-lg fw-600 px-4" data-bs-toggle="modal" data-bs-target="#createSubjectModal">
                    <i class="fas fa-plus me-2 text-success"></i>Add Subject
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Subjects Table Card -->
<div class="card border-0 shadow-sm list-card">
    <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-700"><i class="fas fa-list me-2 text-success"></i>All Subjects (<?php echo $total_subjects; ?>)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0 text-muted small fw-600 text-uppercase">Subject Info</th>
                        <th class="py-3 border-0 text-muted small fw-600 text-uppercase">Code</th>
                        <th class="py-3 border-0 text-muted small fw-600 text-uppercase">Credits</th>
                        <th class="py-3 border-0 text-muted small fw-600 text-uppercase">Status</th>
                        <th class="text-end pe-4 py-3 border-0 text-muted small fw-600 text-uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (empty($subjects)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-book-open fa-3x text-light mb-3 d-block"></i>
                                <p class="text-muted fw-500">No subjects registered yet.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($subjects as $subject): ?>
                            <tr>
                                <td class="ps-4 align-middle">
                                    <div class="fw-700 text-dark"><?php echo escape($subject['subject_name']); ?></div>
                                    <small class="text-muted"><?php echo escape($subject['description'] ? substr($subject['description'], 0, 50) . '...' : 'No description provided'); ?></small>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-light text-success fw-700 border"><?php echo escape($subject['subject_code']); ?></span>
                                </td>
                                <td class="align-middle">
                                    <div class="fw-600"><?php echo escape($subject['credits']); ?> Credits</div>
                                </td>
                                <td class="align-middle">
                                    <?php if ($subject['is_active']): ?>
                                        <span class="text-success small fw-700"><i class="fas fa-check-circle me-1"></i>Active</span>
                                    <?php else: ?>
                                        <span class="text-muted small fw-700"><i class="fas fa-times-circle me-1"></i>Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4 align-middle">
                                    <div class="btn-group shadow-sm rounded">
                                        <button type="button" class="btn btn-white btn-sm edit-subject-btn border-0 px-3" 
                                                data-bs-toggle="modal" data-bs-target="#editSubjectModal" 
                                                data-subject='<?php echo json_encode($subject); ?>'
                                                title="Edit Subject">
                                            <i class="fas fa-edit text-success"></i>
                                        </button>
                                        <button type="button" class="btn btn-white btn-sm delete-subject-btn border-0 px-3"
                                                data-bs-target="#deleteSubjectModal"
                                                data-bs-toggle="modal"
                                                data-subject-id="<?php echo $subject['id']; ?>"
                                                data-subject-name="<?php echo escape($subject['subject_name']); ?>"
                                                title="Delete Subject">
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
    <?php if (!empty($subjects)): ?>
    <div class="card-footer bg-white border-0 py-4 px-4">
        <?php include __DIR__ . '/../../../includes/pagination.php'; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modals -->
<!-- Create Subject Modal -->
<div class="modal fade" id="createSubjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="subjects.php">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <div class="modal-header bg-success text-white border-0 py-3">
                    <h5 class="modal-title fw-700"><i class="fas fa-plus-circle me-2"></i>Add New Subject</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Subject Code</label>
                            <input type="text" class="form-control bg-light border-0 px-3 py-2" name="subject_code" required placeholder="e.g. CS101">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Subject Name</label>
                            <input type="text" class="form-control bg-light border-0 px-3 py-2" name="subject_name" required placeholder="e.g. Computer Science I">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Credits</label>
                            <input type="number" class="form-control bg-light border-0 px-3 py-2" name="credits" value="3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Description</label>
                            <textarea class="form-control bg-light border-0 px-3 py-2" name="description" rows="3" placeholder="Brief overview of the subject..."></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="subjectActive" checked>
                                <label class="form-check-label fw-600" for="subjectActive">Subject is active and available for selection</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-white fw-600" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-600 px-4 shadow-sm">Save Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Subject Modal -->
<div class="modal fade" id="editSubjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="subjects.php">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="subject_id" id="edit_subject_id">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <div class="modal-header bg-success text-white border-0 py-3">
                    <h5 class="modal-title fw-700"><i class="fas fa-edit me-2"></i>Edit Subject Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Subject Code</label>
                            <input type="text" class="form-control bg-light border-0 px-3 py-2" id="edit_subject_code" name="subject_code" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Subject Name</label>
                            <input type="text" class="form-control bg-light border-0 px-3 py-2" id="edit_subject_name" name="subject_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Credits</label>
                            <input type="number" class="form-control bg-light border-0 px-3 py-2" id="edit_credits" name="credits" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-700 text-uppercase tracking-wider">Description</label>
                            <textarea class="form-control bg-light border-0 px-3 py-2" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active">
                                <label class="form-check-label fw-600" for="edit_is_active">Subject is active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-white fw-600" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-600 px-4 shadow-sm">Update Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Subject Modal -->
<div class="modal fade" id="deleteSubjectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="subjects.php">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="subject_id" id="delete_subject_id">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <div class="modal-header bg-danger text-white border-0 py-3">
                    <h5 class="modal-title fw-700"><i class="fas fa-trash-alt me-2"></i>Delete Subject</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-4">
                        <i class="fas fa-book-dead fa-4x text-danger opacity-25"></i>
                    </div>
                    <h5 class="fw-700 mb-2">Are you sure?</h5>
                    <p class="text-muted mb-0 fw-500">You are about to delete <span id="delete_label_subject_name" class="fw-700 text-dark"></span>. This may affect existing timetable entries.</p>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3 justify-content-center">
                    <button type="button" class="btn btn-white fw-600 px-4" data-bs-dismiss="modal">Keep Subject</button>
                    <button type="submit" class="btn btn-danger fw-600 px-4 shadow-sm">Confirm Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Edit Logic
        const editSubjectModal = document.getElementById('editSubjectModal');
        if (editSubjectModal) {
            editSubjectModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const subject = JSON.parse(button.getAttribute('data-subject'));
                
                document.getElementById('edit_subject_id').value = subject.id;
                document.getElementById('edit_subject_code').value = subject.subject_code;
                document.getElementById('edit_subject_name').value = subject.subject_name;
                document.getElementById('edit_description').value = subject.description || '';
                document.getElementById('edit_credits').value = subject.credits;
                document.getElementById('edit_is_active').checked = parseInt(subject.is_active) === 1;
            });
        }

        // Delete Logic
        const deleteSubjectModal = document.getElementById('deleteSubjectModal');
        if (deleteSubjectModal) {
            deleteSubjectModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                document.getElementById('delete_subject_id').value = button.getAttribute('data-subject-id');
                document.getElementById('delete_label_subject_name').textContent = button.getAttribute('data-subject-name');
            });
        }
    });
</script>

<style>
    .tracking-wider { letter-spacing: 0.05em; }
    .uppercase { text-transform: uppercase; }
    .btn-white { background-color: #fff; border: 1px solid #e2e8f0; color: #4a5568; }
    .btn-white:hover { background-color: #f7fafc; color: #2d3748; }
    .fw-700 { font-weight: 700; }
</style>
