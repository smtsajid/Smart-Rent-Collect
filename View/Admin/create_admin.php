<?php
session_start();
include 'config.php';


if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Log_Res/login.php");
    exit();
}

$message = "";
$status_class = "";
$current_admin = $_SESSION['username'];


if (isset($_GET['delete'])) {
    $target = $_GET['delete'];

    $del = $conn->prepare("DELETE FROM users WHERE username = :un AND admin_user = :admin AND ismod = 'yes'");

    if ($del->execute([':un' => $target, ':admin' => $current_admin])) {
        if ($del->rowCount() > 0) {
            $message = "Moderator role for '$target' removed successfully.";
            $status_class = "alert-success";
        } else {
            $message = "Error: Permission denied or moderator record not found.";
            $status_class = "alert-danger";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_admin'])) {

    $new_un      = $current_admin;
    $new_ps      = trim($_POST['password']);
    $new_name    = trim($_POST['full_name']);
    $new_phone   = trim($_POST['phone']);
    $new_nid     = trim($_POST['nid']);
    $new_dob     = $_POST['dob'];
    $new_address = trim($_POST['address']);

    try {

        $sql = "INSERT INTO users (username, pass, role, name, phone, nid, dob, address, admin_user, ismod) 
                VALUES (:un, :ps, 'admin', :nm, :ph, :nid, :dob, :addr, :creator, 'yes')";

        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([
            ':un'      => $new_un,
            ':ps'      => $new_ps,
            ':nm'      => $new_name,
            ':ph'      => $new_phone,
            ':nid'     => $new_nid,
            ':dob'     => $new_dob,
            ':addr'    => $new_address,
            ':creator' => $current_admin
        ]);

        if ($result) {
            $message = "Moderator record created for $current_admin.";
            $status_class = "alert-success";
        }

    } catch (PDOException $e) {

        $status_class = "alert-danger";
    }
}


$query = "SELECT * FROM users WHERE role = 'admin' AND ismod = 'yes' AND admin_user = :admin ORDER BY username ASC";
$stmt = $conn->prepare($query);
$stmt->execute([':admin' => $current_admin]);
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Moderator Control | Smart Rent</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --bg: #0f172a; --card: #1e293b; --accent: #38bdf8; --dim: #94a3b8; --danger: #fb7185; }
        body { background: var(--bg); font-family: 'Inter', sans-serif; color: white; margin: 0; padding: 40px; display: flex; gap: 30px; }
        .card { background: var(--card); padding: 30px; border-radius: 20px; width: 450px; border: 1px solid rgba(255,255,255,0.05); }
        .table-container { background: var(--card); padding: 30px; border-radius: 20px; flex-grow: 1; border: 1px solid rgba(255,255,255,0.05); }
        h2 { color: var(--accent); margin-top: 0; }
        input { width: 100%; padding: 10px; background: #0f172a; border: 1px solid rgba(56,189,248,0.2); border-radius: 8px; color: white; margin-bottom: 10px; outline: none; }
        input[readonly] { background: rgba(255,255,255,0.05); color: var(--dim); cursor: not-allowed; }
        .btn { width: 100%; padding: 12px; background: var(--accent); color: #0f172a; border: none; border-radius: 10px; font-weight: bold; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; color: var(--dim); border-bottom: 1px solid rgba(255,255,255,0.1); padding: 10px; }
        td { padding: 12px 10px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .delete-btn { color: var(--danger); text-decoration: none; border: 1px solid var(--danger); padding: 4px 8px; border-radius: 5px; }
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 14px; }
        .alert-success { background: rgba(74,222,128,0.1); color: #4ade80; }
        .alert-danger { background: rgba(251,113,133,0.1); color: #fb7185; }
    </style>
</head>
<body>

<div class="card">
    <h2><i class="fas fa-user-shield"></i> Add My Moderator</h2>
    <p style="font-size: 12px; color: var(--dim); margin-top: -15px; margin-bottom: 20px;">Setting moderator for: <strong><?= htmlspecialchars($current_admin) ?></strong></p>

    <?php if ($message): ?> <div class="alert <?= $status_class ?>"><?= $message ?></div> <?php endif; ?>

    <form method="POST">
        <label style="font-size: 11px; color: var(--dim);">Target Username (Auto-set)</label>
        <input type="text" value="<?= htmlspecialchars($current_admin) ?>" readonly>

        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="password" name="password" placeholder="Set Moderator Password" required>
        <input type="text" name="phone" placeholder="Phone" required>
        <input type="text" name="nid" placeholder="NID" required>
        <input type="date" name="dob" required>
        <input type="text" name="address" placeholder="Address" required>
        <button type="submit" name="create_admin" class="btn">Create Moderator</button>
    </form>
</div>

<div class="table-container">
    <h2><i class="fas fa-list"></i> Active Moderator Records</h2>
    <table>
        <thead>
        <tr>
            <th>Username</th>
            <th>Name</th>
            <th>Role Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($admins as $row): ?>
            <tr>
                <td><strong><?= htmlspecialchars($row['username']) ?></strong></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><span style="color: var(--accent)">Moderator (Active)</span></td>
                <td>
                    <a href="?delete=<?= urlencode($row['username']) ?>" class="delete-btn" onclick="return confirm('Remove moderator status?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if(empty($admins)): ?>
            <tr><td colspan="4" style="text-align:center; color:var(--dim); padding: 20px;">No moderator record found for your session.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>