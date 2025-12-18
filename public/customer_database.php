<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION["role"] !== 'admin')) {
    header("location: login.php");
    exit;
}

require_once '../config/config.php';
require_once '../src/Customer.php';

$database = new Database();
$db = $database->getConnection();

$customer = new Customer($db);

$name = $phone = $email = "";
$name_err = $phone_err = $email_err = "";

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_customer"])) {
        // Validate name
        if (empty(trim($_POST["name"]))) {
            $name_err = "Please enter a customer name.";
        } else {
            $name = trim($_POST["name"]);
        }

        // Validate phone (Optional but processed if present)
        $phone_input = trim($_POST["phone"]);
        if(!empty($phone_input)) {
             // Allow +, digits, spaces, dashes. Fail if alphabets.
             if(!preg_match("/^[0-9+\-\s]*$/", $phone_input)){
                 $phone_err = "Phone number can only contain numbers, +, - and spaces.";
             } elseif(strlen($phone_input) > 15){
                 $phone_err = "Phone number cannot exceed 15 characters.";
             } else {
                 $phone = $phone_input;
             }
        } else {
            $phone = "";
        }

        // Validate email (Optional)
        if (!empty(trim($_POST["email"])) && !filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
            $email_err = "Please enter a valid email address.";
        } else {
            $email = trim($_POST["email"]);
        }

        if (empty($name_err) && empty($phone_err) && empty($email_err)) {
            $customer->name = $name;
            $customer->phone = $phone;
            $customer->email = $email;

            if ($customer->create()) {
                header("location: customer_database.php");
            } else {
                echo "Something went wrong. Please try again later.";
            }
        }
    } elseif (isset($_POST["update_customer"])) {
        $customer->id = $_POST["id"];
        $customer->name = $_POST["name"];
        $customer->phone = $_POST["phone"];
        $customer->email = $_POST["email"];

        if ($customer->update()) {
            header("location: customer_database.php");
        } else {
            echo "Something went wrong. Please try again later.";
        }
    } elseif (isset($_POST["delete_customer"])) {
        $customer->id = $_POST["id"];
        if ($customer->delete()) {
            header("location: customer_database.php");
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
}

// Read customers
$stmt = $customer->read();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Customer Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font: 14px sans-serif;
        }

        .wrapper {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }

        .navbar-nav .nav-link {
            padding-right: 1rem;
            padding-left: 1rem;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="wrapper">
        <h2 class="mb-4">Customer Management</h2>

        <!-- Add Customer Form -->
        <div class="card mb-4">
            <div class="card-header">Add New Customer</div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Customer Name</label>
                            <input type="text" name="name"
                                class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $name; ?>">
                            <span class="invalid-feedback"><?php echo $name_err; ?></span>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Phone Number</label>
                            <input type="text" name="phone"
                                class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $phone; ?>"
                                maxlength="15"
                                oninput="this.value = this.value.replace(/[^0-9+]/g, '').replace(/(\..*)\./g, '$1');"
                                placeholder="e.g. +8801700000000">
                            <span class="invalid-feedback"><?php echo $phone_err; ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email"
                            class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"
                            value="<?php echo $email; ?>">
                        <span class="invalid-feedback"><?php echo $email_err; ?></span>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="add_customer" class="btn btn-primary" value="Add Customer">
                    </div>
                </form>
            </div>
        </div>

        <!-- Customer List Table -->
        <div class="card">
            <div class="card-header">Existing Customers</div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($customers) > 0): ?>
                            <?php foreach ($customers as $customer_data): ?>
                                <tr>
                                    <td><?php echo $customer_data['id']; ?></td>
                                    <td><?php echo $customer_data['name']; ?></td>
                                    <td><?php echo $customer_data['phone']; ?></td>
                                    <td><?php echo $customer_data['email']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info edit-customer-btn"
                                            data-id="<?php echo $customer_data['id']; ?>"
                                            data-name="<?php echo $customer_data['name']; ?>"
                                            data-phone="<?php echo $customer_data['phone']; ?>"
                                            data-email="<?php echo $customer_data['email']; ?>">Edit</button>
                                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
                                            style="display: inline-block;">
                                            <input type="hidden" name="id" value="<?php echo $customer_data['id']; ?>">
                                            <input type="submit" name="delete_customer" class="btn btn-sm btn-danger"
                                                value="Delete">
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No customers found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Customer Modal -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1" role="dialog" aria-labelledby="editCustomerModalLabel"
        aria-hidden="true">
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
                        <input type="hidden" name="id" id="customer-id">
                        <div class="form-group">
                            <label>Customer Name</label>
                            <input type="text" name="name" id="customer-name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone" id="customer-phone" class="form-control" maxlength="15" oninput="this.value = this.value.replace(/[^0-9+]/g, '').replace(/(\..*)\./g, '$1');">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" id="customer-email" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <input type="submit" name="update_customer" class="btn btn-primary" value="Save changes">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.edit-customer-btn').on('click', function () {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var phone = $(this).data('phone');
                var email = $(this).data('email');

                $('#customer-id').val(id);
                $('#customer-name').val(name);
                $('#customer-phone').val(phone);
                $('#customer-email').val(email);

                $('#editCustomerModal').modal('show');
            });
        });
    </script>
</body>

</html>