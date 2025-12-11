<?php
require_once '../config/config.php';

// Attempt to connect WITHOUT database selected to create it if needed
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to MySQL server successfully.<br>";

    // Read the SQL file
    $sql_file = __DIR__ . '/pharmacy_ims.sql';
    if (!file_exists($sql_file)) {
        die("Error: SQL file not found at " . $sql_file);
    }

    $sql = file_get_contents($sql_file);

    // Execute the SQL commands
    // Note: Breaking by semicolon might be needed if the driver doesn't support multiple queries at once,
    // but PDO often allows it or we can just run exec().
    // For safety, we'll try running the full block.

    $pdo->exec($sql);
    echo "Database and tables created successfully using unified schema.<br>";

    // Optional: Create a default admin user if not exists
    $pdo->exec("USE " . DB_NAME);
    $check_admin = $pdo->query("SELECT id FROM users WHERE username = 'admin'");
    if ($check_admin->rowCount() == 0) {
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $insert_admin = $pdo->prepare("INSERT INTO users (username, password, role) VALUES ('admin', ?, 'admin')");
        $insert_admin->execute([$password]);
        echo "Default admin user created (admin / admin123).<br>";
    } else {
        echo "Admin user already exists.<br>";
    }

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
?>