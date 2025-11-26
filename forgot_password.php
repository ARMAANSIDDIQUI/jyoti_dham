<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/db_connect.php';
require_once __DIR__ . '/PHPMailer.php';
require_once __DIR__ . '/SMTP.php';
require_once __DIR__ . '/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$action = $_GET['action'] ?? 'forgot_password';

$conn = DB::getInstance()->getConnection();

if ($action === 'send_otp' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));

    // Rate limiting
    $max_otp_per_day = 5;
    $otp_resend_wait_time = 300; // 5 minutes

    if (!isset($_SESSION['otp_attempts'])) {
        $_SESSION['otp_attempts'] = [];
    }

    // Clear old attempts
    $_SESSION['otp_attempts'] = array_filter($_SESSION['otp_attempts'], function ($timestamp) {
        return $timestamp > (time() - 86400); // 24 hours
    });

    if (isset($_SESSION['otp_sent_time']) && (time() - $_SESSION['otp_sent_time']) < $otp_resend_wait_time) {
        $_SESSION['message'] = 'Please wait 5 minutes before requesting another OTP.';
        header('Location: forgot_password.php');
        exit();
    }
    
    if (count($_SESSION['otp_attempts']) >= $max_otp_per_day) {
        $_SESSION['message'] = 'You have reached the maximum number of OTP requests for today.';
        header('Location: forgot_password.php');
        exit();
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_email'] = $email;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USERNAME'];
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
            $mail->Port = $_ENV['SMTP_PORT'];
            $mail->setFrom($_ENV['SMTP_USERNAME'], 'Jyotidham');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP for password reset';
            $mail->Body = "Your 6-digit OTP is: <b>$otp</b>";
            $mail->send();

            $_SESSION['otp_sent_time'] = time();
            $_SESSION['otp_attempts'][] = time();

            $_SESSION['message'] = 'An OTP has been sent to your email address.';
            header('Location: forgot_password.php?action=verify_otp');
            exit();
        } catch (Exception $e) {
            $_SESSION['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            header('Location: forgot_password.php');
            exit();
        }
    } else {
        $_SESSION['message'] = 'No account found with that email address.';
        header('Location: forgot_password.php');
        exit();
    }
}

if ($action === 'do_verify_otp' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = trim($_POST['otp']);
    if (isset($_SESSION['otp']) && $entered_otp == $_SESSION['otp']) {
        unset($_SESSION['otp']);
        header('Location: forgot_password.php?action=reset_password');
        exit();
    } else {
        $_SESSION['message'] = 'Invalid OTP. Please try again.';
        header('Location: forgot_password.php?action=verify_otp');
        exit();
    }
}

if ($action === 'do_reset_password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['otp_email'])) {
        header('Location: forgot_password.php');
        exit();
    }
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $_SESSION['message'] = 'Passwords do not match.';
        header('Location: forgot_password.php?action=reset_password');
        exit();
    }
    if (strlen($new_password) < 6) {
        $_SESSION['message'] = 'Password must be at least 6 characters long.';
        header('Location: forgot_password.php?action=reset_password');
        exit();
    }

    $email = $_SESSION['otp_email'];
    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password_hash = :password_hash WHERE email = :email");
    $stmt->bindParam(':password_hash', $password_hash);
    $stmt->bindParam(':email', $email);

    if ($stmt->execute()) {
        unset($_SESSION['otp_email']);
        $_SESSION['message'] = 'Your password has been reset successfully. Please login.';
        header('Location: login.php');
        exit();
    } else {
        $_SESSION['message'] = 'An error occurred. Please try again.';
        header('Location: forgot_password.php?action=reset_password');
        exit();
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="css/login.css">
<main class="form-main">
    <div class="container">
        <?php if ($action === 'forgot_password'): ?>
            <h2>Forgot Password</h2>
            <?php if (isset($_SESSION['message'])): ?>
                <p class="message"><?php echo $_SESSION['message']; ?></p>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            <form action="forgot_password.php?action=send_otp" method="POST">
                <div class="form-group">
                    <label for="email">Enter your email address:</label>
                    <input type="email" id="email" name="email" class="text-input" required>
                </div>
                <button type="submit">Send OTP</button>
            </form>
            <p>Remember your password? <a href="login.php">Login here</a>.</p>

        <?php elseif ($action === 'verify_otp'): ?>
            <h2>Verify OTP</h2>
            <?php if (isset($_SESSION['message'])): ?>
                <p class="message"><?php echo $_SESSION['message']; ?></p>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            <form action="forgot_password.php?action=do_verify_otp" method="POST">
                <div class="form-group">
                    <label for="otp">Enter the 6-digit OTP:</label>
                    <input type="text" id="otp" name="otp" class="text-input" required maxlength="6">
                </div>
                <button type="submit">Verify OTP</button>
            </form>
            <p><a href="forgot_password.php">Go Back</a></p>

        <?php elseif ($action === 'reset_password'): ?>
            <h2>Reset Password</h2>
            <?php if (isset($_SESSION['message'])): ?>
                <p class="message"><?php echo $_SESSION['message']; ?></p>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            <form action="forgot_password.php?action=do_reset_password" method="POST">
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <div class="password-container">
                        <input type="password" id="new_password" name="new_password" class="text-input password-input" required minlength="6">
                        <i class="fas fa-eye toggle-password" data-target="new_password"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password:</label>
                    <div class="password-container">
                        <input type="password" id="confirm_password" name="confirm_password" class="text-input password-input" required minlength="6">
                        <i class="fas fa-eye toggle-password" data-target="confirm_password"></i>
                    </div>
                </div>
                <button type="submit">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>
</main>
<?php include 'includes/footer.php'; ?>