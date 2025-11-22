<?php
require_once '../config.php';

// Get Job ID
$jobId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($jobId <= 0) {
    header('Location: index.php');
    exit;
}

// Fetch Job Details
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ? AND status = 'active'");
$stmt->execute([$jobId]);
$job = $stmt->fetch();

if (!$job) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($job['title']) ?> - <?= sanitize($job['company']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-briefcase-fill"></i> Karachi Jobs
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#newsletter">Newsletter</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Job Details -->
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <!-- Job Header -->
                        <div class="mb-4">
                            <h1 class="h2 mb-2"><?= sanitize($job['title']) ?></h1>
                            <h4 class="text-muted mb-3">
                                <i class="bi bi-building"></i> <?= sanitize($job['company']) ?>
                            </h4>
                            
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-secondary fs-6">
                                    <i class="bi bi-tag"></i> <?= sanitize($job['category']) ?>
                                </span>
                                <span class="badge bg-info fs-6">
                                    <i class="bi bi-geo-alt"></i> <?= sanitize($job['location']) ?>
                                </span>
                                <span class="badge bg-success fs-6">
                                    <i class="bi bi-clock"></i> <?= sanitize($job['job_type']) ?>
                                </span>
                                <?php if ($job['salary']): ?>
                                    <span class="badge bg-warning text-dark fs-6">
                                        <i class="bi bi-cash"></i> <?= sanitize($job['salary']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <p class="text-muted">
                                <i class="bi bi-calendar3"></i> Posted on: <?= date('F d, Y', strtotime($job['created_at'])) ?>
                            </p>
                        </div>

                        <hr>

                        <!-- Job Description -->
                        <div class="mb-4">
                            <h4>Job Description</h4>
                            <p class="lead"><?= sanitize($job['short_description']) ?></p>
                            <div class="mt-3">
                                <?= nl2br(sanitize($job['full_description'])) ?>
                            </div>
                        </div>

                        <!-- Requirements -->
                        <?php if ($job['requirements']): ?>
                            <hr>
                            <div class="mb-4">
                                <h4>Requirements</h4>
                                <?= nl2br(sanitize($job['requirements'])) ?>
                            </div>
                        <?php endif; ?>

                        <hr>

                        <!-- Contact Information -->
                        <div class="alert alert-primary">
                            <h5 class="alert-heading">
                                <i class="bi bi-envelope-fill"></i> Contact Information
                            </h5>
                            <?php if ($job['contact_email']): ?>
                                <p class="mb-2">
                                    <strong>Email:</strong> 
                                    <a href="mailto:<?= sanitize($job['contact_email']) ?>">
                                        <?= sanitize($job['contact_email']) ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                            <?php if ($job['contact_phone']): ?>
                                <p class="mb-0">
                                    <strong>Phone:</strong> 
                                    <a href="tel:<?= sanitize($job['contact_phone']) ?>">
                                        <?= sanitize($job['contact_phone']) ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>

                        <div class="mt-4">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Jobs
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 mt-4 mt-lg-0">
                <!-- Newsletter Card -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-envelope-fill text-primary"></i> Job Alerts
                        </h5>
                        <p class="card-text">Subscribe to get daily job updates!</p>
                        
                        <div id="sidebar-newsletter-message"></div>
                        
                        <form id="sidebar-newsletter-form">
                            <div class="mb-3">
                                <input type="email" name="email" class="form-control" placeholder="Your email" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                Subscribe Now
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Quick Apply Card -->
                <div class="card shadow bg-light">
                    <div class="card-body">
                        <h5 class="card-title">Quick Apply</h5>
                        <p class="card-text small">Contact the employer directly:</p>
                        <?php if ($job['contact_email']): ?>
                            <a href="mailto:<?= sanitize($job['contact_email']) ?>?subject=Application for <?= urlencode($job['title']) ?>" 
                               class="btn btn-success w-100 mb-2">
                                <i class="bi bi-envelope"></i> Email
                            </a>
                        <?php endif; ?>
                        <?php if ($job['contact_phone']): ?>
                            <a href="tel:<?= sanitize($job['contact_phone']) ?>" class="btn btn-info w-100">
                                <i class="bi bi-telephone"></i> Call
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> Karachi Jobs. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
