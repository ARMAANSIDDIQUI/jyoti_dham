<?php
// Admin check
require_once __DIR__ . '/auth_check.php';

// Include database connection
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/admin_header.php';

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
<div class="container-fluid" style="padding-top: 30px; margin-top: 20px;">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-center" style="background: linear-gradient(135deg, #b3e5fc 0%, #e1bee7 100%); color: #2e2e2e;">
                    <h1 class="mb-0"><i class="fas fa-edit"></i> Edit Satsang</h1>
                </div>
                <div class="card-body">
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
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/admin_footer.php'; ?>
