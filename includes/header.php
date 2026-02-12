<?php
/**
 * Common Header
 * Included at the top of every page.
 */
require_once __DIR__ . '/functions.php';
startSession();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo escape(isset($pageTitle) ? $pageTitle : 'Car Rental System'); ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/index.php">
            <i class="bi bi-car-front-fill me-2"></i>CarRental
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNav" aria-controls="mainNav"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto">

                <!-- Always visible -->
                <li class="nav-item">
                    <a class="nav-link" href="/customer/available_cars.php">Available Cars</a>
                </li>

                <?php if (isLoggedIn()): ?>

                    <?php if (getUserType() === 'agency'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/agency/add_car.php">Add Car</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/agency/view_bookings.php">Bookings</a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <span class="nav-link text-light">
                            <i class="bi bi-person-circle me-1"></i><?php echo escape(getUserName()); ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/auth/logout.php">Logout</a>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a class="nav-link" href="/auth/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/auth/register_customer.php">Register</a>
                    </li>

                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<main class="container py-4">
