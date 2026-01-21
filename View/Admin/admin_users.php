<?php
session_start();
include "config.php";


if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Log_Res/login.php");
    exit();
}

$message = '';
$current_admin = $_SESSION['username'];


if (isset($_GET['delete_nid'])) {
    $delete_nid = $_GET['delete_nid'];
    try {

        $sql = "DELETE FROM users WHERE nid = :nid AND role = 'user' AND admin_user = :admin";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':nid' => $delete_nid, ':admin' => $current_admin]);
        $message = "Tenant removed successfully!";
    } catch (PDOException $e) {
        $message = "Error deleting: " . $e->getMessage();
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_tenant'])) {
    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $nid     = trim($_POST['nid'] ?? '');
    $dob     = trim($_POST['dob'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($name && $phone && $nid && $dob && $address) {
        $username = $name;
        $password = "admin";
        $role     = 'user';

        try {

            $sql = "INSERT INTO users (username, pass, role, name, phone, nid, dob, address, admin_user)
                    VALUES (:username, :password, :role, :name, :phone, :nid, :dob, :address, :admin)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                    ':username' => $username,
                    ':password' => $password,
                    ':role'     => $role,
                    ':name'     => $name,
                    ':phone'    => $phone,
                    ':nid'      => $nid,
                    ':dob'      => $dob,
                    ':address'  => $address,
                    ':admin'    => $current_admin
            ]);
            $message = "Tenant registered successfully! Username: $username";
        } catch (PDOException $e) {
            $message = ($e->getCode() == 23505) ? "Error: NID or Username already exists." : "Error: " . $e->getMessage();
        }
    } else {
        $message = "All fields are required.";
    }
}


try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE role = 'user' AND admin_user = :admin ORDER BY name ASC");
    $stmt->execute([':admin' => $current_admin]);
    $tenants = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Fetch error: " . $e->getMessage();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tenant Management | RentPay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        #addUserForm { display: none; }
        .msg-box { padding: 10px; margin-bottom: 20px; border-radius: 4px; background: #e8f4fd; color: #2c3e50; border-left: 5px solid #3498db; }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1><i class="fas fa-users-gear"></i> Tenant Management</h1>
        <button class="pay-btn" id="toggleBtn"><i class="fas fa-user-plus"></i> Add New Tenant</button>
    </div>

    <?php if ($message): ?>
        <div class="msg-box"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div id="addUserForm" class="complain-container" style="margin-bottom: 40px; border: 2px dashed #3498db;">
        <form method="POST">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <input type="text" name="name" class="input-box" placeholder="Full Name" required>
                <input type="tel" name="phone" class="input-box" placeholder="Phone Number" required>
                <input type="text" name="nid" class="input-box" placeholder="NID Number" required>
                <input type="date" name="dob" class="input-box" required>
            </div>
            <input type="text" name="address" class="input-box" placeholder="Address" style="margin-top:15px;" required>
            <button type="submit" name="add_tenant" class="pay-btn" style="margin-top:15px;">Save Tenant</button>
        </form>
    </div>

    <div class="table-section">
        <table>
            <thead>
            <tr>
                <th>Tenant Details</th>
                <th>NID / Phone</th>
                <th>Address</th>
                <th style="text-align: right;">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($tenants as $t): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($t['name']); ?></strong><br>
                        <small>@<?php echo htmlspecialchars($t['username']); ?></small>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($t['phone']); ?><br>
                        <small>NID: <?php echo htmlspecialchars($t['nid']); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($t['address']); ?></td>
                    <td style="text-align: right;">
                        <a href="edit_user.php?nid=<?php echo $t['nid']; ?>" style="color:orange; margin-right:15px;"><i class="fas fa-edit"></i></a>
                        <a href="admin_users.php?delete_nid=<?php echo $t['nid']; ?>"
                           onclick="return confirm('Delete this tenant?');" style="color:red;"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('toggleBtn').onclick = function() {
        const f = document.getElementById('addUserForm');
        f.style.display = (f.style.display === 'none' || f.style.display === '') ? 'block' : 'none';
    }
</script>
</body>
</html>