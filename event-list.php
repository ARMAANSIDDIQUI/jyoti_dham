<?php
// Include database connection
include 'db.php';

// Handle event deletion
if (isset($_GET['delete'])) {
    $event_id = intval($_GET['delete']);
    $conn->query("DELETE FROM events WHERE id = $event_id");
    header("Location: event-list.php");
    exit();
}

// Fetch all events
$result = $conn->query("SELECT id, event_name, event_date, event_time FROM events ORDER BY event_date DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Events</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        function confirmDelete(eventId) {
            if (confirm('Are you sure you want to delete this event?')) {
                window.location.href = 'event-list.php?delete=' + eventId;
            }
        }
    </script>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Manage Events</h2> 
    <a href="dashboard.php" class="btn btn-primary mb-3">Return to dashboard</a> 
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
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['event_name']) ?></td>
                <td><?= htmlspecialchars($row['event_date']) ?></td>
                <td><?= htmlspecialchars($row['event_time']) ?></td>
                <td>
                    <a href="edit-event.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                    <button onclick="confirmDelete(<?= $row['id'] ?>)" class="btn btn-danger btn-sm">Delete</button>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php $conn->close(); ?>
