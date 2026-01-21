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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Logs | RentPay Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .status-badge-success {
            background: rgba(74, 222, 128, 0.1);
            color: #4ade80;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            border: 1px solid rgba(74, 222, 128, 0.2);
        }
        .status-badge-pending {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }
        .reset-link {
            color: #fb7185;
            text-decoration: none;
            font-size: 0.9rem;
            margin-left: 10px;
            transition: 0.3s;
        }
        .reset-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main">
    <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="color: var(--text-light); margin: 0;">
                <i class="fas fa-money-bill-transfer" style="color: var(--accent);"></i> Payment Logs
            </h1>
            <p style="color: var(--text-dim); margin-top: 5px;">Filtering transactions for: <b><?= htmlspecialchars($username) ?></b></p>
        </div>
    </div>


    <div class="payment-bar" style="background: var(--card-bg); padding: 20px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.05); margin-bottom: 30px;">
        <form method="GET" action="admin_payments.php" style="display: flex; gap: 15px; align-items: center; width: 100%;">

            <div style="flex: 2; position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 15px; top: 12px; color: var(--text-dim);"></i>
                <input type="text" name="search" class="input-box" placeholder="Tenant name or Ref ID..."
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="width: 100%; padding-left: 45px;">
            </div>

            <select name="month" class="input-box" style="flex: 1;">
                <option value="all">All Months</option>
                <?php
                for ($m = 1; $m <= 12; $m++) {
                    $mName = date('F', mktime(0, 0, 0, $m, 1));
                    $selected = (isset($_GET['month']) && $_GET['month'] == $m) ? 'selected' : '';
                    echo "<option value='$m' $selected>$mName</option>";
                }
                ?>
            </select>

            <select name="year" class="input-box" style="flex: 1;">
                <?php
                $current_y = (int)date('Y');
                for($y = $current_y; $y >= 2024; $y--) {
                    $selected = ($selected_year == $y) ? 'selected' : '';
                    echo "<option value='$y' $selected>$y</option>";
                }
                ?>
            </select>

            <button type="submit" class="pay-btn" style="padding: 10px 25px;">Filter</button>
            <a href="admin_payments.php" class="reset-link">Reset</a>
        </form>
    </div>

    <div class="table-section" style="background: var(--card-bg); padding: 25px; border-radius: 20px; box-shadow: var(--shadow);">
        <?php if(isset($error_msg)): ?>
            <div style="color: #ef4444; margin-bottom: 20px;"><?= $error_msg ?></div>
        <?php endif; ?>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
            <tr style="text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <th style="padding: 15px; color: var(--accent);">Reference</th>
                <th style="padding: 15px; color: var(--text-dim);">Tenant</th>
                <th style="padding: 15px; color: var(--text-dim);">Amount</th>
                <th style="padding: 15px; color: var(--text-dim);">Method</th>
                <th style="padding: 15px; color: var(--text-dim);">Date</th>
                <th style="padding: 15px; color: var(--text-dim);">Status</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($all_payments)): ?>
                <?php foreach ($all_payments as $row): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td style="padding: 15px; font-family: monospace; color: var(--accent);"><?= htmlspecialchars($row['reference']) ?></td>
                        <td style="padding: 15px; font-weight: 600; color: #fff;"><?= htmlspecialchars($row['username']) ?></td>
                        <td style="padding: 15px; font-weight: bold; color: #fff;">$<?= number_format($row['amount'], 2) ?></td>
                        <td style="padding: 15px; color: var(--text-dim);"><?= htmlspecialchars($row['method']) ?></td>
                        <td style="padding: 15px; color: var(--text-dim); font-size: 0.9rem;">
                            <?= date('M d, Y', strtotime($row['payment_date'])) ?><br>
                            <small><?= date('h:i A', strtotime($row['payment_date'])) ?></small>
                        </td>
                        <td style="padding: 15px;">
                            <?php
                            $status = $row['status'] ?? 'Pending';
                            $badgeClass = ($status == 'Success' || $status == 'Confirmed') ? 'status-badge-success' : 'status-badge-pending';
                            ?>
                            <span class="<?= $badgeClass ?>">
                                    <i class="fas <?= ($status == 'Success' || $status == 'Confirmed') ? 'fa-check-circle' : 'fa-clock' ?>"></i>
                                    <?= htmlspecialchars($status) ?>
                                </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 60px; color: var(--text-dim);">
                        <i class="fas fa-search" style="font-size: 2rem; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                        No payment records found matching your criteria.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>