<?php
// Admin check
require_once __DIR__ . '/admin/auth_check.php';

// Database connection
require_once __DIR__ . '/config/db_connect.php';
require_once __DIR__ . '/vendor/autoload.php';
$conn = DB::getInstance()->getConnection();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $event_end_time = $_POST['event_end_time'];
    $event_name = $_POST['event_name'];
    $event_description = $_POST['event_description'];

    // Calculate day from date
    $day = date('l', strtotime($event_date)); // e.g., 'Monday'
    $time_zone = 'EST'; // Default to Toronto time (EST)

    $created_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // Insert event into database using a prepared statement
    $sql = "INSERT INTO events (day, event_date, event_time, event_end_time, time_zone, event_name, event_description, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$day, $event_date, $event_time, $event_end_time, $time_zone, $event_name, $event_description, $created_by])) {
        echo "<div class='alert alert-success'>New event created successfully</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->errorInfo()[2] . "</div>";
    }
}

# No need to close PDO connection explicitly
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Event Input Form</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Maps API (for autocomplete and map selector) -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBM1nAywoajfBnPLSqZn0z5wvUNj2ZYhF0&libraries=places"></script>
</head>
<body>
<div class="container mt-5" style="margin-top:4rem !important; margin-bottom:4rem !important; max-width: 500px !important; border: 3px solid blue;border-radius:15px">
    <h2>Add Event</h2>
    <form action="add-event.php" method="post" style="margin-top:2rem !important; margin-bottom:2rem !important;">
        <!-- Date Picker -->
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" class="form-control" id="date" name="event_date" min="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <!-- Event Time Dropdown -->
        <div class="form-group">
            <label for="event_time">Start Time</label>
            <select class="form-control" id="event_time" name="event_time" required>
                <option value="">Select Start Time</option>
                <?php
                for ($hour = 8; $hour <= 20; $hour++) {
                    foreach ([0, 30] as $minute) {
                        $displayHour = $hour % 12 === 0 ? 12 : $hour % 12;
                        $ampm = $hour < 12 ? 'AM' : 'PM';
                        $minuteFormatted = str_pad($minute, 2, '0', STR_PAD_LEFT);
                        $value = str_pad($hour, 2, '0', STR_PAD_LEFT) . ":$minuteFormatted:00";
                        echo "<option value='$value'>$displayHour:$minuteFormatted $ampm</option>";
                    }
                }
                ?>
            </select>
        </div>

        <!-- Event End Time Field -->
        <div class="form-group">
            <label for="event_end_time">End Time</label>
            <select class="form-control" id="event_end_time" name="event_end_time" required>
                <option value="">Select End Time</option>
                <?php
                for ($hour = 8; $hour <= 20; $hour++) {
                    foreach ([0, 30] as $minute) {
                        $displayHour = $hour % 12 === 0 ? 12 : $hour % 12;
                        $ampm = $hour < 12 ? 'AM' : 'PM';
                        $minuteFormatted = str_pad($minute, 2, '0', STR_PAD_LEFT);
                        $value = str_pad($hour, 2, '0', STR_PAD_LEFT) . ":$minuteFormatted:00";
                        echo "<option value='$value'>$displayHour:$minuteFormatted $ampm</option>";
                    }
                }
                ?>
            </select>
        </div>

        <!-- Event Name -->
        <div class="form-group">
            <label for="event_name">Title</label>
            <input type="text" class="form-control" id="event_name" name="event_name" placeholder="Enter event title" required>
        </div>

        <!-- Event Description -->
        <div class="form-group">
            <label for="event_description">Description</label>
            <textarea class="form-control" id="event_description" name="event_description" rows="3" placeholder="Enter event description" required></textarea>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Submit</button>
        <a href="./dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </form>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>



</body>
</html>
