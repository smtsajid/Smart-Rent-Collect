<?php
session_start();


require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../Model/user_model.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../View/Log_Res/login.php");
    exit();
}

$email    = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    $_SESSION['login_error'] = "Please fill in all fields.";
    header("Location: ../View/Log_Res/login.php");
    exit();
}


$user = authenticate_user($conn, $email, $password);

if (!$user) {
    $_SESSION['login_error'] = "Invalid email or password.";
    header("Location: ../View/Log_Res/login.php");
    exit();
}


$_SESSION['username'] = $user['username'];
$_SESSION['name']     = $user['name'];
$_SESSION['role']     = $user['role'];

// Redirect based on role
if ($user['role'] === 'admin') {
    header("Location: ../View/Admin/admin_dashboard.php");
} else {
    header("Location: ../users/dashboard.php");
}
exit();
