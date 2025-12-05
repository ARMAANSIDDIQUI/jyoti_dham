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

// Ensure the Google Maps API Key is loaded from environment variables
$googleMapsApiKey = $_ENV['GOOGLE_MAPS_API_KEY'] ?? '';
if (empty($googleMapsApiKey)) {
    echo "<div class='alert alert-danger'>Error: Google Maps API Key is not set. Please add GOOGLE_MAPS_API_KEY to your .env file.</div>";
}
?>
<script>
// Custom Map Picker Script (load first to define initMapPicker)
let addMapDetails;
let isLocationEditable = true; // Initial state for add event - editable since no existing location
let googleMapsLoaded = false;

// Callback function for Google Maps API to initialize the map for adding an event.
// This function is called once the Google Maps API script has fully loaded.
function initEditMapPickerCallback() {
    googleMapsLoaded = true;
    initializeMapIfFormVisible();
}

function initializeMapIfFormVisible() {
    const eventForm = document.getElementById('eventForm');
    if (eventForm && eventForm.style.display !== 'none' && googleMapsLoaded) {
        initializeMap();
    }
}

function initializeMap() {
    const initialLat = "43.8271272"; // Default lat
    const initialLng = "-79.26619269999999"; // Default lng
    const initialVenue = "Shri Param Hans Advait Mat (Jyoti Dham) Ontario";

    // Set initial display for venue and coordinates
    const displayVenueEl = document.getElementById('displayVenue');
    if (displayVenueEl) {
        displayVenueEl.textContent = initialVenue;
    }

    // Initialize map picker (always visible now)
    if (typeof initMapPicker === 'function') {
        addMapDetails = initMapPicker(
            'addEvent', // Unique instance ID for this map
            parseFloat(initialLat),
            parseFloat(initialLng),
            'editEventVenue',
            'editEventLatitude',
            'editEventLongitude',
            'editEventAddressAutocomplete',
            'editEventMap',
            'displayVenue',
            'displayCoordinates',
            false // Do not update venue input field automatically by map
        );

        // For add event, since defaults are set, but to match edit-event, if lat/lng exist, readonly
        // But task says for empty -> editable, but defaults are set, so perhaps editable
        // Initial check for existing coordinates to set editability and display address
        if (initialLat && initialLng && initialLat !== '0' && initialLng !== '0') {
            isLocationEditable = false; // Location is set, so initially not editable
            const venueEl = document.getElementById('editEventVenue');
            const addressEl = document.getElementById('editEventAddressAutocomplete');
            if (venueEl) venueEl.setAttribute('readonly', 'readonly');
            if (addressEl) addressEl.setAttribute('disabled', 'disabled');
            updateDisplayAddress(parseFloat(initialLat), parseFloat(initialLng), 'displayCoordinates', addMapDetails.geocoder);
        } else {
            // No location set, so it's editable by default
            isLocationEditable = true;
            const venueEl = document.getElementById('editEventVenue');
            const addressEl = document.getElementById('editEventAddressAutocomplete');
            const coordsEl = document.getElementById('displayCoordinates');
            if (venueEl) venueEl.removeAttribute('readonly');
            if (addressEl) addressEl.removeAttribute('disabled');
            if (coordsEl) coordsEl.textContent = 'Please set a location.';
        }

        // Apply initial interactivity state to the map
        setMapInteractivity(addMapDetails, isLocationEditable);
    } else {
        console.error('initMapPicker function not available');
    }
}

// Function to perform reverse geocoding for initial display
function updateDisplayAddress(lat, lng, displayCoordinatesId, geocoder) {
    if (lat && lng) {
        const latLng = new google.maps.LatLng(lat, lng);
        geocoder.geocode({ 'location': latLng }, function(results, status) {
            if (status === 'OK' && results[0]) {
                document.getElementById(displayCoordinatesId).textContent = results[0].formatted_address;
            } else {
                document.getElementById(displayCoordinatesId).textContent = `${lat}, ${lng} (Address not found)`;
            }
        });
    } else {
        document.getElementById(displayCoordinatesId).textContent = 'No location set.';
    }
}

function toggleLocationEditability(type) {
    const venueInput = document.getElementById('editEventVenue');
    const addressAutocompleteInput = document.getElementById('editEventAddressAutocomplete');
    const editButton = document.querySelector('button[onclick="toggleLocationEditability(\'edit\')"]');
    const currentLocationDisplay = document.getElementById('currentLocationDisplay');

    if (isLocationEditable) {
        // Currently editable, switch to view-only
        isLocationEditable = false;
        venueInput.setAttribute('readonly', 'readonly');
        addressAutocompleteInput.setAttribute('disabled', 'disabled');
        setMapInteractivity(addMapDetails, false);
        currentLocationDisplay.style.display = 'block';
        document.getElementById('displayVenue').textContent = venueInput.value;
        const lat = document.getElementById('editEventLatitude').value;
        const lng = document.getElementById('editEventLongitude').value;
        updateDisplayAddress(parseFloat(lat), parseFloat(lng), 'displayCoordinates', addMapDetails.geocoder);
        editButton.innerHTML = 'Edit Location';
    } else {
        // Currently view-only, switch to editable
        isLocationEditable = true;
        venueInput.removeAttribute('readonly');
        addressAutocompleteInput.removeAttribute('disabled');
        setMapInteractivity(addMapDetails, true);
        currentLocationDisplay.style.display = 'none';
        editButton.innerHTML = 'Hide Location';
    }
}
</script>
<script src="../js/event-map-picker.js"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?= htmlspecialchars($googleMapsApiKey) ?>&libraries=places&callback=initEditMapPickerCallback"></script>
<?php
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
    $organizer = $_POST['organizer'];
    // Get event venue, latitude, and longitude from form submission
    $event_venue = $_POST['event_venue'] ?? 'Shri Param Hans Advait Mat (Jyoti Dham) Ontario';
    $latitude = $_POST['latitude'] ?? 43.8271272;
    $longitude = $_POST['longitude'] ?? -79.26619269999999;
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
$sql = "SELECT id, event_name, event_date, event_time, event_end_time, organizer, event_venue FROM events";
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
                                <?php while ($event_item = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($event_item['id']) ?></td>
                                    <td><?= htmlspecialchars($event_item['event_name']) ?></td>
                                    <td><?= htmlspecialchars($event_item['event_date']) ?></td>
                                    <td><?= htmlspecialchars($event_item['event_time']) ?></td>
                                    <td><?= htmlspecialchars($event_item['organizer']) ?></td>
                                    <td><?= htmlspecialchars($event_item['event_venue']) ?></td>
                                    <td>
                                        <a href="../admin/edit-event.php?id=<?= $event_item['id'] ?>" class="btn btn-warning btn-sm me-2">Edit</a>
                                        <a href="?delete=<?= $event_item['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this event?')">Delete</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
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

                        <!-- Organizer -->
                        <div class="form-group">
                            <label for="organizer">Organizer</label>
                            <input type="text" class="form-control" id="organizer" name="organizer" placeholder="Enter organizer name" required>
                        </div>

                        <!-- Location Picker Section -->
                        <div class="form-group">
                            <label>Event Location</label>
                            <div id="currentLocationDisplay" style="margin-bottom: 10px; padding: 10px; border: 1px solid #e9ecef; border-radius: 5px; background-color: #f8f9fa;">
                                <strong>Venue:</strong> <span id="displayVenue">Shri Param Hans Advait Mat (Jyoti Dham) Ontario</span><br>
                                <strong>Address:</strong> <span id="displayCoordinates"></span>
                            </div>
                            <button type="button" class="btn btn-info btn-sm mb-2" onclick="toggleLocationEditability('edit')">Edit Location</button>
                            <div id="editEventLocationEditor" style="border: 1px solid #ccc; padding: 15px; border-radius: 5px; background-color: #f9f9f9;">
                                <div class="mb-3">
                                    <label for="editEventVenue" class="form-label">Event Venue:</label>
                                    <input type="text" id="editEventVenue" class="form-control" name="event_venue" value="Shri Param Hans Advait Mat (Jyoti Dham) Ontario">
                                </div>
                                <div class="mb-3">
                                    <label for="editEventAddressAutocomplete" class="form-label">Search Location:</label>
                                    <input type="text" id="editEventAddressAutocomplete" class="form-control" placeholder="Enter event location">
                                </div>
                                <div id="editEventMap" style="height: 400px; width: 100%; margin-bottom: 15px;"></div>
                                <input type="hidden" id="editEventLatitude" name="latitude" value="43.8271272">
                                <input type="hidden" id="editEventLongitude" name="longitude" value="-79.26619269999999">
                            </div>
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

<?php
require_once __DIR__ . '/admin_footer.php';
?>
<script>
// Override toggleForm to initialize map when form is shown
const originalToggleForm = toggleForm;
toggleForm = function() {
    originalToggleForm();
    const eventForm = document.getElementById('eventForm');
    if (eventForm && eventForm.style.display !== 'none') {
        initializeMapIfFormVisible();
    }
};
</script>

