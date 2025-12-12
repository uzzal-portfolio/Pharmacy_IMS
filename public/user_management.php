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

$message = "";
$message_type = ""; // success or danger

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ADD USER
    if (isset($_POST["add_user"])) {
        if ($_POST["password"] !== $_POST["confirm_password"]) {
            $message = "Passwords do not match.";
            $message_type = "danger";
        } else {
            $user_obj->username = $_POST["username"];
            $user_obj->password = $_POST["password"];
            $user_obj->role = $_POST["role"];

            if ($user_obj->usernameExists()) {
                $message = "Username already exists.";
                $message_type = "danger";
            } else {
                if ($user_obj->register()) {
                    $message = "User created successfully.";
                    $message_type = "success";
                } else {
                    $message = "Something went wrong. Please try again.";
                    $message_type = "danger";
                }
            }
        }
    }
    // UPDATE USER
    elseif (isset($_POST["update_user"])) {
        $user_obj->id = $_POST["id"];
        $user_obj->username = $_POST["username"];
        $user_obj->role = $_POST["role"];
        $user_obj->password = !empty($_POST["password"]) ? $_POST["password"] : "";

        if ($user_obj->update()) {
            $message = "User updated successfully.";
            $message_type = "success";
        } else {
            $message = "Something went wrong. Please try again.";
            $message_type = "danger";
        }
    }
    // DELETE USER
    elseif (isset($_POST["delete_user"])) {
        $user_obj->id = $_POST["id"];
        if ($user_obj->delete()) {
            $message = "User deleted successfully.";
            $message_type = "success";
        } else {
            $message = "Something went wrong. Please try again.";
            $message_type = "danger";
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
            width: 90%;
            margin: 0 auto;
            padding: 20px;
        }

        .table-action-btn {
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="wrapper">
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <h2 class="mb-4">User Management</h2>

        <!-- Add New User Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Add New User</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control" required>
                            <option value="" disabled selected>Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="store_clerk">Store Clerk</option>
                            <option value="report_viewer">Report Viewer</option>
                            <option value="online_customer">Online Customer</option>
                        </select>
                    </div>
                    <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                </form>
            </div>
        </div>

        <!-- User List Table -->
        <h3 class="mb-3">All Users</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th style="width: 20%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['role']); ?></td>
                                <td><?php echo $user['created_at']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning table-action-btn edit-user-btn"
                                        data-id="<?php echo $user['id']; ?>"
                                        data-username="<?php echo htmlspecialchars($user['username']); ?>"
                                        data-role="<?php echo htmlspecialchars($user['role']); ?>">
                                        Edit
                                    </button>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
                                        style="display: inline-block;"
                                        onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete_user" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id">

                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" id="edit-username" class="form-control" required
                                readonly>
                            <small class="form-text text-muted">Username cannot be changed here for security reasons (or
                                simple design). Create new if needed.</small>
                        </div>

                        <div class="form-group">
                            <label>Role</label>
                            <select name="role" id="edit-role" class="form-control" required>
                                <option value="admin">Admin</option>
                                <option value="store_clerk">Store Clerk</option>
                                <option value="report_viewer">Report Viewer</option>
                                <option value="online_customer">Online Customer</option>
                            </select>
                        </div>

                        <hr>
                        <h6>Change Password</h6>
                        <small class="text-muted mb-2 d-block">Leave blank to keep current password.</small>

                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="password" class="form-control" placeholder="New Password">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="update_user" class="btn btn-primary">Save changes</button>
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

                $('#edit-id').val(id);
                $('#edit-username').val(username);
                $('#edit-role').val(role);

                $('#editUserModal').modal('show');
            });
        });
    </script>
</body>

</html>