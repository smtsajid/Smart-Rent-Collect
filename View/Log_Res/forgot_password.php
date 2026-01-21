<?php

include 'config.php';


function find_user_by_phone($conn, $phone) {
    $sql = "SELECT name, username FROM users WHERE phone = :phone LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':phone' => $phone]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function update_user_password($conn, $username, $new_password) {
    $sql = "UPDATE users SET pass = :pass WHERE username = :username";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([':pass' => $new_password, ':username' => $username]);
}


$message = "";
$current_step = $_POST['next_step'] ?? 1;


$phone_val    = $_POST['phone'] ?? '';
$username_val = $_POST['username'] ?? '';
$name_val     = $_POST['name'] ?? '';
$otp_val      = $_POST['generated_otp'] ?? '';

// STEP 1 -> 2: VERIFY PHONE & SEND SMS
if (isset($_POST['step1_submit'])) {
    $user = find_user_by_phone($conn, $phone_val);

    if ($user) {
        $otp_val = rand(100000, 999999);
        $username_val = $user['username'];
        $name_val = $user['name'];


        $api_key = "jMlv1LIPfmMf69ATOmB5";
        $senderid = "8809617619638";
        $sms_text = urlencode("Your Smart Rent OTP is: $otp_val");
        $api_url = "http://bulksmsbd.net/api/smsapi?api_key=$api_key&type=text&number=$phone_val&senderid=$senderid&message=$sms_text";

        // Call API
        @file_get_contents($api_url);
        $current_step = 2;
    } else {
        $message = "Phone number not found in our records!";
        $current_step = 1;
    }
}


if (isset($_POST['step2_submit'])) {
    $user_otp_input = $_POST['otp_input'] ?? '';
    if ($user_otp_input == $otp_val && !empty($otp_val)) {
        $current_step = 3;
    } else {
        $message = "Invalid OTP code! Check the console for testing.";
        $current_step = 2;
    }
}


if (isset($_POST['step3_submit'])) {
    if (update_user_password($conn, $username_val, $_POST['new_pass'])) {
        header("Location: login.php?msg=Password+Updated");
        exit();
    } else {
        $message = "Failed to update password.";
        $current_step = 3;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Recovery | Smart Rent Collect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg-dark: #0f172a; --card-bg: #1e293b; --accent: #38bdf8; --text-dim: #94a3b8; --danger: #fb7185; }
        * { box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%); display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; color: white; }
        .card { background-color: var(--card-bg); border-radius: 25px; padding: 40px; width: 450px; text-align: center; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5); border: 1px solid rgba(255, 255, 255, 0.05); }
        h2 { color: var(--accent); margin-bottom: 5px; }
        input { background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(56, 189, 248, 0.2); padding: 14px; margin: 10px 0; width: 100%; border-radius: 12px; color: white; outline: none; transition: 0.3s; }
        input:focus { border-color: var(--accent); box-shadow: 0 0 10px rgba(56, 189, 248, 0.1); }
        .btn { background: var(--accent); color: #0f172a; border: none; padding: 14px; width: 100%; border-radius: 12px; font-weight: bold; cursor: pointer; text-transform: uppercase; margin-top: 15px; }
        .alert { color: var(--danger); background: rgba(251, 113, 133, 0.1); padding: 10px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; }
        .user-badge { color: var(--accent); margin-bottom: 15px; display: block; font-weight: bold; font-size: 18px; }
        .restart { display: block; margin-top: 15px; color: var(--text-dim); text-decoration: none; font-size: 12px; }
    </style>
</head>
<body>

<div class="card">
    <h2><i class="fas fa-shield-alt"></i> Recovery</h2>

    <?php if ($message): ?> <div class="alert"><?= $message ?></div> <?php endif; ?>

    <form method="POST" action="forgot_password.php">
        <input type="hidden" name="phone" value="<?= htmlspecialchars($phone_val) ?>">
        <input type="hidden" name="username" value="<?= htmlspecialchars($username_val) ?>">
        <input type="hidden" name="name" value="<?= htmlspecialchars($name_val) ?>">
        <input type="hidden" name="generated_otp" value="<?= htmlspecialchars($otp_val) ?>">

        <?php if ($current_step == 1): ?>
            <p style="color:var(--text-dim)">Enter your phone number to receive a 6-digit verification code.</p>
            <input type="text" name="phone" placeholder="8801XXXXXXXXX" required value="<?= htmlspecialchars($phone_val) ?>">
            <input type="hidden" name="next_step" value="2">
            <button type="submit" name="step1_submit" class="btn">Get OTP</button>
            <a href="login.php" class="restart">Back to Login</a>

        <?php elseif ($current_step == 2): ?>
            <span class="user-badge">Hello, <?= htmlspecialchars($name_val) ?>!</span>
            <p style="color:var(--text-dim)">We sent a code to <?= htmlspecialchars($phone_val) ?></p>
            <input type="text" name="otp_input" placeholder="Enter OTP" required autofocus>
            <input type="hidden" name="next_step" value="3">
            <button type="submit" name="step2_submit" class="btn">Verify & Continue</button>
            <a href="forgot_password.php" class="restart">Wrong number? Start over</a>

        <?php elseif ($current_step == 3): ?>
            <span class="user-badge"><?= htmlspecialchars($name_val) ?></span>
            <p style="color:var(--text-dim)">Verification complete. Create a new password.</p>
            <input type="password" name="new_pass" placeholder="New Password" required minlength="6" autofocus>
            <button type="submit" name="step3_submit" class="btn">Save Password</button>
        <?php endif; ?>
    </form>
</div>

<script>
    const testOtp = "<?= $otp_val ?>";
    if (testOtp) {
        console.log("%c 🛠️ SMART RENT DEBUGGER ", "background: #38bdf8; color: #000; font-weight: bold;");
        console.log("Current OTP for testing: " + testOtp);
    }
</script>

</body>
</html>