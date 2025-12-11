<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once '../config/config.php';
require_once '../src/Sales.php';

$database = new Database();
$db = $database->getConnection();
$sales = new Sales($db);

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['cart']) && !empty($input['cart'])) {
    $transaction_id = uniqid('TRX-');
    $customer_id = isset($input['customer_id']) && !empty($input['customer_id']) ? $input['customer_id'] : null;
    $total_discount = isset($input['discount']) ? floatval($input['discount']) : 0;
    $payment_method = isset($input['payment_method']) ? $input['payment_method'] : 'Cash';

    $result = $sales->createTransaction($input['cart'], $customer_id, $transaction_id, $total_discount, $payment_method);

    if ($result === true) {
        echo json_encode(['status' => 'success', 'transaction_id' => $transaction_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => $result]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Cart is empty']);
}
?>