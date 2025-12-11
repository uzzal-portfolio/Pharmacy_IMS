<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION["role"] !== 'admin' && $_SESSION["role"] !== 'report_viewer' && $_SESSION["role"] !== 'store_clerk')) {
    header("location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reports</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font: 14px sans-serif;
        }

        .wrapper {
            width: 95%;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="wrapper">
        <h2 class="mb-4">Generate Reports</h2>

        <div class="card">
            <div class="card-header bg-primary text-white">Report Configuration</div>
            <div class="card-body">
                <form action="generate_report.php" method="GET" target="_blank">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control" required
                                value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control" required
                                value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>

                    <hr>
                    <h5>Select Report Type:</h5>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <button type="submit" name="type" value="stock" class="btn btn-info btn-block btn-lg">
                                Stock Report
                            </button>
                            <small class="text-muted d-block text-center mt-2">Current inventory status and
                                location.</small>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" name="type" value="sales" class="btn btn-success btn-block btn-lg">
                                Sales Report
                            </button>
                            <small class="text-muted d-block text-center mt-2">Sales transactions, discounts, and
                                revenue.</small>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" name="type" value="expiry" class="btn btn-danger btn-block btn-lg">
                                Expiry Report
                            </button>
                            <small class="text-muted d-block text-center mt-2">Medicines expiring within date
                                range.</small>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>