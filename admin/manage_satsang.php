<?php
require_once __DIR__ . '/admin_header.php';
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../vendor/autoload.php';
$conn = DB::getInstance()->getConnection();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $time_zone = $_POST['time_zone'];
    $video_url = $_POST['video_url'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Check if satsang entry exists
    $stmt = $conn->query("SELECT id FROM satsang LIMIT 1");
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Update existing
        $stmt = $conn->prepare("UPDATE satsang SET title = ?, description = ?, start_time = ?, end_time = ?, time_zone = ?, video_url = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$title, $description, $start_time, $end_time, $time_zone, $video_url, $is_active, $existing['id']]);
    } else {
        // Insert new
        $stmt = $conn->prepare("INSERT INTO satsang (title, description, start_time, end_time, time_zone, video_url, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $start_time, $end_time, $time_zone, $video_url, $is_active]);
    }

    echo "<div class='alert alert-success'>Satsang settings updated successfully!</div>";
}

// Fetch current satsang settings
$stmt = $conn->query("SELECT * FROM satsang LIMIT 1");
$satsang = $stmt->fetch(PDO::FETCH_ASSOC);

// Default values if no record exists
if (!$satsang) {
    $satsang = [
        'title' => 'Weekly Satsang',
        'description' => 'Join us for our weekly satsang session.',
        'start_time' => date('Y-m-d\TH:i:s'),
        'end_time' => date('Y-m-d\TH:i:s', strtotime('+1 hour')),
        'time_zone' => 'EST',
        'video_url' => '',
        'is_active' => 1
    ];
} else {
    // Convert DATETIME to datetime-local format for input
    $satsang['start_time'] = date('Y-m-d\TH:i:s', strtotime($satsang['start_time']));
    $satsang['end_time'] = date('Y-m-d\TH:i:s', strtotime($satsang['end_time']));
}
?>
<div class="container-fluid" style="padding-top: 30px; margin-top: 20px;">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card">
                <div class="card-header text-center" style="background: linear-gradient(135deg, #b3e5fc 0%, #e1bee7 100%); color: #2e2e2e;">
                    <h1 class="mb-0"><i class="fas fa-video"></i> Manage Satsang</h1>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="form-group">
                            <label for="title"><i class="fas fa-heading"></i> Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($satsang['title']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="description"><i class="fas fa-align-left"></i> Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required><?= htmlspecialchars($satsang['description']) ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_time"><i class="fas fa-clock"></i> Start Date & Time (Canada Time)</label>
                                    <input type="datetime-local" class="form-control" id="start_time" name="start_time" value="<?= htmlspecialchars($satsang['start_time']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_time"><i class="fas fa-clock"></i> End Date & Time (Canada Time)</label>
                                    <input type="datetime-local" class="form-control" id="end_time" name="end_time" value="<?= htmlspecialchars($satsang['end_time']) ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="time_zone"><i class="fas fa-globe"></i> Time Zone</label>
                            <select class="form-control" id="time_zone" name="time_zone" required>
                                <option value="EST" <?= $satsang['time_zone'] == 'EST' ? 'selected' : '' ?>>EST (Eastern Standard Time)</option>
                                <option value="EDT" <?= $satsang['time_zone'] == 'EDT' ? 'selected' : '' ?>>EDT (Eastern Daylight Time)</option>
                                <option value="PST" <?= $satsang['time_zone'] == 'PST' ? 'selected' : '' ?>>PST (Pacific Standard Time)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="video_url"><i class="fab fa-youtube"></i> Video URL</label>
                            <input type="url" class="form-control" id="video_url" name="video_url" value="<?= htmlspecialchars($satsang['video_url']) ?>" placeholder="https://youtube.com/live/..." required>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" <?= $satsang['is_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active"><i class="fas fa-toggle-on"></i> Active (Enable live satsang)</label>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-save"></i> Update Settings</button>
                            <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require_once __DIR__ . '/admin_footer.php';
?>
