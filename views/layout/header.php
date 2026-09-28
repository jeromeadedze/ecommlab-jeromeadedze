<?php
// Ensure core is included if not already (header might be included directly by some views)
require_once __DIR__ . '/../../core/core.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ecomm Lab</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header class="custom-header d-flex justify-content-between align-items-center">
        <!-- Logo -->
        <div>
            <a href="/index.php" class="brand-logo">Logo</a>
        </div>
        

        
        <!-- Navigation Actions -->
        <div class="d-flex align-items-center gap-3">
            <?php if (is_logged_in()): ?>
                <span class="text-muted fw-medium">Hi, <?php echo htmlspecialchars($_SESSION['customer_name'] ?? 'User'); ?>!</span>
                <a href="/logout.php" class="btn btn-outline-danger btn-sm border-0 fw-bold">Logout</a>
            <?php else: ?>
                <a href="/views/login.php" class="btn-light-purple">Login</a>
                <a href="/views/register.php" class="btn-primary-custom text-decoration-none">Register</a>
            <?php endif; ?>
        </div>
    </header>
    
    <main class="d-flex w-100">
