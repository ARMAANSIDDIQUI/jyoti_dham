<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db_connect.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

require_once __DIR__ . '/admin_header.php';

// Admin check
require_once __DIR__ . '/auth_check.php';

$conn = DB::getInstance()->getConnection();

// Configure Cloudinary after DB connection to ensure env is loaded
Configuration::instance($_ENV['CLOUDINARY_URL']);

// Handle event deletion
if (isset($_GET['delete'])) {
    $event_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$event_id]);
    header("Location: manage-events.php");
    exit();
}

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

    // Default values
    $organizer = 'jyotidham';
    $event_venue = 'Shri Param Hans Advait Mat (Jyoti Dham) Ontario';
    $latitude = 43.8271272;
    $longitude = -79.26619269999999;
    $image_url = 'https://res.cloudinary.com/dfxl3oy4y/image/upload/v1764283656/events/wesh9skwtgulo9f2gavm.svg'; // Default image URL

    // Handle image upload
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] == 0) {
        try {
            $uploadResult = (new UploadApi())->upload($_FILES['event_image']['tmp_name'], ['folder' => 'events']);
            $image_url = $uploadResult['secure_url'];
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Image upload failed: " . $e->getMessage() . "</div>";
        }
    }

    // Insert event into database using a prepared statement
    $sql = "INSERT INTO events (day, event_date, event_time, event_end_time, time_zone, event_name, event_description, organizer, event_venue, latitude, longitude, created_by, image_url)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$day, $event_date, $event_time, $event_end_time, $time_zone, $event_name, $event_description, $organizer, $event_venue, $latitude, $longitude, $created_by, $image_url])) {
        echo "<div class='alert alert-success'>New event created successfully</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->errorInfo()[2] . "</div>";
    }
}

// Fetch all events
$sql = "SELECT id, event_name, event_date, event_time, event_end_time FROM events";
$params = [];
$conditions = [];

if (!empty($_GET['search_name'])) {
    $conditions[] = "event_name LIKE ?";
    $params[] = '%' . $_GET['search_name'] . '%';
}
if (!empty($_GET['search_date'])) {
    $conditions[] = "event_date = ?";
    $params[] = $_GET['search_date'];
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY event_date DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);

?>

<div class="container-fluid" style="padding-top: 30px; margin-top: 20px;">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12">
            <div class="card">
                <div class="card-header text-center" style="background: linear-gradient(135deg, #b3e5fc 0%, #e1bee7 100%); color: #2e2e2e;">
                    <h1 class="mb-0"><i class="fas fa-calendar-alt"></i> Manage Events</h1>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <input type="text" name="search_name" class="form-control" placeholder="Search by Event Name" value="<?= htmlspecialchars($_GET['search_name'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <input type="date" name="search_date" class="form-control" value="<?= htmlspecialchars($_GET['search_date'] ?? '') ?>">
                            </div>
                            <div class="col-md-4 text-end">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                                <a href="manage-events.php" class="btn btn-info"><i class="fas fa-sync-alt"></i> Reset</a>
                            </div>
                        </div>
                    </form>
                    <div class="mb-3">
                        <button class="btn btn-primary" onclick="toggleForm()">Add New Event</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Organizer</th>
                                    <th>Venue</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($stmt as $event_item): ?>
                                <tr>
                                    <td><?= $event_item['id'] ?></td>
                                    <td><?= htmlspecialchars($event_item['event_name']) ?></td>
                                    <td><?= date('M j, Y', strtotime($event_item['event_date'])) ?></td>
                                    <td><?= date('g:i A', strtotime($event_item['event_time'])) ?> - <?= $event_item['event_end_time'] ? date('g:i A', strtotime($event_item['event_end_time'])) : 'N/A' ?></td>
                                    <td>jyotidham</td>
                                    <td>Shri Param Hans Advait Mat (Jyoti Dham) Ontario</td>
                                    <td>
                                        <a href="../admin/edit-event.php?id=<?= $event_item['id'] ?>" class="btn btn-warning btn-sm me-2">Edit</a>
                                        <a href="?delete=<?= $event_item['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this event?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="eventForm" class="container-fluid" style="padding-top: 30px; margin-top: 20px; display: none;">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card">
                <div class="card-header text-center" style="background: linear-gradient(135deg, #b3e5fc 0%, #e1bee7 100%); color: #2e2e2e;">
                    <h2 class="mb-0"><i class="fas fa-calendar-plus"></i> Add Event</h2>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
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

                        <!-- Event Image -->
                        <div class="form-group">
                            <label for="event_image">Event Image</label>
                            <input type="file" class="form-control" id="event_image" name="event_image">
                        </div>

                        <!-- Organizer (hidden) -->
                        <input type="hidden" name="organizer" value="jyotidham">

                        <!-- Venue (hidden) -->
                        <input type="hidden" name="event_venue" value="Shri Param Hans Advait Mat (Jyoti Dham) Ontario">

                        <!-- Map (iframe) -->
                        <div class="form-group">
                            <label>Event Location</label>
                            <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d46054.15022816234!2d-79.2661927!3d43.8271272!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89d4d6c80f26b0c5%3A0x30051dd9308eae70!2sShri%20Param%20Hans%20Advait%20Mat%20(Jyoti%20Dham)%20Ontario!5e0!3m2!1sen!2sin!4v1763492598758!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            <input type="hidden" name="latitude" value="43.8271272">
                            <input type="hidden" name="longitude" value="-79.26619269999999">
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-save"></i> Create Event</button>
                            <button type="button" class="btn btn-secondary" onclick="toggleForm()"><i class="fas fa-times"></i> Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleForm() {
    var form = document.getElementById('eventForm');
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}
</script>

<?php
require_once __DIR__ . '/admin_footer.php';
?>
