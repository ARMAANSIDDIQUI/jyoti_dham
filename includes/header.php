<?php
session_start();
require_once __DIR__ . '/../config/db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jyotidham</title> <!-- Title will be dynamic or set by individual pages -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>
    <link rel="stylesheet" href="./css/style.css">
    <?php
    $page_name = basename($_SERVER['PHP_SELF'], ".php");
    $css_file = "./css/{$page_name}.css";
    if (file_exists($css_file)) {
        echo "<link rel='stylesheet' href='{$css_file}'>";
    }
    ?>
</head>
<body>
    <!-- Header Section -->
    <header class="header-section">
            <nav class="navbar navbar-expand-lg navbar-light bg-light nav">
                <a class="navbar-brand" href="index.php">
                    <img src="./images/logo-dark-bold.png" alt="Jyotidham Logo" class="header-logo">
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" target="_blank" href="https://www.youtube.com/live/QCCh6J9TWDw?si=6vgJNra2bprx9AxJ">Live Satsang</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="donate.php">Donate</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="calender.php">Calendar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact.php">Contact</a>
                        </li>
                        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="profile.php">Profile</a>
                            </li>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="admin/dashboard.php">Dashboard</a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a class="nav-link" href="logout.php">Logout</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="login.php">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="register.php">Register</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
        </header>
    <!-- End Header Section -->
