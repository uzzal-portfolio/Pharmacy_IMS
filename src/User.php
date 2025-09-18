<?php

class User {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function getAllUsers() {
        $sql = "SELECT id, username, role, created_at FROM users ORDER BY username ASC";
        $result = mysqli_query($this->conn, $sql);
        $users = [];

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $users[] = $row;
            }
        }
        return $users;
    }

    public function getUserById($id) {
        $sql = "SELECT id, username, role FROM users WHERE id = ?";

        if ($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                if (mysqli_num_rows($result) == 1) {
                    return mysqli_fetch_assoc($result);
                }
            }
            mysqli_stmt_close($stmt);
        }
        return null;
    }

    public function addUser($username, $password, $role) {
        $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";

        if ($stmt = mysqli_prepare($this->conn, $sql)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt, "sss", $username, $hashed_password, $role);

            if (mysqli_stmt_execute($stmt)) {
                return true;
            } else {
                return false;
            }
            mysqli_stmt_close($stmt);
        }
        return false;
    }

    public function updateUser($id, $username, $role, $password = null) {
        if ($password) {
            $sql = "UPDATE users SET username = ?, role = ?, password = ? WHERE id = ?";
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            if ($stmt = mysqli_prepare($this->conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "sssi", $username, $role, $hashed_password, $id);
            }
        } else {
            $sql = "UPDATE users SET username = ?, role = ? WHERE id = ?";
            if ($stmt = mysqli_prepare($this->conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssi", $username, $role, $id);
            }
        }

        if (isset($stmt) && mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else if (isset($stmt)) {
            mysqli_stmt_close($stmt);
            return false;
        }
        return false;
    }

    public function deleteUser($id) {
        $sql = "DELETE FROM users WHERE id = ?";

        if ($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);

            if (mysqli_stmt_execute($stmt)) {
                return true;
            } else {
                return false;
            }
            mysqli_stmt_close($stmt);
        }
        return false;
    }
}

?>