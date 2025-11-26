<?php
// Admin check
require_once __DIR__ . '/auth_check.php';

// Include database connection
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../vendor/autoload.php';
$conn = DB::getInstance()->getConnection();

// Handle form submission for adding new satsang
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $start_datetime = $_POST['start_datetime'];
    $end_datetime = $_POST['end_datetime'];
    $video_url = $_POST['video_url'];

    $sql = "INSERT INTO satsang (title, description, start_time, end_time, video_url) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$title, $description, $start_datetime, $end_datetime, $video_url]);
    header("Location: manage_satsangs.php");
    exit();
}

// Fetch all satsangs
$sql = "SELECT * FROM satsang ORDER BY start_time DESC";
$stmt = $conn->query($sql);
$satsangs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Satsangs</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    <div class="container mt-5">
        <h2>Manage Satsangs</h2>

        <!-- Add New Satsang Form -->
        <form method="post" class="mb-4">
            <input type="hidden" name="action" value="add">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="start_datetime">Start Date & Time</label>
                    <input type="datetime-local" class="form-control" id="start_datetime" name="start_datetime" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="end_datetime">End Date & Time</label>
                    <input type="datetime-local" class="form-control" id="end_datetime" name="end_datetime" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="video_url">Video URL</label>
                    <input type="url" class="form-control" id="video_url" name="video_url" required>
                </div>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Satsang</button>
        </form>

        <!-- Satsangs List -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Video URL</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($satsangs as $satsang): ?>
                <tr>
                    <td><?php echo htmlspecialchars($satsang['title']); ?></td>
                    <td><?php echo htmlspecialchars($satsang['start_time']); ?></td>
                    <td><?php echo htmlspecialchars($satsang['end_time']); ?></td>
                    <td><a href="<?php echo htmlspecialchars($satsang['video_url']); ?>" target="_blank">View</a></td>
                    <td>
                        <a href="edit_satsang.php?id=<?php echo $satsang['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="satsang_action.php?action=delete&id=<?php echo $satsang['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php include 'admin_footer.php'; ?>
</body>
</html>
