<?php
session_start();
include 'config.php';


if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Log_Res/login.php");
    exit();
}

$message = "";
$current_admin = $_SESSION['username'];


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_complaint'])) {
    $complaint_id = $_POST['complaint_id'];

    try {
        $stmt = $conn->prepare("UPDATE complain SET status = 'Confirmed' WHERE id = :id AND admin_user = :admin");
        $stmt->execute([
                ':id' => $complaint_id,
                ':admin' => $current_admin
        ]);
        $message = "<div style='background: rgba(74, 222, 128, 0.1); color: #4ade80; padding: 15px; border-radius: 12px; border: 1px solid #4ade80; margin-bottom: 20px;'>
                        <i class='fas fa-check-double'></i> Complaint status updated to Confirmed!
                    </div>";
    } catch (PDOException $e) {
        $message = "<div style='color: #fb7185; margin-bottom: 20px;'>Error: " . $e->getMessage() . "</div>";
    }
}


try {
    $stmt = $conn->prepare("SELECT * FROM complain WHERE admin_user = :admin ORDER BY date DESC");
    $stmt->execute([':admin' => $current_admin]);
    $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $complaints = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Complaints | RentPay Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main">
    <div style="margin-bottom: 30px;">
        <h1 style="margin: 0; color: white;"><i class="fas fa-clipboard-check" style="color: #fb7185;"></i> Manage Complaints</h1>
        <p style="color: var(--text-dim); margin-top: 5px;">Review and confirm issues assigned to you.</p>
    </div>

    <?= $message ?>

    <div class="table-section" style="background: var(--card-bg); padding: 25px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
            <tr style="text-align: left; color: #fb7185; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <th style="padding: 15px;">Date</th>
                <th style="padding: 15px;">Tenant</th>
                <th style="padding: 15px;">Issue</th>
                <th style="padding: 15px;">Status</th>
                <th style="padding: 15px;">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($complaints)): ?>
                <?php foreach ($complaints as $row): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); color: var(--text-light);">
                        <td style="padding: 15px; font-size: 0.9rem;"><?= date('M d, Y', strtotime($row['date'])) ?></td>
                        <td style="padding: 15px; font-weight: bold;"><?= htmlspecialchars($row['username']) ?></td>
                        <td style="padding: 15px;"><?= htmlspecialchars($row['subject']) ?></td>
                        <td style="padding: 15px;">
                            <?php if ($row['status'] === 'Confirmed'): ?>
                                <span style="background: rgba(74, 222, 128, 0.1); color: #4ade80; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; border: 1px solid #4ade80;">
                                        <i class="fas fa-check-circle"></i> Confirmed
                                    </span>
                            <?php else: ?>
                                <span style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; border: 1px solid #f59e0b;">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 15px;">
                            <?php if ($row['status'] !== 'Confirmed'): ?>
                                <form method="POST" action="admin_complain.php" onsubmit="return confirm('Change status to Confirmed?');">
                                    <input type="hidden" name="complaint_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="confirm_complaint"
                                            style="background: none; border: 1px solid #4ade80; color: #4ade80; padding: 6px 12px; border-radius: 8px; cursor: pointer; font-size: 0.85rem; transition: 0.3s;">
                                        Confirm Issue
                                    </button>
                                </form>
                            <?php else: ?>
                                <span style="color: var(--text-dim); font-size: 0.85rem;"><i class="fas fa-lock"></i> Locked</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align: center; padding: 40px; color: var(--text-dim);">No complaints found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>