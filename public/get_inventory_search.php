<?php
require_once '../config/config.php';
require_once '../src/Inventory.php';

$database = new Database();
$db = $database->getConnection();
$inventory = new Inventory($db);

if (isset($_GET['term'])) {
    $stmt = $inventory->search($_GET['term']);
    $medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $jsonData = array();
    foreach ($medicines as $medicine) {
        $data['id'] = $medicine['id'];
        $data['value'] = $medicine['name']; // For autocomplete display
        $data['label'] = $medicine['name'] . " (" . $medicine['code'] . ") - Stock: " . $medicine['quantity'];
        $data['code'] = $medicine['code'];
        $data['price'] = $medicine['price'];
        $data['stock'] = $medicine['quantity'];
        $data['expiry'] = $medicine['expiry_date'];
        array_push($jsonData, $data);
    }
    echo json_encode($jsonData);
}
?>