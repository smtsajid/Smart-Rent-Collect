<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post Notice | RentPay Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="main">
    <h1><i class="fas fa-bullhorn"></i> Announcement Board</h1>
    <div class="complain-container" style="background:var(--card-bg); padding:30px; border-radius:20px;">
        <label style="display:block; margin-bottom:10px; color:var(--accent)">Notice Title</label>
        <input type="text" class="input-box" style="width:100%; margin-bottom:20px;" placeholder="e.g. Elevator Maintenance">

        <label style="display:block; margin-bottom:10px; color:var(--accent)">Message Content</label>
        <textarea class="input-box" style="width:100%; height:150px; margin-bottom:20px;" placeholder="Write your announcement here..."></textarea>

        <button class="pay-btn" style="width:100%; justify-content:center;">Post Global Notice</button>
    </div>
</div>
</body>
</html>