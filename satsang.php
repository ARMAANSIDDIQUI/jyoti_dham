<?php
require_once 'includes/header.php';
require_once 'config/db_connect.php';
require_once 'vendor/autoload.php';

$conn = DB::getInstance()->getConnection();

// Fetch upcoming satsangs
$stmt = $conn->prepare("SELECT * FROM satsang WHERE start_time > NOW() AND is_active = 1 ORDER BY start_time ASC");
$stmt->execute();
$satsangs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Upcoming Satsangs</h1>

    <?php if (empty($satsangs)): ?>
        <p class="text-center">There is no satsang currently scheduled.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($satsangs as $satsang): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($satsang['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($satsang['description']); ?></p>
                            <p class="card-text">
                                <small class="text-muted">
                                    Start: <?php echo date('M j, Y g:i A', strtotime($satsang['start_time'])); ?><br>
                                    End: <?php echo date('M j, Y g:i A', strtotime($satsang['end_time'])); ?><br>
                                    Time Zone: <?php echo htmlspecialchars($satsang['time_zone']); ?>
                                </small>
                            </p>
                            <?php if (!empty($satsang['video_url'])): ?>
                                <a href="<?php echo htmlspecialchars($satsang['video_url']); ?>" class="btn btn-primary" target="_blank">Watch Live</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
require_once 'includes/footer.php';
?>
