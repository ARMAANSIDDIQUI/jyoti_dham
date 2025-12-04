// This script is for Google Places address autocomplete.
// See: https://developers.google.com/maps/documentation/javascript/places-autocomplete

function initAutocomplete() {
  const autocompleteInputs = document.querySelectorAll(".address-autocomplete");
  if (autocompleteInputs.length === 0) {
    console.error("No autocomplete input elements with class 'address-autocomplete' found.");
    return;
  }
  autocompleteInputs.forEach(setupAutocomplete);
}

function setupAutocomplete(autocompleteInput) {
  // Create a new session token for each new autocomplete instance.
  const sessionToken = new google.maps.places.AutocompleteSessionToken();

  // Create the autocomplete object, restricting the search to geographical
  // location types.
  const autocomplete = new google.maps.places.Autocomplete(autocompleteInput, {
    types: ["address"],
    // Restrict the fields to essentials to avoid unnecessary charges
    fields: ["address_components", "geometry"],
    sessionToken: sessionToken,
  });

  // When the user selects an address from the dropdown, populate the
  // address fields in the form.
  const placeChangedListener = () => {
    fillInAddress(autocomplete, autocompleteInput);
    // It's important to re-setup the autocomplete to get a new session token
    // after a place has been selected.
    google.maps.event.clearInstanceListeners(autocomplete); // Clear old listeners to prevent memory leaks
    setupAutocomplete(autocompleteInput);
  };

  autocomplete.addListener("place_changed", placeChangedListener);

  // Prevent form submission when pressing Enter in the autocomplete input
  autocompleteInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
    }
  });
}

function fillInAddress(autocomplete, inputElement) {
  // Get the place details from the autocomplete object.
  const place = autocomplete.getPlace();

  if (!place || !place.address_components) {
    // If the user hits enter without selecting a place, or if the place has no details.
    return;
  }
  
  // Get the form containing the input element.
  const form = inputElement.closest('form');
  if (!form) {
    console.error("Could not find a parent form for the autocomplete input.");
    return;
  }

  // Get the address components.
  const addressComponents = place.address_components;
  
  // Clear the form fields.
  const streetAddressField = form.querySelector("#street_address");
  const cityField = form.querySelector("#city");
  const stateField = form.querySelector("#state");
  const postalCodeField = form.querySelector("#postal_code");
  const countryField = form.querySelector("#country");

  if (streetAddressField) streetAddressField.value = "";
  if (cityField) cityField.value = "";
  if (stateField) stateField.value = "";
  if (postalCodeField) postalCodeField.value = "";
  if (countryField) countryField.value = "";


  let streetNumber = "";
  let route = "";

  // Populate the form fields.
  for (const component of addressComponents) {
    const addressType = component.types[0];

    switch (addressType) {
        case "street_number":
            streetNumber = component.long_name;
            break;
        case "route":
            route = component.long_name;
            break;
        case "locality":
            if (cityField) cityField.value = component.long_name;
            break;
        case "administrative_area_level_1":
            if (stateField) stateField.value = component.short_name;
            break;
        case "postal_code":
            if (postalCodeField) postalCodeField.value = component.long_name;
            break;
        case "country":
             if (countryField) countryField.value = component.long_name;
             break;
    }
  }

  if (streetAddressField) {
    streetAddressField.value = (streetNumber + " " + route).trim();
  }
}

// Ensure the initAutocomplete function is called when the script loads.
// Note: The Google Maps script must be loaded before this script.
// The Google Maps script should have a callback parameter, e.g.,
// <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places&callback=initAutocomplete" async defer></script>