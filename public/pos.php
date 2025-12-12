<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION["role"] !== 'admin' && $_SESSION["role"] !== 'store_clerk')) {
    header("location: login.php");
    exit;
}

require_once '../config/config.php';
require_once '../src/Customer.php';

$database = new Database();
$db = $database->getConnection();
$customer = new Customer($db);

$stmt = $customer->read();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sales / POS</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        body {
            font: 14px sans-serif;
        }

        .wrapper {
            width: 95%;
            margin: 0 auto;
            padding: 20px;
        }

        .pos-container {
            display: flex;
            gap: 20px;
        }

        .pos-left {
            flex: 2;
        }

        .pos-right {
            flex: 1;
            border-left: 1px solid #ddd;
            padding-left: 20px;
        }

        #cart-table th,
        #cart-table td {
            text-align: center;
            vertical-align: middle;
        }

        .total-section {
            font-size: 1.5em;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="wrapper">
        <h2 class="mb-4">Point of Sale</h2>

        <div class="pos-container">
            <!-- Left Side: Search & Cart -->
            <div class="pos-left">
                <div class="form-group">
                    <label>Search Medicine (Name or Code)</label>
                    <input type="text" id="search-medicine" class="form-control" placeholder="Type to search...">
                </div>

                <table class="table table-bordered table-hover" id="cart-table">
                    <thead class="thead-light">
                        <tr>
                            <th>Item</th>
                            <th>Code</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Stock</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Cart Items will be added here -->
                    </tbody>
                </table>
            </div>

            <!-- Right Side: Customer & Checkout -->
            <div class="pos-right">
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">Customer & Payment</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Select Customer <span class="text-danger">*</span></label>
                            <select id="customer-select" class="form-control" required>
                                <option value="">-- Select Customer --</option>
                                <?php foreach ($customers as $c): ?>
                                    <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?>
                                        (<?php echo $c['phone']; ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select id="payment-method" class="form-control">
                                <option value="Cash">Cash</option>
                                <option value="Bkash">Bkash</option>
                                <option value="Nagad">Nagad</option>
                                <option value="Rocket">Rocket</option>
                                <option value="Card">Card</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Discount (%)</label>
                            <input type="number" id="discount-input" class="form-control" value="0" min="0" max="100"
                                step="1">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="total-section">
                            Subtotal: <span id="sub-total">0.00</span><br>
                            <span class="text-danger" style="font-size: 0.8em;">Discount: -<span
                                    id="display-discount">0.00</span></span><br>
                            Net Total: <span id="grand-total">0.00</span>
                        </div>
                        <button id="checkout-btn" class="btn btn-success btn-lg btn-block mt-3">Complete Sale</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sale Successful</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Transaction ID: <span id="modal-trx-id" class="font-weight-bold"></span></p>
                    <p class="text-success">Inventory updated successfully.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="printReceipt()">Print Receipt</button>
                    <button type="button" class="btn btn-primary" onclick="location.reload()">New Sale</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <script>
        var cart = [];

        function printReceipt() {
            var trxId = $("#modal-trx-id").text();
            if (trxId) {
                window.open('receipt.php?transaction_id=' + trxId, '_blank', 'width=400,height=600');
            }
        }

        $(document).ready(function () {

            // Autocomplete Search
            $("#search-medicine").autocomplete({
                source: "get_inventory_search.php",
                minLength: 2,
                select: function (event, ui) {
                    addItemToCart(ui.item);
                    $(this).val('');
                    return false;
                }
            });

            // Add Item to Cart
            function addItemToCart(item) {
                // Check if already in cart
                var existingItem = cart.find(x => x.id === item.id);

                if (existingItem) {
                    if (existingItem.qty < item.stock) {
                        existingItem.qty++;
                    } else {
                        alert("Not enough stock!");
                    }
                } else {
                    cart.push({
                        id: item.id,
                        name: item.value, // 'value' from API is name
                        code: item.code,
                        price: parseFloat(item.price),
                        stock: parseInt(item.stock),
                        qty: 1
                    });
                }
                renderCart();
            }

            // Remove Item
            window.removeFromCart = function (id) {
                cart = cart.filter(x => x.id !== id);
                renderCart();
            }

            // Update Qty
            window.updateQty = function (id, val) {
                var item = cart.find(x => x.id === id);
                if (item) {
                    var newQty = parseInt(val);
                    if (newQty > 0 && newQty <= item.stock) {
                        item.qty = newQty;
                    } else if (newQty > item.stock) {
                        alert("Not enough stock! Max: " + item.stock);
                        item.qty = item.stock;
                    } else {
                        item.qty = 1;
                    }
                    renderCart();
                }
            }

            // Calculate Totals function
            function calculateTotals() {
                var subTotal = 0;
                cart.forEach(function (item) {
                    subTotal += item.price * item.qty;
                });

                var discountPercent = parseFloat($("#discount-input").val()) || 0;
                if (discountPercent > 100) discountPercent = 100;
                if (discountPercent < 0) discountPercent = 0;

                var discountAmount = subTotal * (discountPercent / 100);
                var netTotal = subTotal - discountAmount;

                if (netTotal < 0) netTotal = 0;

                $("#sub-total").text(subTotal.toFixed(2));
                $("#display-discount").text(discountAmount.toFixed(2));
                $("#grand-total").text(netTotal.toFixed(2));

                return discountAmount;
            }

            $("#discount-input").on('input', function () {
                calculateTotals();
            });

            // Render Cart
            function renderCart() {
                var html = '';
                var total = 0;

                cart.forEach(function (item) {
                    var itemTotal = item.price * item.qty;
                    total += itemTotal;

                    html += `<tr>
                        <td>${item.name}</td>
                        <td>${item.code}</td>
                        <td>${item.price.toFixed(2)}</td>
                        <td><input type="number" min="1" max="${item.stock}" value="${item.qty}" onchange="updateQty(${item.id}, this.value)" style="width: 60px;"></td>
                        <td>${item.stock}</td>
                        <td>${itemTotal.toFixed(2)}</td>
                        <td><button class="btn btn-sm btn-danger" onclick="removeFromCart(${item.id})">X</button></td>
                    </tr>`;
                });

                if (cart.length === 0) {
                    html = '<tr><td colspan="7" class="text-center">Cart is empty</td></tr>';
                }

                $("#cart-table tbody").html(html);
                calculateTotals();
            }

            // Checkout
            $("#checkout-btn").click(function () {
                if (cart.length === 0) {
                    alert("Cart is empty!");
                    return;
                }

                var customerId = $("#customer-select").val();
                if (!customerId) {
                    alert("Please select a valid Customer.");
                    return;
                }

                var discountAmount = calculateTotals(); // Get the calculated amount
                var paymentMethod = $("#payment-method").val();

                $.ajax({
                    url: 'process_sale.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        cart: cart,
                        customer_id: customerId,
                        discount: discountAmount,
                        payment_method: paymentMethod
                    }),
                    success: function (response) {
                        if (response.status === 'success') {
                            $("#modal-trx-id").text(response.transaction_id);
                            $("#successModal").modal('show');
                            cart = [];
                            renderCart();
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function () {
                        alert("Network error processing sale.");
                    }
                });
            });

        });
    </script>
</body>

</html>