<?php
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
$stmt = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container-fluid">
    <h1 class="mt-4">Manage Users</h1>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">User Management</h2>
                    </div>
                    <div class="card-body">
                        <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>

                                    <th>Role</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
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
</script>
<?php
require_once __DIR__ . '/admin_footer.php';
?>
