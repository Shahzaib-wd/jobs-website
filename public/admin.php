<?php
require_once '../config.php';

$error = '';
$success = '';

// Handle Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $password = $_POST['password'] ?? '';
    
    if ($password === ADMIN_PASSWORD) {
        $_SESSION['admin'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Invalid password!';
    }
}

// Handle Logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin']);
    header('Location: admin.php');
    exit;
}

// Check if admin is logged in
$isLoggedIn = isset($_SESSION['admin']) && $_SESSION['admin'] === true;

// If logged in, fetch data
if ($isLoggedIn) {
    // Fetch all jobs
    $jobsStmt = $pdo->query("SELECT * FROM jobs ORDER BY created_at DESC");
    $jobs = $jobsStmt->fetchAll();
    
    // Fetch subscribers
    $subscribersStmt = $pdo->query("SELECT * FROM newsletter_subscribers WHERE status = 'active' ORDER BY subscribed_at DESC");
    $subscribers = $subscribersStmt->fetchAll();
    
    // Count stats
    $totalJobs = count($jobs);
    $activeJobs = count(array_filter($jobs, fn($j) => $j['status'] === 'active'));
    $totalSubscribers = count($subscribers);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Karachi Jobs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php if (!$isLoggedIn): ?>
        <!-- Login Page -->
        <div class="container">
            <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
                <div class="col-md-5">
                    <div class="card shadow">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <i class="bi bi-shield-lock-fill text-primary" style="font-size: 3rem;"></i>
                                <h2 class="mt-3">Admin Login</h2>
                                <p class="text-muted">Enter password to access admin panel</p>
                            </div>

                            <?php if ($error): ?>
                                <div class="alert alert-danger">
                                    <i class="bi bi-exclamation-triangle"></i> <?= $error ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="admin.php">
                                <div class="mb-4">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" id="password" class="form-control form-control-lg" required autofocus>
                                </div>
                                <button type="submit" name="login" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-box-arrow-in-right"></i> Login
                                </button>
                            </form>

                            <div class="text-center mt-4">
                                <a href="index.php" class="text-muted">
                                    <i class="bi bi-arrow-left"></i> Back to Homepage
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Admin Dashboard -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="admin.php">
                    <i class="bi bi-speedometer2"></i> Admin Panel
                </a>
                <div class="ms-auto">
                    <a href="index.php" class="btn btn-outline-light me-2" target="_blank">
                        <i class="bi bi-globe"></i> View Site
                    </a>
                    <a href="admin.php?logout=1" class="btn btn-danger">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>
        </nav>

        <div class="container-fluid mt-4">
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Total Jobs</h6>
                                    <h2 class="mb-0"><?= $totalJobs ?></h2>
                                </div>
                                <i class="bi bi-briefcase-fill" style="font-size: 3rem; opacity: 0.5;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Active Jobs</h6>
                                    <h2 class="mb-0"><?= $activeJobs ?></h2>
                                </div>
                                <i class="bi bi-check-circle-fill" style="font-size: 3rem; opacity: 0.5;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Subscribers</h6>
                                    <h2 class="mb-0"><?= $totalSubscribers ?></h2>
                                </div>
                                <i class="bi bi-people-fill" style="font-size: 3rem; opacity: 0.5;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jobs Management -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-briefcase"></i> Job Management
                            </h5>
                            <a href="add-job.php" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Add New Job
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Company</th>
                                            <th>Category</th>
                                            <th>Location</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($jobs as $job): ?>
                                            <tr>
                                                <td><?= $job['id'] ?></td>
                                                <td><?= sanitize($job['title']) ?></td>
                                                <td><?= sanitize($job['company']) ?></td>
                                                <td><span class="badge bg-secondary"><?= sanitize($job['category']) ?></span></td>
                                                <td><?= sanitize($job['location']) ?></td>
                                                <td>
                                                    <?php if ($job['status'] === 'active'): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= date('M d, Y', strtotime($job['created_at'])) ?></td>
                                                <td>
                                                    <a href="job.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-info" target="_blank" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="edit-job.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="delete-job.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Are you sure you want to delete this job?')" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Newsletter Subscribers -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">
                                <i class="bi bi-envelope"></i> Newsletter Subscribers
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Email</th>
                                            <th>Subscribed Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($subscribers as $subscriber): ?>
                                            <tr>
                                                <td><?= $subscriber['id'] ?></td>
                                                <td><?= sanitize($subscriber['email']) ?></td>
                                                <td><?= date('M d, Y H:i', strtotime($subscriber['subscribed_at'])) ?></td>
                                                <td><span class="badge bg-success">Active</span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
