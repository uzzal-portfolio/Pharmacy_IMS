<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$role = isset($_SESSION["role"]) ? $_SESSION["role"] : '';
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : 'Guest';
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <!-- 1. Left Aligned Logo -->
    <a class="navbar-brand" href="welcome.php">
        <img src="../src/img/Masterlogo.png" alt="Pharmacy IMS" height="40" class="d-inline-block align-top">
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- 2. Middle Aligned Links -->
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="welcome.php">Home</a>
            </li>

            <?php if ($role == 'admin' || $role == 'store_clerk'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="inventory.php">Inventory</a>
                </li>
            <?php endif; ?>

            <?php if ($role == 'admin' || $role == 'store_clerk' || $role == 'online_customer' || $role == 'report_viewer'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="sales.php">Sales History</a>
                </li>
            <?php endif; ?>

            <?php if ($role == 'admin' || $role == 'store_clerk' || $role == 'online_customer'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="pos.php" style="font-weight: bold; color: #007bff;">POS</a>
                </li>
            <?php endif; ?>

            <?php if ($role == 'admin' || $role == 'store_clerk'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="procurement.php">Procurement</a>
                </li>
            <?php endif; ?>

            <?php if ($role == 'admin' || $role == 'store_clerk' || $role == 'report_viewer'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="reports.php">Reports</a>
                </li>
            <?php endif; ?>

            <?php if ($role == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="user_management.php">User Management</a>
                </li>
            <?php endif; ?>

            <?php if ($role == 'admin' || $role == 'online_customer'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="customer_database.php">Customer Database</a>
                </li>
            <?php endif; ?>
        </ul>

        <!-- 3. Right Aligned User & Logout -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <span class="navbar-text mr-3">Hi, <b><?php echo htmlspecialchars($username); ?></b>
                    (<?php echo htmlspecialchars($role); ?>)</span>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="btn btn-danger btn-sm mt-1">Sign Out</a>
            </li>
        </ul>
    </div>
</nav>