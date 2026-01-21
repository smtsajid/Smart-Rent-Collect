<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenant = $_POST['tenant_name'];
    $unit = $_POST['unit_no'];
    $message = "Unit $unit has been successfully assigned to $tenant.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentPay | Apartment Allocation</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="sidebar">
    <div class="logo">RentPay</div>
    <a href="dashboard.php" class="nav-link"><i class="fas fa-th-large"></i> Dashboard</a>
    <a href="allocate.php" class="nav-link active"><i class="fas fa-key"></i> Allocation</a>
    <a href="history.php" class="nav-link"><i class="fas fa-history"></i> My History</a>
    <a href="complain.php" class="nav-link"><i class="fas fa-exclamation-circle"></i> Complain</a>
    <a href="messages.php" class="nav-link"><i class="fas fa-envelope"></i> Message</a>
    <a href="settings.php" class="nav-link"><i class="fas fa-cog"></i> Settings</a>
</div>

<div class="main">
    <div style="margin-bottom: 40px;">
        <h1 style="color: var(--text-light); margin: 0;">Apartment Allocation</h1>
        <p style="color: var(--text-dim); margin-top: 10px;">Link units to users and set rent values.</p>
    </div>

    <?php if(isset($message)): ?>
        <div style="background: rgba(74, 222, 128, 0.1); color: var(--success); padding: 20px; border-radius: 12px; margin-bottom: 30px; border: 1px solid var(--success);">
            <i class="fas fa-info-circle"></i> <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div style="background: var(--card-bg); padding: 40px; border-radius: 20px; box-shadow: var(--shadow); border: 1px solid rgba(255,255,255,0.05);">
        <form action="allocate.php" method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <label style="color: var(--accent); font-size: 0.9rem;">SELECT TENANT</label>
                <select name="tenant_name" class="input-box" required>
                    <option value="">-- Choose User --</option>
                    <option>Sajid Mohammad Talukdar</option>
                    <option>Rahat Kabir</option>
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                <label style="color: var(--accent); font-size: 0.9rem;">ASSIGN UNIT</label>
                <select name="unit_no" class="input-box" required>
                    <option value="">-- Choose Apartment --</option>
                    <option>A-101 (Sweety Villa)</option>
                    <option>B-202 (Sweety Villa)</option>
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                <label style="color: var(--accent); font-size: 0.9rem;">MONTHLY RENT</label>
                <input type="number" name="rent" class="input-box" placeholder="Amount in USD" required>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                <label style="color: var(--accent); font-size: 0.9rem;">START DATE</label>
                <input type="date" name="start" class="input-box" value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div style="grid-column: span 2;">
                <button type="submit" class="pay-btn" style="width: 100%; justify-content: center; height: 50px;">
                    <i class="fas fa-save"></i> ALLOCATE APARTMENT
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>