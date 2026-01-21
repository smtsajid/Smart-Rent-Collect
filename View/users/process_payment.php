<?php
session_start();
include 'config.php';


if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: ../Log_Res/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $logged_in_user = $_SESSION['username'];
    $method = $_POST['method'] ?? 'Manual';


    $payment_val = preg_replace('/[^0-9.]/', '', $_POST['amount']);
    $payment_amount = (float)$payment_val;
    $reference = "TXN-" . strtoupper(uniqid());

    try {
        $conn->beginTransaction();


        $adminQuery = "SELECT h.created_by 
                       FROM allocate a 
                       JOIN houses h ON a.house_id::text = h.house_id::text 
                       WHERE a.username = :uname LIMIT 1";

        $adminStmt = $conn->prepare($adminQuery);
        $adminStmt->execute([':uname' => $logged_in_user]);
        $house_data = $adminStmt->fetch(PDO::FETCH_ASSOC);


        $ad_user = ($house_data && !empty($house_data['created_by'])) ? $house_data['created_by'] : 'Admin';


        $updateSql = "UPDATE allocate SET amount = '0' WHERE username = :uname";
        $stmt1 = $conn->prepare($updateSql);
        $stmt1->execute([':uname' => $logged_in_user]);


        $insertSql = "INSERT INTO payments (username, amount, method, reference, status, ad_user, payment_date) 
                      VALUES (:uname, :amt, :method, :ref, 'Success', :ad, CURRENT_TIMESTAMP)";

        $stmt2 = $conn->prepare($insertSql);
        $stmt2->execute([
            ':uname'  => $logged_in_user,
            ':amt'    => $payment_amount,
            ':method' => $method,
            ':ref'    => $reference,
            ':ad'     => $ad_user
        ]);

        $conn->commit();
        header("Location: dashboard.php?payment=success");
        exit();

    } catch (PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        header("Location: dashboard.php?payment=error&msg=" . urlencode($e->getMessage()));
        exit();
    }
}