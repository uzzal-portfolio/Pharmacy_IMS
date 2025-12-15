<?php
require_once '../config/config.php';
require_once '../src/Inventory.php';

$database = new Database();
$db = $database->getConnection();
$inventory = new Inventory($db);

if (isset($_GET['term'])) {
    $stmt = $inventory->searchGroups($_GET['term']);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $jsonData = array();
    foreach ($rows as $row) {
        if (!empty($row['medicine_group'])) {
             // jQuery UI Autocomplete expects label/value or just a string array
             $jsonData[] = $row['medicine_group']; 
        }
    }
    echo json_encode($jsonData);
}
?>
