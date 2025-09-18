<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check user role for access control (only admin can access)
if ($_SESSION['role'] !== 'admin') {
    header("location: welcome.php"); // Redirect to welcome page if not authorized
    exit;
}

require_once '../config/config.php';
require_once '../src/User.php';

$user_obj = new User($link);

$username = $password = $confirm_password = $role = "";
$username_err = $password_err = $confirm_password_err = $role_err = "";

// Handle Add/Edit User Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add_user') {
            // Validate username
            if (empty(trim($_POST['username']))) {
                $username_err = "Please enter a username.";
            } else {
                $sql = "SELECT id FROM users WHERE username = ?";
                if ($stmt = mysqli_prepare($link, $sql)) {
                    mysqli_stmt_bind_param($stmt, "s", $param_username);
                    $param_username = trim($_POST['username']);
                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_store_result($stmt);
                        if (mysqli_stmt_num_rows($stmt) == 1) {
                            $username_err = "This username is already taken.";
                        } else {
                            $username = trim($_POST['username']);
                        }
                    }
                    mysqli_stmt_close($stmt);
                }
            }

            // Validate password
            if (empty(trim($_POST['password']))) {
                $password_err = "Please enter a password.";
            } elseif (strlen(trim($_POST['password'])) < 6) {
                $password_err = "Password must have at least 6 characters.";
            } else {
                $password = trim($_POST['password']);
            }

            // Validate confirm password
            if (empty(trim($_POST['confirm_password']))) {
                $confirm_password_err = "Please confirm password.";
            } else {
                $confirm_password = trim($_POST['confirm_password']);
                if (empty($password_err) && ($password != $confirm_password)) {
                    $confirm_password_err = "Password did not match.";
                }
            }

            // Validate role
            if (empty(trim($_POST['role']))) {
                $role_err = "Please select a role.";
            } else {
                $role = trim($_POST['role']);
            }

            // Check input errors before inserting in database
            if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($role_err)) {
                if ($user_obj->addUser($username, $password, $role)) {
                    echo "<script>alert('User added successfully!'); window.location.href='user_management.php';</script>";
                } else {
                    echo "<script>alert('Error: Could not add user.'); window.location.href='user_management.php';</script>";
                }
            }
        } elseif ($_POST['action'] == 'edit_user') {
            $user_id = $_POST['user_id'];
            $username = trim($_POST['username']);
            $role = trim($_POST['role']);
            $password = !empty(trim($_POST['password'])) ? trim($_POST['password']) : null;

            // Basic validation for username and role
            if (empty($username)) {
                $username_err = "Please enter a username.";
            }
            if (empty($role)) {
                $role_err = "Please select a role.";
            }

            if (empty($username_err) && empty($role_err)) {
                if ($user_obj->updateUser($user_id, $username, $role, $password)) {
                    echo "<script>alert('User updated successfully!'); window.location.href='user_management.php';</script>";
                } else {
                    echo "<script>alert('Error: Could not update user.'); window.location.href='user_management.php';</script>";
                }
            }
        } elseif ($_POST['action'] == 'delete_user') {
            $user_id = $_POST['user_id'];
            if ($user_obj->deleteUser($user_id)) {
                echo "<script>alert('User deleted successfully!'); window.location.href='user_management.php';</script>";
            } else {
                echo "<script>alert('Error: Could not delete user.'); window.location.href='user_management.php';</script>";
            }
        }
    }
}

$all_users = $user_obj->getAllUsers();

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 90%; margin: 0 auto; padding: 20px; }
        .table-responsive { margin-top: 20px; }
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
                <li class="nav-item">
                    <a class="nav-link" href="welcome.php">Home</a>
                </li>
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'store_clerk'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="inventory.php">Inventory</a>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'store_clerk' || $_SESSION['role'] == 'online_customer'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="sales.php">Sales</a>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'store_clerk'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="procurement.php">Procurement</a>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'report_viewer'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php">Reports</a>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <li class="nav-item active">
                        <a class="nav-link" href="user_management.php">User Management <span class="sr-only">(current)</span></a>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'online_customer'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Customer Database</a>
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
        <h2>User Management</h2>

        <!-- Add New User Form -->
        <div class="mb-4">
            <h3>Add New User</h3>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="action" value="add_user">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                        <span class="invalid-feedback"><?php echo $password_err; ?></span>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                        <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" class="form-control <?php echo (!empty($role_err)) ? 'is-invalid' : ''; ?>">
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="store_clerk">Store Clerk</option>
                        <option value="online_customer">Online Customer</option>
                        <option value="report_viewer">Report Viewer</option>
                    </select>
                    <span class="invalid-feedback"><?php echo $role_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Add User">
                    <input type="reset" class="btn btn-secondary ml-2" value="Reset">
                </div>
            </form>
        </div>

        <!-- All Users Table -->
        <h3>All Users</h3>
        <?php if (!empty($all_users)): ?>
            <div class="table-responsive">
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
                        <?php foreach ($all_users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['role']); ?></td>
                                <td><?php echo $user['created_at']; ?></td>
                                <td>
                                    <a href="#editUserModal" class="btn btn-sm btn-warning edit-user" data-toggle="modal" 
                                       data-id="<?php echo $user['id']; ?>" 
                                       data-username="<?php echo htmlspecialchars($user['username']); ?>" 
                                       data-role="<?php echo htmlspecialchars($user['role']); ?>">Edit</a>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No users found.</p>
        <?php endif; ?>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
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
                        <input type="hidden" name="action" value="edit_user">
                        <input type="hidden" name="user_id" id="edit_user_id">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" id="edit_username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select name="role" id="edit_role" class="form-control" required>
                                <option value="admin">Admin</option>
                                <option value="store_clerk">Store Clerk</option>
                                <option value="online_customer">Online Customer</option>
                                <option value="report_viewer">Report Viewer</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>New Password (leave blank to keep current)</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.edit-user').on('click', function(){
                var userId = $(this).data('id');
                var username = $(this).data('username');
                var role = $(this).data('role');
                
                $('#edit_user_id').val(userId);
                $('#edit_username').val(username);
                $('#edit_role').val(role);
            });
        });
    </script>
</body>
</html>