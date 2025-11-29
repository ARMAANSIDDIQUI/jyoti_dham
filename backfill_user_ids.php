<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/db_connect.php';

$conn = DB::getInstance()->getConnection();

try {
    $conn->beginTransaction();

    // Find all users where user_id is NULL
    $stmt = $conn->prepare("SELECT id FROM users WHERE user_id IS NULL");
    $stmt->execute();
    $users_to_update = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $updated_count = 0;

    $update_stmt = $conn->prepare("UPDATE users SET user_id = :formatted_user_id WHERE id = :user_id");

    foreach ($users_to_update as $user) {
        $user_id = $user['id'];
        $formatted_user_id = 'JD-' . str_pad($user_id, 4, '0', STR_PAD_LEFT);

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
