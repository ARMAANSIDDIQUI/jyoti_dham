<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables unconditionally at the earliest point
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

require_once __DIR__ . '/../config/db_connect.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

require_once __DIR__ . '/admin_header.php';

// Admin check
require_once __DIR__ . '/auth_check.php';

// Ensure the Google Maps API Key is loaded from environment variables
$googleMapsApiKey = $_ENV['GOOGLE_MAPS_API_KEY'] ?? '';
if (empty($googleMapsApiKey)) {
    // This alert might not be visible if JS is already trying to load the map
    error_log("Error: Google Maps API Key is not set in edit-event.php");
    // Optionally display an error to the user if appropriate, e.g., in a dedicated error div
}

$conn = DB::getInstance()->getConnection();

// Configure Cloudinary after DB connection to ensure env is loaded
Configuration::instance($_ENV['CLOUDINARY_URL']);

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
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $event_end_time = $_POST['event_end_time'];
    $event_name = $_POST['event_name'];
    $event_description = $_POST['event_description'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $address = $_POST['address'];

    // --- Cloudinary Image Upload ---
    $imageUrl = $event['image_url']; // Keep old image by default
    
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] == 0) {
        try {
            $uploadResult = (new UploadApi())->upload($_FILES['event_image']['tmp_name'], ['folder' => 'events']);
            $imageUrl = $uploadResult['secure_url'];
            // Here you could also delete the old image from Cloudinary if you stored the public_id
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Image upload failed: " . $e->getMessage() . "</div>";
        }
    }

    // Calculate day from date
    $day = date('l', strtotime($event_date)); // e.g., 'Monday'
    $time_zone = 'EST'; // Default to Toronto time (EST)
    $organizer = $_POST['organizer'];
    $event_venue = $_POST['event_venue']; // Get event venue from form submission

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
        longitude=?,
        image_url=?,
        address=?
        WHERE id = ?";

    $stmt = $conn->prepare($update_sql);

    if ($stmt->execute([$day, $event_date, $event_time, $event_end_time, $time_zone, $event_name, $event_description, $organizer, $event_venue, $latitude, $longitude, $imageUrl, $address, $event_id])) {
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

<div class="container-fluid" style="padding-top: 30px; margin-top: 20px;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mt-5 mb-5">
                <div class="card-header text-center" style="background: linear-gradient(135deg, #b3e5fc 0%, #e1bee7 100%); color: #2e2e2e;">
                    <h2 class="mb-0"><i class="fas fa-edit"></i> Edit Event</h2>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">

                        <!-- Date -->
                        <div class="form-group">
                            <label for="event_date">Date</label>
                            <input type="date" class="form-control" name="event_date" value="<?= $event['event_date'] ?>" required>
                        </div>

                        <!-- Start Time -->
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
                        
                        <!-- Current Image -->
                        <div class="form-group">
                            <label>Current Image</label>
                            <div>
                                <img src="<?= htmlspecialchars($event['image_url']) ?>" alt="Event Image" style="max-width: 200px; max-height: 200px; margin-bottom: 10px;">
                            </div>
                        </div>

                        <!-- Event Image Upload -->
                        <div class="form-group">
                            <label for="event_image">Upload New Image (optional)</label>
                            <input type="file" class="form-control-file" id="event_image" name="event_image">
                        </div>

                        <!-- Organizer -->
                        <div class="form-group">
                            <label for="organizer">Organizer</label>
                            <input type="text" class="form-control" id="organizer" name="organizer" value="<?= htmlspecialchars($event['organizer']) ?>" required>
                        </div>
                        
                        <!-- Location Picker Section -->
                        <div class="form-group">
                            <label>Event Location</label>
                            <div id="currentLocationDisplay" style="margin-bottom: 10px; padding: 10px; border: 1px solid #e9ecef; border-radius: 5px; background-color: #f8f9fa;">
                                <strong>Venue:</strong> <span id="displayVenue"><?= htmlspecialchars($event['event_venue']) ?></span><br>
                                <strong>Address:</strong> <span id="displayAddress"><?= htmlspecialchars($event['address'] ?? '') ?></span><br>
                                <strong>Coordinates:</strong> <span id="displayCoordinates"></span>
                            </div>
                            <button type="button" class="btn btn-info btn-sm mb-2" onclick="toggleLocationEditability('edit')">Edit Location</button>
                                                            <div id="editEventLocationEditor" style="border: 1px solid #ccc; padding: 15px; border-radius: 5px; background-color: #f9f9f9;">                                <div class="mb-3">
                                    <label for="editEventVenue" class="form-label">Event Venue:</label>
                                    <input type="text" id="editEventVenue" class="form-control" name="event_venue" value="<?= htmlspecialchars($event['event_venue']) ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="editEventAddress" class="form-label">Address:</label>
                                    <textarea id="editEventAddress" name="address" class="form-control" rows="3"><?= htmlspecialchars($event['address'] ?? '') ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="editEventAddressAutocomplete" class="form-label">Search Location:</label>
                                    <input type="text" id="editEventAddressAutocomplete" class="form-control" placeholder="Enter event location">
                                </div>
                                <div id="editEventMap" style="height: 400px; width: 100%; margin-bottom: 15px;"></div>
                                <input type="hidden" id="editEventLatitude" name="latitude" value="<?= htmlspecialchars($event['latitude']) ?>">
                                <input type="hidden" id="editEventLongitude" name="longitude" value="<?= htmlspecialchars($event['longitude']) ?>">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">Update Event</button>
                        <a href="manage-events.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let editMapDetails;
let isLocationEditable = false; // Initial state

// Callback function for Google Maps API to initialize the map for editing an event.
// This function is called once the Google Maps API script has fully loaded.
function initEditMapPickerCallback() {
    const initialLat = "<?= htmlspecialchars($event['latitude']) ?>";
    const initialLng = "<?= htmlspecialchars($event['longitude']) ?>";
    const initialVenue = "<?= htmlspecialchars($event['event_venue']) ?>";

    // Set initial display for venue and coordinates with null checks
    const displayVenueEl = document.getElementById('displayVenue');
    if (displayVenueEl) {
        displayVenueEl.textContent = initialVenue;
    }

    // Initialize map picker (always visible now)
    if (typeof initMapPicker === 'function') {
        editMapDetails = initMapPicker(
            'editEvent', // Unique instance ID for this map
            parseFloat(initialLat),
            parseFloat(initialLng),
            'editEventVenue',
            'editEventLatitude',
            'editEventLongitude',
            'editEventAddressAutocomplete',
            'editEventMap',
            'displayVenue',
            'displayCoordinates', // This will be used for coordinates display
            false, // Do not update venue input field automatically by map
            'editEventAddress' // NEW: ID of the address textarea
        );

        // Initial check for existing coordinates to set editability and display address
        if (initialLat && initialLng && initialLat !== '0' && initialLng !== '0') {
            isLocationEditable = false; // Location is set, so initially not editable
            const venueEl = document.getElementById('editEventVenue');
            const addressEl = document.getElementById('editEventAddressAutocomplete');
            const addressTextarea = document.getElementById('editEventAddress');
            if (venueEl) venueEl.setAttribute('readonly', 'readonly');
            if (addressEl) addressEl.setAttribute('disabled', 'disabled');
            if (addressTextarea) addressTextarea.setAttribute('readonly', 'readonly');
            updateDisplayAddress(parseFloat(initialLat), parseFloat(initialLng), 'displayAddress', editMapDetails.geocoder);
            document.getElementById('displayCoordinates').textContent = `${parseFloat(initialLat)}, ${parseFloat(initialLng)}`;
        } else {
            // No location set, so it's editable by default
            isLocationEditable = true;
            const venueEl = document.getElementById('editEventVenue');
            const addressEl = document.getElementById('editEventAddressAutocomplete');
            const addressTextarea = document.getElementById('editEventAddress');
            const coordsEl = document.getElementById('displayCoordinates');
            const displayAddressEl = document.getElementById('displayAddress');
            if (venueEl) venueEl.removeAttribute('readonly');
            if (addressEl) addressEl.removeAttribute('disabled');
            if (addressTextarea) addressTextarea.removeAttribute('readonly');
            if (coordsEl) coordsEl.textContent = 'Please set a location.';
            if (displayAddressEl) displayAddressEl.textContent = 'Please set a location.';
        }

        // Apply initial interactivity state to the map
        setMapInteractivity(editMapDetails, isLocationEditable);
    } else {
        console.error('initMapPicker function not available');
    }
}

// Function to perform reverse geocoding for initial display
function updateDisplayAddress(lat, lng, displayAddressId, geocoder) {
    if (lat && lng) {
        const latLng = new google.maps.LatLng(lat, lng);
        geocoder.geocode({ 'location': latLng }, function(results, status) {
            if (status === 'OK' && results[0]) {
                document.getElementById(displayAddressId).textContent = results[0].formatted_address;
            } else {
                document.getElementById(displayAddressId).textContent = `${lat}, ${lng} (Address not found)`;
            }
        });
    } else {
        document.getElementById(displayAddressId).textContent = 'No location set.';
    }
}



function toggleLocationEditability(type) {
    const venueInput = document.getElementById('editEventVenue');
    const addressAutocompleteInput = document.getElementById('editEventAddressAutocomplete');
    const addressTextarea = document.getElementById('editEventAddress');
    const editButton = document.querySelector('button[onclick="toggleLocationEditability(\'edit\')"]');
    const currentLocationDisplay = document.getElementById('currentLocationDisplay'); // Corrected: Retrieve element here

    if (isLocationEditable) {
        // Currently editable, switch to view-only
        isLocationEditable = false;
        venueInput.setAttribute('readonly', 'readonly');
        addressAutocompleteInput.setAttribute('disabled', 'disabled');
        addressTextarea.setAttribute('readonly', 'readonly');
        setMapInteractivity(editMapDetails, false);
        currentLocationDisplay.style.display = 'block';
        document.getElementById('displayVenue').textContent = venueInput.value;
        const lat = document.getElementById('editEventLatitude').value;
        const lng = document.getElementById('editEventLongitude').value;
        updateDisplayAddress(parseFloat(lat), parseFloat(lng), 'displayAddress', editMapDetails.geocoder);
        document.getElementById('displayCoordinates').textContent = `${lat}, ${lng}`;
        editButton.innerHTML = 'Edit Location'; // Change button text
    } else {
        // Currently view-only, switch to editable
        isLocationEditable = true;
        venueInput.removeAttribute('readonly');
        addressAutocompleteInput.removeAttribute('disabled');
        addressTextarea.removeAttribute('readonly');
        setMapInteractivity(editMapDetails, true);
        currentLocationDisplay.style.display = 'none';
        editButton.innerHTML = 'Hide Location'; // Change button text
    }
}
</script>

<!-- Custom Map Picker Script (load first to define initMapPicker) -->
<script src="../js/event-map-picker.js"></script>
<!-- Google Maps API Script -->
<script src="https://maps.googleapis.com/maps/api/js?key=<?= htmlspecialchars($googleMapsApiKey) ?>&libraries=places&callback=initEditMapPickerCallback" async defer></script>


<?php
require_once __DIR__ . '/admin_footer.php';
?>
