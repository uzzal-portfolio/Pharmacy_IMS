<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check user role for access control
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'online_customer') {
    header("location: welcome.php"); // Redirect to welcome page if not authorized
    exit;
}

require_once '../config/config.php';
require_once '../src/Customer.php';

$customer_obj = new Customer($link);

$name = $phone = $email = "";
$name_err = $phone_err = $email_err = "";

// Handle Add/Edit Customer Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add_customer') {
            // Validate name
            if (empty(trim($_POST["name"]))) {
                $name_err = "Please enter a customer name.";
            } else {
                $name = trim($_POST["name"]);
            }

            // Validate phone (optional)
            $phone = trim($_POST["phone"]);

            // Validate email (optional)
            $email = trim($_POST["email"]);

            // Check input errors before inserting in database
            if (empty($name_err)) {
                if ($customer_obj->addCustomer($name, $phone, $email)) {
                    echo "<script>alert('Customer added successfully!'); window.location.href='customer_database.php';</script>";
                } else {
                    echo "<script>alert('Error: Could not add customer.'); window.location.href='customer_database.php';</script>";
                }
            }
        } elseif ($_POST['action'] == 'edit_customer') {
            $customer_id = $_POST['customer_id'];
            $name = trim($_POST['name']);
            $phone = trim($_POST['phone']);
            $email = trim($_POST['email']);

            // Basic validation for name
            if (empty($name)) {
                $name_err = "Please enter a customer name.";
            }

            if (empty($name_err)) {
                if ($customer_obj->updateCustomer($customer_id, $name, $phone, $email)) {
                    echo "<script>alert('Customer updated successfully!'); window.location.href='customer_database.php';</script>";
                } else {
                    echo "<script>alert('Error: Could not update customer.'); window.location.href='customer_database.php';</script>";
                }
            }
        } elseif ($_POST['action'] == 'delete_customer') {
            $customer_id = $_POST['customer_id'];
            if ($customer_obj->deleteCustomer($customer_id)) {
                echo "<script>alert('Customer deleted successfully!'); window.location.href='customer_database.php';</script>";
            } else {
                echo "<script>alert('Error: Could not delete customer.'); window.location.href='customer_database.php';</script>";
            }
        }
    }
}

$all_customers = $customer_obj->getAllCustomers();

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Database</title>
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
                    <li class="nav-item active">
                        <a class="nav-link" href="customer_database.php">Customer Database <span class="sr-only">(current)</span></a>
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
        <h2>Customer Database</h2>

        <!-- Add New Customer Form -->
        <div class="mb-4">
            <h3>Add New Customer</h3>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="action" value="add_customer">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Customer Name</label>
                        <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>" required>
                        <span class="invalid-feedback"><?php echo $name_err; ?></span>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $phone; ?>">
                        <span class="invalid-feedback"><?php echo $phone_err; ?></span>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                        <span class="invalid-feedback"><?php echo $email_err; ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Add Customer">
                    <input type="reset" class="btn btn-secondary ml-2" value="Reset">
                </div>
            </form>
        </div>

        <!-- All Customers Table -->
        <h3>All Customers</h3>
        <?php if (!empty($all_customers)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Registered On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_customers as $customer): ?>
                            <tr>
                                <td><?php echo $customer['id']; ?></td>
                                <td><?php echo htmlspecialchars($customer['name']); ?></td>
                                <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                                <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                <td><?php echo $customer['created_at']; ?></td>
                                <td>
                                    <a href="#editCustomerModal" class="btn btn-sm btn-warning edit-customer" data-toggle="modal" 
                                       data-id="<?php echo $customer['id']; ?>" 
                                       data-name="<?php echo htmlspecialchars($customer['name']); ?>" 
                                       data-phone="<?php echo htmlspecialchars($customer['phone']); ?>" 
                                       data-email="<?php echo htmlspecialchars($customer['email']); ?>">Edit</a>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                        <input type="hidden" name="action" value="delete_customer">
                                        <input type="hidden" name="customer_id" value="<?php echo $customer['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                    <a href="#viewPurchaseHistoryModal" class="btn btn-sm btn-info view-purchase-history" data-toggle="modal" data-customer-id="<?php echo $customer['id']; ?>">History</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No customers found.</p>
        <?php endif; ?>
    </div>

    <!-- Edit Customer Modal -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1" role="dialog" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit_customer">
                        <input type="hidden" name="customer_id" id="edit_customer_id">
                        <div class="form-group">
                            <label>Customer Name</label>
                            <input type="text" name="name" id="edit_customer_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" id="edit_customer_phone" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" id="edit_customer_email" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Purchase History Modal -->
    <div class="modal fade" id="viewPurchaseHistoryModal" tabindex="-1" role="dialog" aria-labelledby="viewPurchaseHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewPurchaseHistoryModalLabel">Purchase History for <span id="history_customer_name"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="purchase_history_content">
                        <!-- Purchase history will be loaded here via AJAX -->
                        <p>Loading purchase history...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.edit-customer').on('click', function(){
                var customerId = $(this).data('id');
                var customerName = $(this).data('name');
                var customerPhone = $(this).data('phone');
                var customerEmail = $(this).data('email');
                
                $('#edit_customer_id').val(customerId);
                $('#edit_customer_name').val(customerName);
                $('#edit_customer_phone').val(customerPhone);
                $('#edit_customer_email').val(customerEmail);
            });

            $('.view-purchase-history').on('click', function(){
                var customerId = $(this).data('customer-id');
                var customerName = $(this).closest('tr').find('td:nth-child(2)').text(); // Get name from table row
                
                $('#history_customer_name').text(customerName);
                $('#purchase_history_content').html('<p>Loading purchase history...</p>'); // Show loading message

                // Load purchase history via AJAX
                $.ajax({
                    url: 'get_purchase_history.php', // This file will be created next
                    type: 'GET',
                    data: { customer_id: customerId },
                    success: function(response){
                        $('#purchase_history_content').html(response);
                    },
                    error: function(){
                        $('#purchase_history_content').html('<p class="text-danger">Error loading purchase history.</p>');
                    }
                });
            });
        });
    </script>
</body>
</html>