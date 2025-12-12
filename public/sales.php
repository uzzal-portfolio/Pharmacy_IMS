<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION["role"] !== 'admin' && $_SESSION["role"] !== 'store_clerk' && $_SESSION["role"] !== 'report_viewer')){
    header("location: login.php");
    exit;
}

require_once '../config/config.php';
require_once '../src/Sales.php';

$database = new Database();
$db = $database->getConnection();
$sales = new Sales($db);

$stmt = $sales->read();
// Grouping by transaction might be better, but for now flat list or grouped manually
$sales_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Optional: Logic to group by transaction_id for display if desired, otherwise simple list
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales History</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 95%; margin: 0 auto; padding: 20px; }
        .navbar-nav .nav-link { padding-right: 1rem; padding-left: 1rem; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="wrapper">
        <h2 class="mb-4">Sales History</h2>
        
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Transaction ID</th>
                    <th>Customer</th>
                    <th>Medicine</th>
                    <th>Qty</th>
                    <th>Total Price</th>
                    <th>Discount (Amt)</th>
                    <th>Final Price</th>
                    <th>Payment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($sales_raw) > 0): ?>
                    <?php foreach($sales_raw as $sale): ?>
                        <tr>
                            <td><?php echo $sale['sale_date']; ?></td>
                            <td><?php echo isset($sale['transaction_id']) ? $sale['transaction_id'] : 'N/A'; ?></td>
                            <td><?php echo $sale['customer_name'] ? $sale['customer_name'] : 'Walk-in'; ?></td>
                            <td><?php echo $sale['medicine_name']; ?></td>
                            <td><?php echo $sale['quantity']; ?></td>
                            <td><?php echo $sale['total_price']; ?></td>
                            <td><?php echo $sale['discount']; ?></td>
                            <td><?php echo number_format($sale['total_price'] - $sale['discount'], 2); ?></td>
                            <td><?php echo $sale['payment_method']; ?></td>
                            <td>
                                <?php if(isset($sale['transaction_id'])): ?>
                                    <a href="#" class="btn btn-sm btn-info" onclick="window.open('receipt.php?transaction_id=<?php echo $sale['transaction_id']; ?>', '_blank', 'width=400,height=600'); return false;">Print</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6">No sales records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
