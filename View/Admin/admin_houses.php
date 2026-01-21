<?php
include "config.php";
require_once __DIR__ . '/../../Controllers/HouseController.php';

$houseCtrl = new HouseController($conn);
$data = $houseCtrl->handleRequest();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Houses | RentPay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1><i class="fas fa-hotel"></i> Your Houses</h1>
        <div class="status-badge">
            <i class="fas fa-user-circle"></i> User: <?= htmlspecialchars($data['username']) ?>
        </div>
    </div>

    <?php if ($data['message']): ?>
        <div class="status-badge" style="margin-bottom: 20px; width: fit-content; background: rgba(74, 222, 128, 0.1); color: var(--success);">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($data['message']) ?>
        </div>
    <?php endif; ?>

    <div class="complain-container">
        <h3 style="color: var(--accent); margin-bottom: 20px;">Add New Property</h3>
        <form method="POST" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
            <div style="flex: 1; min-width: 150px;">
                <input type="text" name="house_id" class="input-box" placeholder="House ID (e.g. H-101)" required>
            </div>
            <div style="flex: 2; min-width: 200px;">
                <input type="text" name="house_name" class="input-box" placeholder="Property Name" required>
            </div>
            <div style="flex: 2; min-width: 200px;">
                <input type="text" name="location" class="input-box" placeholder="Location/Address" required>
            </div>
            <button type="submit" name="add_house" class="pay-btn">
                <i class="fas fa-plus"></i> Add House
            </button>
        </form>
    </div>

    <div class="table-section">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Location</th>
                <th style="text-align: right;">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($data['houses'])): ?>
                <?php foreach ($data['houses'] as $h): ?>
                    <tr>
                        <td style="font-weight: bold; color: var(--accent);"><?= htmlspecialchars($h['house_id']) ?></td>
                        <td><strong><?= htmlspecialchars($h['house_name']) ?></strong></td>
                        <td style="color: var(--text-dim);"><i class="fas fa-map-marker-alt" style="font-size: 0.8rem;"></i> <?= htmlspecialchars($h['location']) ?></td>
                        <td style="text-align: right;">
                            <a href="admin_houses.php?action=delete&id=<?= urlencode($h['house_id']) ?>"
                               class="action-btn"
                               style="color: var(--danger);"
                               onclick="return confirm('Delete this property?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center; color: var(--text-dim); padding: 40px;">
                        No houses found for your account.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>