<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo '<p class="text-danger">Unauthorized access.</p>';
    exit;
}

// Check user role for access control
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'online_customer') {
    echo '<p class="text-danger">Unauthorized access.</p>';
    exit;
}

require_once '../config/config.php';
require_once '../src/Customer.php';

$customer_obj = new Customer($link);

if (isset($_GET['customer_id']) && !empty(trim($_GET['customer_id']))) {
    $customer_id = trim($_GET['customer_id']);
    $purchase_history = $customer_obj->getCustomerPurchaseHistory($customer_id);

    if (!empty($purchase_history)) {
        echo '<div class="table-responsive">';
        echo '<table class="table table-bordered table-striped table-sm">';
        echo '<thead><tr><th>Sale ID</th><th>Medicine Name</th><th>Quantity</th><th>Total Price</th><th>Sale Date</th></tr></thead>';
        echo '<tbody>';
        foreach ($purchase_history as $sale) {
            echo '<tr>';
            echo '<td>' . $sale['sale_id'] . '</td>';
            echo '<td>' . htmlspecialchars($sale['medicine_name']) . '</td>';
            echo '<td>' . $sale['quantity'] . '</td>';
            echo '<td>' . $sale['total_price'] . '</td>';
            echo '<td>' . $sale['sale_date'] . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    } else {
        echo '<p>No purchase history found for this customer.</p>';
    }
} else {
    echo '<p class="text-danger">Invalid customer ID.</p>';
}

mysqli_close($link);
?>