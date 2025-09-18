<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check user role for access control
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'report_viewer') {
    header("location: welcome.php"); // Redirect to welcome page if not authorized
    exit;
}

require_once '../config/config.php';
require_once '../src/Report.php';

$report_obj = new Report($link);

$report_type = $_GET['type'] ?? 'stock';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

$report_data = [];
$report_title = "";

switch ($report_type) {
    case 'stock':
        $report_data = $report_obj->getStockReport();
        $report_title = "Stock Report";
        break;
    case 'expiry':
        $report_data = $report_obj->getExpiryReport($start_date, $end_date);
        $report_title = "Expiry Report";
        break;
    case 'sales':
        if (!empty($start_date) && !empty($end_date)) {
            $report_data = $report_obj->getSalesReport($start_date, $end_date);
            $report_title = "Sales Report from " . htmlspecialchars($start_date) . " to " . htmlspecialchars($end_date);
        } else {
            $report_title = "Sales Report (Please select a date range)";
        }
        break;
    case 'procurement':
        if (!empty($start_date) && !empty($end_date)) {
            $report_data = $report_obj->getProcurementReport($start_date, $end_date);
            $report_title = "Procurement Report from " . htmlspecialchars($start_date) . " to " . htmlspecialchars($end_date);
        } else {
            $report_title = "Procurement Report (Please select a date range)";
        }
        break;
    case 'customer':
        $report_data = $report_obj->getCustomerReport();
        $report_title = "Customer Report";
        break;
    case 'procurement_request':
        if (!empty($start_date) && !empty($end_date)) {
            $report_data = $report_obj->getProcurementReport($start_date, $end_date);
            $report_title = "Procurement Request Report from " . htmlspecialchars($start_date) . " to " . htmlspecialchars($end_date);
        } else {
            $report_title = "Procurement Request Report (Please select a date range)";
        }
        break;
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 90%; margin: 0 auto; padding: 20px; }
        .table-responsive { margin-top: 20px; }
        .navbar-nav .nav-link {
            padding-right: 1rem;
            padding-left: 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Pharmacy IMS</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="welcome.php">Home</a>
                </li>
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'store_clerk'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="inventory.php">Inventory</a>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'store_clerk' || $_SESSION['role'] == 'online_customer'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="sales.php">Sales</a>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'store_clerk'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="procurement.php">Procurement</a>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'report_viewer'): ?>
                    <li class="nav-item active">
                        <a class="nav-link" href="reports.php">Reports <span class="sr-only">(current)</span></a>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="user_management.php">User Management</a>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'online_customer'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="customer_database.php">Customer Database</a>
                    </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <span class="navbar-text mr-3">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b> (<?php echo htmlspecialchars($_SESSION["role"]); ?>)</span>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="btn btn-danger">Sign Out</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="wrapper">
        <h2>Reports</h2>

        <div class="mb-4">
            <h3>Select Report Type</h3>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" class="form-inline">
                <div class="form-group mr-3">
                    <label for="report_type" class="mr-2">Report Type:</label>
                    <select name="type" id="report_type" class="form-control">
                        <option value="stock" <?php echo ($report_type == 'stock') ? 'selected' : ''; ?>>Stock Report</option>
                        <option value="expiry" <?php echo ($report_type == 'expiry') ? 'selected' : ''; ?>>Expiry Report</option>
                        <option value="sales" <?php echo ($report_type == 'sales') ? 'selected' : ''; ?>>Sales Report</option>
                        <option value="procurement" <?php echo ($report_type == 'procurement') ? 'selected' : ''; ?>>Procurement Report</option>
                        <option value="customer" <?php echo ($report_type == 'customer') ? 'selected' : ''; ?>>Customer Report</option>
                        <option value="procurement_request" <?php echo ($report_type == 'procurement_request') ? 'selected' : ''; ?>>Procurement Request Report</option>
                    </select>
                </div>
                <div class="form-group mr-3" id="date_range_fields" style="display: <?php echo ($report_type == 'sales' || $report_type == 'expiry' || $report_type == 'procurement' || $report_type == 'procurement_request') ? 'block' : 'none'; ?>;">
                    <label for="start_date" class="mr-2">Start Date:</label>
                    <input type="date" name="start_date" id="start_date" class="form-control mr-2" value="<?php echo htmlspecialchars($start_date); ?>">
                    <label for="end_date" class="mr-2">End Date:</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo htmlspecialchars($end_date); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Generate Report</button>
            </form>
        </div>

        <h3><?php echo $report_title; ?></h3>
        <?php if (!empty($report_data)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <?php if ($report_type == 'stock'): ?>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Quantity</th>
                                <th>Expiry Date</th>
                                <th>Location</th>
                            <?php elseif ($report_type == 'expiry'): ?>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Quantity</th>
                                <th>Expiry Date</th>
                                <th>Location</th>
                            <?php elseif ($report_type == 'sales'): ?>
                                <th>Sale ID</th>
                                <th>Medicine Name</th>
                                <th>Customer Name</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                                <th>Sale Date</th>
                            <?php elseif ($report_type == 'procurement'): ?>
                                <th>Request ID</th>
                                <th>Medicine Name</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Request Date</th>
                            <?php elseif ($report_type == 'customer'): ?>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Registered On</th>
                            <?php elseif ($report_type == 'procurement_request'): ?>
                                <th>Request ID</th>
                                <th>Medicine Name</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Request Date</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report_data as $row): ?>
                            <tr>
                                <?php if ($report_type == 'stock' || $report_type == 'expiry'): ?>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['code']); ?></td>
                                    <td><?php echo $row['quantity']; ?></td>
                                    <td><?php echo $row['expiry_date']; ?></td>
                                    <td><?php echo htmlspecialchars($row['location_code']); ?></td>
                                <?php elseif ($report_type == 'sales'): ?>
                                    <td><?php echo $row['sale_id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['medicine_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['customer_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo $row['quantity']; ?></td>
                                    <td><?php echo $row['total_price']; ?></td>
                                    <td><?php echo $row['sale_date']; ?></td>
                                <?php elseif ($report_type == 'procurement'): ?>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['medicine_name']); ?></td>
                                    <td><?php echo $row['quantity']; ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td><?php echo $row['request_date']; ?></td>
                                <?php elseif ($report_type == 'customer'): ?>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo $row['created_at']; ?></td>
                                <?php elseif ($report_type == 'procurement_request'): ?>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['medicine_name']); ?></td>
                                    <td><?php echo $row['quantity']; ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td><?php echo $row['request_date']; ?></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No data available for this report type or date range.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function(){
            $('#report_type').change(function(){
                var selectedReport = $(this).val();
                if (selectedReport === 'sales' || selectedReport === 'expiry' || selectedReport === 'procurement' || selectedReport === 'procurement_request') {
                    $('#date_range_fields').show();
                } else {
                    $('#date_range_fields').hide();
                }
            });
        });
    </script>
</body>
</html>