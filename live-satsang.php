<?php
require_once 'config/db_connect.php';
require_once 'vendor/autoload.php';

$conn = DB::getInstance()->getConnection();

// Fetch satsang settings
$stmt = $conn->query("SELECT * FROM satsang WHERE is_active = 1 LIMIT 1");
$satsang = $stmt->fetch(PDO::FETCH_ASSOC);

if ($satsang) {
    // Get current date and time in Canada timezone
    $canada_tz = new DateTimeZone($satsang['time_zone']);
    $now = new DateTime('now', $canada_tz);
    $current_date = $now->format('Y-m-d');
    $current_time = $now->format('H:i:s');

    $satsang_date = $satsang['satsang_date'];
    $start_time = $satsang['start_time'];
    $end_time = $satsang['end_time'];

    // Check if current date matches satsang date and time is within satsang duration
    if ($current_date == $satsang_date && $current_time >= $start_time && $current_time <= $end_time) {
        // Redirect to YouTube live
        header("Location: " . $satsang['yt_link']);
        exit();
    }
}

// If not live or no settings, show the page
require_once 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <?php if ($satsang && $satsang['is_active']): ?>
                        <p>Satsang is scheduled on <?= htmlspecialchars($satsang['satsang_date']) ?> from <?= htmlspecialchars($satsang['start_time']) ?> to <?= htmlspecialchars($satsang['end_time']) ?> (<?= htmlspecialchars($satsang['time_zone']) ?>).</p>
                        <p>Please check back during the scheduled time.</p>
                        <img src="images/Live-Satsang-Thumbnail-2.png" alt="Live Satsang Thumbnail" class="img-fluid mt-3">
                    <?php else: ?>
                        <p>Live Satsang is currently not active. Please check back later.</p>
                        <img src="images/Live-Satsang-Thumbnail-2.png" alt="Live Satsang Thumbnail" class="img-fluid mt-3">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
