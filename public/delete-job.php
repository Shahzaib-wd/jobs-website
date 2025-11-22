<?php
require_once '../config.php';

// Check admin authentication
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: admin.php');
    exit;
}

// Get job ID
$jobId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($jobId <= 0) {
    header('Location: admin.php');
    exit;
}

try {
    // Delete job
    $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
    $stmt->execute([$jobId]);
    
    // Redirect back to admin panel with success message
    $_SESSION['delete_success'] = true;
    header('Location: admin.php');
    exit;
} catch (PDOException $e) {
    // Redirect back with error
    $_SESSION['delete_error'] = true;
    header('Location: admin.php');
    exit;
}
?>