<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction History | RentPay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="margin: 0;"><i class="fas fa-history" style="color: var(--accent);"></i> Transaction History</h1>
            <p style="color: var(--text-dim); margin-top: 5px;">View and filter all your past rent payments.</p>
        </div>
    </div>

    <div class="payment-bar" style="border-color: rgba(255,255,255,0.1); margin-bottom: 30px;">
        <form action="history.php" method="GET" style="display: flex; align-items: center; gap: 15px; width: 100%;">
            <label style="color: var(--text-dim); font-size: 0.9rem; font-weight: bold;">Filter by Month:</label>

            <select name="month" class="input-box" style="width: 150px;">
                <option value="all">All Months</option>
                <option value="01">January</option>
                <option value="02">February</option>
                <option value="12">December</option>
            </select>

            <select name="year" class="input-box" style="width: 120px;">
                <option value="2026">2026</option>
                <option value="2025">2025</option>
            </select>

            <button type="submit" class="pay-btn" style="padding: 10px 20px;">
                <i class="fas fa-filter"></i> Apply Filter
            </button>

            <a href="history.php" style="color: var(--text-dim); text-decoration: none; font-size: 0.8rem; margin-left: auto;">Clear All</a>
        </form>
    </div>

    <div class="table-section" style="background: var(--card-bg); padding: 20px; border-radius: 20px; box-shadow: var(--shadow);">
        <table>
            <thead>
            <tr>
                <th><i class="fas fa-calendar"></i> Date</th>
                <th><i class="fas fa-credit-card"></i> Method</th>
                <th><i class="fas fa-tag"></i> Reference</th>
                <th><i class="fas fa-dollar-sign"></i> Amount</th>
                <th><i class="fas fa-circle-check"></i> Status</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Jan 15, 2026</td>
                <td>Bank Transfer</td>
                <td style="color: var(--text-dim);">#TXN-99281</td>
                <td style="font-weight: bold;">$1,200.00</td>
                <td><span class="status-badge"><i class="fas fa-check"></i> Success</span></td>
            </tr>
            <tr>
                <td>Dec 12, 2025</td>
                <td>Credit Card</td>
                <td style="color: var(--text-dim);">#TXN-88172</td>
                <td style="font-weight: bold;">$1,200.00</td>
                <td><span class="status-badge"><i class="fas fa-check"></i> Success</span></td>
            </tr>
            <tr>
                <td>Nov 10, 2025</td>
                <td>Mobile Banking</td>
                <td style="color: var(--text-dim);">#TXN-77261</td>
                <td style="font-weight: bold;">$1,200.00</td>
                <td><span class="status-badge"><i class="fas fa-check"></i> Success</span></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>