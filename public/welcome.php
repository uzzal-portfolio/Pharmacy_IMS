<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$role = $_SESSION["role"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 80%; margin: 0 auto; padding: 20px; }
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
                <li class="nav-item active">
                    <a class="nav-link" href="welcome.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <?php if ($role == 'admin' || $role == 'store_clerk'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="inventory.php">Inventory</a>
                    </li>
                <?php endif; ?>
                <?php if ($role == 'admin' || $role == 'store_clerk' || $role == 'online_customer'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="sales.php">Sales</a>
                    </li>
                <?php endif; ?>
                <?php if ($role == 'admin' || $role == 'store_clerk'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="procurement.php">Procurement</a>
                    </li>
                <?php endif; ?>
                <?php if ($role == 'admin' || $role == 'report_viewer'): ?>
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
        <h1 class="my-5">Welcome to the Pharmacy Inventory Management System!</h1>
        <p>Your role is: <b><?php echo htmlspecialchars($_SESSION["role"]); ?></b></p>
        <p>Use the navigation bar above to access different modules.</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>