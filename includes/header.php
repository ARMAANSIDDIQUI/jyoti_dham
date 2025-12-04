<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../vendor/autoload.php';

$conn = DB::getInstance()->getConnection();

$satsang_link = 'satsang.php'; // Default satsang page
$satsang_status_text = 'Livestream';
try {
    // Check for a LIVE satsang
    $stmt = $conn->prepare("SELECT video_url FROM satsang
        WHERE NOW() BETWEEN start_time AND end_time
        ORDER BY start_time DESC LIMIT 1");
    $stmt->execute();
    $live_satsang = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($live_satsang && !empty($live_satsang['video_url'])) {
        $satsang_link = $live_satsang['video_url'];
        $satsang_status_text = 'Livestream (LIVE!)';
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jyotidham</title> <!-- Title will be dynamic or set by individual pages -->
    <link rel="icon" href="/images/Logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css">
    <?php
    // Add cache busting to style.css
    $style_css_path = __DIR__ . '/../css/style.css';
    if (file_exists($style_css_path)) {
        echo "<link rel='stylesheet' href='/css/style.css?v=" . filemtime($style_css_path) . "'>";
    }

    $page_name = basename($_SERVER['PHP_SELF'], ".php");
    $dynamic_css_path = __DIR__ . "/../css/{$page_name}.css";
    if (file_exists($dynamic_css_path)) {
        echo "<link rel='stylesheet' href='/css/{$page_name}.css?v=" . filemtime($dynamic_css_path) . "'>";
    }
    // Add index-podcast.css for index.php
    if ($page_name === 'index') {
        $podcast_css_path = __DIR__ . '/../css/index-podcast.css';
        if (file_exists($podcast_css_path)) {
            echo "<link rel='stylesheet' href='/css/index-podcast.css?v=" . filemtime($podcast_css_path) . "'>";
        }
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
                            <a class="nav-link" href="<?php echo htmlspecialchars($satsang_link); ?>"><?php echo htmlspecialchars($satsang_status_text); ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="donate.php">Donate</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="bhajan.php">Bhajans</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="calendar.php">Calendar</a>
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
<main class="main-content">
