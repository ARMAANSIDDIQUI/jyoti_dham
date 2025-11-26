<?php
// Admin check
require_once __DIR__ . '/admin/auth_check.php';

// Include admin header
require_once __DIR__ . '/admin/admin_header.php';

// Include database connection
require_once __DIR__ . '/config/db_connect.php';
require_once __DIR__ . '/vendor/autoload.php';
$conn = DB::getInstance()->getConnection();

// Handle event deletion
if (isset($_GET['delete'])) {
    $event_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$event_id]);
    header("Location: event-list.php");
    exit();
}

// Fetch all events
$stmt = $conn->query("SELECT id, event_name, event_date, event_time FROM events ORDER BY event_date DESC");
?>

<div class="container mt-5">
    <h2 class="mb-4">Manage Events</h2> 
    <a href="admin/dashboard.php" class="btn btn-primary mb-3">Dashboard</a> 
    <br>
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Event Name</th>
                <th>Event Date</th>
                <th>Start Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?= htmlspecialchars($row['event_name']) ?></td>
                <td><?= htmlspecialchars($row['event_date']) ?></td>
                <td><?= htmlspecialchars($row['event_time']) ?></td>
                <td>
                    <a href="edit-event.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Edit Event</a>
                    <button onclick="confirmDelete(<?= $row['id'] ?>)" class="btn btn-danger btn-sm">Delete</button>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
    function confirmDelete(eventId) {
        if (confirm('Are you sure you want to delete this event?')) {
            window.location.href = 'event-list.php?delete=' + eventId;
        }
    }
</script>

<?php
// Include admin footer
require_once __DIR__ . '/admin/admin_footer.php';
?>
