// js/event-map-picker.js

let mapInstances = {}; // To store multiple map instances if needed, though for now only one 'add' map

function initMapPicker(instanceId, initialLat, initialLng, venueInputId, latInputId, lngInputId, addressInputId, mapDivId, displayVenueId, displayCoordinatesId, updateVenueInputField = true, addressTextareaId = null) {
    const defaultLocation = { lat: initialLat || 43.827377, lng: initialLng ||  -79.266829 };
    
    const map = new google.maps.Map(document.getElementById(mapDivId), {
        center: defaultLocation,
        zoom: 12,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: false,
        // Initially non-interactive
        zoomControl: false,
        gestureHandling: 'none',
        clickableIcons: false
    });

    const marker = new google.maps.Marker({
        position: defaultLocation,
        map: map,
        draggable: false, // Initially not draggable
        title: "Event Location"
    });

    const addressInput = document.getElementById(addressInputId);
    let autocomplete = null;
    if (addressInput) {
        autocomplete = new google.maps.places.Autocomplete(addressInput, {
            types: ['address'],
            fields: ['geometry', 'formatted_address', 'address_components'],
        });

        // Prevent form submission when pressing Enter in the address input
        addressInput.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    }

    const geocoder = new google.maps.Geocoder();

    const mapDetails = {
        map: map,
        marker: marker,
        autocomplete: autocomplete,
        geocoder: geocoder,
        venueInputId: venueInputId,
        latInputId: latInputId,
        lngInputId: lngInputId,
        addressInputId: addressInputId,
        displayVenueId: displayVenueId,
        displayCoordinatesId: displayCoordinatesId,
        updateVenueInputField: updateVenueInputField,
        addressTextareaId: addressTextareaId,
        // Store references to listeners to remove them later
        dragendListener: null,
        placeChangedListener: null
    };

    mapInstances[instanceId] = mapDetails;
    return mapDetails;
}

// Function to perform reverse geocoding
function reverseGeocode(latLng, venueInputId, displayVenueId, updateVenueInputField, geocoder, addressTextareaId) {
    geocoder.geocode({ 'location': latLng }, function(results, status) {
        if (status === 'OK') {
            if (results[0]) {
                if (updateVenueInputField) {
                    document.getElementById(venueInputId).value = results[0].formatted_address;
                }
                if (displayVenueId) { // Update display venue
                    document.getElementById(displayVenueId).textContent = results[0].formatted_address;
                }
                if (addressTextareaId) {
                    document.getElementById(addressTextareaId).value = results[0].formatted_address;
                }
            } else {
                console.log('No results found for reverse geocoding.');
            }
        } else {
            console.log('Geocoder failed due to: ' + status);
        }
    });
}

function setMapInteractivity(mapDetails, editable) {
    const { map, marker, autocomplete, geocoder, venueInputId, latInputId, lngInputId, addressInputId, displayVenueId, displayCoordinatesId, updateVenueInputField, addressTextareaId } = mapDetails;

    // Set map controls
    map.setOptions({
        zoomControl: editable,
        gestureHandling: editable ? 'auto' : 'none',
        clickableIcons: editable
    });

    // Set marker draggable
    marker.setDraggable(editable);

    // Remove existing listeners if they exist
    if (mapDetails.dragendListener) {
        google.maps.event.removeListener(mapDetails.dragendListener);
        mapDetails.dragendListener = null;
    }
    if (mapDetails.placeChangedListener) {
        google.maps.event.removeListener(mapDetails.placeChangedListener);
        mapDetails.placeChangedListener = null;
    }

    if (editable) {
        // Add dragend listener for marker
        mapDetails.dragendListener = marker.addListener('dragend', function() {
            const newPosition = marker.getPosition();
            document.getElementById(latInputId).value = newPosition.lat();
            document.getElementById(lngInputId).value = newPosition.lng();
            
            if (displayCoordinatesId) {
                document.getElementById(displayCoordinatesId).textContent = `${newPosition.lat()}, ${newPosition.lng()}`;
            }

            reverseGeocode(newPosition, venueInputId, displayVenueId, updateVenueInputField, geocoder, addressTextareaId);
        });

        // Add place_changed listener for autocomplete
        if (autocomplete) {
            mapDetails.placeChangedListener = autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (!place.geometry) {
                    window.alert("No details available for input: '" + place.name + "'");
                    return;
                }

                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }

                marker.setPosition(place.geometry.location);
                document.getElementById(latInputId).value = place.geometry.location.lat();
                document.getElementById(lngInputId).value = place.geometry.location.lng();
                
                if (updateVenueInputField) {
                    document.getElementById(venueInputId).value = place.formatted_address;
                }
                
                if (addressTextareaId) {
                    document.getElementById(addressTextareaId).value = place.formatted_address;
                }

                if (displayVenueId) {
                    document.getElementById(displayVenueId).textContent = place.formatted_address;
                }
                if (displayCoordinatesId) {
                    document.getElementById(displayCoordinatesId).textContent = `${place.geometry.location.lat()}, ${place.geometry.location.lng()}`;
                }
            });
        }
    }
}