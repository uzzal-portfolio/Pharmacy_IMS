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
        $data['value'] = (isset($_GET['type']) && $_GET['type'] == 'code') ? $row['code'] : $row['name'];
        $data['label'] = $row['name'] . " (" . $row['medicine_group'] . ")"; // Display name + group
        if (isset($_GET['type']) && $_GET['type'] == 'code') {
             $data['label'] = $row['code'] . " - " . $row['name'];
        }
        $data['name'] = $row['name']; // Explicitly send name
        $data['code'] = $row['code']; // Extra data for callback
        $data['medicine_group'] = $row['medicine_group'];
        array_push($jsonData, $data);
    }
    echo json_encode($jsonData);
}
?>