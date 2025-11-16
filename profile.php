<?php
require_once __DIR__ . '/includes/header.php'; // Include the header component
?>
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
    $stmt = $conn->prepare("SELECT id, name, email, gender, dob, phone, address, family_size, vehicle_number, profile_image_url, profile_image_public_id FROM users WHERE id = :id");
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
<main class="form-main">


    <div class="container">
        <h2>Edit Your Profile</h2>

        <?php if ($message): ?>
            <p class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <!-- Part 1 & 2: User Details and Profile Picture -->
        <form action="update_profile.php" method="POST" enctype="multipart/form-data">
            <h3>Your Details</h3>
            <div class="profile-picture-section">
                <?php if ($user['profile_image_url']): ?>
                    <img src="<?php echo htmlspecialchars($user['profile_image_url']); ?>" alt="Profile Picture">
                <?php else: ?>
                    <img src="<?php echo htmlspecialchars($_ENV['DEFAULT_PROFILE_IMAGE_URL'] ?? 'images/default-profile.png'); ?>" alt="Default Profile Picture">
                <?php endif; ?>
                <div class="form-group">
                    <label for="profile_image">Update Profile Image (Optional):</label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*">
                </div>
            </div>

                            <div class="form-group">
                                <label for="name">Full Name:</label>
                                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($profileFormData['name'] ?? $user['name'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender:</label>
                                <select id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" <?php echo ((($profileFormData['gender'] ?? '') == 'male') || (($user['gender'] ?? '') == 'male' && !isset($profileFormData['gender']))) ? 'selected' : ''; ?>>Male</option>
                                    <option value="female" <?php echo ((($profileFormData['gender'] ?? '') == 'female') || (($user['gender'] ?? '') == 'female' && !isset($profileFormData['gender']))) ? 'selected' : ''; ?>>Female</option>
                                    <option value="other" <?php echo ((($profileFormData['gender'] ?? '') == 'other') || (($user['gender'] ?? '') == 'other' && !isset($profileFormData['gender']))) ? 'selected' : ''; ?>>Other</option>
                                    <option value="prefer_not_to_say" <?php echo ((($profileFormData['gender'] ?? '') == 'prefer_not_to_say') || (($user['gender'] ?? '') == 'prefer_not_to_say' && !isset($profileFormData['gender']))) ? 'selected' : ''; ?>>Prefer not to say</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="dob">Date of Birth:</label>
                                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($profileFormData['dob'] ?? $user['dob'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number:</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($profileFormData['phone'] ?? $user['phone'] ?? ''); ?>" pattern="^\+?[0-9\s\-()]{7,20}$" title="Phone number must be 7-20 digits, optionally starting with +, and can include spaces, hyphens, or parentheses.">
                            </div>
                            <div class="form-group">
                                <label for="address">Address:</label>
                                <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($profileFormData['address'] ?? $user['address'] ?? ''); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="vehicle_number">Vehicle Number (Optional):</label>
                                <input type="text" id="vehicle_number" name="vehicle_number" value="<?php echo htmlspecialchars($profileFormData['vehicle_number'] ?? $user['vehicle_number'] ?? ''); ?>">
                            </div>
        </form>

        <!-- Part 3: Family Management -->
        <div class="family-members-list">
            <h3>Your Family Members</h3>
            <?php if (!empty($family_members)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($family_members as $member): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($member['name']); ?></td>
                                <td><?php echo htmlspecialchars($member['age']); ?></td>
                                <td><?php echo htmlspecialchars($member['gender']); ?></td>
                                <td>
                                    <form action="delete_family_member.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this family member?');">
                                        <input type="hidden" name="member_id" value="<?php echo $member['id']; ?>">
                                        <button type="submit" class="delete-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No family members added yet.</p>
            <?php endif; ?>
        </div>

        <div class="add-family-member-form">
            <h3>Add New Family Member</h3>
            <form action="add_family_member.php" method="POST">
                <div class="form-group">
                    <label for="new_member_name">Name:</label>
                    <input type="text" id="new_member_name" name="name" value="<?php echo htmlspecialchars($addFamilyMemberFormData['name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="new_member_age">Age:</label>
                    <input type="number" id="new_member_age" name="age" min="0" value="<?php echo htmlspecialchars($addFamilyMemberFormData['age'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="new_member_gender">Gender:</label>
                    <select id="new_member_gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="male" <?php echo (($addFamilyMemberFormData['gender'] ?? '') == 'male') ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo (($addFamilyMemberFormData['gender'] ?? '') == 'female') ? 'selected' : ''; ?>>Female</option>
                        <option value="other" <?php echo (($addFamilyMemberFormData['gender'] ?? '') == 'other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                <button type="submit">Add Family Member</button>
            </form>
        </div>
    </div>
</main>
<?php include 'includes/footer.php'; ?>