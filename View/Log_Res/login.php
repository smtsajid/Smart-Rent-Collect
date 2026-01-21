<?php
session_start();


$error      = $_SESSION['login_error'] ?? null;
$success    = $_SESSION['success_msg'] ?? null;
$reg_errors = $_SESSION['reg_errors'] ?? null;


unset($_SESSION['login_error'], $_SESSION['success_msg'], $_SESSION['reg_errors']);

if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../Admin/admin_dashboard.php");
    } else {
        header("Location: ../users/dashboard.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Smart Rent Collect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0f172a; --card-bg: #1e293b; --accent: #38bdf8;
            --text-light: #f8fafc; --text-dim: #94a3b8; --shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            --success: #4ade80; --danger: #fb7185;
        }

        * { box-sizing: border-box; font-family: 'Inter', sans-serif; }

        body {
            background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%);
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            min-height: 100vh; margin: 0; color: var(--text-light); padding: 20px;
        }


        .alert-box { width: 1100px; max-width: 96%; margin-bottom: 20px; border-radius: 15px; padding: 15px 25px; font-size: 14px; border: 1px solid; }
        .alert-success { background: rgba(74, 222, 128, 0.1); color: var(--success); border-color: rgba(74, 222, 128, 0.2); }
        .alert-danger { background: rgba(251, 113, 133, 0.1); color: var(--danger); border-color: rgba(251, 113, 133, 0.2); }


        .container {
            background-color: var(--card-bg); border-radius: 25px; box-shadow: var(--shadow);
            position: relative; overflow: hidden; width: 1100px; max-width: 96%;
            min-height: 720px; border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .form-container { position: absolute; top: 0; height: 100%; width: 50%; transition: all 0.6s ease-in-out; }


        .sign-in-container { left: 0; z-index: 2; }
        .container.right-panel-active .sign-in-container { transform: translateX(100%); }


        .sign-up-container {
            left: 0; opacity: 0; z-index: 1;
            overflow-y: auto;
        }
        .container.right-panel-active .sign-up-container {
            transform: translateX(100%); opacity: 1; z-index: 5; animation: show 0.6s;
        }

        @keyframes show { 0%, 49.99% { opacity: 0; z-index: 1; } 50%, 100% { opacity: 1; z-index: 5; } }

        form {
            background-color: var(--card-bg); display: flex; align-items: center;
            justify-content: center; flex-direction: column; padding: 50px;
            min-height: 100%; text-align: center;
        }

        input {
            background-color: rgba(15, 23, 42, 0.5); border: 1px solid rgba(56, 189, 248, 0.2);
            padding: 12px 15px; margin: 6px 0; width: 100%; border-radius: 10px; color: white; outline: none;
        }

        .main-btn {
            border-radius: 12px; border: none; background: var(--accent); color: #0f172a;
            font-size: 13px; font-weight: bold; padding: 12px 45px; text-transform: uppercase;
            cursor: pointer; margin-top: 15px; width: 100%; transition: 0.3s;
        }
        .demo-btn { background: transparent; border: 1px solid var(--accent); color: var(--accent); margin-top: 10px; }


        .overlay-container {
            position: absolute; top: 0; left: 50%; width: 50%; height: 100%;
            overflow: hidden; transition: transform 0.6s ease-in-out; z-index: 100;
        }
        .container.right-panel-active .overlay-container { transform: translateX(-100%); }

        .overlay {
            background: linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%); color: #0f172a;
            position: relative; left: -100%; height: 100%; width: 200%; transform: translateX(0); transition: transform 0.6s ease-in-out;
        }
        .container.right-panel-active .overlay { transform: translateX(50%); }

        .overlay-panel {
            position: absolute; display: flex; align-items: center; justify-content: center;
            flex-direction: column; padding: 0 80px; text-align: center; top: 0; height: 100%; width: 50%;
        }
        .overlay-right { right: 0; }

        .ghost {
            background-color: transparent; border: 2px solid #0f172a; color: #0f172a;
            border-radius: 15px; font-size: 13px; font-weight: bold; padding: 12px 40px; text-transform: uppercase; cursor: pointer;
        }
        .forgot { color: var(--text-dim); font-size: 13px; text-decoration: none; margin: 12px 0; }
    </style>
</head>
<body>

<?php if ($success): ?>
    <div class="alert-box alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert-box alert-danger"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="container" id="container">

    <div class="form-container sign-up-container">
        <form method="POST" action="../../Controllers/res_controller.php">
            <h2 style="color: var(--accent); margin-bottom: 10px;">Create Account</h2>
            <input type="text" name="username" placeholder="Username" required />
            <input type="password" name="password" placeholder="Password" required />
            <input type="text" name="name" placeholder="Full Name" required />
            <input type="text" name="phone" placeholder="Phone Number" required />
            <input type="text" name="nid" placeholder="NID Number" required />
            <label style="align-self: flex-start; font-size: 11px; margin-left: 5px; color: var(--text-dim);">Date of Birth</label>
            <input type="date" name="dob" required />
            <input type="text" name="address" placeholder="Residential Address" required />
            <button class="main-btn" type="submit">Sign Up</button>
        </form>
    </div>

    <div class="form-container sign-in-container">
        <form method="POST" action="../../Controllers/auth_controller.php">
            <h1 style="color: var(--accent);">Sign In</h1>
            <input type="text" name="username" placeholder="Username" required />
            <input type="password" name ="password" placeholder="Password" required />

            <a href="forgot_password.php" class="forgot">Forget Your Password?</a>

            <button class="main-btn" type="submit">Sign In</button>

            <button type="button" class="main-btn demo-btn" onclick="window.location.href='../Demo/dashboard.php'">
                <i class="fas fa-play"></i> Try Demo Mode
            </button>
        </form>
    </div>

    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h1>Welcome Back!</h1>
                <p>Already have an account? Sign in here.</p>
                <button class="ghost" id="signIn">Sign In</button>
            </div>
            <div class="overlay-panel overlay-right">
                <h1>Hello, Friend!</h1>
                <p>Register as a Landlord to start managing properties.</p>
                <button class="ghost" id="signUp">Sign Up</button>
            </div>
        </div>
    </div>
</div>

<script>
    const signUpBtn = document.getElementById('signUp');
    const signInBtn = document.getElementById('signIn');
    const container = document.getElementById('container');


    signUpBtn.addEventListener('click', () => {
        container.classList.add("right-panel-active");
    });

    signInBtn.addEventListener('click', () => {
        container.classList.remove("right-panel-active");
    });


    <?php if ($reg_errors): ?>
    container.classList.add("right-panel-active");
    <?php endif; ?>
</script>

</body>
</html>