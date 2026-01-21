<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Complaint | RentPay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
    <div style="margin-bottom: 30px;">
        <h1 style="margin: 0;"><i class="fas fa-circle-exclamation" style="color: #f87171;"></i> Submit a Complaint</h1>
        <p style="color: var(--text-dim); margin-top: 5px;">Report maintenance issues or other concerns to your landlord.</p>
    </div>

    <div class="complain-container">
        <form action="complain.php" method="POST">
            <div class="form-section">
                <label><i class="fas fa-heading"></i> Subject</label>
                <input type="text" class="input-box" placeholder="e.g. Kitchen sink leaking" required>
            </div>

            <div class="form-section" style="margin-top: 20px;">
                <label><i class="fas fa-layer-group"></i> Category</label>
                <select class="input-box">
                    <option>Plumbing</option>
                    <option>Electricity</option>
                    <option>Security</option>
                    <option>Other</option>
                </select>
            </div>

            <div class="form-section" style="margin-top: 20px;">
                <label><i class="fas fa-align-left"></i> Description</label>
                <textarea class="input-box" placeholder="Please provide details about the issue..." style="height: 150px; resize: none;"></textarea>
            </div>

            <button type="submit" class="pay-btn" style="margin-top: 30px; width: 100%; justify-content: center;">
                <i class="fas fa-paper-plane"></i> SUBMIT COMPLAINT
            </button>
        </form>
    </div>
</div>

</body>
</html>