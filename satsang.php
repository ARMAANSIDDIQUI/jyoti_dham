<?php
require_once 'includes/header.php';
require_once 'config/db_connect.php';
require_once 'vendor/autoload.php';

$conn = DB::getInstance()->getConnection();

// Fetch live satsang
$stmt = $conn->prepare("SELECT * FROM satsang WHERE is_active = 1 AND start_time <= NOW() AND end_time > NOW() ORDER BY start_time DESC LIMIT 1");
$stmt->execute();
$live_satsang = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch upcoming satsangs
$stmt = $conn->prepare("SELECT * FROM satsang WHERE start_time > NOW() AND is_active = 1 ORDER BY start_time ASC");
$stmt->execute();
$satsangs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">

    <?php if ($live_satsang): ?>
        <?php if (!empty($live_satsang['video_url'])): ?>
            <h1 class="text-center mb-4"><a href="<?php echo htmlspecialchars($live_satsang['video_url']); ?>" target="_blank" style="color: inherit; text-decoration: none;">Live Satsang</a></h1>
        <?php else: ?>
            <h1 class="text-center mb-4">Live Satsang</h1>
        <?php endif; ?>
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-10 offset-lg-1 mb-4">
                <div class="card">
                        <div class="card-body">
                            <?php if (!empty($live_satsang['video_url'])): ?>
                                <h5 class="card-title"><a href="<?php echo htmlspecialchars($live_satsang['video_url']); ?>" target="_blank" style="color: inherit; text-decoration: none;"><?php echo htmlspecialchars($live_satsang['title']); ?></a></h5>
                            <?php else: ?>
                                <h5 class="card-title"><?php echo htmlspecialchars($live_satsang['title']); ?></h5>
                            <?php endif; ?>
                            <p class="card-text"><?php echo htmlspecialchars($live_satsang['description']); ?></p>
                        <p class="card-text">
                            <small class="text-muted">
                                Start: <?php echo date('M j, Y g:i A', strtotime($live_satsang['start_time'])); ?><br>
                                End: <?php echo date('M j, Y g:i A', strtotime($live_satsang['end_time'])); ?><br>
                                Time Zone: <?php echo htmlspecialchars($live_satsang['time_zone']); ?>
                            </small>
                        </p>
                        <?php if (!empty($live_satsang['video_url'])): ?>
                            <a href="<?php echo htmlspecialchars($live_satsang['video_url']); ?>" class="btn btn-primary" target="_blank">Watch Live</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (empty($satsangs) && empty($live_satsang)): ?>
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-10 offset-lg-1 mb-4">
                <div class="card">
                    <img src="images/Live-Satsang-Thumbnail-2.png" class="card-img-top" alt="No Satsang Thumbnail">
                    <div class="card-body">
                        <h5 class="card-title">No Satsang Currently Scheduled</h5>
                        <p class="card-text">Check back later for upcoming sessions.</p>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row justify-content-center">
            <?php foreach ($satsangs as $satsang): ?>
                <div class="col-md-12 col-lg-10 offset-lg-1 mb-4">
<div class="card">
                        <img src="images/Live-Satsang-Thumbnail-2.png" class="card-img-top" alt="Satsang Thumbnail">
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
