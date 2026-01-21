<?php
session_start();
include "config.php";


if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$currentUser = $_SESSION['username'];
$id = $_GET['id'] ?? '';
$house = null;


if ($id) {
    $stmt = $conn->prepare("SELECT * FROM houses WHERE house_id = :id AND created_by = :user");
    $stmt->execute([':id' => $id, ':user' => $currentUser]);
    $house = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$house) {
    die("<div style='color:white; background:#0f172a; height:100vh; display:flex; align-items:center; justify-content:center; font-family:sans-serif;'>
            <div style='text-align:center;'>
                <h2>Access Denied</h2>
                <p>House not found or you do not have permission to edit this property.</p>
                <a href='admin_houses.php' style='color:#38bdf8;'>Back to Dashboard</a>
            </div>
         </div>");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['house_name']);
    $loc  = trim($_POST['location']);

    try {
        $stmt = $conn->prepare("UPDATE houses SET house_name = :name, location = :loc WHERE house_id = :id AND created_by = :user");
        $stmt->execute([
                ':name' => $name,
                ':loc' => $loc,
                ':id' => $id,
                ':user' => $currentUser
        ]);
        header("Location: admin_houses.php?msg=updated");
        exit;
    } catch (PDOException $e) {
        $error = "Error updating property: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Property | RentPay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main">
    <h1><i class="fas fa-edit"></i> Edit Property</h1>

    <?php if (isset($error)): ?>
        <div class="status-badge" style="background: rgba(248, 113, 113, 0.1); color: var(--danger); border-color: var(--danger); margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="complain-container" style="max-width: 600px;">
        <div style="margin-bottom: 25px;">
            <span class="status-badge">Property ID: <?php echo htmlspecialchars($id); ?></span>
        </div>

        <form method="POST">
            <div style="margin-bottom: 20px;">
                <label style="display:block; color:var(--text-dim); margin-bottom:8px; font-size:0.9rem;">Building Name</label>
                <input type="text" name="house_name" class="input-box"
                       value="<?php echo htmlspecialchars($house['house_name']); ?>" required>
            </div>

            <div style="margin-bottom: 30px;">
                <label style="display:block; color:var(--text-dim); margin-bottom:8px; font-size:0.9rem;">Location / Address</label>
                <input type="text" name="location" class="input-box"
                       value="<?php echo htmlspecialchars($house['location']); ?>" required>
            </div>

            <div style="display: flex; align-items: center; gap: 20px;">
                <button type="submit" class="pay-btn">
                    <i class="fas fa-save"></i> Update Property
                </button>
                <a href="admin_houses.php" style="color: var(--text-dim); text-decoration: none; font-size: 0.9rem; transition: 0.3s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-dim)'">
                    <i class="fas fa-arrow-left"></i> Cancel & Go Back
                </a>
            </div>
        </form>
    </div>
</div>

</body>
</html>