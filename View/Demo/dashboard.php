<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentPay | Tenant Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php
include 'sidebar.php';
// Static Variables
$user_name = "Sajid Mohammad Talukdar";
$house_name = "Sweety Villa";
$unit_no = "A-101";
$fixed_rent = 1200;
$due_date = "Feb 01, 2026";
?>

<div class="main">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 45px;">
        <div>
            <h1 style="margin: 0; font-size: 2.2rem;">Hi — <?php echo $user_name; ?></h1>
            <p style="color: var(--text-dim); margin-top: 8px; font-size: 1.1rem;">Welcome to your Rent Dashboard</p>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 1.3rem; font-weight: 700; color: var(--accent);">
                <i class="fas fa-house-user"></i> <?php echo $house_name; ?>
            </div>
            <div style="color: var(--text-dim); font-size: 1rem; margin-top: 5px;">Unit No: <?php echo $unit_no; ?></div>
        </div>
    </div>

    <div class="stats-row">
        <div class="stat-pill">
            <i class="fas fa-wallet" style="color: var(--accent);"></i>
            <small>Total Balance</small>
            <span class="stat-value">$<?php echo number_format($fixed_rent, 2); ?></span>
        </div>

        <div class="stat-pill">
            <i class="fas fa-calendar-check" style="color: var(--warning);"></i>
            <small>Net Due Date</small>
            <span class="stat-value" style="color: var(--warning);"><?php echo $due_date; ?></span>
        </div>

        <div class="stat-pill">
            <i class="fas fa-file-invoice-dollar" style="color: var(--success);"></i>
            <small>Monthly Rent</small>
            <span class="stat-value">$<?php echo number_format($fixed_rent, 2); ?></span>
        </div>
    </div>

    <form class="payment-bar" method="POST">
        <label style="color:var(--accent); font-weight:bold; min-width: 170px; font-size: 1.1rem;">
            <i class="fas fa-credit-card"></i> Make a payment
        </label>

        <input type="text" class="input-box" name="amount" value="$<?php echo $fixed_rent; ?>" readonly style="width: 120px; text-align: center;">

        <select class="input-box" name="method" style="flex: 1; max-width: 250px;">
            <option value="Bank">🏦 Bank Transfer</option>
            <option value="Card">💳 Credit / Debit Card</option>
            <option value="Mobile">📱 Mobile Banking (Bkash/Nagad)</option>
        </select>

        <button type="submit" class="pay-btn">
            <i class="fas fa-paper-plane"></i> PAY NOW
        </button>
    </form>

    <div class="table-section">
        <h3 style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <span><i class="fas fa-history" style="margin-right: 10px;"></i> Payment Status</span>
            <span style="font-size: 0.75rem; background: rgba(14, 165, 233, 0.15); color: var(--accent); padding: 6px 15px; border-radius: 20px; border: 1px solid rgba(56, 189, 248, 0.3);">
                Showing current month record
            </span>
        </h3>
        <table>
            <thead>
            <tr>
                <th>Payment Date</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Jan 15, 2026</td>
                <td style="font-weight: bold;">$<?php echo number_format($fixed_rent, 2); ?></td>
                <td>Bank Transfer</td>
                <td>
                        <span class="status-badge">
                            <i class="fas fa-check-circle"></i> Success
                        </span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>