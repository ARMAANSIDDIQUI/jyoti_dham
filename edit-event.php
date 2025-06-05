<?php
// Include database connection
include 'db.php';

// Get event ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid event ID.");
}

$event_id = intval($_GET['id']);

// Fetch event data
$sql = "SELECT * FROM events WHERE id = $event_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Event not found.");
}

$event = $result->fetch_assoc();

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
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    $update_sql = "UPDATE events SET 
        day='$day',
        event_date='$event_date',
        event_time='$event_time',
        event_end_time='$event_end_time',
        time_zone='$time_zone',
        event_name='$event_name',
        event_description='$event_description',
        organizer='$organizer',
        event_venue='$event_venue',
        latitude='$latitude',
        longitude='$longitude',
        is_featured='$is_featured'
        WHERE id = $event_id";

    if ($conn->query($update_sql) === TRUE) {
        echo "<div class='alert alert-success'>Event updated successfully</div>";
        $event = array_merge($event, $_POST); // Refresh form with updated data
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Event</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBM1nAywoajfBnPLSqZn0z5wvUNj2ZYhF0&libraries=places"></script>
</head>
<body>
<div class="container mt-5" style="max-width: 500px; border: 3px solid blue; border-radius:15px">
    <h2>Edit Event</h2>
    <form method="post" style="margin-top:2rem; margin-bottom:2rem;">

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
            <label>Event Location (Select on Map)</label>
            <div id="map" style="height: 400px;"></div>
            <input type="hidden" id="latitude" name="latitude" value="<?= $event['latitude'] ?>">
            <input type="hidden" id="longitude" name="longitude" value="<?= $event['longitude'] ?>">
        </div>

        <!-- Featured -->
        <div class="form-group">
            <label for="is_featured">Is Featured?</label>
            <select class="form-control" name="is_featured">
                <option value="0" <?= $event['is_featured'] == 0 ? 'selected' : '' ?>>No</option>
                <option value="1" <?= $event['is_featured'] == 1 ? 'selected' : '' ?>>Yes</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Update Event</button>
        <a href="event-list.php" class="btn btn-secondary">Cancel</a>
    </form>
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


</body>
</html>
