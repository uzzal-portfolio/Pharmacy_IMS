<?php
require_once '../config/config.php';
require_once '../src/Customer.php';

$database = new Database();
$db = $database->getConnection();
$customer = new Customer($db);

if (isset($_GET['term'])) {
    $stmt = $customer->searchCustomers($_GET['term']);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $jsonData = array();
    foreach ($rows as $row) {
        // jQuery UI Autocomplete source format
        $jsonData[] = array(
            'id' => $row['id'],
            'value' => $row['name'], // Value to show in input
            'label' => $row['name'] . ' (' . ($row['phone'] ? $row['phone'] : 'No Phone') . ')'
        );
    }
    echo json_encode($jsonData);
}
?>
