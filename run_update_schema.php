<?php
require_once 'config/db_connect.php';
$conn = DB::getInstance()->getConnection();

try {
    // Read the update_schema.sql file
    $sql = file_get_contents('update_schema.sql');

    // Remove comments and split the SQL into individual statements
    $sql = preg_replace('/--.*$/m', '', $sql); // Remove single-line comments
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $conn->exec($statement);
                echo "Executed: " . substr($statement, 0, 50) . "...\n";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                    echo "Column already exists, skipping: " . substr($statement, 0, 50) . "...\n";
                } else {
                    throw $e;
                }
            }
        }
    }

    echo "Schema update completed successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
