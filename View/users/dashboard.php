<?php
session_start();
include 'config.php';


if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: ../Log_Res/login.php");
    exit();
}

$logged_in_user = $_SESSION['username'];
$user_name = $_SESSION['name'] ?? $logged_in_user;


try {
    $stmt = $conn->prepare("SELECT * FROM allocate WHERE username = :uname LIMIT 1");
    $stmt->execute([':uname' => $logged_in_user]);
    $allocation = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($allocation) {
        $house_id = $allocation['house_id'];
        $unit_no  = $allocation['apartment_id'];
        $current_balance = (float)$allocation['amount'];

        $hStmt = $conn->prepare("SELECT house_name FROM houses WHERE house_id::text = :hid::text");
        $hStmt->execute([':hid' => $house_id]);
        $house_data = $hStmt->fetch(PDO::FETCH_ASSOC);
        $house_name = $house_data ? $house_data['house_name'] : "House ID: " . $house_id;
    } else {
        $house_name = "Not Assigned";
        $unit_no = "N/A";
        $current_balance = 0.00;
    }
} catch (PDOException $e) { $error_msg = $e->getMessage(); }

$date = new DateTime('first day of next month');
$date->setDate((int)$date->format('Y'), (int)$date->format('m'), 10);
$due_date = $date->format('M d, Y');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tenant Dashboard | RentPay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="sidebar">
    <div class="logo">RentPay</div>
    <a href="dashboard.php" class="nav-link active"><i class="fas fa-th-large"></i> Dashboard</a>
    <a href="history.php" class="nav-link"><i class="fas fa-history"></i> My History</a>
    <a href="complain.php" class="nav-link"><i class="fas fa-exclamation-circle"></i> Complain</a>
    <a href="settings.php" class="nav-link"><i class="fas fa-user-gear"></i> Settings</a> <a href="logout.php" class="nav-link" style="margin-top: auto; color: #fb7185;"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
<div class="main">
    <?php if(isset($_GET['payment']) && $_GET['payment'] == 'success'): ?>
        <div style="background: rgba(74,222,128,0.1); color: #4ade80; padding: 15px; border-radius: 12px; border: 1px solid #4ade80; margin-bottom: 20px;">
            <i class="fas fa-check-circle"></i> Payment Successful!
        </div>
    <?php endif; ?>

    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 45px;">
        <div>
            <h1 style="margin: 0; color: white; font-size: 2.2rem;">Hi — <?= htmlspecialchars($user_name) ?></h1>
            <p style="color: #94a3b8;">Your real-time rent overview</p>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 1.2rem; font-weight: 700; color: #6366f1;">
                <i class="fas fa-building"></i> <?= htmlspecialchars($house_name) ?>
            </div>
            <div style="color: #94a3b8; font-size: 0.9rem;">Unit: <?= htmlspecialchars($unit_no) ?></div>
        </div>
    </div>

    <div class="stats-row" style="display: flex; gap: 20px; margin-bottom: 30px;">
        <div class="stat-pill" style="flex: 1; background: #1e293b; padding: 25px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
            <i class="fas fa-wallet" style="color: #6366f1; font-size: 1.5rem; margin-bottom: 15px; display: block;"></i>
            <small style="color: #94a3b8; display: block;">Remaining Balance</small>
            <span style="font-size: 1.8rem; font-weight: bold; color: white;">$<?= number_format($current_balance, 2) ?></span>
        </div>
        <div class="stat-pill" style="flex: 1; background: #1e293b; padding: 25px; border-radius: 20px; border-left: 4px solid #f59e0b;">
            <i class="fas fa-calendar-day" style="color: #f59e0b; font-size: 1.5rem; margin-bottom: 15px; display: block;"></i>
            <small style="color: #94a3b8; display: block;">Next Due Date</small>
            <span style="font-size: 1.8rem; font-weight: bold; color: #f59e0b;"><?= $due_date ?></span>
        </div>
    </div>

    <div style="background: #1e293b; padding: 35px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
        <h3 style="color: white; margin-top: 0; margin-bottom: 25px;"><i class="fas fa-credit-card"></i> Make a Payment</h3>
        <?php if ($current_balance > 0): ?>
            <form method="POST" action="process_payment.php" style="display: flex; gap: 20px; align-items: flex-end;">
                <div style="flex: 1;">
                    <label style="font-size: 0.8rem; color: #94a3b8; display: block; margin-bottom: 8px;">Amount</label>
                    <input type="text" name="amount" class="input-box" value="<?= $current_balance ?>" readonly style="width: 100%; background: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1); padding: 10px; border-radius: 8px;">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 0.8rem; color: #94a3b8; display: block; margin-bottom: 8px;">Method</label>
                    <select name="method" class="input-box" style="width: 100%; background: #0f172a; color: white; border: 1px solid rgba(255,255,255,0.1); padding: 10px; border-radius: 8px;">
                        <option value="Bkash">Bkash/Nagad</option>
                        <option value="Bank">Bank Transfer</option>
                    </select>
                </div>
                <button type="submit" class="pay-btn" style="height: 45px; background: #6366f1; color: white; border: none; padding: 0 30px; border-radius: 8px; font-weight: bold; cursor: pointer;">Pay Now</button>
            </form>
        <?php else: ?>
            <div style="padding: 20px; text-align: center; border: 1px dashed #4ade80; border-radius: 15px; color: #4ade80;">
                <i class="fas fa-check-double"></i> All payments settled!
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>