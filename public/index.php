<?php
require_once '../config.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Search and Filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';

// Build Query
$query = "SELECT * FROM jobs WHERE status = 'active'";
$params = [];

if ($search) {
    $query .= " AND (title LIKE ? OR company LIKE ? OR short_description LIKE ?)";
    $searchTerm = "%{$search}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
}

if ($location) {
    $query .= " AND location LIKE ?";
    $params[] = "%{$location}%";
}

$query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$jobs = $stmt->fetchAll();

// Count Total Jobs
$countQuery = "SELECT COUNT(*) FROM jobs WHERE status = 'active'";
$countParams = [];
if ($search) {
    $countQuery .= " AND (title LIKE ? OR company LIKE ? OR short_description LIKE ?)";
    $countParams = [$searchTerm, $searchTerm, $searchTerm];
}
if ($category) {
    $countQuery .= " AND category = ?";
    $countParams[] = $category;
}
if ($location) {
    $countQuery .= " AND location LIKE ?";
    $countParams[] = "%{$location}%";
}

$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($countParams);
$totalJobs = $countStmt->fetchColumn();
$totalPages = ceil($totalJobs / $perPage);

// Get Categories for Filter
$categoriesStmt = $pdo->query("SELECT DISTINCT category FROM jobs WHERE status = 'active' ORDER BY category");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karachi Jobs - Latest Job Postings</title>
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
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#newsletter">Newsletter</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="bg-light py-5">
        <div class="container">
            <h1 class="text-center mb-4">Find Your Dream Job in Karachi</h1>
            
            <!-- Search Form -->
            <form method="GET" action="index.php" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Job title or company..." value="<?= sanitize($search) ?>">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= sanitize($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                                <?= sanitize($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" name="location" class="form-control" placeholder="Location" value="<?= sanitize($location) ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Job Listings -->
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Latest Job Postings (<?= $totalJobs ?> jobs found)</h2>
                
                <?php if (empty($jobs)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No jobs found. Try adjusting your search criteria.
                    </div>
                <?php else: ?>
                    <?php foreach ($jobs as $job): ?>
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-9">
                                        <h5 class="card-title">
                                            <a href="job.php?id=<?= $job['id'] ?>" class="text-decoration-none">
                                                <?= sanitize($job['title']) ?>
                                            </a>
                                        </h5>
                                        <h6 class="text-muted">
                                            <i class="bi bi-building"></i> <?= sanitize($job['company']) ?>
                                        </h6>
                                        <p class="card-text"><?= sanitize($job['short_description']) ?></p>
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-tag"></i> <?= sanitize($job['category']) ?>
                                            </span>
                                            <span class="badge bg-info">
                                                <i class="bi bi-geo-alt"></i> <?= sanitize($job['location']) ?>
                                            </span>
                                            <span class="badge bg-success">
                                                <i class="bi bi-clock"></i> <?= sanitize($job['job_type']) ?>
                                            </span>
                                            <?php if ($job['salary']): ?>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-cash"></i> <?= sanitize($job['salary']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                        <small class="text-muted d-block mb-2">
                                            <?= date('M d, Y', strtotime($job['created_at'])) ?>
                                        </small>
                                        <a href="job.php?id=<?= $job['id'] ?>" class="btn btn-primary">
                                            View Details <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Job listings pagination">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>&location=<?= urlencode($location) ?>">Previous</a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>&location=<?= urlencode($location) ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>&location=<?= urlencode($location) ?>">Next</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Newsletter Section -->
    <div class="bg-primary text-white py-5" id="newsletter">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <h3 class="mb-3">Subscribe to Our Newsletter</h3>
                    <p class="mb-4">Get daily job updates delivered to your inbox!</p>
                    
                    <div id="newsletter-message"></div>
                    
                    <form id="newsletter-form" class="row g-2 justify-content-center">
                        <div class="col-md-6">
                            <input type="email" name="email" id="newsletter-email" class="form-control" placeholder="Enter your email" required>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-light w-100">
                                <i class="bi bi-envelope"></i> Subscribe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> Karachi Jobs. All rights reserved.</p>
            <p class="mb-0 mt-2">Daily job postings for Karachi professionals</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
