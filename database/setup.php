<?php
// Include the config file
require_once '../config/config.php';

// Close the initial connection that included DB_NAME
mysqli_close($link);

// Reconnect to MySQL server without specifying a database
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

// Check connection
if($link === false){
    die("ERROR: Could not connect to MySQL server. " . mysqli_connect_error());
}

// Drop database if it exists
$sql_drop_db = "DROP DATABASE IF EXISTS " . DB_NAME;
if (mysqli_query($link, $sql_drop_db)) {
    echo "Database '" . DB_NAME . "' dropped successfully (if it existed).<br>";
} else {
    echo "ERROR: Could not drop database '" . DB_NAME . "': " . mysqli_error($link) . "<br>";
}

// Create database
$sql_create_db = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if (mysqli_query($link, $sql_create_db)) {
    echo "Database '" . DB_NAME . "' created successfully.<br>";
} else {
    echo "ERROR: Could not create database '" . DB_NAME . "': " . mysqli_error($link) . "<br>";
}

// Select the database
if (!mysqli_select_db($link, DB_NAME)) {
    die("ERROR: Could not select database '" . DB_NAME . "': " . mysqli_error($link));
}

// Read the SQL file
$sql_content = file_get_contents('database.sql');

// Split SQL statements by semicolon
$sql_statements = explode(';', $sql_content);

$errors = [];
foreach ($sql_statements as $statement) {
    $statement = trim($statement);
    if (!empty($statement)) {
        if (mysqli_query($link, $statement)) {
            // echo "Query executed successfully: " . htmlspecialchars($statement) . "<br>";
        } else {
            $errors[] = "ERROR executing query: " . htmlspecialchars($statement) . " - " . mysqli_error($link);
        }
    }
}

if (empty($errors)) {
    echo "Database and tables created successfully!";
} else {
    echo "<br>Errors encountered during database setup:<br>";
    foreach ($errors as $error) {
        echo $error . "<br>";
    }
}

// Close the connection
mysqli_close($link);
?>