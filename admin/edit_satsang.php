<?php
// Admin check
require_once __DIR__ . '/auth_check.php';

// Include database connection
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../vendor/autoload.php';
$conn = DB::getInstance()->getConnection();

// Get satsang ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid satsang ID.");
}

$satsang_id = intval($_GET['id']);

// Fetch satsang data
$sql = "SELECT * FROM satsang WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$satsang_id]);
$satsang = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$satsang) {
    die("Satsang not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Satsang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    <div class="container mt-5">
        <h2>Edit Satsang</h2>
        <form action="satsang_action.php" method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?php echo $satsang['id']; ?>">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($satsang['title']); ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="start_datetime">Start Date & Time</label>
                    <input type="datetime-local" class="form-control" id="start_datetime" name="start_datetime" value="<?php echo date('Y-m-d\TH:i:s', strtotime($satsang['start_time'])); ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="end_datetime">End Date & Time</label>
                    <input type="datetime-local" class="form-control" id="end_datetime" name="end_datetime" value="<?php echo date('Y-m-d\TH:i:s', strtotime($satsang['end_time'])); ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="video_url">Video URL</label>
                    <input type="url" class="form-control" id="video_url" name="video_url" value="<?php echo htmlspecialchars($satsang['video_url']); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($satsang['description']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Satsang</button>
            <a href="manage_satsangs.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <?php include 'admin_footer.php'; ?>
</body>
</html>
