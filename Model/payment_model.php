<?php
session_start();
include 'config.php';


if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Log_Res/login.php");
    exit();
}

$username = $_SESSION['username'];
$all_payments = [];


try {

    $query = "SELECT * FROM payments WHERE ad_user = :username";
    $params = [':username' => $username];


    if (!empty($_GET['search'])) {
        $search = $_GET['search'];
        $query .= " AND (username ILIKE :search OR reference ILIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }


    if (!empty($_GET['month']) && $_GET['month'] !== 'all') {
        $query .= " AND EXTRACT(MONTH FROM payment_date) = :month";
        $params[':month'] = (int)$_GET['month'];
    }

    $selected_year = $_GET['year'] ?? date('Y');
    $query .= " AND EXTRACT(YEAR FROM payment_date) = :year";
    $params[':year'] = (int)$selected_year;


    $query .= " ORDER BY payment_date DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $all_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_msg = "Database Error: " . $e->getMessage();
}
?>