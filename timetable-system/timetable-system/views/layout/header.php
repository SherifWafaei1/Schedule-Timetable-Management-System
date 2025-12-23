<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . APP_NAME : APP_NAME; ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS Frameworks -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo APP_URL; ?>/assets/css/dashboard.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php if (isset($extra_css)): ?>
        <?php foreach ($extra_css as $css): ?>
            <link href="<?php echo APP_URL; ?>/assets/css/<?php echo $css; ?>?v=<?php echo time(); ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7fe; }
        .fw-500 { font-weight: 500; }
        .fw-600 { font-weight: 600; }
    </style>
</head>
<body class="<?php echo $body_class ?? ''; ?>">
    <?php if (!isset($no_nav) || !$no_nav): ?>
        <?php include __DIR__ . '/../../includes/main_nav.php'; ?>
    <?php endif; ?>

    <main class="<?php echo isset($container_class) ? $container_class : 'container-fluid mt-4'; ?>">
        <?php if (isset($flash) && $flash): ?>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-<?php echo ($flash['type'] === 'error' || $flash['type'] === 'danger') ? 'danger' : 'success'; ?> alert-dismissible fade show">
                        <i class="fas <?php echo ($flash['type'] === 'error' || $flash['type'] === 'danger') ? 'fa-exclamation-circle' : 'fa-check-circle'; ?> me-2"></i>
                        <?php echo escape($flash['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
