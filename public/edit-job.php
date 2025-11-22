<?php
require_once '../config.php';

// Check admin authentication
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: admin.php');
    exit;
}

$error = '';
$success = '';

// Get job ID
$jobId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($jobId <= 0) {
    header('Location: admin.php');
    exit;
}

// Fetch job details
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->execute([$jobId]);
$job = $stmt->fetch();

if (!$job) {
    header('Location: admin.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        // Get and sanitize form data
        $title = trim($_POST['title'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $job_type = trim($_POST['job_type'] ?? 'Full-time');
        $salary = trim($_POST['salary'] ?? '');
        $short_description = trim($_POST['short_description'] ?? '');
        $full_description = trim($_POST['full_description'] ?? '');
        $requirements = trim($_POST['requirements'] ?? '');
        $contact_email = trim($_POST['contact_email'] ?? '');
        $contact_phone = trim($_POST['contact_phone'] ?? '');
        $status = $_POST['status'] ?? 'active';

        // Validation
        if (empty($title) || empty($company) || empty($category) || empty($location) || 
            empty($short_description) || empty($full_description)) {
            $error = 'Please fill in all required fields.';
        } elseif (!empty($contact_email) && !filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email address.';
        } else {
            try {
                // Update job
                $stmt = $pdo->prepare("
                    UPDATE jobs 
                    SET title = ?, company = ?, category = ?, location = ?, job_type = ?, 
                        salary = ?, short_description = ?, full_description = ?, 
                        requirements = ?, contact_email = ?, contact_phone = ?, status = ?
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $title, $company, $category, $location, $job_type, $salary,
                    $short_description, $full_description, $requirements,
                    $contact_email, $contact_phone, $status, $jobId
                ]);

                $success = 'Job updated successfully!';
                
                // Refresh job data
                $stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
                $stmt->execute([$jobId]);
                $job = $stmt->fetch();
            } catch (PDOException $e) {
                $error = 'Failed to update job. Please try again.';
            }
        }
    }
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin.php">
                <i class="bi bi-arrow-left"></i> Back to Admin Panel
            </a>
            <a href="admin.php?logout=1" class="btn btn-danger btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="card-header bg-warning">
                        <h4 class="mb-0">
                            <i class="bi bi-pencil"></i> Edit Job
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i> <?= sanitize($error) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle"></i> <?= sanitize($success) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="edit-job.php?id=<?= $jobId ?>">
                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="title" class="form-label">Job Title *</label>
                                    <input type="text" name="title" id="title" class="form-control" required 
                                           value="<?= sanitize($job['title']) ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="company" class="form-label">Company Name *</label>
                                    <input type="text" name="company" id="company" class="form-control" required
                                           value="<?= sanitize($job['company']) ?>">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="category" class="form-label">Category *</label>
                                    <input type="text" name="category" id="category" class="form-control" required
                                           value="<?= sanitize($job['category']) ?>">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="location" class="form-label">Location *</label>
                                    <input type="text" name="location" id="location" class="form-control" required
                                           value="<?= sanitize($job['location']) ?>">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="job_type" class="form-label">Job Type *</label>
                                    <select name="job_type" id="job_type" class="form-select" required>
                                        <option value="Full-time" <?= $job['job_type'] === 'Full-time' ? 'selected' : '' ?>>Full-time</option>
                                        <option value="Part-time" <?= $job['job_type'] === 'Part-time' ? 'selected' : '' ?>>Part-time</option>
                                        <option value="Contract" <?= $job['job_type'] === 'Contract' ? 'selected' : '' ?>>Contract</option>
                                        <option value="Internship" <?= $job['job_type'] === 'Internship' ? 'selected' : '' ?>>Internship</option>
                                        <option value="Freelance" <?= $job['job_type'] === 'Freelance' ? 'selected' : '' ?>>Freelance</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="salary" class="form-label">Salary (Optional)</label>
                                    <input type="text" name="salary" id="salary" class="form-control"
                                           value="<?= sanitize($job['salary']) ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="active" <?= $job['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= $job['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="short_description" class="form-label">Short Description *</label>
                                    <textarea name="short_description" id="short_description" class="form-control" 
                                              rows="2" required><?= sanitize($job['short_description']) ?></textarea>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="full_description" class="form-label">Full Description *</label>
                                    <textarea name="full_description" id="full_description" class="form-control" 
                                              rows="5" required><?= sanitize($job['full_description']) ?></textarea>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="requirements" class="form-label">Requirements (Optional)</label>
                                    <textarea name="requirements" id="requirements" class="form-control" 
                                              rows="4"><?= sanitize($job['requirements']) ?></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="contact_email" class="form-label">Contact Email (Optional)</label>
                                    <input type="email" name="contact_email" id="contact_email" class="form-control"
                                           value="<?= sanitize($job['contact_email']) ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="contact_phone" class="form-label">Contact Phone (Optional)</label>
                                    <input type="text" name="contact_phone" id="contact_phone" class="form-control"
                                           value="<?= sanitize($job['contact_phone']) ?>">
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <i class="bi bi-check-circle"></i> Update Job
                                </button>
                                <a href="admin.php" class="btn btn-secondary btn-lg">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                                <a href="job.php?id=<?= $jobId ?>" class="btn btn-info btn-lg ms-auto" target="_blank">
                                    <i class="bi bi-eye"></i> View Job
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
