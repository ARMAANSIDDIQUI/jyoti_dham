<?php
ob_start();
require_once __DIR__ . '/admin_header.php';
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../vendor/autoload.php';

$conn = DB::getInstance()->getConnection();

// Handle user deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
    $stmt->execute([$user_id]);
    header("Location: manage_users.php");
    exit();
}

// Handle role update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_role'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = $_POST['role'];
    if (in_array($new_role, ['user', 'admin'])) {
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$new_role, $user_id]);
    }
    header("Location: manage_users.php");
    exit();
}

// Fetch all users
$sql = "SELECT id, name, email, role, created_at, gender, dob, phone, address, family_size, vehicle_number FROM users";
$params = [];
$conditions = [];

if (!empty($_GET['search_name'])) {
    $conditions[] = "name LIKE ?";
    $params[] = '%' . $_GET['search_name'] . '%';
}
if (!empty($_GET['search_email'])) {
    $conditions[] = "email LIKE ?";
    $params[] = '%' . $_GET['search_email'] . '%';
}
if (!empty($_GET['search_phone'])) {
    $conditions[] = "phone LIKE ?";
    $params[] = '%' . $_GET['search_phone'] . '%';
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container-fluid" style="padding-top: 30px; margin-top: 20px;">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-center" style="background: linear-gradient(135deg, #b3e5fc 0%, #e1bee7 100%); color: #2e2e2e;">
                    <h1 class="mb-0"><i class="fas fa-users"></i> Manage Users</h1>
                </div>
                <div class="card-body">


                    <form method="GET" class="mb-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-3">
                                <input type="text" name="search_name" class="form-control" placeholder="Search by Name" value="<?= htmlspecialchars($_GET['search_name'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="search_email" class="form-control" placeholder="Search by Email" value="<?= htmlspecialchars($_GET['search_email'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="search_phone" class="form-control" placeholder="Search by Phone Number" value="<?= htmlspecialchars($_GET['search_phone'] ?? '') ?>">
                            </div>
                            <div class="col-md-3 text-right"> <!-- Use text-end for right alignment in Bootstrap 5, text-right for Bootstrap 4 -->
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                                <a href="manage_users.php" class="btn btn-info"><i class="fas fa-sync-alt"></i> Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th><i class="fas fa-id-badge"></i> ID</th>
                                    <th><i class="fas fa-user"></i> Name</th>
                                    <th><i class="fas fa-envelope"></i> Email</th>
                                    <th><i class="fas fa-user-tag"></i> Role</th>
                                    <th><i class="fas fa-calendar-alt"></i> Created At</th>
                                    <th><i class="fas fa-cogs"></i> Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['id']) ?></td>
                                    <td><?= htmlspecialchars($user['name']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>

                                    <td>
                                        <form method="post" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <select name="role" onchange="this.form.submit()" class="form-control form-control-sm" style="width: auto; display: inline;">
                                                <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                                                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                            </select>
                                            <input type="hidden" name="update_role" value="1">
                                        </form>
                                    </td>
                                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                                    <td>
                                        <button onclick="viewUserDetails(<?= $user['id'] ?>)" class="btn btn-info btn-sm">View Details</button>
                                        <?php if ($user['role'] != 'admin'): ?>
                                            <button onclick="confirmDelete(<?= $user['id'] ?>, '<?= htmlspecialchars($user['name']) ?>')" class="btn btn-danger btn-sm">Delete</button>
                                        <?php else: ?>
                                            <span class="text-muted">Cannot delete admin</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function confirmDelete(userId, userName) {
        if (confirm('Are you sure you want to delete user: ' + userName + '?')) {
            window.location.href = 'manage_users.php?delete=' + userId;
        }
    }

    function viewUserDetails(userId) {
        // Fetch user details via AJAX
        fetch('get_user_details.php?id=' + userId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const user = data.user;
                    const modalContent = `
                        <div class="modal fade" id="userDetailsModal" tabindex="-1" role="dialog" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="userDetailsModalLabel">User Details: ${user.name}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>ID:</strong> ${user.id}</p>
                                                <p><strong>Name:</strong> ${user.name}</p>
                                                <p><strong>Email:</strong> ${user.email}</p>
                                                <p><strong>Role:</strong> ${user.role}</p>
                                                <p><strong>Gender:</strong> ${user.gender || 'Not specified'}</p>
                                                <p><strong>Date of Birth:</strong> ${user.dob || 'Not specified'}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Phone:</strong> ${user.phone || 'Not specified'}</p>
                                                <p><strong>Address:</strong> ${user.address || 'Not specified'}</p>
                                                <p><strong>Family Size:</strong> ${user.family_size || 'Not specified'}</p>
                                                <p><strong>Vehicle Number:</strong> ${user.vehicle_number || 'Not specified'}</p>
                                                <p><strong>Created At:</strong> ${user.created_at}</p>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <h6><strong>Family Members:</strong></h6>
                                            <div id="family-members-${user.id}"></div>
                                        </div>
                                        ${user.profile_image_url ? `<div class="text-center mt-3"><img src="${user.profile_image_url}" alt="Profile Image" class="img-fluid rounded" style="max-width: 200px;"></div>` : ''}
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.insertAdjacentHTML('beforeend', modalContent);
                    $('#userDetailsModal').modal('show');

                    // Fetch family members
                    fetch('get_family_members.php?id=' + userId)
                        .then(response => response.json())
                        .then(familyData => {
                            const familyContainer = document.getElementById('family-members-' + userId);
                            if (familyData.success && familyData.members.length > 0) {
                                let familyHtml = '<ul>';
                                familyData.members.forEach(member => {
                                    familyHtml += `<li>${member.name} (${member.age} years old, ${member.gender})</li>`;
                                });
                                familyHtml += '</ul>';
                                familyContainer.innerHTML = familyHtml;
                            } else {
                                familyContainer.innerHTML = '<p>No family members listed.</p>';
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching family members:', error);
                            document.getElementById('family-members-' + userId).innerHTML = '<p>Error loading family members.</p>';
                        });

                    $('#userDetailsModal').on('hidden.bs.modal', function () {
                        $(this).remove();
                    });
                } else {
                    alert('Error fetching user details');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error fetching user details');
            });
    }
</script>
<?php
require_once __DIR__ . '/admin_footer.php';
?>
