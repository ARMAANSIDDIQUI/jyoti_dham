<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/db_connect.php';

$conn = DB::getInstance()->getConnection();

try {
    $conn->beginTransaction();

    // Find all users
    $stmt = $conn->prepare("SELECT id FROM users");
    $stmt->execute();
    $users_to_update = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $updated_count = 0;

    $update_stmt = $conn->prepare("UPDATE users SET user_id = :formatted_user_id WHERE id = :user_id");

    foreach ($users_to_update as $user) {
        $user_id = $user['id'];
        $serial_str = (string)$user_id;
        $n = strlen($serial_str);
        $formatted_user_id = '';
        $attempts = 0;
        do {
            $random_digits = '';
            for ($i = 0; $i < 6 - $n; $i++) {
                $random_digits .= mt_rand(0, 9);
            }
            $formatted_user_id = 'JD-' . $random_digits . $serial_str;
            $check_stmt = $conn->prepare("SELECT id FROM users WHERE user_id = :user_id");
            $check_stmt->bindParam(':user_id', $formatted_user_id);
            $check_stmt->execute();
            $attempts++;
        } while ($check_stmt->rowCount() > 0 && $attempts < 100); // Prevent infinite loop

        if ($attempts >= 100) {
            echo "Failed to generate unique ID for user $user_id after 100 attempts. Skipping.\n";
            continue;
        }

        $update_stmt->bindParam(':formatted_user_id', $formatted_user_id);
        $update_stmt->bindParam(':user_id', $user_id);
        $update_stmt->execute();

        $updated_count++;
    }

    $conn->commit();

    echo "Successfully updated $updated_count existing users with new formatted IDs.";

} catch (Exception $e) {
    $conn->rollBack();
    echo "An error occurred: " . $e->getMessage();
}
?>
