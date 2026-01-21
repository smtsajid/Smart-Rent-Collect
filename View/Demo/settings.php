<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings | RentPay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
    <div style="margin-bottom: 30px;">
        <h1 style="margin: 0;"><i class="fas fa-user-gear" style="color: var(--accent);"></i> Account Settings</h1>
        <p style="color: var(--text-dim); margin-top: 5px;">Update your contact information or change your security credentials.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 30px;">

        <div class="complain-container" style="max-width: 100%;">
            <h3 style="margin-top: 0; color: var(--accent);"><i class="fas fa-phone"></i> Contact Information</h3>
            <p style="color: var(--text-dim); font-size: 0.85rem; margin-bottom: 25px;">Change your registered phone number used for SMS alerts.</p>

            <form action="settings.php" method="POST">
                <div class="form-section">
                    <label>New Phone Number</label>
                    <input type="tel" class="input-box" name="phone" value="+88017000000" required>
                </div>
                <button type="submit" name="update_phone" class="pay-btn" style="margin-top: 20px; width: 100%; justify-content: center;">
                    Update Phone
                </button>
            </form>
        </div>

        <div class="complain-container" style="max-width: 100%; border-color: rgba(248, 113, 113, 0.2);">
            <h3 style="margin-top: 0; color: #f87171;"><i class="fas fa-lock"></i> Security</h3>
            <p style="color: var(--text-dim); font-size: 0.85rem; margin-bottom: 25px;">Ensure your account is using a long, random password to stay secure.</p>

            <form action="settings.php" method="POST">
                <div class="form-section">
                    <label>Current Password</label>
                    <input type="password" class="input-box" name="old_pass" placeholder="••••••••" required>
                </div>
                <div class="form-section" style="margin-top: 15px;">
                    <label>New Password</label>
                    <input type="password" class="input-box" name="new_pass" placeholder="Enter new password" required>
                </div>
                <button type="submit" name="update_pass" class="pay-btn" style="margin-top: 20px; width: 100%; justify-content: center; background: #f87171; color: white;">
                    Change Password
                </button>
            </form>
        </div>

    </div>
</div>

</body>
</html>