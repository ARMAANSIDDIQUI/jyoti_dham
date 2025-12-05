            </div>
        </div>
        <!-- /#page-content-wrapper -->
    </div>
    <!-- /#wrapper -->

    <!-- Bootstrap core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Intl Tel Input JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>
    
    <!-- Menu Toggle Script -->
    <script>
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });
    </script>

    <script>
let addEventMapInstance = null; // Store map details for the 'add' event form

// This function will be called when the Google Maps API is loaded.
// It is also called by DOMContentLoaded as a fallback if the API loads very fast.
window.initMapPickerCallback = function() {
    const defaultLat = "43.8271272";
    const defaultLng = "-79.26619269999999";
    const defaultVenue = "Shri Param Hans Advait Mat (Jyoti Dham) Ontario";

    // Get DOM elements
    const addEventLatitudeInput = document.getElementById('addEventLatitude');
    const addEventLongitudeInput = document.getElementById('addEventLongitude');
    const displayAddVenueSpan = document.getElementById('displayAddVenue');
    const displayAddCoordinatesSpan = document.getElementById('displayAddCoordinates');

    // 1. Initialize display values for static display
    if (displayAddVenueSpan) displayAddVenueSpan.textContent = defaultVenue;
    if (displayAddCoordinatesSpan) displayAddCoordinatesSpan.textContent = `${defaultLat}, ${defaultLng}`;

    // 2. Initialize the map (always visible, initially non-interactive)
    if (addEventLatitudeInput && addEventLongitudeInput && typeof initMapPicker === 'function') {
        addEventMapInstance = initMapPicker(
            "addEventMapInstance",
            parseFloat(addEventLatitudeInput.value),
            parseFloat(addEventLongitudeInput.value),
            'addEventVenue',
            'addEventLatitude',
            'addEventLongitude',
            'addEventAddressAutocomplete',
            'addEventMap',
            'displayAddVenue',
            'displayAddCoordinates',
            false // updateVenueInputField = false
        );
        // Set map to non-interactive initially
        if (addEventMapInstance) {
            setMapInteractivity(addEventMapInstance, false);
        }
    }
}


document.addEventListener('DOMContentLoaded', function() {
    const defaultVenue = "Shri Param Hans Advait Mat (Jyoti Dham) Ontario";

    // Get DOM elements
    const toggleAddLocationEditorBtn = document.getElementById('toggleAddLocationEditor');
    const addEventVenueInput = document.getElementById('addEventVenue');
    const addEventAddressAutocompleteContainer = document.getElementById('addEventAddressAutocompleteContainer');
    const addEventLatitudeInput = document.getElementById('addEventLatitude');
    const addEventLongitudeInput = document.getElementById('addEventLongitude');
    const displayAddVenueSpan = document.getElementById('displayAddVenue');
    const displayAddCoordinatesSpan = document.getElementById('displayAddCoordinates');

    // If Google Maps API loads very fast, initMapPickerCallback might have already been called.
    if (!addEventMapInstance) {
        window.initMapPickerCallback();
    }

    // 3. Set initial state of editor elements
    if (addEventVenueInput) addEventVenueInput.readOnly = true; // Ensure readonly initially
    if (addEventAddressAutocompleteContainer) addEventAddressAutocompleteContainer.style.display = 'none'; // Hide search bar initially


    // 4. Handle "Edit Location" button click
    if (toggleAddLocationEditorBtn && addEventVenueInput) {
        toggleAddLocationEditorBtn.addEventListener('click', function() {
            const isEditing = addEventVenueInput.readOnly; // Check current state

            if (isEditing) {
                // Switch to editing mode
                toggleAddLocationEditorBtn.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Location';
                addEventVenueInput.readOnly = false; // Make venue input editable
                if (addEventAddressAutocompleteContainer) addEventAddressAutocompleteContainer.style.display = 'block'; // Show search bar
                if (addEventMapInstance) { // Ensure map instance exists before interacting
                    setMapInteractivity(addEventMapInstance, true); // Make map interactive
                }
            } else {
                // Switch back to non-editing mode
                toggleAddLocationEditorBtn.innerHTML = '<i class="fas fa-map-marker-alt"></i> Edit Location';
                addEventVenueInput.readOnly = true; // Make venue input read-only
                if (addEventAddressAutocompleteContainer) addEventAddressAutocompleteContainer.style.display = 'none'; // Hide search bar
                if (addEventMapInstance) { // Ensure map instance exists before interacting
                    setMapInteractivity(addEventMapInstance, false); // Make map non-interactive
                }

                // Update displayed coordinates from the input fields after editing
                if (displayAddCoordinatesSpan && addEventLatitudeInput && addEventLongitudeInput) displayAddCoordinatesSpan.textContent = `${addEventLatitudeInput.value}, ${addEventLongitudeInput.value}`;
                if (displayAddVenueSpan) displayAddVenueSpan.textContent = addEventVenueInput.value;
            }
        });
    }
});

// Primary toggleForm function
function toggleForm() {
    var eventForm = document.getElementById('eventForm');
    if (!eventForm) return;

    const toggleAddLocationEditorBtn = document.getElementById('toggleAddLocationEditor');
    const addEventVenueInput = document.getElementById('addEventVenue');
    const addEventAddressAutocompleteContainer = document.getElementById('addEventAddressAutocompleteContainer');
    const addEventLatitudeInput = document.getElementById('addEventLatitude');
    const addEventLongitudeInput = document.getElementById('addEventLongitude');
    const displayAddVenueSpan = document.getElementById('displayAddVenue');
    const displayAddCoordinatesSpan = document.getElementById('displayAddCoordinates');

    if (eventForm.style.display === 'none') {
        eventForm.style.display = 'block';
        // Reset the form values to defaults when opening the form
        // This ensures the form is clean for a new entry
        const dateInput = document.getElementById('date');
        if (dateInput) dateInput.value = '';

        const eventTimeInput = document.getElementById('event_time');
        if (eventTimeInput) eventTimeInput.value = '';

        const eventEndTimeInput = document.getElementById('event_end_time');
        if (eventEndTimeInput) eventEndTimeInput.value = '';

        const eventNameInput = document.getElementById('event_name');
        if (eventNameInput) eventNameInput.value = '';

        const eventDescriptionInput = document.getElementById('event_description');
        if (eventDescriptionInput) eventDescriptionInput.value = '';

        const eventImageInput = document.getElementById('event_image');
        if (eventImageInput) eventImageInput.value = ''; // Clear file input

        const organizerInput = document.getElementById('organizer');
        if (organizerInput) organizerInput.value = '';

        // Reset location editor to initial non-editing state when opening form
        if (toggleAddLocationEditorBtn) toggleAddLocationEditorBtn.innerHTML = '<i class="fas fa-map-marker-alt"></i> Edit Location';
        if (addEventVenueInput) {
            addEventVenueInput.value = "Shri Param Hans Advait Mat (Jyoti Dham) Ontario"; // Reset venue text
            addEventVenueInput.readOnly = true;
        }
        if (addEventAddressAutocompleteContainer) addEventAddressAutocompleteContainer.style.display = 'none';
        if (addEventLatitudeInput) addEventLatitudeInput.value = "43.8271272";
        if (addEventLongitudeInput) addEventLongitudeInput.value = "-79.26619269999999";
        if (addEventMapInstance && addEventLatitudeInput && addEventLongitudeInput) {
            setMapInteractivity(addEventMapInstance, false);
            // Also reset map center and marker position to default
            addEventMapInstance.map.setCenter({
                lat: parseFloat(addEventLatitudeInput.value),
                lng: parseFloat(addEventLongitudeInput.value)
            });
            addEventMapInstance.marker.setPosition({
                lat: parseFloat(addEventLatitudeInput.value),
                lng: parseFloat(addEventLongitudeInput.value)
            });
        }
        if (displayAddVenueSpan && addEventVenueInput) displayAddVenueSpan.textContent = addEventVenueInput.value;
        if (displayAddCoordinatesSpan && addEventLatitudeInput && addEventLongitudeInput) displayAddCoordinatesSpan.textContent = `${addEventLatitudeInput.value}, ${addEventLongitudeInput.value}`;

    } else {
        eventForm.style.display = 'none';

        // Reset location editor to initial non-editing state when form is closed
        if (toggleAddLocationEditorBtn) toggleAddLocationEditorBtn.innerHTML = '<i class="fas fa-map-marker-alt"></i> Edit Location';
        if (addEventVenueInput) addEventVenueInput.readOnly = true;
        if (addEventAddressAutocompleteContainer) addEventAddressAutocompleteContainer.style.display = 'none';
        if (addEventMapInstance) {
            setMapInteractivity(addEventMapInstance, false);
        }
        // Ensure displayed coordinates are updated to current state of hidden inputs
        if (displayAddCoordinatesSpan && addEventLatitudeInput && addEventLongitudeInput) displayAddCoordinatesSpan.textContent = `${addEventLatitudeInput.value}, ${addEventLongitudeInput.value}`;
        if (displayAddVenueSpan && addEventVenueInput) displayAddVenueSpan.textContent = addEventVenueInput.value; // Update display venue as well
    }
}
</script>
</body>
</html>
