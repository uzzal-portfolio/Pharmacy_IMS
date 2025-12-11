<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION["role"] !== 'admin' && $_SESSION["role"] !== 'store_clerk')) {
    header("location: login.php");
    exit;
}

require_once '../config/config.php';
require_once '../src/Procurement.php';

$database = new Database();
$db = $database->getConnection();

$procurement = new Procurement($db);

$medicine_name = $quantity = $status = "";
$medicine_name_err = $quantity_err = "";

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_request"])) {
        // Validate medicine name
        if (empty(trim($_POST["medicine_name"]))) {
            $medicine_name_err = "Please enter a medicine name.";
        } else {
            $medicine_name = trim($_POST["medicine_name"]);
        }

        // Validate quantity
        if (empty(trim($_POST["quantity"]))) {
            $quantity_err = "Please enter a quantity.";
        } elseif (!is_numeric($_POST["quantity"]) || $_POST["quantity"] < 0) {
            $quantity_err = "Quantity must be a positive number.";
        } else {
            $quantity = trim($_POST["quantity"]);
        }

        $status = "pending"; // Default status

        if (empty($medicine_name_err) && empty($quantity_err)) {
            $procurement->medicine_name = $medicine_name;
            $procurement->quantity = $quantity;
            $procurement->status = $status;

            if ($procurement->create()) {
                header("location: procurement.php");
            } else {
                echo "Something went wrong. Please try again later.";
            }
        }
    } elseif (isset($_POST["update_request"])) {
        $procurement->id = $_POST["id"];
        $procurement->medicine_name = $_POST["medicine_name"];
        $procurement->quantity = $_POST["quantity"];
        $procurement->status = $_POST["status"];

        if ($procurement->update()) {
            header("location: procurement.php");
        } else {
            echo "Something went wrong. Please try again later.";
        }
    } elseif (isset($_POST["delete_request"])) {
        $procurement->id = $_POST["id"];
        if ($procurement->delete()) {
            header("location: procurement.php");
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
}

// Read procurement requests
$stmt = $procurement->read();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Procurement Management</title>
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
        <h2 class="mb-4">Procurement Management</h2>

        <!-- Add Procurement Request Form -->
        <div class="card mb-4">
            <div class="card-header">Request New Medicine</div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Medicine Name</label>
                            <input type="text" name="medicine_name"
                                class="form-control <?php echo (!empty($medicine_name_err)) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $medicine_name; ?>">
                            <span class="invalid-feedback"><?php echo $medicine_name_err; ?></span>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Quantity</label>
                            <input type="number" name="quantity"
                                class="form-control <?php echo (!empty($quantity_err)) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $quantity; ?>">
                            <span class="invalid-feedback"><?php echo $quantity_err; ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="add_request" class="btn btn-primary" value="Submit Request">
                    </div>
                </form>
            </div>
        </div>

        <!-- Procurement Request List Table -->
        <div class="card">
            <div class="card-header">Procurement Requests</div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Medicine Name</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Request Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($requests) > 0): ?>
                            <?php foreach ($requests as $request): ?>
                                <tr>
                                    <td><?php echo $request['id']; ?></td>
                                    <td><?php echo $request['medicine_name']; ?></td>
                                    <td><?php echo $request['quantity']; ?></td>
                                    <td><?php echo $request['status']; ?></td>
                                    <td><?php echo $request['request_date']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info edit-request-btn"
                                            data-id="<?php echo $request['id']; ?>"
                                            data-name="<?php echo $request['medicine_name']; ?>"
                                            data-quantity="<?php echo $request['quantity']; ?>"
                                            data-status="<?php echo $request['status']; ?>">Edit</button>
                                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
                                            style="display: inline-block;">
                                            <input type="hidden" name="id" value="<?php echo $request['id']; ?>">
                                            <input type="submit" name="delete_request" class="btn btn-sm btn-danger"
                                                value="Delete">
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No procurement requests found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Procurement Request Modal -->
    <div class="modal fade" id="editRequestModal" tabindex="-1" role="dialog" aria-labelledby="editRequestModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRequestModalLabel">Edit Procurement Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="request-id">
                        <div class="form-group">
                            <label>Medicine Name</label>
                            <input type="text" name="medicine_name" id="request-name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" name="quantity" id="request-quantity" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" id="request-status" class="form-control">
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <input type="submit" name="update_request" class="btn btn-primary" value="Save changes">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.edit-request-btn').on('click', function () {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var quantity = $(this).data('quantity');
                var status = $(this).data('status');

                $('#request-id').val(id);
                $('#request-name').val(name);
                $('#request-quantity').val(quantity);
                $('#request-status').val(status);

                $('#editRequestModal').modal('show');
            });
        });
    </script>
</body>

</html>