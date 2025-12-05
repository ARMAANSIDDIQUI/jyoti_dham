<?php
require_once __DIR__ . '/includes/header.php'; // Include the header component

$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']); // Clear form data after retrieving
// No direct database connection here, form will submit to register_action.php
?>
<main class="form-main">
    <div class="container">
        <h2>Register for Jyoti Dham</h2>
        <?php
        if (isset($_SESSION['message'])) {
            echo '<p class="message ' . ($_SESSION['message_type'] ?? '') . '">' . htmlspecialchars($_SESSION['message']) . '</p>';
            unset($_SESSION['message']); // Clear the message after displaying
            unset($_SESSION['message_type']);
        }
        ?>
        <form action="register_action.php" method="POST" enctype="multipart/form-data">
            <h3>Your Details</h3>
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" class="text-input" placeholder="Enter your full name" value="<?php echo htmlspecialchars($formData['name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="text-input" placeholder="Enter your email address" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password: <small>(min. 6 characters, 1 uppercase, 1 special character)</small></label>
                <div class="password-container">
                    <input type="password" id="password" name="password" class="text-input password-input" placeholder="6+ characters" minlength="6" pattern="(?=.*[A-Z])(?=.*[!@#$%^&*]).{6,}" title="Password must be at least 6 characters long and contain at least one uppercase letter and one special character." value="<?php echo htmlspecialchars($formData['password'] ?? ''); ?>" autocomplete="new-password" required>
                    <i class="fas fa-eye toggle-password" data-target="password"></i>
                </div>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <div class="password-container">
                    <input type="password" id="confirm_password" name="confirm_password" class="text-input password-input" placeholder="Confirm your password" autocomplete="new-password" required>
                    <i class="fas fa-eye toggle-password" data-target="confirm_password"></i>
                </div>
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="male" <?php echo (($formData['gender'] ?? '') == 'male') ? 'selected' : ''; ?>>Male</option>
                    <option value="female" <?php echo (($formData['gender'] ?? '') == 'female') ? 'selected' : ''; ?>>Female</option>
                    <option value="prefer_not_to_say" <?php echo (($formData['gender'] ?? '') == 'prefer_not_to_say') ? 'selected' : ''; ?>>Prefer not to say</option>
                </select>
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($formData['dob'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" class="text-input" value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>">
            </div>
            <!-- Address Autocomplete Input -->
            <div class="form-group">
                <label for="autocomplete">Search for Address</label>
                <input type="text" class="form-control address-autocomplete" placeholder="Start typing your address...">
            </div>

            <!-- Address Fields -->
            <div class="form-group">
                <label for="street_address">Street Address</label>
                <input type="text" class="form-control" id="street_address" name="street_address" placeholder="Street Address" required>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" class="form-control" id="city" name="city" placeholder="City" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="state">State / Province</label>
                        <input type="text" class="form-control" id="state" name="state" placeholder="State / Province" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="postal_code">Zip / Postal Code</label>
                        <input type="text" class="form-control" id="postal_code" name="postal_code" placeholder="Zip / Postal Code" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" class="form-control" id="country" name="country" placeholder="Country" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="vehicle_number">Vehicle Number (Optional):</label>
                <input type="text" id="vehicle_number" name="vehicle_number" class="text-input" placeholder="e.g., A1B 2C3" value="<?php echo htmlspecialchars($formData['vehicle_number'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="profile_image">Profile Image (Optional):</label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*">
            </div>

            <h3>Family Members</h3>
            <div class="form-group">
                <label for="family_size">Number of Family Members (excluding yourself):</label>
                <input type="number" id="family_size" name="family_size" min="0" value="<?php echo htmlspecialchars($formData['family_size'] ?? '0'); ?>">
            </div>

            <div id="family-members-container">
                <!-- Dynamic family member fields will be inserted here by JavaScript -->
            </div>

            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>

    <script>
        const phpFormData = <?php echo json_encode($formData); ?>;
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>
    <script>
        // Initialize intl-tel-input
        var phoneInputField = document.querySelector("#phone");
        var iti = window.intlTelInput(phoneInputField, {
            initialCountry: "auto",
            geoIpLookup: function(callback) {
                fetch('https://ipinfo.io/json')
                    .then(function(response) { return response.json(); })
                    .then(function(data) { callback(data.country); })
                    .catch(function() { callback("ca"); });
            },
            separateDialCode: true,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"
        });

        // Handle form submission
        document.querySelector("form").addEventListener("submit", function(event) {
            // Update the phone input's value to the full international number
            if (iti.isValidNumber()) {
                phoneInputField.value = iti.getNumber();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const familySizeInput = document.getElementById('family_size');
            const familyMembersContainer = document.getElementById('family-members-container');

            function generateFamilyMemberFields() {
                const familySize = parseInt(familySizeInput.value);
                familyMembersContainer.innerHTML = ''; // Clear existing fields

                for (let i = 0; i < familySize; i++) {
                    const memberDiv = document.createElement('div');
                    memberDiv.classList.add('family-member-fields');
                    memberDiv.innerHTML = `
                        <h4>Family Member ${i + 1}</h4>
                        <div class="form-group">
                            <label for="family_name_${i}">Name:</label>
                            <input type="text" id="family_name_${i}" name="family_name[]" value="${phpFormData['family_name']?.[i] ? htmlspecialchars(phpFormData['family_name'][i]) : ''}" required>
                        </div>
                        <div class="form-group">
                            <label for="family_age_${i}">Age:</label>
                            <input type="number" id="family_age_${i}" name="family_age[]" min="0" value="${phpFormData['family_age']?.[i] ? htmlspecialchars(phpFormData['family_age'][i]) : ''}" required>
                        </div>
                        <div class="form-group">
                            <label for="family_gender_${i}">Gender:</label>
                            <select id="family_gender_${i}" name="family_gender[]" required>
                                <option value="">Select Gender</option>
                                <option value="male" ${phpFormData['family_gender']?.[i] === 'male' ? 'selected' : ''}>Male</option>
                                <option value="female" ${phpFormData['family_gender']?.[i] === 'female' ? 'selected' : ''}>Female</option>
                            </select>
                        </div>
                    `;
                    familyMembersContainer.appendChild(memberDiv);
                }
            }

            // Generate fields on page load if family_size has a default value
            generateFamilyMemberFields();

            // Listen for changes to the family_size input
            familySizeInput.addEventListener('change', generateFamilyMemberFields);
            familySizeInput.addEventListener('keyup', generateFamilyMemberFields); // Also update on keyup for quicker response
        });
    </script>
</main>
<!-- Google Maps API -->
<!-- <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places&callback=initAutocomplete" async defer></script> -->
<script src="js/address-autocomplete.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= htmlspecialchars($_ENV['GOOGLE_MAPS_API_KEY']) ?>&libraries=places&callback=initAutocomplete" async defer></script>
<!--custom autocomplete script -->

<?php include 'includes/footer.php'; ?>