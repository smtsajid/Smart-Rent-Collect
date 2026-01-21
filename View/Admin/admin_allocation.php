<?php
session_start();
include 'config.php';

// 1. SECURITY CHECK
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Log_Res/login.php");
    exit();
}

$message = "";
$current_admin = $_SESSION['username'];

// --- HANDLE DELETION ---
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM allocate WHERE id = :id AND admin_user = :admin");
        $stmt->execute([':id' => $delete_id, ':admin' => $current_admin]);
        $message = "<div class='alert success'>Record removed successfully!</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert error'>Error: " . $e->getMessage() . "</div>";
    }
}

// --- HANDLE ALLOCATION FORM SUBMISSION ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['allocate_btn'])) {
    $house_id = $_POST['house_id'];
    $unit_id = $_POST['unit_id'];
    $tenant_username = $_POST['tenant_username'];
    $amount = $_POST['amount'];
    $date = $_POST['allocation_date'];

    try {
        $sql = "INSERT INTO allocate (house_id, apartment_id, username, amount, allocation_date, admin_user) 
                VALUES (:h_id, :u_id, :u_name, :amt, :a_date, :admin)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
                ':h_id'   => $house_id,
                ':u_id'   => $unit_id,
                ':u_name' => $tenant_username,
                ':amt'    => $amount,
                ':a_date' => $date,
                ':admin'  => $current_admin
        ]);
        $message = "<div class='alert success'>Unit allocated successfully to $tenant_username!</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert error'>Error: " . $e->getMessage() . "</div>";
    }
}


try {
    $stmt = $conn->prepare("SELECT * FROM allocate WHERE admin_user = :admin ORDER BY id DESC");
    $stmt->execute([':admin' => $current_admin]);
    $view_allocations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $view_allocations = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Allocation Management | Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid; }
        .success { background: rgba(74, 222, 128, 0.1); color: #4ade80; border-color: #4ade8033; }
        .error { background: rgba(239, 68, 68, 0.1); color: #fb7185; border-color: #fb718533; }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main">
    <div style="margin-bottom: 40px;">
        <h1 style="color: var(--text-light); margin: 0;">Allocation Management</h1>
        <p style="color: var(--text-dim);">Assign units to tenants and auto-fetch rent amounts.</p>
    </div>

    <?= $message ?>

    <div style="background: var(--card-bg); padding: 35px; border-radius: 20px; box-shadow: var(--shadow); border: 1px solid rgba(255,255,255,0.05);">
        <form method="POST" id="allocationForm" style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
            <div>
                <label style="color: var(--accent); display: block; margin-bottom: 8px;">House ID</label>
                <input type="text" name="house_id" id="house_id" class="input-box" placeholder="Enter House ID" required>
            </div>
            <div>
                <label style="color: var(--accent); display: block; margin-bottom: 8px;">Unit ID</label>
                <input type="text" name="unit_id" id="unit_id" class="input-box" placeholder="Enter Unit ID" required>
            </div>
            <div>
                <label style="color: var(--accent); display: block; margin-bottom: 8px;">Tenant Username</label>
                <input type="text" name="tenant_username" class="input-box" placeholder="Tenant username" required>
            </div>
            <div>
                <label style="color: var(--accent); display: block; margin-bottom: 8px;">Rent Amount ($)</label>
                <input type="text" name="amount" id="amount_box" class="input-box" placeholder="Automatic Fetching..." required readonly style="background: rgba(255,255,255,0.03); cursor: not-allowed;">
            </div>
            <div style="grid-column: span 2;">
                <label style="color: var(--accent); display: block; margin-bottom: 8px;">Allocation Date</label>
                <input type="date" name="allocation_date" class="input-box" value="<?= date('Y-m-d') ?>" readonly required>
            </div>
            <div style="grid-column: span 2; margin-top: 15px;">
                <button type="submit" name="allocate_btn" class="pay-btn" style="width: 100%; justify-content: center;">
                    <i class="fas fa-plus-circle"></i> Complete Allocation
                </button>
            </div>
        </form>
    </div>

    <div style="background: var(--card-bg); padding: 30px; border-radius: 20px; box-shadow: var(--shadow); margin-top: 40px; border: 1px solid rgba(255,255,255,0.05);">
        <h3 style="color: var(--text-light); margin-bottom: 20px;">Current Allocations</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
            <tr style="text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <th style="padding: 12px;">House</th>
                <th style="padding: 12px;">Unit</th>
                <th style="padding: 12px;">Tenant</th>
                <th style="padding: 12px;">Rent</th>
                <th style="padding: 12px;">Date</th>
                <th style="padding: 12px;">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($view_allocations as $row): ?>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <td style="padding: 12px;"><?= htmlspecialchars($row['house_id']) ?></td>
                    <td style="padding: 12px;"><?= htmlspecialchars($row['apartment_id']) ?></td>
                    <td style="padding: 12px; font-weight: 600;"><?= htmlspecialchars($row['username']) ?></td>
                    <td style="padding: 12px; color: var(--accent);">$<?= number_format($row['amount'], 2) ?></td>
                    <td style="padding: 12px; color: var(--text-dim);"><?= date('M d, Y', strtotime($row['allocation_date'])) ?></td>
                    <td style="padding: 12px;">
                        <a href="admin_allocation.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Remove this allocation?')" style="color: #fb7185;">
                            <i class="fas fa-trash-can"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        function getRent() {
            let hID = $('#house_id').val();
            let uID = $('#unit_id').val();

            if (hID !== "" && uID !== "") {
                $.ajax({
                    url: 'get_rent.php',
                    method: 'POST',
                    data: { house_id: hID, unit_id: uID },
                    success: function(response) {
                        if (response !== "0") {
                            $('#amount_box').val(response);
                            $('#amount_box').css('border-color', '#4ade80');
                        } else {
                            $('#amount_box').val("");
                            $('#amount_box').attr("placeholder", "Unit not found in database");
                            $('#amount_box').css('border-color', '#fb7185');
                        }
                    }
                });
            }
        }


        $('#house_id, #unit_id').on('keyup blur change', function() {
            getRent();
        });
    });
</script>

</body>
</html>