<?php
// transporter_dashboard.php
// Deliveries management for Transporters

require_once 'includes/auth.php';
require_once 'config/db.php';

// Check if user is a Transporter
checkAccess(['Transporter']);

$transporter_id = getUserId();
$database = new Database();
$db = $database->getConnection();

$error = '';
$success = '';

// Handle Accept Delivery Job
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'accept_job') {
    $delivery_id = intval($_POST['delivery_id']);

    // Verify delivery is still available
    $stmt = $db->prepare("SELECT * FROM `DELIVERY` WHERE `delivery_id` = :did AND `user_id` IS NULL LIMIT 1");
    $stmt->execute(['did' => $delivery_id]);
    $job = $stmt->fetch();

    if (!$job) {
        $error = "Job is no longer available or has already been accepted.";
    } else {
        $stmt_update = $db->prepare("UPDATE `DELIVERY` SET `user_id` = :tid, `status` = 'Assigned' WHERE `delivery_id` = :did");
        $result = $stmt_update->execute([
            'tid' => $transporter_id,
            'did' => $delivery_id
        ]);
        if ($result) {
            $success = "Delivery task accepted successfully! Check 'My Deliveries' below.";
        } else {
            $error = "Failed to accept task. Please try again.";
        }
    }
}

// Handle Update Delivery Status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $delivery_id = intval($_POST['delivery_id']);
    $new_status = trim($_POST['status'] ?? '');
    $confirmation_img = trim($_POST['confirmation_img'] ?? '');

    if (!in_array($new_status, ['Picked Up', 'In Transit', 'Delivered'])) {
        $error = "Invalid status update.";
    } else {
        try {
            $db->beginTransaction();

            if ($new_status === 'Delivered') {
                $mock_img = empty($confirmation_img) ? 'delivered_signature_mock.jpg' : $confirmation_img;
                
                // Update Delivery table
                $stmt_del = $db->prepare("UPDATE `DELIVERY` SET `status` = 'Delivered', `confirmation_img` = :img WHERE `delivery_id` = :did AND `user_id` = :tid");
                $stmt_del->execute([
                    'img' => $mock_img,
                    'did' => $delivery_id,
                    'tid' => $transporter_id
                ]);

                // Get associated order_id
                $stmt_oid = $db->prepare("SELECT `order_id` FROM `DELIVERY` WHERE `delivery_id` = :did");
                $stmt_oid->execute(['did' => $delivery_id]);
                $order_id = $stmt_oid->fetchColumn();

                // Update Order table status to Delivered
                if ($order_id) {
                    $stmt_ord = $db->prepare("UPDATE `ORDER` SET `status` = 'Delivered' WHERE `order_id` = :oid");
                    $stmt_ord->execute(['oid' => $order_id]);
                }
            } else {
                // Update Delivery status (Picked Up or In Transit)
                $stmt_del = $db->prepare("UPDATE `DELIVERY` SET `status` = :status WHERE `delivery_id` = :did AND `user_id` = :tid");
                $stmt_del->execute([
                    'status' => $new_status,
                    'did' => $delivery_id,
                    'tid' => $transporter_id
                ]);
            }

            $db->commit();
            $success = "Delivery status successfully updated to: " . htmlspecialchars($new_status);
        } catch (Exception $e) {
            $db->rollBack();
            $error = "Failed to update status: " . $e->getMessage();
        }
    }
}

// Fetch Transporter Details (Capacity, Vehicle, Rating)
$stmt = $db->prepare("SELECT * FROM `TRANSPORTER` WHERE `user_id` = :tid LIMIT 1");
$stmt->execute(['tid' => $transporter_id]);
$transporter = $stmt->fetch();

// Fetch Pending Delivery Jobs (Unassigned)
$sql_jobs = "SELECT d.*, o.quantity, o.price, c.crop_name 
             FROM `DELIVERY` d 
             JOIN `ORDER` o ON d.order_id = o.order_id 
             JOIN `HAS` h ON o.order_id = h.order_id 
             JOIN `CROP` c ON h.crop_id = c.crop_id 
             WHERE d.status = 'Pending' AND d.user_id IS NULL 
             ORDER BY d.delivery_id ASC";
$stmt_j = $db->prepare($sql_jobs);
$stmt_j->execute();
$jobs = $stmt_j->fetchAll();

// Fetch Transporter's Assigned Deliveries
$sql_my_deliveries = "SELECT d.*, o.quantity, o.price, c.crop_name, b.name AS buyer_name, b.contact_no AS buyer_contact 
                      FROM `DELIVERY` d 
                      JOIN `ORDER` o ON d.order_id = o.order_id 
                      JOIN `HAS` h ON o.order_id = h.order_id 
                      JOIN `CROP` c ON h.crop_id = c.crop_id 
                      JOIN `BUYER` b ON o.user_id = b.user_id 
                      WHERE d.user_id = :tid 
                      ORDER BY d.delivery_id DESC";
$stmt_my = $db->prepare($sql_my_deliveries);
$stmt_my->execute(['tid' => $transporter_id]);
$my_deliveries = $stmt_my->fetchAll();

require_once 'includes/header.php';
?>

<div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="user-profile">
            <div class="profile-avatar" style="background-color: var(--info);">T</div>
            <h4><?php echo htmlspecialchars($transporter['name']); ?></h4>
            <span>Transporter</span>
            <div style="margin-top: 10px; font-size: 0.85rem; color: var(--text-muted);">
                Rating: <strong>⭐ <?php echo htmlspecialchars($transporter['rating']); ?>/5</strong><br>
                Vehicle: <strong><?php echo htmlspecialchars($transporter['vehicle_plate_no']); ?></strong><br>
                Capacity: <strong><?php echo htmlspecialchars($transporter['max_capacity']); ?> kg</strong>
            </div>
        </div>
        <ul class="sidebar-menu">
            <li class="active"><a href="#jobs"><ion-icon name="map-outline"></ion-icon> Available Tasks</a></li>
            <li><a href="#my-deliveries"><ion-icon name="trail-sign-outline"></ion-icon> My Deliveries</a></li>
        </ul>
    </aside>

    <!-- Main Content Area -->
    <div class="dashboard-content">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <!-- Available Jobs -->
        <section id="jobs" style="margin-bottom: 40px;">
            <h2 style="color: var(--primary-dark); margin-bottom: 20px;">Available Delivery Tasks</h2>
            
            <?php if (count($jobs) === 0): ?>
                <div class="card" style="text-align: center; padding: 40px; color: var(--text-muted);">
                    <ion-icon name="information-circle-outline" style="font-size: 3rem; color: var(--secondary-color);"></ion-icon>
                    <p style="margin-top: 10px;">No pending delivery jobs available right now. Buyers need to place orders first.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Task ID</th>
                                <th>Crop Info</th>
                                <th>Cargo Load</th>
                                <th>Pickup Location</th>
                                <th>Destination</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jobs as $job): ?>
                                <tr>
                                    <td>#DEL-<?php echo htmlspecialchars($job['delivery_id']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($job['crop_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($job['quantity']); ?> kg</td>
                                    <td style="font-size: 0.85rem;"><?php echo htmlspecialchars($job['pickup_location']); ?></td>
                                    <td style="font-size: 0.85rem;"><?php echo htmlspecialchars($job['delivery_location']); ?></td>
                                    <td>
                                        <form action="transporter_dashboard.php" method="POST">
                                            <input type="hidden" name="action" value="accept_job">
                                            <input type="hidden" name="delivery_id" value="<?php echo $job['delivery_id']; ?>">
                                            <button type="submit" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 5px;">
                                                <ion-icon name="checkmark-circle-outline"></ion-icon> Accept Job
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <!-- Transporter's Deliveries -->
        <section id="my-deliveries" style="margin-bottom: 40px;">
            <h2 style="color: var(--primary-dark); margin-bottom: 20px;">My Delivery Worklog</h2>
            
            <?php if (count($my_deliveries) === 0): ?>
                <div class="card" style="text-align: center; padding: 40px; color: var(--text-muted);">
                    <ion-icon name="bus-outline" style="font-size: 3rem; color: var(--secondary-color);"></ion-icon>
                    <p style="margin-top: 10px;">You haven't accepted any delivery jobs yet. Select a task above to begin.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Task ID</th>
                                <th>Crop Details</th>
                                <th>Recipient (Buyer)</th>
                                <th>Cargo Load</th>
                                <th>Pickup / Destination</th>
                                <th>Status</th>
                                <th>Action / Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($my_deliveries as $del): ?>
                                <tr>
                                    <td>#DEL-<?php echo htmlspecialchars($del['delivery_id']); ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($del['crop_name']); ?></strong><br>
                                        <small style="color: var(--text-muted);">Date: <?php echo htmlspecialchars($del['date']); ?></small>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($del['buyer_name']); ?></strong><br>
                                        <small style="color: var(--text-muted);"><?php echo htmlspecialchars($del['buyer_contact']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($del['quantity']); ?> kg</td>
                                    <td style="font-size: 0.8rem;">
                                        🟢 <strong>Pickup:</strong> <?php echo htmlspecialchars($del['pickup_location']); ?><br>
                                        🔴 <strong>Dropoff:</strong> <?php echo htmlspecialchars($del['delivery_location']); ?>
                                    </td>
                                    <td>
                                        <span class="badge <?php 
                                            if ($del['status'] === 'Delivered') echo 'badge-success';
                                            elseif ($del['status'] === 'Assigned') echo 'badge-warning';
                                            else echo 'badge-info';
                                        ?>">
                                            <?php echo htmlspecialchars($del['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($del['status'] !== 'Delivered'): ?>
                                            <form action="transporter_dashboard.php" method="POST" style="display: flex; flex-direction: column; gap: 8px; max-width: 150px;">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="delivery_id" value="<?php echo $del['delivery_id']; ?>">
                                                
                                                <select name="status" class="form-control" style="padding: 4px; font-size: 0.85rem;" required>
                                                    <option value="Picked Up" <?php echo $del['status'] == 'Picked Up' ? 'selected' : ''; ?>>Picked Up</option>
                                                    <option value="In Transit" <?php echo $del['status'] == 'In Transit' ? 'selected' : ''; ?>>In Transit</option>
                                                    <option value="Delivered">Delivered</option>
                                                </select>
                                                
                                                <!-- Mock image/signature name -->
                                                <input type="text" name="confirmation_img" class="form-control" style="padding: 4px; font-size: 0.75rem;" placeholder="Confirmation label (opt)">
                                                
                                                <button type="submit" class="btn btn-primary btn-sm" style="padding: 4px 8px;">Update</button>
                                            </form>
                                        <?php else: ?>
                                            <span style="color: var(--success); font-size: 0.85rem; font-weight: bold; display: flex; align-items: center; gap: 3px;">
                                                <ion-icon name="checkmark-done-circle-outline" style="font-size: 1.1rem;"></ion-icon>
                                                Task Complete
                                            </span>
                                            <?php if ($del['confirmation_img']): ?>
                                                <small style="display: block; color: var(--text-muted); font-size: 0.7rem; margin-top: 3px;">Conf: <?php echo htmlspecialchars($del['confirmation_img']); ?></small>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
