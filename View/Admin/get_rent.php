<?php
include 'config.php';

if (isset($_POST['house_id']) && isset($_POST['unit_id'])) {
    $h_id = trim($_POST['house_id']);
    $u_id = trim($_POST['unit_id']);

    try {
        // Query the apartment table for the rent amount
        $stmt = $conn->prepare("SELECT rent FROM apartments WHERE house_id::text = :h AND unit_id::text = :u LIMIT 1");
        $stmt->execute([':h' => $h_id, ':u' => $u_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            echo $row['rent']; // Return only the number
        } else {
            echo "0";
        }
    } catch (PDOException $e) {
        echo "0";
    }
}
?>