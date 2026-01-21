<?php
session_start();
include "config.php";

$nid = $_GET['nid'] ?? '';
if (!$nid) { header("Location: admin_users.php"); exit; }

// Load User
$stmt = $conn->prepare("SELECT * FROM users WHERE nid = :nid");
$stmt->execute([':nid' => $nid]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = "UPDATE users SET name=:n, phone=:p, address=:a, dob=:d WHERE nid=:target";
        $conn->prepare($sql)->execute([
            ':n' => $_POST['name'], ':p' => $_POST['phone'],
            ':a' => $_POST['address'], ':d' => $_POST['dob'], ':target' => $nid
        ]);
        header("Location: admin_users.php?msg=updated");
        exit;
    } catch (PDOException $e) { $error = $e->getMessage(); }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Tenant</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="padding:40px;">
<div class="complain-container" style="max-width:500px; margin:auto;">
    <h2>Edit Tenant</h2>
    <form method="POST">
        <label>Name</label>
        <input type="text" name="name" class="input-box" value="<?php echo $user['name']; ?>">
        <label>Phone</label>
        <input type="text" name="phone" class="input-box" value="<?php echo $user['phone']; ?>">
        <label>DOB</label>
        <input type="date" name="dob" class="input-box" value="<?php echo $user['dob']; ?>">
        <label>Address</label>
        <input type="text" name="address" class="input-box" value="<?php echo $user['address']; ?>">
        <br><br>
        <button type="submit" class="pay-btn">Update</button>
        <a href="admin_users.php">Back</a>
    </form>
</div>
</body>
</html>