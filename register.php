<?php
require_once __DIR__ . '/includes/header.php'; // Include the header component
?>
<?php
require_once __DIR__ . '/includes/header.php'; // Include the header component

$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']); // Clear form data after retrieving
// No direct database connection here, form will submit to register_action.php
?>
<main>


    <div class="container">
        <h2>Register for Jyotidham</h2>
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
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($formData['name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" minlength="6" value="<?php echo htmlspecialchars($formData['password'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="male" <?php echo (($formData['gender'] ?? '') == 'male') ? 'selected' : ''; ?>>Male</option>
                    <option value="female" <?php echo (($formData['gender'] ?? '') == 'female') ? 'selected' : ''; ?>>Female</option>
                    <option value="other" <?php echo (($formData['gender'] ?? '') == 'other') ? 'selected' : ''; ?>>Other</option>
                    <option value="prefer_not_to_say" <?php echo (($formData['gender'] ?? '') == 'prefer_not_to_say') ? 'selected' : ''; ?>>Prefer not to say</option>
                </select>
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($formData['dob'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" placeholder="e.g., +15551234567" value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>" pattern="^\+?[0-9\s\-()]{7,20}$" title="Phone number must be 7-20 digits, optionally starting with +, and can include spaces, hyphens, or parentheses.">
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($formData['address'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="vehicle_number">Vehicle Number (Optional):</label>
                <input type="text" id="vehicle_number" name="vehicle_number" value="<?php echo htmlspecialchars($formData['vehicle_number'] ?? ''); ?>">
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
    <script>
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
                                <option value="other" ${phpFormData['family_gender']?.[i] === 'other' ? 'selected' : ''}>Other</option>
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
<?php include 'includes/footer.php'; ?>