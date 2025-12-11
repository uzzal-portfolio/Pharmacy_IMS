<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION["role"] !== 'admin' && $_SESSION["role"] !== 'store_clerk')) {
    header("location: login.php");
    exit;
}

require_once '../config/config.php';
require_once '../src/Inventory.php';

$database = new Database();
$db = $database->getConnection();

$inventory = new Inventory($db);

$name = $code = $quantity = $price = $expiry_date = $location_code = "";
$name_err = $code_err = $quantity_err = $price_err = $expiry_date_err = "";

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_medicine"])) {
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

        // Validate quantity
        if (empty(trim($_POST["quantity"]))) {
            $quantity_err = "Please enter a quantity.";
        } elseif (!is_numeric($_POST["quantity"]) || $_POST["quantity"] < 0) {
            $quantity_err = "Quantity must be a positive number.";
        } else {
            $quantity = trim($_POST["quantity"]);
        }

        // Validate price
        if (empty(trim($_POST["price"]))) {
            $price_err = "Please enter a price.";
        } elseif (!is_numeric($_POST["price"]) || $_POST["price"] < 0) {
            $price_err = "Price must be a positive number.";
        } else {
            $price = trim($_POST["price"]);
        }

        // Validate expiry date
        if (empty(trim($_POST["expiry_date"]))) {
            $expiry_date_err = "Please enter an expiry date.";
        } else {
            $expiry_date = trim($_POST["expiry_date"]);
        }

        $location_code = trim($_POST["location_code"]);

        if (empty($name_err) && empty($code_err) && empty($quantity_err) && empty($price_err) && empty($expiry_date_err)) {
            $inventory->name = $name;
            $inventory->code = $code;
            $inventory->quantity = $quantity;
            $inventory->price = $price;
            $inventory->expiry_date = $expiry_date;
            $inventory->location_code = $location_code;

            if ($inventory->create()) {
                header("location: inventory.php");
            } else {
                echo "Something went wrong. Please try again later.";
            }
        }
    } elseif (isset($_POST["update_medicine"])) {
        $inventory->id = $_POST["id"];
        $inventory->name = $_POST["name"];
        $inventory->code = $_POST["code"];
        $inventory->quantity = $_POST["quantity"];
        $inventory->price = $_POST["price"];
        $inventory->expiry_date = $_POST["expiry_date"];
        $inventory->location_code = $_POST["location_code"];

        if ($inventory->update()) {
            header("location: inventory.php");
        } else {
            echo "Something went wrong. Please try again later.";
        }
    } elseif (isset($_POST["delete_medicine"])) {
        $inventory->id = $_POST["id"];
        if ($inventory->delete()) {
            header("location: inventory.php");
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
}

// Read medicines
$stmt = $inventory->read();
$medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Inventory Management</title>
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
        <h2 class="mb-4">Inventory Management</h2>

        <!-- Add Medicine Form -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Add New Medicine</span>
                <?php if ($_SESSION["role"] == 'admin'): ?>
                    <a href="upload_csv.php" class="btn btn-sm btn-success">Upload CSV Catalog</a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Medicine Name</label>
                            <input type="text" name="name"
                                class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $name; ?>">
                            <span class="invalid-feedback"><?php echo $name_err; ?></span>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Medicine Code</label>
                            <input type="text" name="code"
                                class="form-control <?php echo (!empty($code_err)) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $code; ?>">
                            <span class="invalid-feedback"><?php echo $code_err; ?></span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Quantity</label>
                            <input type="number" name="quantity"
                                class="form-control <?php echo (!empty($quantity_err)) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $quantity; ?>">
                            <span class="invalid-feedback"><?php echo $quantity_err; ?></span>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Price</label>
                            <input type="number" step="0.01" name="price"
                                class="form-control <?php echo (!empty($price_err)) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $price; ?>">
                            <span class="invalid-feedback"><?php echo $price_err; ?></span>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Expiry Date</label>
                            <input type="date" name="expiry_date"
                                class="form-control <?php echo (!empty($expiry_date_err)) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $expiry_date; ?>">
                            <span class="invalid-feedback"><?php echo $expiry_date_err; ?></span>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Location Code</label>
                            <input type="text" name="location_code" class="form-control"
                                value="<?php echo $location_code; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="add_medicine" class="btn btn-primary" value="Add Medicine">
                    </div>
                </form>
            </div>
        </div>

        <!-- Current Inventory Table -->
        <div class="card">
            <div class="card-header">Current Inventory</div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Expiry Date</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($medicines) > 0): ?>
                            <?php foreach ($medicines as $medicine): ?>
                                <tr>
                                    <td><?php echo $medicine['id']; ?></td>
                                    <td><?php echo $medicine['name']; ?></td>
                                    <td><?php echo $medicine['code']; ?></td>
                                    <td><?php echo $medicine['quantity']; ?></td>
                                    <td><?php echo $medicine['price']; ?></td>
                                    <td><?php echo $medicine['expiry_date']; ?></td>
                                    <td><?php echo $medicine['location_code']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info edit-medicine-btn"
                                            data-id="<?php echo $medicine['id']; ?>"
                                            data-name="<?php echo $medicine['name']; ?>"
                                            data-code="<?php echo $medicine['code']; ?>"
                                            data-code="<?php echo $medicine['code']; ?>"
                                            data-quantity="<?php echo $medicine['quantity']; ?>"
                                            data-price="<?php echo $medicine['price']; ?>"
                                            data-expiry="<?php echo $medicine['expiry_date']; ?>"
                                            data-location="<?php echo $medicine['location_code']; ?>">Edit</button>
                                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
                                            style="display: inline-block;">
                                            <input type="hidden" name="id" value="<?php echo $medicine['id']; ?>">
                                            <input type="submit" name="delete_medicine" class="btn btn-sm btn-danger"
                                                value="Delete">
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">No medicines found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Medicine Modal -->
    <div class="modal fade" id="editMedicineModal" tabindex="-1" role="dialog" aria-labelledby="editMedicineModalLabel"
        aria-hidden="true">
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
                        <input type="hidden" name="id" id="medicine-id">
                        <div class="form-group">
                            <label>Medicine Name</label>
                            <input type="text" name="name" id="medicine-name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Medicine Code</label>
                            <input type="text" name="code" id="medicine-code" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" name="quantity" id="medicine-quantity" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Price</label>
                            <input type="number" step="0.01" name="price" id="medicine-price" class="form-control"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <input type="date" name="expiry_date" id="medicine-expiry" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Location Code</label>
                            <input type="text" name="location_code" id="medicine-location" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <input type="submit" name="update_medicine" class="btn btn-primary" value="Save changes">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.edit-medicine-btn').on('click', function () {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var code = $(this).data('code');
                var quantity = $(this).data('quantity');
                var price = $(this).data('price');
                var expiry = $(this).data('expiry');
                var location = $(this).data('location');

                $('#medicine-id').val(id);
                $('#medicine-name').val(name);
                $('#medicine-code').val(code);
                $('#medicine-quantity').val(quantity);
                $('#medicine-price').val(price);
                $('#medicine-expiry').val(expiry);
                $('#medicine-location').val(location);

                $('#editMedicineModal').modal('show');
            });

            // Autocomplete for Add Medicine
            $('input[name="name"]').autocomplete({
                source: "get_medicine_suggestions.php",
                minLength: 2,
                select: function (event, ui) {
                    $('input[name="code"]').val(ui.item.code);
                }
            });

            // Autocomplete for Edit Medicine (in modal)
            $('#medicine-name').autocomplete({
                source: "get_medicine_suggestions.php",
                minLength: 2,
                select: function (event, ui) {
                    $('#medicine-code').val(ui.item.code);
                }
            });
        });
    </script>
</body>

</html>