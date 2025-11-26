<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
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
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" target="_blank" href="https://www.youtube.com/live/QCCh6J9TWDw?si=6vgJNra2bprx9AxJ">Live Satsang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="donate.html">Donate</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="calender.php">Calendar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.html">Contact</a>
                    </li>
                    <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                        <li class="nav-item active">
                            <a class="nav-link" href="user_dashboard.php">Dashboard</a>
                        </li>
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

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?>!</h2>
                    </div>
                    <div class="card-body">
                        <p>This is your personal dashboard. You can manage your profile and settings here.</p>
                        <p>User ID: <?php echo htmlspecialchars($_SESSION["id"]); ?></p>
                        <p>Email: <?php echo htmlspecialchars($_SESSION["email"]); ?></p>
                        <a href="logout.php" class="btn btn-danger">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer-section">
        <div class="container">
            <div class="row">
                <!-- Left Side: Logos -->
                <div class="col-lg-4 col-md-12 logos">
                    <div class="logo">
                        <img src="./images/logo-jd-light.png" alt="Jyotidham Logo" />
                    </div>
                    <div class="logo">
                        <img src="./images/logo-round-white.png" alt="logo-round-white" />
                    </div>
                </div>

                <!-- Right Side: Content and Links -->
                <div class="col-lg-8 col-md-12 content">
                    <div class="row">
                        <!-- Text Section -->
                        <div class="col-12 text-section">
                            <p>After deep prayer and meditation, a devotee is in touch with his divine
                                consciousness; there is no greater power than that inward protection.</p>
                        </div>

                        <!-- Two Columns -->
                        <div class="col-lg-6 col-md-12 links">
                            <h5>Find Us Here</h5>
                            <p>Shri Param Hans Advait Mat Ontario</p>
                            <p class="address">
                                <img class="map-pin" src="./images/location.png"
                                    alt="Map Pin" />
                                260 Ingleton Blvd, Scarborough,<br>
                                ON M1V 3R1, Canada
                            </p>
                        </div>
                        <div class="col-lg-6 col-md-12 quick-links">
                            <h5>Quick Links</h5>
                            <p><a href="donate.html">Donate</a></p>
                            <p><a href="terms.html">Refund &amp; Privacy Policy</a>
                            </p>
                            <p><a href="./login.php">Admin Login</a>
                            </p>
                            <p>We accept</p>
                            <img src="./images/payment-cards-updated.png" alt="Payment Cards">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
