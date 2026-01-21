<?php
session_start();
include 'config.php';

// 1. SECURITY CHECK
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Log_Res/login.php");
    exit();
}

$message = "";
$current_admin = $_SESSION['username'];

// --- HANDLE DELETION ---
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM allocate WHERE id = :id AND admin_user = :admin");
        $stmt->execute([':id' => $delete_id, ':admin' => $current_admin]);
        $message = "<div class='alert success'>Record removed successfully!</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert error'>Error: " . $e->getMessage() . "</div>";
    }
}

// --- HANDLE ALLOCATION FORM SUBMISSION ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['allocate_btn'])) {
    $house_id = $_POST['house_id'];
    $unit_id = $_POST['unit_id'];
    $tenant_username = $_POST['tenant_username'];
    $amount = $_POST['amount'];
    $date = $_POST['allocation_date'];

    try {
        $sql = "INSERT INTO allocate (house_id, apartment_id, username, amount, allocation_date, admin_user) 
                VALUES (:h_id, :u_id, :u_name, :amt, :a_date, :admin)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':h_id'   => $house_id,
            ':u_id'   => $unit_id,
            ':u_name' => $tenant_username,
            ':amt'    => $amount,
            ':a_date' => $date,
            ':admin'  => $current_admin
        ]);
        $message = "<div class='alert success'>Unit allocated successfully to $tenant_username!</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert error'>Error: " . $e->getMessage() . "</div>";
    }
}


try {
    $stmt = $conn->prepare("SELECT * FROM allocate WHERE admin_user = :admin ORDER BY id DESC");
    $stmt->execute([':admin' => $current_admin]);
    $view_allocations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $view_allocations = [];
}
?>