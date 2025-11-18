<?php
require_once __DIR__ . '/admin_header.php';

// Admin check
require_once __DIR__ . '/auth_check.php';

// Include database connection
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../vendor/autoload.php';
$conn = DB::getInstance()->getConnection();

// Get event ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid event ID.");
}

$event_id = intval($_GET['id']);

// Fetch event data
$sql = "SELECT * FROM events WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die("Event not found.");
}

// Handle update submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $day = $_POST['day'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $event_end_time = $_POST['event_end_time'];
    $time_zone = $_POST['time_zone'];
    $event_name = $_POST['event_name'];
    $event_description = $_POST['event_description'];
    $organizer = $_POST['organizer'];
    $event_venue = $_POST['event_venue'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $update_sql = "UPDATE events SET 
        day=?,
        event_date=?,
        event_time=?,
        event_end_time=?,
        time_zone=?,
        event_name=?,
        event_description=?,
        organizer=?,
        event_venue=?,
        latitude=?,
        longitude=?
        WHERE id = ?";

    $stmt = $conn->prepare($update_sql);

    if ($stmt->execute([$day, $event_date, $event_time, $event_end_time, $time_zone, $event_name, $event_description, $organizer, $event_venue, $latitude, $longitude, $event_id])) {
        echo "<div class='alert alert-success'>Event updated successfully</div>";
        // Refresh form with updated data by re-fetching from the database
        $sql = "SELECT * FROM events WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$event_id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->errorInfo()[2] . "</div>";
    }
}
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mt-5 mb-5">
                <div class="card-header">
                    <h2>Edit Event</h2>
                </div>
                <div class="card-body">
                    <form method="post">

        <!-- Day -->
        <div class="form-group">
            <label for="day">Day</label>
            <select class="form-control" name="day" required>
                <?php
                foreach (["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"] as $dayOption) {
                    $selected = $event['day'] == $dayOption ? 'selected' : '';
                    echo "<option value='$dayOption' $selected>$dayOption</option>";
                }
                ?>
            </select>
        </div>

        <!-- Date -->
        <div class="form-group">
            <label for="event_date">Date</label>
            <input type="date" class="form-control" name="event_date" value="<?= $event['event_date'] ?>" required>
        </div>

        <!-- Start Time -->
        <!-- Event Time Dropdown -->
<div class="form-group">
    <label for="event_time">Event Start Time</label>
    <select class="form-control" id="event_time" name="event_time" required>
        <option value="">Select Start Time</option>
        <?php
        for ($hour = 8; $hour <= 20; $hour++) {
            foreach ([0, 30] as $minute) {
                $value = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minute, 2, '0', STR_PAD_LEFT) . ':00';
                $displayHour = $hour % 12 === 0 ? 12 : $hour % 12;
                $ampm = $hour < 12 ? 'AM' : 'PM';
                $display = $displayHour . ':' . str_pad($minute, 2, '0', STR_PAD_LEFT) . ' ' . $ampm;
                $selected = ($value == $event['event_time']) ? 'selected' : '';
                echo "<option value=\"$value\" $selected>$display</option>";
            }
        }
        ?>
    </select>
</div>


        <!-- End Time -->
       <div class="form-group">
        <label for="event_end_time">Event End Time</label>
        <select class="form-control" id="event_end_time" name="event_end_time" required>
            <option value="">Select End Time</option>
            <?php
            for ($hour = 8; $hour <= 20; $hour++) {
                foreach ([0, 30] as $minute) {
                    $value = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minute, 2, '0', STR_PAD_LEFT) . ':00';
                    $displayHour = $hour % 12 === 0 ? 12 : $hour % 12;
                    $ampm = $hour < 12 ? 'AM' : 'PM';
                    $display = $displayHour . ':' . str_pad($minute, 2, '0', STR_PAD_LEFT) . ' ' . $ampm;
                    $selected = ($value == $event['event_end_time']) ? 'selected' : '';
                    echo "<option value=\"$value\" $selected>$display</option>";
                }
            }
            ?>
        </select>
    </div>


        <!-- Time Zone -->
        <div class="form-group">
            <label for="time_zone">Time Zone</label>
            <select class="form-control" name="time_zone" required>
                <?php
                $zones = ["IST", "EST", "EDT", "PST", "GMT"];
                foreach ($zones as $zone) {
                    $selected = $event['time_zone'] == $zone ? 'selected' : '';
                    echo "<option value='$zone' $selected>$zone</option>";
                }
                ?>
            </select>
        </div>

        <!-- Event Name -->
        <div class="form-group">
            <label for="event_name">Event Name</label>
            <input type="text" class="form-control" name="event_name" value="<?= htmlspecialchars($event['event_name']) ?>" required>
        </div>

        <!-- Description -->
        <div class="form-group">
            <label for="event_description">Description</label>
            <textarea class="form-control" name="event_description" rows="3" required><?= htmlspecialchars($event['event_description']) ?></textarea>
        </div>

        <!-- Organizer -->
        <div class="form-group">
            <label for="organizer">Organizer</label>
            <input type="text" class="form-control" name="organizer" value="<?= htmlspecialchars($event['organizer']) ?>" required>
        </div>

        <!-- Venue -->
        <div class="form-group">
            <label for="event_venue">Venue</label>
            <input type="text" class="form-control" id="event_venue" name="event_venue" value="<?= htmlspecialchars($event['event_venue']) ?>" required>
        </div>

        <!-- Map -->
        <div class="form-group">
            <label>Event Location</label>
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d46054.15022816234!2d-79.2661927!3d43.8271272!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89d4d6c80f26b0c5%3A0x30051dd9308eae70!2sShri%20Param%20Hans%20Advait%20Mat%20(Jyoti%20Dham)%20Ontario!5e0!3m2!1sen!2sin!4v1763492598758!5m2!1sen!2sin" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <input type="hidden" id="latitude" name="latitude" value="43.8271272">
            <input type="hidden" id="longitude" name="longitude" value="-79.26619269999999">
        </div>

                        <button type="submit" class="btn btn-success">Update Event</button>
                        <a href="manage-events.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS scripts for Google Maps -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script>
    function initMap() {
        const lat = parseFloat("<?= $event['latitude'] ?>");
        const lng = parseFloat("<?= $event['longitude'] ?>");
        const location = { lat: lat, lng: lng };

        const map = new google.maps.Map(document.getElementById('map'), {
            center: location,
            zoom: 12
        });

        const marker = new google.maps.Marker({
            position: location,
            map: map,
            draggable: true
        });

        marker.addListener('dragend', function () {
            const lat = marker.getPosition().lat();
            const lng = marker.getPosition().lng();
            $('#latitude').val(lat);
            $('#longitude').val(lng);
        });

        const autocomplete = new google.maps.places.Autocomplete(document.getElementById('event_venue'), {
            types: ['geocode'],
            componentRestrictions: { country: 'ca' }
        });

        autocomplete.addListener('place_changed', function () {
            const place = autocomplete.getPlace();
            if (place.geometry) {
                map.setCenter(place.geometry.location);
                marker.setPosition(place.geometry.location);
                $('#latitude').val(place.geometry.location.lat());
                $('#longitude').val(place.geometry.location.lng());
            }
        });
    }

    window.onload = initMap;
</script>

<?php
require_once __DIR__ . '/admin_footer.php';
?>
</body>
</html>
