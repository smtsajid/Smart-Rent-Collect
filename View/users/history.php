<?php
session_start();
include 'config.php';


if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: ../Log_Res/login.php");
    exit();
}

$logged_in_user = $_SESSION['username'];


try {
    $query = "SELECT * FROM payments WHERE username = :uname";
    $params = [':uname' => $logged_in_user];


    if (isset($_GET['month']) && $_GET['month'] !== 'all') {
        $query .= " AND EXTRACT(MONTH FROM payment_date) = :month";
        $params[':month'] = $_GET['month'];
    }
    if (isset($_GET['year']) && !empty($_GET['year'])) {
        $query .= " AND EXTRACT(YEAR FROM payment_date) = :year";
        $params[':year'] = $_GET['year'];
    }

    $query .= " ORDER BY payment_date DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction History | RentPay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="sidebar">
    <div class="logo">RentPay</div>
    <a href="dashboard.php" class="nav-link"><i class="fas fa-th-large"></i> Dashboard</a>
    <a href="history.php" class="nav-link active"><i class="fas fa-history"></i> My History</a>
    <a href="complain.php" class="nav-link"><i class="fas fa-exclamation-circle"></i> Complain</a>
    <a href="settings.php" class="nav-link"><i class="fas fa-cog"></i> Settings</a>
    <a href="logout.php" class="nav-link" style="margin-top: auto; color: #fb7185;"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="margin: 0;"><i class="fas fa-history" style="color: var(--accent);"></i> Transaction History</h1>
            <p style="color: var(--text-dim); margin-top: 5px;">All verified rent payments for your account.</p>
        </div>
    </div>

    <div class="payment-bar" style="border-color: rgba(255,255,255,0.1); margin-bottom: 30px; padding: 20px; background: var(--card-bg); border-radius: 15px;">
        <form action="history.php" method="GET" style="display: flex; align-items: center; gap: 15px; width: 100%;">
            <label style="color: var(--text-dim); font-size: 0.9rem;">Filter:</label>

            <select name="month" class="input-box" style="width: 150px;">
                <option value="all">All Months</option>
                <?php
                for ($m=1; $m<=12; $m++) {
                    $monthName = date('F', mktime(0, 0, 0, $m, 1));
                    $selected = (isset($_GET['month']) && $_GET['month'] == $m) ? 'selected' : '';
                    echo "<option value='$m' $selected>$monthName</option>";
                }
                ?>
            </select>

            <select name="year" class="input-box" style="width: 120px;">
                <option value="2026" <?= (isset($_GET['year']) && $_GET['year'] == '2026') ? 'selected' : '' ?>>2026</option>
                <option value="2025" <?= (isset($_GET['year']) && $_GET['year'] == '2025') ? 'selected' : '' ?>>2025</option>
            </select>

            <button type="submit" class="pay-btn" style="padding: 10px 20px;">
                <i class="fas fa-filter"></i> Apply
            </button>

            <a href="history.php" style="color: var(--text-dim); text-decoration: none; font-size: 0.8rem; margin-left: auto;">Reset</a>
        </form>
    </div>

    <div class="table-section" style="background: var(--card-bg); padding: 20px; border-radius: 20px; box-shadow: var(--shadow);">
        <table>
            <thead>
            <tr>
                <th>Date</th>
                <th>Method</th>
                <th>Reference</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($history)): ?>
                <?php foreach ($history as $row): ?>
                    <tr>
                        <td style="color: var(--text-light);"><?= date('M d, Y', strtotime($row['payment_date'])) ?></td>
                        <td><?= htmlspecialchars($row['method']) ?></td>
                        <td style="color: var(--text-dim); font-family: monospace;"><?= htmlspecialchars($row['reference']) ?></td>
                        <td style="font-weight: bold; color: var(--accent);">$<?= number_format($row['amount'], 2) ?></td>
                        <td>
                        <span class="status-badge" style="background: rgba(74, 222, 128, 0.1); color: #4ade80; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; border: 1px solid rgba(74, 222, 128, 0.2);">
                            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($row['status']) ?>
                        </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-dim);">
                        <i class="fas fa-folder-open" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                        No transactions found for this period.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>