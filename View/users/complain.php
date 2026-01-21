<?php
session_start();
include 'config.php';


if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: ../Log_Res/login.php");
    exit();
}

$message = "";
$logged_in_user = $_SESSION['username'];


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_complain'])) {
    $category = $_POST['category'];
    $subject_text = $_POST['subject'];
    $description = $_POST['description'] ?? '';
    $full_subject = "[" . $category . "] " . $subject_text;
    $current_date = date('Y-m-d');

    try {

        $findAdminSql = "SELECT h.created_by 
                         FROM allocate a 
                         JOIN houses h ON a.house_id::text = h.house_id::text 
                         WHERE a.username = :uname LIMIT 1";

        $adminStmt = $conn->prepare($findAdminSql);
        $adminStmt->execute([':uname' => $logged_in_user]);
        $house_info = $adminStmt->fetch(PDO::FETCH_ASSOC);

        $admin_for_this_tenant = ($house_info && !empty($house_info['created_by'])) ? $house_info['created_by'] : 'System';


        $sql = "INSERT INTO complain (username, subject, date, admin_user, status) 
                VALUES (:uname, :subj, :dt, :ad_user, 'Pending')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
                ':uname'   => $logged_in_user,
                ':subj'    => $full_subject,
                ':dt'      => $current_date,
                ':ad_user' => $admin_for_this_tenant
        ]);

        $message = "<div style='background: rgba(74, 222, 128, 0.1); color: #4ade80; padding: 15px; border-radius: 12px; border: 1px solid #4ade80; margin-bottom: 20px;'>
                        <i class='fas fa-check-circle'></i> Complaint submitted! Sent to Admin: <b>" . htmlspecialchars($admin_for_this_tenant) . "</b>
                    </div>";
    } catch (PDOException $e) {
        $message = "<div style='color: #f87171; margin-bottom: 20px;'>Error: " . $e->getMessage() . "</div>";
    }
}


try {
    $historySql = "SELECT * FROM complain WHERE username = :uname ORDER BY date DESC";
    $historyStmt = $conn->prepare($historySql);
    $historyStmt->execute([':uname' => $logged_in_user]);
    $complaints = $historyStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $complaints = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Complaint | RentPay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .table-section table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table-section th { text-align: left; padding: 12px; color: var(--accent); border-bottom: 1px solid rgba(255,255,255,0.1); }
        .table-section td { padding: 12px; border-bottom: 1px solid rgba(255,255,255,0.05); color: var(--text-light); }

        /* Dynamic Status Pills */
        .status-pill { padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; border: 1px solid; display: inline-block; }
        .status-pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border-color: rgba(245, 158, 11, 0.2); }
        .status-confirmed { background: rgba(74, 222, 128, 0.1); color: #4ade80; border-color: rgba(74, 222, 128, 0.2); }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
    <div style="margin-bottom: 30px;">
        <h1 style="margin: 0;"><i class="fas fa-circle-exclamation" style="color: #f87171;"></i> Support Center</h1>
        <p style="color: var(--text-dim); margin-top: 5px;">Report issues and monitor their resolution status.</p>
    </div>

    <?= $message ?>

    <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 30px; align-items: start;">

        <div style="background: var(--card-bg); padding: 30px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
            <h3 style="margin-top: 0; color: var(--text-light);">New Complaint</h3>
            <form action="complain.php" method="POST">
                <div style="margin-bottom: 15px;">
                    <label style="color: var(--accent); display: block; margin-bottom: 5px;">Subject</label>
                    <input type="text" name="subject" class="input-box" placeholder="Brief title" required style="width: 100%;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="color: var(--accent); display: block; margin-bottom: 5px;">Category</label>
                    <select name="category" class="input-box" style="width: 100%;">
                        <option value="Plumbing">Plumbing</option>
                        <option value="Electricity">Electricity</option>
                        <option value="Security">Security</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="color: var(--accent); display: block; margin-bottom: 5px;">Description</label>
                    <textarea name="description" class="input-box" placeholder="Explain the issue..." style="height: 100px; resize: none; width: 100%;"></textarea>
                </div>

                <button type="submit" name="submit_complain" class="pay-btn" style="width: 100%; justify-content: center; height: 45px;">
                    <i class="fas fa-paper-plane"></i> Submit to Landlord
                </button>
            </form>
        </div>

        <div class="table-section" style="background: var(--card-bg); padding: 25px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
            <h3 style="margin-top: 0; color: var(--text-light);">Recent History</h3>
            <table>
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Subject</th>
                    <th>Admin</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($complaints)): ?>
                    <?php foreach ($complaints as $row): ?>
                        <tr>
                            <td style="font-size: 0.85rem;"><?= date('M d', strtotime($row['date'])) ?></td>
                            <td style="font-size: 0.9rem;"><?= htmlspecialchars($row['subject']) ?></td>
                            <td style="color: var(--text-dim); font-size: 0.85rem;"><?= htmlspecialchars($row['admin_user']) ?></td>
                            <td>
                                <?php
                                // Fetch status and determine class
                                $status = htmlspecialchars($row['status'] ?? 'Pending');
                                $statusClass = ($status === 'Confirmed') ? 'status-confirmed' : 'status-pending';
                                ?>
                                <span class="status-pill <?= $statusClass ?>">
                                    <?= $status ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align: center; padding: 20px; color: var(--text-dim);">No history found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

</body>
</html>