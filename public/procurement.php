<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check user role for access control
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'store_clerk') {
    header("location: welcome.php"); // Redirect to welcome page if not authorized
    exit;
}

require_once '../config/config.php';
require_once '../src/Procurement.php';

$procurement = new Procurement($link);

$medicine_name = $quantity = "";
$medicine_name_err = $quantity_err = "";

// Process request form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'update_status') {
        $request_id = $_POST['request_id'];
        $status = $_POST['status'];

        if ($procurement->updateRequestStatus($request_id, $status)) {
            echo "<script>alert('Request status updated successfully!'); window.location.href='procurement.php';</script>";
        } else {
            echo "<script>alert('Error: Could not update request status.'); window.location.href='procurement.php';</script>";
        }
    } else {
        // Validate medicine name
        if (empty(trim($_POST["medicine_name"]))) {
            $medicine_name_err = "Please enter a medicine name.";
        } else {
            $medicine_name = trim($_POST["medicine_name"]);
        }

        // Validate quantity
        if (empty(trim($_POST["quantity"]))) {
            $quantity_err = "Please enter the quantity.";
        } elseif (!ctype_digit(trim($_POST["quantity"]))) {
            $quantity_err = "Quantity must be an integer.";
        } else {
            $quantity = trim($_POST["quantity"]);
        }

        // Check input errors before inserting in database
        if (empty($medicine_name_err) && empty($quantity_err)) {
            if ($procurement->requestPurchase($medicine_name, $quantity)) {
                echo "<script>alert('Purchase request submitted successfully!'); window.location.href='procurement.php';</script>";
            } else {
                echo "<script>alert('Error: Could not submit purchase request.'); window.location.href='procurement.php';</script>";
            }
        }
    }
}

$all_requests = $procurement->getAllRequests();

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Procurement Management</title>
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
                    <li class="nav-item active">
                        <a class="nav-link" href="procurement.php">Procurement <span class="sr-only">(current)</span></a>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'report_viewer'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php">Reports</a>
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
        <h2>Procurement Management</h2>

        <!-- Request New Purchase Form -->
        <div class="mb-4">
            <h3>Request New Purchase</h3>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Medicine Name</label>
                        <input type="text" name="medicine_name" class="form-control <?php echo (!empty($medicine_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $medicine_name; ?>">
                        <span class="invalid-feedback"><?php echo $medicine_name_err; ?></span>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Quantity</label>
                        <input type="number" name="quantity" class="form-control <?php echo (!empty($quantity_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $quantity; ?>" min="1">
                        <span class="invalid-feedback"><?php echo $quantity_err; ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Submit Request">
                    <input type="reset" class="btn btn-secondary ml-2" value="Reset">
                </div>
            </form>
        </div>

        <!-- All Procurement Requests -->
        <h3>All Procurement Requests</h3>
        <?php if (!empty($all_requests)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Medicine Name</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Request Date</th>
                            <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'store_clerk'): ?>
                                <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_requests as $request): ?>
                            <tr>
                                <td><?php echo $request['id']; ?></td>
                                <td><?php echo htmlspecialchars($request['medicine_name']); ?></td>
                                <td><?php echo $request['quantity']; ?></td>
                                <td><?php echo htmlspecialchars($request['status']); ?></td>
                                <td><?php echo $request['request_date']; ?></td>
                                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'store_clerk'): ?>
                                    <td>
                                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="d-inline">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                            <select name="status" class="form-control form-control-sm d-inline w-auto" onchange="this.form.submit()">
                                                <option value="pending" <?php echo ($request['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                                <option value="approved" <?php echo ($request['status'] == 'approved') ? 'selected' : ''; ?>>Approved</option>
                                                <option value="rejected" <?php echo ($request['status'] == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                                            </select>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No procurement requests found.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>