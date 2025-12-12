<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
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
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
        }

        .hero-section {
            background: linear-gradient(135deg, #007bff 0%, #0062cc 100%);
            color: white;
            padding: 3rem 2rem;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            position: relative;
        }

        .hero-logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .hero-title {
            font-weight: 700;
            font-size: 2.5rem;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .dashboard-card {
            border: none;
            border-radius: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
            background: white;
            height: 100%;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #007bff;
        }

        .card-title {
            font-weight: 600;
            font-size: 1.2rem;
        }

        /* Custom class for 7 columns in a row on large screens */
        @media (min-width: 992px) {
            .col-lg-custom-7 {
                -ms-flex: 0 0 14.2857%;
                flex: 0 0 14.2857%;
                max-width: 14.2857%;
            }
        }
    </style>

    <div class="hero-section text-center">
        <a href="logout.php" class="btn btn-danger btn-sm hero-logout-btn">Sign Out</a>
        <h1 class="hero-title">Welcome Back, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
        <p class="hero-subtitle">Pharmacy Inventory Management Dashboard</p>
        <p class="mt-2"><span class="badge badge-light p-2"><?php echo ucfirst(str_replace('_', ' ', $role)); ?>
                Access</span></p>
    </div>

    <div class="container mb-5">
        <h4 class="mb-3 text-secondary font-weight-bold">Quick Actions</h4>
        <div class="row">
            <!-- POS Card -->
            <?php if ($role == 'admin' || $role == 'store_clerk' || $role == 'online_customer'): ?>
                <div class="col-6 col-md-4 col-lg-custom-7 mb-3">
                    <a href="pos.php" class="text-decoration-none text-dark">
                        <div class="card dashboard-card p-3 text-center">
                            <div class="card-icon" style="font-size: 2rem;">🛒</div>
                            <h6 class="card-title" style="font-size: 1rem;">POS</h6>
                        </div>
                    </a>
                </div>
            <?php endif; ?>

            <!-- Inventory Card -->
            <?php if ($role == 'admin' || $role == 'store_clerk'): ?>
                <div class="col-6 col-md-4 col-lg-custom-7 mb-3">
                    <a href="inventory.php" class="text-decoration-none text-dark">
                        <div class="card dashboard-card p-3 text-center">
                            <div class="card-icon" style="font-size: 2rem;">💊</div>
                            <h6 class="card-title" style="font-size: 1rem;">Inventory</h6>
                        </div>
                    </a>
                </div>
            <?php endif; ?>

            <!-- Sales History -->
            <?php if ($role == 'admin' || $role == 'store_clerk' || $role == 'online_customer' || $role == 'report_viewer'): ?>
                <div class="col-6 col-md-4 col-lg-custom-7 mb-3">
                    <a href="sales.php" class="text-decoration-none text-dark">
                        <div class="card dashboard-card p-3 text-center">
                            <div class="card-icon" style="font-size: 2rem;">📋</div>
                            <h6 class="card-title" style="font-size: 1rem;">Sales</h6>
                        </div>
                    </a>
                </div>
            <?php endif; ?>

            <!-- Reports -->
            <?php if ($role == 'admin' || $role == 'store_clerk' || $role == 'report_viewer'): ?>
                <div class="col-6 col-md-4 col-lg-custom-7 mb-3">
                    <a href="reports.php" class="text-decoration-none text-dark">
                        <div class="card dashboard-card p-3 text-center">
                            <div class="card-icon" style="font-size: 2rem;">📊</div>
                            <h6 class="card-title" style="font-size: 1rem;">Reports</h6>
                        </div>
                    </a>
                </div>
            <?php endif; ?>

            <!-- Procurement -->
            <?php if ($role == 'admin' || $role == 'store_clerk'): ?>
                <div class="col-6 col-md-4 col-lg-custom-7 mb-3">
                    <a href="procurement.php" class="text-decoration-none text-dark">
                        <div class="card dashboard-card p-3 text-center">
                            <div class="card-icon" style="font-size: 2rem;">🚚</div>
                            <h6 class="card-title" style="font-size: 1rem;">Procurement</h6>
                        </div>
                    </a>
                </div>
            <?php endif; ?>

            <!-- User Management -->
            <?php if ($role == 'admin'): ?>
                <div class="col-6 col-md-4 col-lg-custom-7 mb-3">
                    <a href="user_management.php" class="text-decoration-none text-dark">
                        <div class="card dashboard-card p-3 text-center">
                            <div class="card-icon" style="font-size: 2rem;">👤</div>
                            <h6 class="card-title" style="font-size: 1rem;">Users</h6>
                        </div>
                    </a>
                </div>
            <?php endif; ?>

            <!-- Customer Database -->
            <?php if ($role == 'admin' || $role == 'online_customer'): ?>
                <div class="col-6 col-md-4 col-lg-custom-7 mb-3">
                    <a href="customer_database.php" class="text-decoration-none text-dark">
                        <div class="card dashboard-card p-3 text-center">
                            <div class="card-icon" style="font-size: 2rem;">👥</div>
                            <h6 class="card-title" style="font-size: 1rem;">Customers</h6>
                        </div>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Contributors Section -->
        <h4 class="mb-4 mt-5 text-secondary font-weight-bold">Contributors</h4>
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <a href="https://www.linkedin.com/in/uzzal-chandra-boissha-997792187"><img src="../src/img/1.png"
                        alt="Contributor 1" class="rounded-circle shadow-sm mb-3" width="120" height="120"></a>
                <h5>Uzzal Chandra Boisssha</h5>
                <p class="text-muted">Team lead</p>
            </div>
            <div class="col-md-4 mb-4">
                <a href="https://www.linkedin.com/in/mdhameem/"><img src="../src/img/2.png" alt="Contributor 2"
                        class="rounded-circle shadow-sm mb-3" width="120" height="120"></a>
                <h5>Md. Mosaddek Al Hameem</h5>
                <p class="text-muted">Lead Developer</p>
            </div>
            <div class="col-md-4 mb-4">
                <a href="https://facebook.com/100014065848212?log_join_id=c6e8e9aa-65da-49be-a368-6cb64c92c338"><img
                        src="../src/img/3.png" alt="Contributor 3" class="rounded-circle shadow-sm mb-3" width="120"
                        height="120"></a>
                <h5>Afsana Mimi</h5>
                <p class="text-muted">Designer</p>
            </div>
        </div>

        <!-- Our obj Section -->
        <div class="jumbotron text-center bg-white shadow-sm mt-4">
            <h2 class="display-4" style="font-size: 2rem;">Objectives</h2>
            <p class="lead font-italic">"To create a opensource comprehensive, secure, and user-friendly Pharmacy
                Inventory Management System that automates core pharmacy operations, improves efficiency, and ensures
                compliance with industry standards.
                "</p>
        </div>
    </div>

    <footer class="footer mt-auto py-3 bg-light text-center">
        <div class="container">
            <span class="text-muted">© 2025 Pharmacy IMS Contributors.</span>
            <p class="small mb-0 mt-1">
                This project is open source software licensed under the
                <a href="https://www.gnu.org/licenses/gpl-3.0.html" target="_blank" rel="noopener noreferrer">GNU
                    General Public License v3.0</a>.
            </p>
            <p class="small font-italic mt-1">"Free to run, free to study, free to change, and free to share."</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>

</html>