<?php
session_start();

// Check if user is admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: login.php");
    exit;
}

require_once '../config/config.php';
require_once '../src/MedicineCatalog.php';

$database = new Database();
$db = $database->getConnection();
$catalog = new MedicineCatalog($db);

$message = "";

if (isset($_POST["import"])) {
    $fileName = $_FILES["file"]["tmp_name"];

    if ($_FILES["file"]["size"] > 0) {
        $file = fopen($fileName, "r");

        $count_success = 0;
        $count_skip = 0;

        // Skip header row if present (optional, usually good practice to check)
        // fgetcsv($file); 

        while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
            // Assuming CSV format: name, code, group
            if (isset($column[0]) && isset($column[1])) {
                $catalog->name = trim($column[0]);
                $catalog->code = trim($column[1]);
                $catalog->medicine_group = isset($column[2]) ? trim($column[2]) : "";

                if (!empty($catalog->name) && !empty($catalog->code)) {
                    if (!$catalog->exists()) {
                        if ($catalog->create()) {
                            $count_success++;
                        } else {
                            $count_skip++; // Error creating
                        }
                    } else {
                        $count_skip++; // Already exists
                    }
                }
            }
        }
        fclose($file);
        $message = "Import finished. Added: $count_success, Skipped/Error: $count_skip";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Upload Medicine Catalog</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font: 14px sans-serif;
        }

        .wrapper {
            width: 360px;
            padding: 20px;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <h2>Upload Medicine CSV</h2>
        <p>CSV Format: Name, Code, Group (No Header)</p>

        <?php if (!empty($message)) {
            echo '<div class="alert alert-info">' . $message . '</div>';
        } ?>

        <form class="form-horizontal" action="" method="post" name="upload_excel" enctype="multipart/form-data">
            <div class="form-group">
                <label>Select CSV File</label>
                <input type="file" name="file" id="file" class="form-control" accept=".csv" required>
            </div>
            <div class="form-group">
                <button type="submit" id="submit" name="import" class="btn btn-primary">Import</button>
                <a href="inventory.php" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</body>

</html>