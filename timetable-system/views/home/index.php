<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Frameworks -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo APP_URL; ?>/assets/css/index.css" rel="stylesheet">
    <style>
        :root { --primary: #4361ee; --secondary: #3f37c9; --accent: #4cc9f0; --glass: rgba(255, 255, 255, 0.1); }
        body { font-family: 'Outfit', sans-serif; background: #0b0d17; color: #fff; }
        .hero-gradient { background: radial-gradient(circle at top right, rgba(67, 97, 238, 0.15), transparent), radial-gradient(circle at bottom left, rgba(76, 201, 240, 0.1), transparent); }
        .glass-nav { background: rgba(11, 13, 23, 0.8); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255,255,255,0.05); }
        .hero-title { font-size: 4rem; font-weight: 800; line-height: 1.1; letter-spacing: -0.02em; }
        .text-gradient { background: linear-gradient(45deg, #fff, #94a3b8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .btn-premium { background: #fff; color: #000; border-radius: 100px; padding: 14px 32px; font-weight: 700; transition: all 0.3s; border: none; }
        .btn-premium:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(255,255,255,0.1); }
        .feature-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 24px; padding: 40px; transition: all 0.3s; }
        .feature-card:hover { background: rgba(255,255,255,0.05); transform: translateY(-10px); }
        .feature-icon { width: 64px; height: 64px; background: rgba(67, 97, 238, 0.1); color: #4361ee; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 24px; }
        /* Override specific texts to black as requested */
        .hero-badge { color: #000 !important; background: #fff !important; }
        .hero-title { color: #000 !important; }
        .hero-title .text-gradient { background: none !important; -webkit-background-clip: unset !important; -webkit-text-fill-color: #000 !important; color: #000 !important; }
        .hero-lead { color: #000 !important; }
        .feature-title { color: #000 !important; }
        .feature-desc { color: #000 !important; }
    </style>
</head>
<body class="landing hero-gradient">
    <!-- Nav -->
    <nav class="navbar navbar-expand-lg navbar-dark glass-nav sticky-top py-3">
        <div class="container">
            <a class="navbar-brand fw-800 fs-4" href="#">
                <span class="text-primary"><i class="fas fa-bolt me-2"></i></span><?php echo APP_NAME; ?>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item mx-2"><a class="nav-link fw-600" href="#features">Features</a></li>
                    <li class="nav-item mx-2"><a class="nav-link fw-600" href="#about">Architecture</a></li>
                    <li class="nav-item ms-3">
                        <a href="login.php" class="btn btn-primary rounded-pill px-4 fw-700 shadow-sm">
                            <i class="fas fa-sign-in-alt me-2"></i>Dashboard Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <header class="py-5 mt-5">
        <div class="container">
            <div class="row align-items-center min-vh-75">
                <div class="col-lg-7 text-center text-lg-start">
                    <div class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 mb-4 fw-700 hero-badge">
                        <i class="fas fa-sparkles me-2"></i>Refactored with MVC Architecture
                    </div>
                    <h1 class="hero-title mb-4">Master Your <span class="text-gradient">Institutional</span> Time</h1>
                    <p class="lead text-white-50 mb-5 fs-4 hero-lead" style="max-width: 600px;">
                        The ultimate timetable management solution designed with Clean Code principles and a premium user experience.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start">
                        <a href="login.php" class="btn-premium fs-5">Get Started Free <i class="fas fa-arrow-right ms-2"></i></a>
                        <a href="#features" class="btn btn-outline-light rounded-pill px-4 py-3 fw-700 border-opacity-25">Explore Features</a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="position-relative">
                        <div class="bg-primary rounded-circle blur-3xl opacity-20 position-absolute" style="width:400px; height:400px; filter: blur(100px); top:-50px; right:-50px;"></div>
                        <div class="feature-card border-primary border-opacity-25 p-0 overflow-hidden shadow-2xl">
                             <img src="https://images.unsplash.com/photo-1506784983877-45594efa4cbe?auto=format&fit=crop&q=80&w=800" class="img-fluid opacity-75" alt="Schedule Preview">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Features -->
    <section id="features" class="py-5 mt-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-800 mb-3">Engineered for Excellence</h2>
                <p class="text-white-50">Streamlined tools for administrators, teachers, and students.</p>
            </div>
            <div class="row g-4">
                <?php
                $feats = [
                    ['icon' => 'fa-shield-halved', 'title' => 'Secure Auth', 'desc' => 'MVC-driven security with robust session handling and CSRF protection.'],
                    ['icon' => 'fa-calendar-days', 'title' => 'Dynamic Views', 'desc' => 'Seamless month, week, and day calendar views with real-time updates.'],
                    ['icon' => 'fa-brain', 'title' => 'Conflict Detection', 'desc' => 'Intelligent room and resource allocation to prevent scheduling overlaps.'],
                    ['icon' => 'fa-chart-pie', 'title' => 'Admin Insights', 'desc' => 'Rich dashboard with system-wide analytics and resource tracking.'],
                    ['icon' => 'fa-file-export', 'title' => 'Pro Exports', 'desc' => 'Generate production-ready PDF and CSV reports for offline usage.'],
                    ['icon' => 'fa-mobile', 'title' => 'Fluid UX', 'desc' => 'A fully responsive, mobile-first design that feels premium on any device.'],
                ];
                foreach ($feats as $f): ?>
                <div class="col-md-4">
                    <div class="feature-card h-100">
                        <div class="feature-icon"><i class="fas <?php echo $f['icon']; ?>"></i></div>
                        <h4 class="fw-700 mb-3 feature-title"><?php echo $f['title']; ?></h4>
                        <p class="text-white-50 mb-0 feature-desc"><?php echo $f['desc']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-5 mt-5 border-top border-white border-opacity-10">
        <div class="container text-center">
            <p class="text-white-50">© <?php echo date('Y'); ?> <?php echo APP_NAME; ?> Management System. Refactored for Clean Code.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
