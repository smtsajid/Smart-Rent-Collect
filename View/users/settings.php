<?php
session_start();
include 'config.php';


if (!isset($_SESSION['username'])) {
    header("Location: ../Log_Res/login.php");
    exit();
}

$logged_in_user = $_SESSION['username'];
$status_message = "";


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_phone'])) {
    $new_phone = $_POST['phone'];
    try {
        $stmt = $conn->prepare("UPDATE users SET phone = :phone WHERE username = :uname");
        $stmt->execute([':phone' => $new_phone, ':uname' => $logged_in_user]);
        $status_message = "<div class='alert-success'><i class='fas fa-check-circle'></i> Phone number updated successfully!</div>";
    } catch (PDOException $e) {
        $status_message = "<div class='alert-error'>Error: " . $e->getMessage() . "</div>";
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_pass'])) {
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];

    try {

        $stmt = $conn->prepare("SELECT pass FROM users WHERE username = :uname");
        $stmt->execute([':uname' => $logged_in_user]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && isset($user['pass'])) {
            $db_pass = $user['pass'];


            $is_valid = false;
            if (strpos($db_pass, '$2y$') === 0) {
                $is_valid = password_verify($old_pass, $db_pass);
            } else {
                $is_valid = ($old_pass === $db_pass);
            }

            if ($is_valid) {

                $hashed_pass = $new_pass;
                $update = $conn->prepare("UPDATE users SET pass = :pass WHERE username = :uname");
                $update->execute([':pass' => $hashed_pass, ':uname' => $logged_in_user]);
                $status_message = "<div class='alert-success'><i class='fas fa-shield-check'></i> Password updated and secured!</div>";
            } else {
                $status_message = "<div class='alert-error'><i class='fas fa-times-circle'></i> Incorrect current password.</div>";
            }
        } else {
            $status_message = "<div class='alert-error'><i class='fas fa-user-slash'></i> User session error.</div>";
        }
    } catch (PDOException $e) {
        $status_message = "<div class='alert-error'>Error: " . $e->getMessage() . "</div>";
    }
}


try {
    $stmt = $conn->prepare("SELECT phone FROM users WHERE username = :uname");
    $stmt->execute([':uname' => $logged_in_user]);
    $current_phone = $stmt->fetchColumn() ?: "";
} catch (PDOException $e) { $current_phone = ""; }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings | RentPay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .alert-success { background: rgba(74, 222, 128, 0.1); color: #4ade80; padding: 15px; border-radius: 12px; border: 1px solid #4ade80; margin-bottom: 25px; }
        .alert-error { background: rgba(248, 113, 113, 0.1); color: #f87171; padding: 15px; border-radius: 12px; border: 1px solid #f87171; margin-bottom: 25px; }
        .complain-container { background: #1e293b; padding: 30px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05); }
        .input-box { width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: white; margin-top: 8px; }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
    <div style="margin-bottom: 30px;">
        <h1 style="margin: 0; color: white;"><i class="fas fa-user-gear" style="color: #6366f1;"></i> Account Settings</h1>
    </div>

    <?php echo $status_message; ?>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 30px;">
        <div class="complain-container">
            <h3 style="color: #6366f1;"><i class="fas fa-phone"></i> Phone Number</h3>
            <form action="settings.php" method="POST">
                <input type="tel" class="input-box" name="phone" value="<?= htmlspecialchars($current_phone) ?>" required>
                <button type="submit" name="update_phone" class="pay-btn" style="margin-top: 20px; width: 100%;">Update Phone</button>
            </form>
        </div>

        <div class="complain-container">
            <h3 style="color: #f87171;"><i class="fas fa-lock"></i> Security</h3>
            <form action="settings.php" method="POST">
                <label style="color: #94a3b8; font-size: 0.9rem;">Current Password</label>
                <input type="password" class="input-box" name="old_pass" required>

                <label style="color: #94a3b8; font-size: 0.9rem; display: block; margin-top: 15px;">New Password</label>
                <input type="password" class="input-box" name="new_pass" required>

                <button type="submit" name="update_pass" class="pay-btn" style="margin-top: 20px; width: 100%; background: #f87171; color: white;">Change Password</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>