<?php
session_start();
 
require_once '../config/config.php';
require_once '../src/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $user->username = trim($_POST["username"]);
    $user->password = trim($_POST["password"]);

    if($user->login()){
        $_SESSION["loggedin"] = true;
        $_SESSION["id"] = $user->id;
        $_SESSION["username"] = $user->username;
        $_SESSION["role"] = $user->role;
        
        header("location: welcome.php");
    } else{
        header("location: login.php?error=1");
    }
}
?>