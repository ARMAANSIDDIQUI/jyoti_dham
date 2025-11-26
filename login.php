<?php
require_once __DIR__ . '/includes/header.php'; // Include the header component
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/db_connect.php';

$conn = DB::getInstance()->getConnection();

$message = '';
$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']); // Clear form data after retrieving

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password']; // Password will be verified, no need to htmlspecialchars yet

    if (empty($email) || empty($password)) {
        $message = "Please enter both email and password.";
        $_SESSION['form_data']['email'] = $email; // Store email for persistence
    } else {
        try {
            $stmt = $conn->prepare("SELECT id, name, password_hash, role FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                // Password is correct, start a session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['loggedin'] = true;
                unset($_SESSION['form_data']); // Clear form data on successful login

                // Role-based redirection
                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                    exit();
                } else {
                    header("Location: profile.php"); // Redirect to profile.php for regular users
                    exit();
                }
            } else {
                $message = "Invalid email or password.";
                $_SESSION['form_data']['email'] = $email; // Store email for persistence
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $message = "An error occurred during login. Please try again later.";
            $_SESSION['form_data']['email'] = $email; // Store email for persistence
        }
    }
}
?>
<main class="form-main">


    <div class="container">
        <h2>Login</h2>
        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="text-input" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" class="text-input password-input" required>
                    <i class="fas fa-eye toggle-password" data-target="password"></i>
                </div>
            </div>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
        <p><a href="forgot_password.php">Forgot Password?</a></p>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
