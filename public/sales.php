<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check user role for access control
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'store_clerk' && $_SESSION['role'] !== 'online_customer') {
    header("location: welcome.php"); // Redirect to welcome page if not authorized
    exit;
}

require_once '../config/config.php';
require_once '../src/Sales.php';
require_once '../src/Inventory.php'; // Needed to get medicine details for sales

$sales = new Sales($link);
$inventory = new Inventory($link);

$medicine_id = $customer_id = $quantity = $total_price = "";
$medicine_id_err = $quantity_err = $total_price_err = "";
$search_results = [];
$search_term = "";

// Handle medicine search
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_term = trim($_GET['search']);
    $search_results = $sales->searchMedicines($search_term);
}

// Process sale form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate medicine ID
    if (empty(trim($_POST["medicine_id"]))) {
        $medicine_id_err = "Please select a medicine.";
    } else {
        $medicine_id = trim($_POST["medicine_id"]);
    }

    // Validate quantity
    if (empty(trim($_POST["quantity"]))) {
        $quantity_err = "Please enter the quantity.";
    } elseif (!ctype_digit(trim($_POST["quantity"]))) {
        $quantity_err = "Quantity must be an integer.";
    } else {
        $quantity = trim($_POST["quantity"]);
    }

    // Validate total price
    if (empty(trim($_POST["total_price"]))) {
        $total_price_err = "Please enter the total price.";
    } elseif (!is_numeric(trim($_POST["total_price"]))) {
        $total_price_err = "Total price must be a number.";
    } else {
        $total_price = trim($_POST["total_price"]);
    }

    // Customer ID is optional for now, will be handled by customer module later
    $customer_id = !empty(trim($_POST["customer_id"])) ? trim($_POST["customer_id"]) : null;

    // Check input errors before recording sale
    if (empty($medicine_id_err) && empty($quantity_err) && empty($total_price_err)) {
        if ($sales->recordSale($medicine_id, $customer_id, $quantity, $total_price)) {
            echo "<script>alert('Sale recorded successfully!'); window.location.href='sales.php';</script>";
        } else {
            echo "<script>alert('Error: Could not record sale. Check stock or input.'); window.location.href='sales.php';</script>";
        }
    }
}

$all_sales = $sales->getAllSales();

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales System</title>
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
                    <li class="nav-item active">
                        <a class="nav-link" href="sales.php">Sales <span class="sr-only">(current)</span></a>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'store_clerk'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="procurement.php">Procurement</a>
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
        <h2>Sales System</h2>

        <!-- Medicine Search -->
        <div class="mb-4">
            <h3>Search Medicines</h3>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" class="form-inline">
                <input type="text" name="search" class="form-control mr-sm-2" placeholder="Search by name or code" value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>

            <?php if (!empty($search_results)): ?>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Available Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($search_results as $medicine): ?>
                                <tr>
                                    <td><?php echo $medicine['id']; ?></td>
                                    <td><?php echo htmlspecialchars($medicine['name']); ?></td>
                                    <td><?php echo htmlspecialchars($medicine['code']); ?></td>
                                    <td><?php echo $medicine['quantity']; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-success add-to-sale" 
                                                data-id="<?php echo $medicine['id']; ?>" 
                                                data-name="<?php echo htmlspecialchars($medicine['name']); ?>" 
                                                data-code="<?php echo htmlspecialchars($medicine['code']); ?>">
                                            Add to Sale
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif (isset($_GET['search']) && empty($search_results)): ?>
                <p class="mt-3">No medicines found matching "<?php echo htmlspecialchars($search_term); ?>".</p>
            <?php endif; ?>
        </div>

        <!-- Record New Sale Form -->
        <div class="mb-4">
            <h3>Record New Sale</h3>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Medicine (ID - Name - Code)</label>
                        <input type="text" name="medicine_display" id="medicine_display" class="form-control" readonly>
                        <input type="hidden" name="medicine_id" id="medicine_id" value="<?php echo $medicine_id; ?>">
                        <span class="invalid-feedback" style="display:block;"><?php echo $medicine_id_err; ?></span>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Quantity</label>
                        <input type="number" name="quantity" id="quantity" class="form-control <?php echo (!empty($quantity_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $quantity; ?>" min="1">
                        <span class="invalid-feedback"><?php echo $quantity_err; ?></span>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Total Price</label>
                        <input type="text" name="total_price" id="total_price" class="form-control <?php echo (!empty($total_price_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $total_price; ?>">
                        <span class="invalid-feedback"><?php echo $total_price_err; ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Customer ID (Optional)</label>
                    <input type="text" name="customer_id" class="form-control" value="<?php echo $customer_id; ?>">
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Record Sale">
                    <input type="reset" class="btn btn-secondary ml-2" value="Reset">
                </div>
            </form>
        </div>

        <!-- Recent Sales -->
        <h3>Recent Sales</h3>
        <?php if (!empty($all_sales)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Sale ID</th>
                            <th>Medicine Name</th>
                            <th>Customer Name</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                            <th>Sale Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_sales as $sale): ?>
                            <tr>
                                <td><?php echo $sale['id']; ?></td>
                                <td><?php echo htmlspecialchars($sale['medicine_name']); ?></td>
                                <td><?php echo htmlspecialchars($sale['customer_name'] ?? 'N/A'); ?></td>
                                <td><?php echo $sale['quantity']; ?></td>
                                <td><?php echo $sale['total_price']; ?></td>
                                <td><?php echo $sale['sale_date']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No sales recorded yet.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.add-to-sale').on('click', function(){
                var medicineId = $(this).data('id');
                var medicineName = $(this).data('name');
                var medicineCode = $(this).data('code');
                
                $('#medicine_id').val(medicineId);
                $('#medicine_display').val(medicineId + ' - ' + medicineName + ' - ' + medicineCode);
            });
        });
    </script>
</body>
</html>