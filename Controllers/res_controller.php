<?php

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../Model/res_model.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../View/Log_Res/login.php");
    exit();
}


$name     = trim($_POST['name'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$nid      = trim($_POST['nid'] ?? '');
$dob      = trim($_POST['dob'] ?? '');
$address  = trim($_POST['address'] ?? '');


$errors = [];
if (empty($name) || empty($username) || empty($password)) {
    $errors[] = "Name, Username, and Password are required.";
}
if (strlen($password) < 6) {
    $errors[] = "Password must be at least 6 characters.";
}

if (!empty($errors)) {
    $_SESSION['reg_errors'] = $errors;
    header("Location: ../View/Log_Res/login.php");
    exit();
}


$data = [
    'name' => $name,
    'username' => $username,
    'pass' => $password,
    'phone' => $phone,
    'nid' => $nid,
    'dob' => $dob,
    'address' => $address
];


if (register_user($conn, $data)) {
    $_SESSION['success_msg'] = "Admin Created Successfully!";
} else {
    // Check if pg_last_error($conn) exists to debug why it failed
    $_SESSION['login_error'] = "Registration failed. Username might already exist.";
}

header("Location: ../View/Log_Res/login.php");
exit();