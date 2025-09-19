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
require_once '../src/Inventory.php';

$inventory = new Inventory($link);

$name = $code = $expiry_date = $location_code = "";
$quantity = 0; // Default quantity for new registration
$name_err = $code_err = $expiry_date_err = $location_code_err = "";

$medicine_id_to_add_stock = $quantity_to_add_stock = "";
$medicine_id_to_add_stock_err = $quantity_to_add_stock_err = "";

$search_results = [];
$search_term = "";

// Handle medicine search
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_term = trim($_GET['search']);
    $search_results = $inventory->searchMedicines($search_term);
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'register_medicine') {
            // Validate name
            if (empty(trim($_POST["name"]))) {
                $name_err = "Please enter a medicine name.";
            } else {
                $name = trim($_POST["name"]);
            }

            // Validate code
            if (empty(trim($_POST["code"]))) {
                $code_err = "Please enter a medicine code.";
            } else {
                $code = trim($_POST["code"]);
            }

            // For new medicine registration, quantity, expiry_date, and location_code are not directly set here.
            // They will be added via the 'Add Stock' functionality.
            // Initialize them to default/null values for the addMedicine call.
            $quantity = 0;
            $expiry_date = NULL;
            $location_code = NULL;

            // Check input errors before inserting in database
            if (empty($name_err) && empty($code_err)) {
                if ($inventory->addMedicine($name, $code, $quantity, $expiry_date, $location_code)) {
                    echo "<script>alert('Medicine registered successfully!'); window.location.href='inventory.php';</script>";
                } else {
                    echo "<script>alert('Error: Could not register medicine. It might already exist.'); window.location.href='inventory.php';</script>";
                }
            }
        } elseif ($_POST['action'] == 'add_stock') {
            // Validate medicine ID
            if (empty(trim($_POST["medicine_id_to_add_stock"]))) {
                $medicine_id_to_add_stock_err = "Medicine ID is missing.";
            } else {
                $medicine_id_to_add_stock = trim($_POST["medicine_id_to_add_stock"]);
            }

            // Validate quantity to add
            if (empty(trim($_POST["quantity_to_add_stock"]))) {
                $quantity_to_add_stock_err = "Please enter quantity to add.";
            } elseif (!ctype_digit(trim($_POST["quantity_to_add_stock"]))) {
                $quantity_to_add_stock_err = "Quantity must be an integer.";
            } elseif (trim($_POST["quantity_to_add_stock"]) <= 0) {
                $quantity_to_add_stock_err = "Quantity to add must be greater than 0.";
            } else {
                $quantity_to_add_stock = trim($_POST["quantity_to_add_stock"]);
            }

            if (empty($medicine_id_to_add_stock_err) && empty($quantity_to_add_stock_err)) {
                $existing_medicine = $inventory->getMedicineById($medicine_id_to_add_stock);
                if ($existing_medicine) {
                    $new_quantity = $existing_medicine['quantity'] + $quantity_to_add_stock;
                    $new_expiry_date = trim($_POST['expiry_date_to_add_stock']);
                    $new_location_code = trim($_POST['location_code_to_add_stock']);

                    if ($inventory->updateMedicine(
                        $medicine_id_to_add_stock,
                        $existing_medicine['name'],
                        $existing_medicine['code'],
                        $new_quantity,
                        $new_expiry_date,
                        $new_location_code
                    )) {
                        echo "<script>alert('Stock updated successfully!'); window.location.href='inventory.php';</script>";
                    } else {
                        echo "<script>alert('Error: Could not update stock.'); window.location.href='inventory.php';</script>";
                    }
                } else {
                    echo "<script>alert('Error: Medicine not found for stock update.'); window.location.href='inventory.php';</script>";
                }
            }
        } elseif ($_POST['action'] == 'update_medicine') {
            $medicine_id = $_POST['medicine_id'];
            $name = trim($_POST['name']);
            $code = trim($_POST['code']);
            $quantity = trim($_POST['quantity']);
            $expiry_date = trim($_POST['expiry_date']);
            $location_code = trim($_POST['location_code']);

            // Basic validation
            if (empty($name)) { $name_err = "Please enter a medicine name."; }
            if (empty($code)) { $code_err = "Please enter a medicine code."; }
            if (empty($quantity) || !ctype_digit($quantity)) { $quantity_err = "Quantity must be an integer."; }
            if (empty($expiry_date)) { $expiry_date_err = "Please enter an expiry date."; }
            if (empty($location_code)) { $location_code_err = "Please enter a location code."; }

            if (empty($name_err) && empty($code_err) && empty($quantity_err) && empty($expiry_date_err) && empty($location_code_err)) {
                if ($inventory->updateMedicine($medicine_id, $name, $code, $quantity, $expiry_date, $location_code)) {
                    echo "<script>alert('Medicine updated successfully!'); window.location.href='inventory.php';</script>";
                } else {
                    echo "<script>alert('Error: Could not update medicine.'); window.location.href='inventory.php';</script>";
                }
            }
        } elseif ($_POST['action'] == 'delete_medicine') {
            $medicine_id = $_POST['medicine_id'];
            if ($inventory->deleteMedicine($medicine_id)) {
                echo "<script>alert('Medicine deleted successfully!'); window.location.href='inventory.php';</script>";
            } else {
                echo "<script>alert('Error: Could not delete medicine.'); window.location.href='inventory.php';</script>";
            }
        }
    }
}

$medicines = $inventory->getAllMedicines();

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management</title>
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
                <li class="nav-item active">
                    <a class="nav-link" href="inventory.php">Inventory <span class="sr-only">(current)</span></a>
                </li>
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
        <h2>Inventory Management</h2>

        <!-- Medicine Registration -->
        <div class="mb-4">
            <h3>Register New Medicine</h3>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="action" value="register_medicine">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Medicine Name</label>
                        <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>" required>
                        <span class="invalid-feedback"><?php echo $name_err; ?></span>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Medicine Code</label>
                        <input type="text" name="code" class="form-control <?php echo (!empty($code_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $code; ?>" required>
                        <span class="invalid-feedback"><?php echo $code_err; ?></span>
                    </div>
                </div>
                
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Register Medicine">
                    <input type="reset" class="btn btn-secondary ml-2" value="Reset">
                </div>
            </form>
        </div>

        <hr>

        <!-- Medicine Entry (Add Stock) -->
        <div class="mb-4">
            <h3>Medicine Entry (Add Stock)</h3>
            <p>Search for an existing medicine to add stock.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" class="form-inline mb-3">
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
                                <th>Current Quantity</th>
                                <th>Expiry Date</th>
                                <th>Location</th>
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
                                    <td><?php echo $medicine['expiry_date']; ?></td>
                                    <td><?php echo htmlspecialchars($medicine['location_code']); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-success add-stock-btn" 
                                                data-toggle="modal" data-target="#addStockModal"
                                                data-id="<?php echo $medicine['id']; ?>" 
                                                data-name="<?php echo htmlspecialchars($medicine['name']); ?>" 
                                                data-code="<?php echo htmlspecialchars($medicine['code']); ?>">
                                            Add Stock
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

        <hr>

        <div class="table-responsive">
            <h3>Current Inventory Overview</h3>
            <?php if (!empty($medicines)): ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Quantity</th>
                            <th>Expiry Date</th>
                            <th>Location</th>
                            <th>Added On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($medicines as $medicine): ?>
                            <tr>
                                <td><?php echo $medicine['id']; ?></td>
                                <td><?php echo htmlspecialchars($medicine['name']); ?></td>
                                <td><?php echo htmlspecialchars($medicine['code']); ?></td>
                                <td><?php echo $medicine['quantity']; ?></td>
                                <td><?php echo $medicine['expiry_date']; ?></td>
                                <td><?php echo htmlspecialchars($medicine['location_code']); ?></td>
                                <td><?php echo $medicine['created_at']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info view-medicine" 
                                            data-toggle="modal" data-target="#editMedicineModal"
                                            data-id="<?php echo $medicine['id']; ?>" 
                                            data-name="<?php echo htmlspecialchars($medicine['name']); ?>" 
                                            data-code="<?php echo htmlspecialchars($medicine['code']); ?>"
                                            data-quantity="<?php echo $medicine['quantity']; ?>"
                                            data-expiry_date="<?php echo $medicine['expiry_date']; ?>"
                                            data-location_code="<?php echo htmlspecialchars($medicine['location_code']); ?>"
                                            data-readonly="true">View</button>
                                    <button class="btn btn-sm btn-warning edit-medicine" 
                                            data-toggle="modal" data-target="#editMedicineModal"
                                            data-id="<?php echo $medicine['id']; ?>" 
                                            data-name="<?php echo htmlspecialchars($medicine['name']); ?>" 
                                            data-code="<?php echo htmlspecialchars($medicine['code']); ?>"
                                            data-quantity="<?php echo $medicine['quantity']; ?>"
                                            data-expiry_date="<?php echo $medicine['expiry_date']; ?>"
                                            data-location_code="<?php echo htmlspecialchars($medicine['location_code']); ?>"
                                            data-readonly="false">Edit</button>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this medicine?');">
                                        <input type="hidden" name="action" value="delete_medicine">
                                        <input type="hidden" name="medicine_id" value="<?php echo $medicine['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No medicines found in the inventory.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Stock Modal -->
    <div class="modal fade" id="addStockModal" tabindex="-1" role="dialog" aria-labelledby="addStockModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStockModalLabel">Add Stock to <span id="modal_medicine_name"></span> (<span id="modal_medicine_code"></span>)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_stock">
                        <input type="hidden" name="medicine_id_to_add_stock" id="modal_medicine_id">
                        <div class="form-group">
                            <label>Quantity to Add</label>
                            <input type="number" name="quantity_to_add_stock" id="modal_quantity_to_add_stock" class="form-control" min="1" required>
                        </div>
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <input type="date" name="expiry_date_to_add_stock" id="modal_expiry_date_to_add_stock" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Location Code</label>
                            <input type="text" name="location_code_to_add_stock" id="modal_location_code_to_add_stock" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Medicine Modal -->
    <div class="modal fade" id="editMedicineModal" tabindex="-1" role="dialog" aria-labelledby="editMedicineModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMedicineModalLabel">Edit Medicine</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_medicine">
                        <input type="hidden" name="medicine_id" id="edit_medicine_id">
                        <div class="form-group">
                            <label>Medicine Name</label>
                            <input type="text" name="name" id="edit_medicine_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Medicine Code</label>
                            <input type="text" name="code" id="edit_medicine_code" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" name="quantity" id="edit_medicine_quantity" class="form-control" min="0" required>
                        </div>
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <input type="date" name="expiry_date" id="edit_medicine_expiry_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Location Code</label>
                            <input type="text" name="location_code" id="edit_medicine_location_code" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="edit_medicine_submit_btn">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.add-stock-btn').on('click', function(){
                var medicineId = $(this).data('id');
                var medicineName = $(this).data('name');
                var medicineCode = $(this).data('code');
                
                $('#modal_medicine_id').val(medicineId);
                $('#modal_medicine_name').text(medicineName);
                $('#modal_medicine_code').text(medicineCode);
                $('#modal_quantity_to_add_stock').val(''); // Clear previous quantity
            });

            $('.edit-medicine, .view-medicine').on('click', function(){
                var isReadOnly = $(this).data('readonly');
                var medicineId = $(this).data('id');
                var medicineName = $(this).data('name');
                var medicineCode = $(this).data('code');
                var quantity = $(this).data('quantity');
                var expiryDate = $(this).data('expiry_date');
                var locationCode = $(this).data('location_code');
                
                $('#edit_medicine_id').val(medicineId);
                $('#edit_medicine_name').val(medicineName);
                $('#edit_medicine_code').val(medicineCode);
                $('#edit_medicine_quantity').val(quantity);
                $('#edit_medicine_expiry_date').val(expiryDate);
                $('#edit_medicine_location_code').val(locationCode);

                // Set readonly state for inputs
                $('#editMedicineModal input, #editMedicineModal select').prop('readonly', isReadOnly);
                
                // Hide/show submit button based on readonly state
                if (isReadOnly) {
                    $('#edit_medicine_submit_btn').hide();
                    $('#editMedicineModalLabel').text('View Medicine Details');
                } else {
                    $('#edit_medicine_submit_btn').show();
                    $('#editMedicineModalLabel').text('Edit Medicine');
                }
            });
        });
    </script>
</body>
</html>