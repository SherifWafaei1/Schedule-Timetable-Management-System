<!-- Page Header -->
<div class="row mb-4 no-print">
    <div class="col-12">
        <div class="card bg-info text-white border-0 shadow-sm">
            <div class="card-body py-4">
                <h2 class="card-title mb-1 fw-700"><i class="fas fa-chart-bar me-2"></i>System Intelligence & Reports</h2>
                <p class="card-text opacity-75 fs-5 mb-0">Aggregate scheduling data and institutional performance metrics</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm mb-4 no-print">
    <div class="card-header bg-transparent border-0 py-3">
        <h5 class="mb-0 fw-700"><i class="fas fa-filter me-2 text-info"></i>Report Parameters</h5>
    </div>
    <div class="card-body p-4">
        <form method="GET" action="reports.php" class="row g-4">
            <div class="col-md-3">
                <label class="form-label small fw-700 text-uppercase text-muted">Start Date</label>
                <input type="date" class="form-control bg-light border-0 py-2" name="start_date" value="<?php echo $start_date; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-700 text-uppercase text-muted">End Date</label>
                <input type="date" class="form-control bg-light border-0 py-2" name="end_date" value="<?php echo $end_date; ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-700 text-uppercase text-muted">Type</label>
                <select class="form-select bg-light border-0 py-2" name="event_type">
                    <option value="">All Types</option>
                    <option value="class" <?php echo $event_type === 'class' ? 'selected' : ''; ?>>Class</option>
                    <option value="exam" <?php echo $event_type === 'exam' ? 'selected' : ''; ?>>Exam</option>
                    <option value="meeting" <?php echo $event_type === 'meeting' ? 'selected' : ''; ?>>Meeting</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-700 text-uppercase text-muted">Teacher</label>
                <select class="form-select bg-light border-0 py-2" name="teacher_id">
                    <option value="">All Teachers</option>
                    <?php foreach ($teachers as $t): ?>
                        <option value="<?php echo $t['id']; ?>" <?php echo $teacher_id == $t['id'] ? 'selected' : ''; ?>>
                            <?php echo escape($t['first_name'] . ' ' . $t['last_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-700 text-uppercase text-muted">Cohort</label>
                <select class="form-select bg-light border-0 py-2" name="class_id">
                    <option value="">All Classes</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo $class_id == $c['id'] ? 'selected' : ''; ?>>
                            <?php echo escape($c['class_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-info text-white fw-700 px-5 shadow-sm">
                    <i class="fas fa-sync-alt me-2"></i>Generate Real-time Report
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Report Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm list-card h-100 overflow-hidden">
            <div class="card-body p-0 d-flex">
                <div class="bg-primary p-4 d-flex align-items-center justify-content-center" style="width: 80px;">
                    <i class="fas fa-calendar-check fa-2x text-white opacity-50"></i>
                </div>
                <div class="p-4">
                    <div class="small fw-700 text-uppercase text-muted mb-1">Total Scheduled Events</div>
                    <div class="h2 mb-0 fw-800"><?php echo $stats['total_events']; ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm list-card h-100 overflow-hidden">
            <div class="card-body p-0 d-flex">
                <div class="bg-success p-4 d-flex align-items-center justify-content-center" style="width: 80px;">
                    <i class="fas fa-door-open fa-2x text-white opacity-50"></i>
                </div>
                <div class="p-4">
                    <div class="small fw-700 text-uppercase text-muted mb-1">Active Rooms Utilized</div>
                    <div class="h2 mb-0 fw-800"><?php echo $stats['room_count'] ?? 0; ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm list-card h-100 overflow-hidden">
            <div class="card-body p-0 d-flex">
                <div class="bg-warning p-4 d-flex align-items-center justify-content-center" style="width: 80px;">
                    <i class="fas fa-clock fa-2x text-white opacity-50"></i>
                </div>
                <div class="p-4">
                    <div class="small fw-700 text-uppercase text-muted mb-1">Cumulative Booked Hours</div>
                    <div class="h2 mb-0 fw-800"><?php echo round($stats['total_hours'], 1); ?> <small class="h6">HRS</small></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PDF Export Container (Hidden, only for PDF generation) -->
<div id="pdf-export-content" style="display: none;">
    <div style="padding: 20px; background: white;">
        <!-- PDF Header -->
        <div style="text-align: center; margin-bottom: 20px; border-bottom: 3px solid #4361ee; padding-bottom: 15px;">
            <h2 style="color: #1e293b; margin: 0; font-size: 24px; font-weight: 700;">Schedule Time Table Management System</h2>
            <h3 style="color: #64748b; margin: 5px 0 0 0; font-size: 18px; font-weight: 600;">System Intelligence & Reports</h3>
            <p style="color: #94a3b8; margin: 5px 0 0 0; font-size: 12px;">Report Period: <?php echo formatDate($start_date); ?> to <?php echo formatDate($end_date); ?></p>
        </div>
        
        <!-- PDF Statistics -->
        <div style="display: flex; justify-content: space-around; margin-bottom: 20px; gap: 10px;">
            <div style="text-align: center; padding: 15px; background: #f1f5f9; border-radius: 8px; flex: 1;">
                <div style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; margin-bottom: 5px;">Total Events</div>
                <div style="font-size: 28px; color: #1e293b; font-weight: 800;"><?php echo $stats['total_events']; ?></div>
            </div>
            <div style="text-align: center; padding: 15px; background: #f1f5f9; border-radius: 8px; flex: 1;">
                <div style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; margin-bottom: 5px;">Active Rooms</div>
                <div style="font-size: 28px; color: #1e293b; font-weight: 800;"><?php echo $stats['room_count'] ?? 0; ?></div>
            </div>
            <div style="text-align: center; padding: 15px; background: #f1f5f9; border-radius: 8px; flex: 1;">
                <div style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; margin-bottom: 5px;">Total Hours</div>
                <div style="font-size: 28px; color: #1e293b; font-weight: 800;"><?php echo round($stats['total_hours'], 1); ?></div>
            </div>
        </div>
        
        <!-- Event Type Breakdown -->
        <?php
        // Calculate event type breakdown
        $eventTypeBreakdown = [];
        foreach ($events as $event) {
            $type = ucfirst($event['event_type']);
            if (!isset($eventTypeBreakdown[$type])) {
                $eventTypeBreakdown[$type] = 0;
            }
            $eventTypeBreakdown[$type]++;
        }
        ?>
        <?php if (!empty($eventTypeBreakdown)): ?>
        <div style="margin-bottom: 20px; padding: 15px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 4px;">
            <div style="font-size: 12px; font-weight: 700; color: #92400e; margin-bottom: 10px; text-transform: uppercase;">Event Breakdown by Type</div>
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <?php foreach ($eventTypeBreakdown as $type => $count): ?>
                    <div style="flex: 1; min-width: 120px;">
                        <span style="font-size: 10px; color: #78350f; font-weight: 600;"><?php echo escape($type); ?>:</span>
                        <span style="font-size: 14px; color: #1e293b; font-weight: 800; margin-left: 5px;"><?php echo $count; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- PDF Table -->
        <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th style="padding: 10px 8px; text-align: left; border-bottom: 2px solid #cbd5e1; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 9px;">Date & Time</th>
                    <th style="padding: 10px 8px; text-align: left; border-bottom: 2px solid #cbd5e1; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 9px;">Title</th>
                    <th style="padding: 10px 8px; text-align: left; border-bottom: 2px solid #cbd5e1; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 9px;">Type</th>
                    <th style="padding: 10px 8px; text-align: left; border-bottom: 2px solid #cbd5e1; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 9px;">Subject/Room</th>
                    <th style="padding: 10px 8px; text-align: left; border-bottom: 2px solid #cbd5e1; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 9px;">Instructor/Class</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="5" style="padding: 30px; text-align: center; color: #94a3b8;">No records matching criteria.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($events as $e): ?>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 10px 8px; color: #1e293b;">
                                <div style="font-weight: 700; margin-bottom: 2px;"><?php echo formatDate($e['event_date']); ?></div>
                                <div style="font-size: 9px; color: #64748b;"><?php echo formatTime($e['start_time']); ?> - <?php echo formatTime($e['end_time']); ?></div>
                            </td>
                            <td style="padding: 10px 8px; color: #1e293b; font-weight: 600;"><?php echo escape($e['title']); ?></td>
                            <td style="padding: 10px 8px;">
                                <span style="background: #e0e7ff; color: #3730a3; padding: 4px 10px; border-radius: 12px; font-size: 8px; font-weight: 700; text-transform: uppercase;">
                                    <?php echo $e['event_type']; ?>
                                </span>
                            </td>
                            <td style="padding: 10px 8px; color: #1e293b;">
                                <div style="font-weight: 600; margin-bottom: 2px;"><?php echo escape($e['subject_name'] ?? 'N/A'); ?></div>
                                <div style="font-size: 9px; color: #64748b;"><?php echo escape($e['room_name'] ?? 'TBA'); ?></div>
                            </td>
                            <td style="padding: 10px 8px; color: #1e293b;">
                                <div style="font-weight: 600; margin-bottom: 2px; color: #4361ee;"><?php echo escape($e['teacher_first_name'] . ' ' . $e['teacher_last_name']); ?></div>
                                <div style="font-size: 9px; color: #64748b;"><?php echo escape($e['class_name'] ?? 'N/A'); ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- PDF Footer -->
        <div style="margin-top: 20px; padding-top: 15px; border-top: 2px solid #e2e8f0; text-align: center; color: #94a3b8; font-size: 9px;">
            Generated on <?php echo date('F j, Y \a\t g:i A'); ?> | Schedule Time Table Management System
        </div>
    </div>
</div>

<!-- Detailed Data -->
<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-header border-0 py-4 px-4 d-flex justify-content-between align-items-center text-white" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
        <div>
            <h5 class="mb-0 fw-700"><i class="fas fa-list-alt me-2 text-info"></i>Detailed Ledger</h5>
            <small class="opacity-75">Filtered results for current report scope</small>
        </div>
        <div class="no-print d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-light btn-sm fw-700 rounded-pill px-3 border-opacity-25">
                <i class="fas fa-print me-2"></i>Print Ledger
            </button>
            <button id="download-pdf" 
                    class="btn btn-light btn-sm fw-700 rounded-pill px-3 shadow-sm text-primary"
                    data-start="<?php echo $start_date; ?>" 
                    data-end="<?php echo $end_date; ?>">
                <i class="fas fa-file-pdf me-2"></i>Save as Document
            </button>
        </div>
    </div>
    <div class="card-body p-0 printable-area">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0 text-muted small fw-600 text-uppercase">Time & Date</th>
                        <th class="py-3 border-0 text-muted small fw-600 text-uppercase">Title</th>
                        <th class="py-3 border-0 text-muted small fw-600 text-uppercase">Type</th>
                        <th class="py-3 border-0 text-muted small fw-600 text-uppercase">Details</th>
                        <th class="py-3 border-0 text-muted small fw-600 text-uppercase">Participants</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (empty($events)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted italic">No records matching criteria.</td></tr>
                    <?php else: ?>
                        <?php foreach ($events as $e): ?>
                            <tr>
                                <td class="ps-4 align-middle">
                                    <div class="fw-700 text-dark"><?php echo formatDate($e['event_date']); ?></div>
                                    <small class="text-muted"><?php echo formatTime($e['start_time']); ?> — <?php echo formatTime($e['end_time']); ?></small>
                                </td>
                                <td class="align-middle fw-600"><?php echo escape($e['title']); ?></td>
                                <td class="align-middle">
                                    <span class="badge rounded-pill bg-light text-dark border px-3 py-1 fw-700 uppercase" style="font-size:0.65rem">
                                        <?php echo $e['event_type']; ?>
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <div class="small fw-600"><i class="fas fa-book me-2 text-muted opacity-50"></i><?php echo escape($e['subject_name'] ?? 'N/A'); ?></div>
                                    <div class="small"><i class="fas fa-map-marker-alt me-2 text-muted opacity-50"></i><?php echo escape($e['room_name'] ?? 'TBA'); ?></div>
                                </td>
                                <td class="align-middle">
                                    <div class="small fw-600 text-primary">Instructor: <?php echo escape($e['teacher_first_name'] . ' ' . $e['teacher_last_name']); ?></div>
                                    <div class="small text-muted">Cohort: <?php echo escape($e['class_name'] ?? 'N/A'); ?></div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const downloadBtn = document.getElementById('download-pdf');
        if (downloadBtn) {
            downloadBtn.addEventListener('click', function() {
                // Clone the hidden PDF export content into a visible temporary node
                const source = document.getElementById('pdf-export-content');
                if (!source) return;

                const temp = document.createElement('div');
                temp.id = 'pdf-export-temp';
                temp.style.background = '#ffffff';
                temp.style.padding = '20px';
                temp.style.position = 'relative';
                temp.style.width = '210mm';
                temp.style.minHeight = '297mm';
                temp.style.boxSizing = 'border-box';
                // Inject small inline stylesheet to ensure table/text are visible when cloned
                const resetStyles = `
                    <style>
                        #pdf-export-temp { background: #fff; color: #333 !important; }
                        #pdf-export-temp table { width: 100% !important; border-collapse: collapse !important; }
                        #pdf-export-temp th, #pdf-export-temp td { color: #333 !important; }
                        #pdf-export-temp .badge { background: #eee !important; color: #333 !important; }
                        #pdf-export-temp img { max-width: 100% !important; }
                        @media print { #pdf-export-temp { background: #fff; } }
                    </style>
                `;
                temp.innerHTML = resetStyles + source.innerHTML;
                document.body.appendChild(temp);

                const filename = `timetable_report_${this.dataset.start}_to_${this.dataset.end}.pdf`;
                const opt = {
                    margin: [10, 10, 10, 10],
                    filename: filename,
                    image: { type: 'jpeg', quality: 0.95 },
                    html2canvas: {
                        scale: 2,
                        letterRendering: true,
                        useCORS: true,
                        backgroundColor: '#ffffff',
                        logging: false
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: 'a4',
                        orientation: 'portrait',
                        compress: true
                    },
                    pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
                };

                // Show loading state
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating PDF...';
                this.disabled = true;

                // Allow the browser a short time to render the cloned content (fonts/images)
                setTimeout(() => {
                    html2pdf().set(opt).from(temp).save().then(() => {
                        // Restore button state and cleanup
                        this.innerHTML = originalText;
                        this.disabled = false;
                        if (temp && temp.parentNode) temp.parentNode.removeChild(temp);
                    }).catch((error) => {
                        console.error('PDF generation error:', error);
                        this.innerHTML = originalText;
                        this.disabled = false;
                        if (temp && temp.parentNode) temp.parentNode.removeChild(temp);
                        alert('Error generating PDF. Please try again.');
                    });
                }, 350);
            });
        }
    });
</script>

<style>
    .uppercase { text-transform: uppercase; }
    .fw-800 { font-weight: 800; }
    .fw-700 { font-weight: 700; }
    .fw-600 { font-weight: 600; }
    @media print {
        .no-print { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
        .bg-light { background-color: #fff !important; }
        .printable-area { background: white !important; color: #333 !important; }
        .printable-area td, .printable-area th { color: #333 !important; }
    }
</style>
