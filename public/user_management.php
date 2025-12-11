<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: login.php");
    exit;
}

require_once '../config/config.php';
require_once '../src/User.php';

$database = new Database();
$db = $database->getConnection();

$user_obj = new User($db);

$user_id = $role = "";

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["update_role"])) {
        $user_obj->id = $_POST["id"];
        $user_obj->role = $_POST["role"];

        if ($user_obj->updateRole()) {
            header("location: user_management.php");
        } else {
            echo "Something went wrong. Please try again later.";
        }
    } elseif (isset($_POST["delete_user"])) {
        $user_obj->id = $_POST["id"];
        if ($user_obj->delete()) {
            header("location: user_management.php");
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
}

// Read all users
$stmt = $user_obj->read();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Management</title>
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
        <h2 class="mb-4">User Management</h2>

        <!-- User List Table -->
        <div class="card">
            <div class="card-header">Existing Users</div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($users) > 0): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo $user['username']; ?></td>
                                    <td><?php echo $user['role']; ?></td>
                                    <td><?php echo $user['created_at']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info edit-user-btn"
                                            data-id="<?php echo $user['id']; ?>"
                                            data-username="<?php echo $user['username']; ?>"
                                            data-role="<?php echo $user['role']; ?>">Edit Role</button>
                                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
                                            style="display: inline-block;">
                                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                            <input type="submit" name="delete_user" class="btn btn-sm btn-danger"
                                                value="Delete User">
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit User Role Modal -->
    <div class="modal fade" id="editUserRoleModal" tabindex="-1" role="dialog" aria-labelledby="editUserRoleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserRoleModalLabel">Edit User Role</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="user-id">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" id="user-username" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select name="role" id="user-role" class="form-control">
                                <option value="admin">Admin</option>
                                <option value="store_clerk">Store Clerk</option>
                                <option value="online_customer">Online Customer</option>
                                <option value="report_viewer">Report Viewer</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <input type="submit" name="update_role" class="btn btn-primary" value="Save changes">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.edit-user-btn').on('click', function () {
                var id = $(this).data('id');
                var username = $(this).data('username');
                var role = $(this).data('role');

                $('#user-id').val(id);
                $('#user-username').val(username);
                $('#user-role').val(role);

                $('#editUserRoleModal').modal('show');
            });
        });
    </script>
</body>

</html>