<?php
require_once '../config/config.php';

try {
    // 1. Connect to MySQL Server (without selecting DB)
    $pdo = new PDO("mysql:host=" . DB_SERVER, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h3>Pharmacy IMS Database Setup</h3>";
    echo "Connected to MySQL server.<br>";

    // 2. Define SQL Schema directly
    $sql = "
    CREATE DATABASE IF NOT EXISTS pharmacy_management;
    USE pharmacy_management;

    CREATE TABLE IF NOT EXISTS users (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'store_clerk', 'report_viewer') NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS medicines (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        code VARCHAR(50) NOT NULL UNIQUE,
        medicine_group VARCHAR(100) NULL,
        quantity INT NOT NULL,
        price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        expiry_date DATE NULL,
        location_code VARCHAR(50) NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS medicine_catalog (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL UNIQUE,
        code VARCHAR(50) NOT NULL UNIQUE,
        medicine_group VARCHAR(100) NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS customers (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        email VARCHAR(100),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS sales (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        transaction_id VARCHAR(50) NOT NULL,
        medicine_id INT NOT NULL,
        customer_id INT,
        quantity INT NOT NULL,
        total_price DECIMAL(10, 2) NOT NULL,
        payment_method VARCHAR(50) NOT NULL DEFAULT 'Cash',
        discount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        sale_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (medicine_id) REFERENCES medicines(id),
        FOREIGN KEY (customer_id) REFERENCES customers(id)
    );

    CREATE TABLE IF NOT EXISTS procurement (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        medicine_name VARCHAR(100) NOT NULL,
        quantity INT NOT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        request_date DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS audit_log (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        action VARCHAR(255) NOT NULL,
        log_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    );
    ";

    // 3. Execute SQL Schema
    $pdo->exec($sql);
    echo "Database `pharmacy_management` and all tables created successfully.<br>";

    // 4. Create Default Admin User
    $pdo->exec("USE " . DB_NAME);
    $check_admin = $pdo->query("SELECT id FROM users WHERE username = 'admin'");
    if ($check_admin->rowCount() == 0) {
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $insert_admin = $pdo->prepare("INSERT INTO users (username, password, role) VALUES ('admin', ?, 'admin')");
        $insert_admin->execute([$password]);
        echo "Default admin user created: <b>admin</b> / <b>admin123</b><br>";
    } else {
        echo "Admin user already exists.<br>";
    }

    echo "<br><b>Setup Complete!</b> <a href='../index.php'>Go to Login</a>";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
?>