<?php
require_once '../config/config.php';
require_once '../src/MedicineCatalog.php';

$database = new Database();
$db = $database->getConnection();
$catalog = new MedicineCatalog($db);

if (isset($_GET['term'])) {
    $stmt = $catalog->search($_GET['term']);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $jsonData = array();
    foreach ($rows as $row) {
        $data['value'] = $row['name'];
        $data['label'] = $row['name']; // Display in dropdown
        $data['code'] = $row['code']; // Extra data for callback
        array_push($jsonData, $data);
    }
    echo json_encode($jsonData);
}
?>