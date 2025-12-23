<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Timetable Export</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; margin: 30px; color: #333; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 3px solid #4361ee; padding-bottom: 20px; }
        .header h1 { margin: 0; color: #4361ee; font-size: 24px; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 5px 0; color: #555; font-weight: 600; }
        .export-info { margin-bottom: 25px; display: flex; justify-content: space-between; background: #f8f9fa; padding: 15px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; border-radius: 8px; overflow: hidden; }
        th { background-color: #4361ee; color: white; font-weight: 700; text-transform: uppercase; font-size: 10px; padding: 12px 10px; text-align: left; }
        td { border-bottom: 1px solid #eee; padding: 12px 10px; }
        tr:nth-child(even) { background-color: #fcfcfc; }
        tr:hover { background-color: #f1f4ff; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-weight: 700; font-size: 9px; text-transform: uppercase; background: #eee; color: #666; }
        .badge-lecture { background: #e3f2fd; color: #1976d2; }
        .badge-exam { background: #ffebee; color: #d32f2f; }
        .badge-lab { background: #e8f5e9; color: #388e3c; }
        .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 20px; }
        @media print {
            body { margin: 0; background: white !important; color: black !important; }
            .no-print { display: none; }
            .header { border-bottom-color: #000; }
            th { background-color: #eee !important; color: black !important; border: 1px solid #ddd !important; }
            td { border: 1px solid #eee !important; color: black !important; }
            .badge { border: 1px solid #ddd !important; background: white !important; color: black !important; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php echo APP_NAME; ?></h1>
        <p>Official Schedule Report</p>
    </div>
    
    <div class="export-info">
        <div>
            <strong>Report Period:</strong> <?php echo formatDate($start_date); ?> — <?php echo formatDate($end_date); ?>
        </div>
        <div style="text-align: right;">
            <strong>Generated:</strong> <?php echo date('M j, Y | g:i A'); ?><br>
            <strong>Total Records:</strong> <?php echo count($events); ?>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Time Range</th>
                <th>Event Title</th>
                <th>Type</th>
                <th>Subject</th>
                <th>Resource / Room</th>
                <th>Faculty / Instructor</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($events)): ?>
                <tr><td colspan="7" style="text-align:center; padding: 40px; color: #999;">No records found for the selected period.</td></tr>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td style="font-weight:700;"><?php echo formatDate($event['event_date']); ?></td>
                        <td><?php echo formatTime($event['start_time']); ?> - <?php echo formatTime($event['end_time']); ?></td>
                        <td style="font-weight:600;"><?php echo escape($event['title']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo strtolower($event['event_type']); ?>">
                                <?php echo ucfirst(escape($event['event_type'])); ?>
                            </span>
                        </td>
                        <td><?php echo escape($event['subject_name'] ?? '-'); ?></td>
                        <td><?php echo escape($event['room_name'] ?? 'TBA'); ?></td>
                        <td><?php echo escape($event['teacher_first_name'] . ' ' . $event['teacher_last_name']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div class="footer">
        <p>This is a computer-generated report from <?php echo APP_NAME; ?>. All rights reserved &copy; <?php echo date('Y'); ?>.</p>
        <p class="no-print" style="margin-top:10px;">
            <button onclick="window.print()" style="padding: 8px 16px; background: #4361ee; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 700;">Print to PDF</button>
        </p>
    </div>
</body>
</html>
