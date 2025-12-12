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
        <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'welcome.php') ? 'font-weight-bold' : ''; ?>"
                    href="welcome.php">Home</a>
            </li>

            <?php if ($role == 'admin' || $role == 'store_clerk'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'inventory.php') ? 'font-weight-bold' : ''; ?>"
                        href="inventory.php">Inventory</a>
                </li>
            <?php endif; ?>

            <?php if ($role == 'admin' || $role == 'store_clerk' || $role == 'report_viewer'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'sales.php') ? 'font-weight-bold' : ''; ?>"
                        href="sales.php">Sales History</a>
                </li>
            <?php endif; ?>

            <?php if ($role == 'admin' || $role == 'store_clerk'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'pos.php') ? 'font-weight-bold' : ''; ?>" href="pos.php"
                        style="<?php echo ($current_page == 'pos.php') ? '' : 'color: #007bff;'; ?>">POS</a>
                </li>
            <?php endif; ?>

            <?php if ($role == 'admin' || $role == 'store_clerk'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'procurement.php') ? 'font-weight-bold' : ''; ?>"
                        href="procurement.php">Procurement</a>
                </li>
            <?php endif; ?>

            <?php if ($role == 'admin' || $role == 'store_clerk' || $role == 'report_viewer'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'reports.php') ? 'font-weight-bold' : ''; ?>"
                        href="reports.php">Reports</a>
                </li>
            <?php endif; ?>

            <?php if ($role == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'user_management.php') ? 'font-weight-bold' : ''; ?>"
                        href="user_management.php">User Management</a>
                </li>
            <?php endif; ?>

            <?php if ($role == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'customer_database.php') ? 'font-weight-bold' : ''; ?>"
                        href="customer_database.php">Customer Database</a>
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