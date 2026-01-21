<?php
session_start();
include 'config.php';
require_once dirname(__FILE__) . '/../../Controllers/ApartmentController.php';

$appCtrl = new ApartmentController($conn);
$data = $appCtrl->handleRequest();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Apartments | RentPay Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main">
    <h1><i class="fas fa-door-open"></i> Apartment Units</h1>

    <?php if (isset($data['message']) && $data['message']): ?>
        <div style="background: rgba(74, 222, 128, 0.1); color: var(--success); padding: 15px; margin-bottom: 20px; border-radius: 10px; border: 1px solid var(--success);">
            <?php echo htmlspecialchars($data['message']); ?>
        </div>
    <?php endif; ?>

    <div class="complain-container">
        <h3 style="color: var(--accent); margin-bottom: 20px;">Create New Unit</h3>
        <form method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: flex-end;">
            <div>
                <label style="display:block; margin-bottom:5px; font-size:0.8rem; color:var(--text-dim)">Select House</label>
                <select name="house_id" class="input-box" required style="width: 100%;">
                    <option value="">-- Choose Building --</option>
                    <?php foreach($data['houses'] as $house): ?>
                        <option value="<?php echo htmlspecialchars($house['house_id']); ?>">
                            <?php echo htmlspecialchars($house['house_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="display:block; margin-bottom:5px; font-size:0.8rem; color:var(--text-dim)">Unit No.</label>
                <input type="text" name="unit_no" class="input-box" placeholder="e.g. B-5" required style="width: 100%;">
            </div>
            <div>
                <label style="display:block; margin-bottom:5px; font-size:0.8rem; color:var(--text-dim)">Monthly Rent</label>
                <input type="number" name="rent" class="input-box" placeholder="1200" required style="width: 100%;">
            </div>
            <button type="submit" name="add_unit" class="pay-btn" style="height: 45px; justify-content:center;">
                <i class="fas fa-plus"></i> Add Unit
            </button>
        </form>
    </div>

    <div class="table-section">
        <table>
            <thead>
            <tr>
                <th>Unit</th>
                <th>Belongs to House</th>
                <th>Rent</th>
                <th>Status</th>
                <th style="text-align: right;">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($data['units'])): ?>
                <?php foreach($data['units'] as $unit): ?>
                    <tr>
                        <td style="font-weight:bold"><?php echo htmlspecialchars($unit['unit_id']); ?></td>
                        <td style="color:var(--accent)"><?php echo htmlspecialchars($unit['house_name']); ?></td>
                        <td>$<?php echo number_format($unit['rent'], 2); ?></td>
                        <td><span class="status-badge"><?php echo htmlspecialchars($unit['status']); ?></span></td>
                        <td style="text-align: right;">
                            <a href="edit_unit.php?id=<?php echo urlencode($unit['unit_id']); ?>" style="color:var(--warning); margin-right:15px;"><i class="fas fa-pen-to-square"></i></a>
                            <a href="admin_apartments.php?delete_id=<?php echo urlencode($unit['unit_id']); ?>" style="color:var(--danger);" onclick="return confirm('Delete this unit?')"><i class="fas fa-trash-can"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center; padding:20px;">No apartment units found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>