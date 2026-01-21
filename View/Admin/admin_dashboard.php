<?php
session_start();
include 'config.php';


if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Log_Res/login.php");
    exit();
}

$current_admin = $_SESSION['username'];
$error_msg = "";

try {

    $stmt = $conn->prepare("SELECT SUM(amount::numeric) as total FROM payments WHERE ad_user = :admin AND status = 'Success'");
    $stmt->execute([':admin' => $current_admin]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_collection = $result['total'] ?? 0;


    $stmt = $conn->prepare("SELECT COUNT(*) FROM complain WHERE admin_user = :admin AND status = 'Pending'");
    $stmt->execute([':admin' => $current_admin]);
    $active_complaints = $stmt->fetchColumn() ?: 0;


    $stmt = $conn->prepare("SELECT * FROM payments WHERE ad_user = :admin ORDER BY payment_date DESC LIMIT 10");
    $stmt->execute([':admin' => $current_admin]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_msg = "Database Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | RentPay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --danger: #ef4444;
            --success: #4ade80;
            --warning: #f59e0b;
        }
        .logout-btn {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            padding: 10px 20px;
            border-radius: 10px;
            border: 1px solid rgba(239, 68, 68, 0.2);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .logout-btn:hover {
            background: var(--danger);
            color: white;
        }
        .status-pill {
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.8rem;
            border: 1px solid;
        }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px;">
        <div>
            <h1 style="color: white; margin: 0; font-size: 2.2rem;">Admin Overview</h1>
            <p style="color: var(--text-dim); margin-top: 8px;">
                Welcome back, <span style="color: var(--accent); font-weight: 600;"><?= htmlspecialchars($current_admin) ?></span>
            </p>
        </div>
        <a href="logout.php" class="logout-btn" onclick="return confirm('Log out of the system?')">
            <i class="fas fa-power-off"></i> Logout
        </a>
    </div>

    <?php if($error_msg): ?>
        <div style="background: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 15px; border-radius: 10px; border: 1px solid rgba(239, 68, 68, 0.2); margin-bottom: 25px;">
            <i class="fas fa-circle-exclamation"></i> <?= $error_msg ?>
        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: var(--card-bg); padding: 25px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
            <i class="fas fa-sack-dollar" style="color: var(--success); font-size: 1.5rem;"></i>
            <div style="margin-top: 15px;">
                <small style="color: var(--text-dim); display: block;">Total Collection</small>
                <span style="font-size: 1.8rem; font-weight: bold; color: white;">$<?= number_format($total_collection, 2) ?></span>
            </div>
        </div>

        <div style="background: var(--card-bg); padding: 25px; border-radius: 20px; border-left: 4px solid var(--danger); border-top: 1px solid rgba(255,255,255,0.05);">
            <i class="fas fa-envelope-open-text" style="color: var(--danger); font-size: 1.5rem;"></i>
            <div style="margin-top: 15px;">
                <small style="color: var(--text-dim); display: block;">Pending Complaints</small>
                <span style="font-size: 1.8rem; font-weight: bold; color: var(--danger);"><?= $active_complaints ?> Active</span>
            </div>
        </div>
    </div>


    <div style="background: var(--card-bg); padding: 30px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
        <h3 style="color: white; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-history" style="color: var(--accent);"></i> Recent Transactions
        </h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
            <tr style="text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <th style="padding: 15px; color: var(--text-dim);">Tenant</th>
                <th style="padding: 15px; color: var(--text-dim);">Date</th>
                <th style="padding: 15px; color: var(--text-dim);">Amount</th>
                <th style="padding: 15px; color: var(--text-dim);">Status</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($transactions)): ?>
                <?php foreach ($transactions as $row): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td style="padding: 15px; color: white;"><?= htmlspecialchars($row['username']) ?></td>
                        <td style="padding: 15px; color: var(--text-dim);"><?= date('M d, Y', strtotime($row['payment_date'])) ?></td>
                        <td style="padding: 15px; font-weight: bold; color: var(--accent);">$<?= number_format($row['amount'], 2) ?></td>
                        <td style="padding: 15px;">
                            <?php
                            $status = $row['status'] ?? 'Pending';
                            $isSuccess = ($status === 'Success' || $status === 'Confirmed');
                            $color = $isSuccess ? 'var(--success)' : 'var(--warning)';
                            $bg = $isSuccess ? 'rgba(74, 222, 128, 0.1)' : 'rgba(245, 158, 11, 0.1)';
                            ?>
                            <span class="status-pill" style="background: <?= $bg ?>; color: <?= $color ?>; border-color: <?= $color ?>33;">
                                    <?= htmlspecialchars($status) ?>
                                </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align: center; padding: 30px; color: var(--text-dim);">No transactions recorded yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>