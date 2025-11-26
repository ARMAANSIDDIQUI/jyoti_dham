<?php
require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Database connection details
require_once 'config/db_connect.php';
require_once 'vendor/autoload.php';

$conn = DB::getInstance()->getConnection();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $message = $_POST["message"];

    // Create table if not exists
    $conn->exec("CREATE TABLE IF NOT EXISTS ContactFormSubmissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        message TEXT,
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );");

    // Prepare and execute the database query
    $stmt = $conn->prepare("INSERT INTO ContactFormSubmissions(name, email, phone, message) VALUES (?, ?, ?, ?)");
    $stmt->bindParam(1, $name);
    $stmt->bindParam(2, $email);
    $stmt->bindParam(3, $phone);
    $stmt->bindParam(4, $message);

    if ($stmt->execute()) {

        // Load environment variables
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USERNAME'];
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
            $mail->Port = $_ENV['SMTP_PORT'];

            $mail->setFrom($_ENV['SMTP_USERNAME'], 'Contact Form');
            $mail->addAddress($_ENV['SMTP_USERNAME']); // Send to the same email or a designated recipient

            $mail->isHTML(false);
            $mail->Subject = 'New Contact Form Submission';
            $mail->Body = "Name: $name\nEmail: $email\nPhone: $phone\nMessage: $message";

            $mail->send();
        } catch (Exception $e) {
            // Log the error and provide a generic error message to the user
            error_log("Email sending failed: " . $mail->ErrorInfo);
            echo "<script>alert('There was an issue sending your message. Please try again later.'); window.history.back();</script>";
            exit; // Stop execution to prevent redirect
        }

        // Redirect to homepage on success
        header("Location: success.html");
        exit;
    } else {
        // Show alert if database insertion fails
        $errorInfo = $stmt->errorInfo();
        echo "<script>alert('Error saving data: " . $errorInfo[2] . "'); window.history.back();</script>";
    }

    # No need to close PDO connection explicitly
}
?>
