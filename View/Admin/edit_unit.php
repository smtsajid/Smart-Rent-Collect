<?php
session_start();
include 'config.php';


$currentUser = $_SESSION['username'] ?? null;
if (!$currentUser) { header("Location: login.php"); exit; }

$unit_id = $_GET['id'] ?? '';
$unit = null;
$error = '';


if ($unit_id) {

    $stmt = $conn->prepare("SELECT a.* FROM apartments a 
                            JOIN houses h ON a.house_id = h.house_id 
                            WHERE a.unit_id = :id AND h.created_by = :user");
    $stmt->execute([':id' => $unit_id, ':user' => $currentUser]);
    $unit = $stmt->fetch(PDO::FETCH_ASSOC);


    $stmtH = $conn->prepare("SELECT house_id, house_name FROM houses WHERE created_by = :user ORDER BY house_name ASC");
    $stmtH->execute([':user' => $currentUser]);
    $houses = $stmtH->fetchAll(PDO::FETCH_ASSOC);
}

if (!$unit) {
    die("<div style='color:white; background:#0f172a; height:100vh; padding:20px;'>Access Denied or Unit Not Found.</div>");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_unit'])) {
    $new_house_id = $_POST['house_id'];
    $rent = $_POST['rent'];
    $status = $_POST['status'];
    $target_id = $_POST['unit_id'];

    try {

        $sql = "UPDATE apartments SET 
                house_id = :h, 
                rent = :r, 
                status = :s 
                WHERE unit_id = :u 
                AND house_id IN (SELECT house_id FROM houses WHERE created_by = :user)";

        $updateStmt = $conn->prepare($sql);
        $result = $updateStmt->execute([
                ':h' => $new_house_id,
                ':r' => $rent,
                ':s' => $status,
                ':u' => $target_id,
                ':user' => $currentUser
        ]);

        if ($result) {
            header("Location: admin_apartments.php?msg=updated");
            exit;
        } else {
            $error = "Update failed. You might not have permission.";
        }
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Apartment | RentPay Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main">
    <h1><i class="fas fa-edit"></i> Edit Unit: <?php echo htmlspecialchars($unit['unit_id']); ?></h1>

    <?php if ($error): ?>
        <div style="background:rgba(248, 113, 113, 0.1); color: var(--danger); padding:15px; border-radius:10px; margin-bottom:20px;">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="complain-container" style="max-width: 600px;">
        <form method="POST">
            <input type="hidden" name="unit_id" value="<?php echo htmlspecialchars($unit['unit_id']); ?>">

            <div style="margin-bottom: 20px;">
                <label style="display:block; margin-bottom:8px; color:var(--text-dim)">Belongs to House</label>
                <select name="house_id" class="input-box" required style="width: 100%;">
                    <?php foreach($houses as $h): ?>
                        <option value="<?php echo $h['house_id']; ?>" <?php echo ($h['house_id'] == $unit['house_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($h['house_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display:block; margin-bottom:8px; color:var(--text-dim)">Monthly Rent</label>
                <input type="number" name="rent" class="input-box" value="<?php echo htmlspecialchars($unit['rent']); ?>" required style="width: 100%;">
            </div>

            <div style="margin-bottom: 30px;">
                <label style="display:block; margin-bottom:8px; color:var(--text-dim)">Status</label>
                <select name="status" class="input-box" style="width: 100%;">
                    <option value="Vacant" <?php echo ($unit['status'] == 'Vacant') ? 'selected' : ''; ?>>Vacant</option>
                    <option value="Occupied" <?php echo ($unit['status'] == 'Occupied') ? 'selected' : ''; ?>>Occupied</option>
                    <option value="Maintenance" <?php echo ($unit['status'] == 'Maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                </select>
            </div>

            <div style="display: flex; gap: 15px;">
                <button type="submit" name="update_unit" class="pay-btn">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="admin_apartments.php" style="text-decoration:none; color:var(--text-dim); padding:12px;">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>