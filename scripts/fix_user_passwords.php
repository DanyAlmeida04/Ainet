<?php
// Reset all user passwords to a valid bcrypt hash ('123') and unblock users
$dbFile = __DIR__ . '/../database/database.sqlite';
if (!file_exists($dbFile)) {
    echo "Database file not found: $dbFile\n";
    exit(1);
}
try {
    $db = new PDO('sqlite:' . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $hash = password_hash('123', PASSWORD_BCRYPT);
    // Update all users: set password and unblock
    $updated = $db->exec("UPDATE users SET password='" . $hash . "', blocked=0 WHERE 1=1");
    echo "Updated users. Rows affected: " . ($updated === false ? 0 : $updated) . "\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
