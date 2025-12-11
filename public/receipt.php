<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once '../config/config.php';
require_once '../src/Sales.php';

$transaction_id = isset($_GET['transaction_id']) ? $_GET['transaction_id'] : die('Transaction ID not found.');

$database = new Database();
$db = $database->getConnection();
$sales = new Sales($db);

$stmt = $sales->readByTransactionId($transaction_id);
if ($stmt->rowCount() == 0)
    die('Transaction not found.');

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate Totals
$grand_total_price = 0; // Subtotal (Price * Qty)
$total_discount = 0; // Total Discount Amount
$final_total = 0; // Net to Pay

$sale_date = $items[0]['sale_date'];
$customer_name = $items[0]['customer_name'] ? $items[0]['customer_name'] : 'Walk-in';
$customer_phone = isset($items[0]['customer_phone']) ? $items[0]['customer_phone'] : '';
$payment_method = $items[0]['payment_method'];

foreach ($items as $item) {
    $grand_total_price += $item['total_price'];
    $total_discount += $item['discount'];
}
$final_total = $grand_total_price - $total_discount;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Receipt #<?php echo $transaction_id; ?></title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            width: 300px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .store-name {
            font-size: 20px;
            font-weight: bold;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
        }

        .totals {
            margin-top: 15px;
            text-align: right;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="header">
        <div class="store-name">Pharmacy IMS</div>
        <div>Dhaka, Bangladesh</div>
        <div>Tel: +880123456789</div>
    </div>

    <div class="divider"></div>

    <div>TRX ID: <?php echo $transaction_id; ?></div>
    <div>Date: <?php echo $sale_date; ?></div>
    <div>Customer: <?php echo $customer_name; ?> <?php echo $customer_phone ? "($customer_phone)" : ""; ?></div>
    <div>Payment: <?php echo $payment_method; ?></div>

    <div class="divider"></div>

    <div style="font-weight: bold;">
        <span style="display:inline-block; width: 140px;">Item</span>
        <span style="display:inline-block; width: 30px;">Qty</span>
        <span style="float:right;">Price</span>
    </div>

    <div class="divider"></div>

    <?php foreach ($items as $item): ?>
        <div class="item-row">
            <span
                style="width: 140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo $item['medicine_name']; ?></span>
            <span style="width: 30px; text-align: center;"><?php echo $item['quantity']; ?></span>
            <span><?php echo number_format($item['total_price'], 2); ?></span>
        </div>
    <?php endforeach; ?>

    <div class="divider"></div>

    <div class="totals">
        <div>Subtotal: <?php echo number_format($grand_total_price, 2); ?></div>
        <div>Discount: -<?php echo number_format($total_discount, 2); ?></div>
        <div style="font-weight: bold; font-size: 16px;">Net Total: <?php echo number_format($final_total, 2); ?></div>
    </div>

    <div class="footer">
        Thank you for your purchase!<br>
        Please come again.
    </div>

    <button class="no-print" onclick="window.print()" style="margin-top: 20px; width: 100%; padding: 10px;">Print
        Again</button>
</body>

</html>