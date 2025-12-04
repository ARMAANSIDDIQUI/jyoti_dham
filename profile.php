<?php
require_once __DIR__ . '/includes/header.php'; // Include the header component

// Protection: Redirect to login.php if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user = null;
$family_members = [];
$message = '';
$message_type = '';

// Retrieve form data from session if available (for profile update form)
$profileFormData = $_SESSION['profile_form_data'] ?? [];
unset($_SESSION['profile_form_data']);

// Retrieve form data from session if available (for add family member form)
$addFamilyMemberFormData = $_SESSION['add_family_member_form_data'] ?? [];
unset($_SESSION['add_family_member_form_data']);

// Fetch user data
try {
    $stmt = $conn->prepare("SELECT id, user_id, name, email, gender, dob, phone, street_address, city, state, postal_code, country, family_size, vehicle_number, profile_image_url, profile_image_public_id FROM users WHERE id = :id");
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // User not found, redirect to login (shouldn't happen if session is valid)
        session_destroy();
        header("Location: login.php");
        exit();
    }

    // Fetch family members
    $stmt = $conn->prepare("SELECT id, name, gender, age FROM family_members WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $family_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Profile data fetch error: " . $e->getMessage());
    $message = "Error loading profile data. Please try again later.";
    $message_type = "error";
}

// Check for messages from update_profile.php, add_family_member.php, delete_family_member.php
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'] ?? 'info';
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>
<main class="container my-5">
    <div class="profile-card">
        <div class="row">
            <div class="col-md-4">
                <div class="profile-sidebar">
                    <div class="profile-picture-section text-center">
                        <?php if ($user['profile_image_url'] && $user['profile_image_public_id']): ?>
                            <img src="<?php echo htmlspecialchars($user['profile_image_url']); ?>" alt="Profile Picture" class="profile-img">
                        <?php else: ?>
                            <img src="<?php echo htmlspecialchars($_ENV['DEFAULT_PROFILE_IMAGE_URL'] ?? 'images/default-profile.png'); ?>" alt="Default Profile Picture" class="profile-img">
                        <?php endif; ?>
                        <h4 class="mt-3"><?php echo htmlspecialchars($user['name'] ?? ''); ?></h4>
                        <p class="text-muted"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
                        <p class="text-muted">User ID: <?php echo htmlspecialchars($user['user_id'] ?? ''); ?></p>
                    </div>
                    <ul class="nav nav-pills flex-column" id="profile-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab" aria-controls="details" aria-selected="true"><i class="fas fa-user-edit"></i> Edit Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="family-tab" data-toggle="tab" href="#family" role="tab" aria-controls="family" aria-selected="false"><i class="fas fa-users"></i> Family Members</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="password-tab" data-toggle="tab" href="#password" role="tab" aria-controls="password" aria-selected="false"><i class="fas fa-key"></i> Change Password</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-8">
                <div class="tab-content" id="profile-tabs-content">
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $message_type === 'error' ? 'danger' : 'success'; ?>"><?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>

                    <!-- Profile Details Tab -->
                    <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                        <h3 class="mb-4">Your Details</h3>
                        <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Profile Image:</label>
                                <div class="d-flex align-items-center">
                                    <input type="file" id="profile_image" name="profile_image" class="form-control-file" accept="image/*">
                                    <?php if ($user['profile_image_public_id']): ?>
                                        <a href="remove_profile_photo.php" class="btn btn-sm btn-outline-danger ml-3" onclick="return confirm('Are you sure you want to remove your profile photo?');">Remove</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="name">Full Name:</label>
                                    <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($profileFormData['name'] ?? $user['name'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="email">Email:</label>
                                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="gender">Gender:</label>
                                    <select id="gender" name="gender" class="form-control" required>
                                        <option value="">Select Gender</option>
                                        <option value="male" <?php echo ((($profileFormData['gender'] ?? '') == 'male') || (($user['gender'] ?? '') == 'male' && !isset($profileFormData['gender']))) ? 'selected' : ''; ?>>Male</option>
                                        <option value="female" <?php echo ((($profileFormData['gender'] ?? '') == 'female') || (($user['gender'] ?? '') == 'female' && !isset($profileFormData['gender']))) ? 'selected' : ''; ?>>Female</option>
                                        <option value="other" <?php echo ((($profileFormData['gender'] ?? '') == 'other') || (($user['gender'] ?? '') == 'other' && !isset($profileFormData['gender']))) ? 'selected' : ''; ?>>Other</option>
                                        <option value="prefer_not_to_say" <?php echo ((($profileFormData['gender'] ?? '') == 'prefer_not_to_say') || (($user['gender'] ?? '') == 'prefer_not_to_say' && !isset($profileFormData['gender']))) ? 'selected' : ''; ?>>Prefer not to say</option>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="dob">Date of Birth:</label>
                                    <input type="date" id="dob" name="dob" class="form-control" value="<?php echo htmlspecialchars($profileFormData['dob'] ?? $user['dob'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number:</label>
                                <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($profileFormData['phone'] ?? $user['phone'] ?? ''); ?>">
                            </div>
                            <button type="button" id="edit-address-btn" class="btn btn-secondary mb-3">Edit Address</button>
                            <!-- Address Autocomplete Input -->
                            <div class="form-group">
                                <label for="autocomplete">Search for Address</label>
                                <input type="text" class="form-control address-autocomplete" placeholder="Start typing your address..." readonly>
                            </div>

                            <!-- Address Fields -->
                            <div class="form-group">
                                <label for="street_address">Street Address</label>
                                <input type="text" class="form-control address-field" id="street_address" name="street_address" placeholder="Street Address" value="<?php echo htmlspecialchars($profileFormData['street_address'] ?? $user['street_address'] ?? ''); ?>" readonly required>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="city">City</label>
                                        <input type="text" class="form-control address-field" id="city" name="city" placeholder="City" value="<?php echo htmlspecialchars($profileFormData['city'] ?? $user['city'] ?? ''); ?>" readonly required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="state">State / Province</label>
                                        <input type="text" class="form-control address-field" id="state" name="state" placeholder="State / Province" value="<?php echo htmlspecialchars($profileFormData['state'] ?? $user['state'] ?? ''); ?>" readonly required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="postal_code">Zip / Postal Code</label>
                                        <input type="text" class="form-control address-field" id="postal_code" name="postal_code" placeholder="Zip / Postal Code" value="<?php echo htmlspecialchars($profileFormData['postal_code'] ?? $user['postal_code'] ?? ''); ?>" readonly required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="country">Country</label>
                                        <input type="text" class="form-control address-field" id="country" name="country" placeholder="Country" value="<?php echo htmlspecialchars($profileFormData['country'] ?? $user['country'] ?? ''); ?>" readonly required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vehicle_number">Vehicle Number (Optional):</label>
                                <input type="text" id="vehicle_number" name="vehicle_number" class="form-control" value="<?php echo htmlspecialchars($profileFormData['vehicle_number'] ?? $user['vehicle_number'] ?? ''); ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>

                    <!-- Family Members Tab -->
                    <div class="tab-pane fade" id="family" role="tabpanel" aria-labelledby="family-tab">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3>Your Family Members</h3>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#addFamilyMemberModal"><i class="fas fa-plus"></i> Add Member</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Age</th>
                                        <th>Gender</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($family_members)): ?>
                                        <?php foreach ($family_members as $member): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($member['name']); ?></td>
                                                <td><?php echo htmlspecialchars($member['age']); ?></td>
                                                <td><?php echo htmlspecialchars(ucfirst($member['gender'])); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-info edit-member-btn" data-toggle="modal" data-target="#editFamilyMemberModal"
                                                        title="Edit"
                                                        data-id="<?php echo $member['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($member['name']); ?>"
                                                        data-age="<?php echo htmlspecialchars($member['age']); ?>"
                                                        data-gender="<?php echo htmlspecialchars($member['gender']); ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form action="delete_family_member.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this family member?');" style="display: inline;">
                                                        <input type="hidden" name="member_id" value="<?php echo $member['id']; ?>">
                                                        <button type="submit" class="btn btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No family members added yet.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Change Password Tab -->
                    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                        <h3 class="mb-4">Change Password</h3>
                        <form action="change_password.php" method="POST">
                            <div class="form-group">
                                <label for="current_password">Current Password:</label>
                                <div class="password-container">
                                    <input type="password" id="current_password" name="current_password" class="form-control password-input" required>
                                    <i class="fas fa-eye toggle-password" data-target="current_password"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="new_password">New Password: <small>(min. 6 characters, 1 uppercase, 1 special character)</small></label>
                                <div class="password-container">
                                    <input type="password" id="new_password" name="new_password" class="form-control password-input" required minlength="6" pattern="(?=.*[A-Z])(?=.*[!@#$%^&*]).{6,}" title="Password must be at least 6 characters long and contain at least one uppercase letter and one special character.">
                                    <i class="fas fa-eye toggle-password" data-target="new_password"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password:</label>
                                <div class="password-container">
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control password-input" required minlength="6">
                                    <i class="fas fa-eye toggle-password" data-target="confirm_password"></i>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Add Family Member Modal -->
<div class="modal fade" id="addFamilyMemberModal" tabindex="-1" role="dialog" aria-labelledby="addFamilyMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFamilyMemberModalLabel">Add New Family Member</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="add_family_member.php" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="new_member_name">Name:</label>
                        <input type="text" id="new_member_name" name="name" class="form-control" value="<?php echo htmlspecialchars($addFamilyMemberFormData['name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="new_member_age">Age:</label>
                        <input type="number" id="new_member_age" name="age" class="form-control" min="0" value="<?php echo htmlspecialchars($addFamilyMemberFormData['age'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="new_member_gender">Gender:</label>
                        <select id="new_member_gender" name="gender" class="form-control" required>
                            <option value="">Select Gender</option>
                            <option value="male" <?php echo (($addFamilyMemberFormData['gender'] ?? '') == 'male') ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo (($addFamilyMemberFormData['gender'] ?? '') == 'female') ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo (($addFamilyMemberFormData['gender'] ?? '') == 'other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Family Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Family Member Modal -->
<div class="modal fade" id="editFamilyMemberModal" tabindex="-1" role="dialog" aria-labelledby="editFamilyMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFamilyMemberModalLabel">Edit Family Member</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="edit_family_member.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="edit_member_id" name="member_id">
                    <div class="form-group">
                        <label for="edit_member_name">Name:</label>
                        <input type="text" id="edit_member_name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_member_age">Age:</label>
                        <input type="number" id="edit_member_age" name="age" class="form-control" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_member_gender">Gender:</label>
                        <select id="edit_member_gender" name="gender" class="form-control" required>
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize intl-tel-input
    var phoneInputField = document.querySelector("#phone");
    var iti = window.intlTelInput(phoneInputField, {
        separateDialCode: true,
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"
    });

    // Handle form submission for profile update
    document.querySelector("#details form").addEventListener("submit", function(event) {
        // Update the phone input's value to the full international number
        if (iti.isValidNumber()) {
            phoneInputField.value = iti.getNumber();
        }
    });

    $('#editFamilyMemberModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var memberId = button.data('id');
        var name = button.data('name');
        var age = button.data('age');
        var gender = button.data('gender');

        var modal = $(this);
        modal.find('.modal-body #edit_member_id').val(memberId);
        modal.find('.modal-body #edit_member_name').val(name);
        modal.find('.modal-body #edit_member_age').val(age);
        modal.find('.modal-body #edit_member_gender').val(gender);
    });

    // Handle edit address button click
    const editAddressBtn = document.getElementById('edit-address-btn');
    const addressFields = document.querySelectorAll('.address-field');
    const autocompleteField = document.querySelector('.address-autocomplete');

    editAddressBtn.addEventListener('click', function() {
        addressFields.forEach(function(field) {
            field.readOnly = false;
        });
        autocompleteField.readOnly = false;
        editAddressBtn.style.display = 'none';
    });
});
</script>


<!-- Google Maps API -->
<!-- <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places&callback=initAutocomplete" async defer></script> -->
<script src="js/address-autocomplete.js"></script>    
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAInvDgYwKOXsI9h3moFBEP1wtWtipPOYc&libraries=places&callback=initAutocomplete" async defer></script>

<!-- Your custom autocomplete script -->
    

<?php include 'includes/footer.php'; ?>