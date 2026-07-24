<?php
// admin_dashboard.php
// Central administration dashboard for Arunella system managers

require_once 'includes/auth.php';
require_once 'config/db.php';

// Check if user is an Admin
checkAccess(['Admin']);

$admin_id = getUserId();
$database = new Database();
$db = $database->getConnection();

$error = '';
$success = '';

// Handle Crop Status Flagging
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $crop_id = intval($_POST['crop_id']);
    
    if ($_POST['action'] === 'flag_crop') {
        $stmt = $db->prepare("UPDATE `CROP` SET `status` = 'Flagged' WHERE `crop_id` = :cid");
        if ($stmt->execute(['cid' => $crop_id])) {
            $success = "Crop listing has been flagged and suspended from the market.";
        } else {
            $error = "Failed to update crop status.";
        }
    } elseif ($_POST['action'] === 'unflag_crop') {
        $stmt = $db->prepare("UPDATE `CROP` SET `status` = 'Available' WHERE `crop_id` = :cid");
        if ($stmt->execute(['cid' => $crop_id])) {
            $success = "Crop listing reinstated successfully.";
        } else {
            $error = "Failed to reinstate crop.";
        }
    }
}

// Fetch stats counters
$count_farmers = $db->query("SELECT COUNT(*) FROM `FARMER`")->fetchColumn();
$count_buyers = $db->query("SELECT COUNT(*) FROM `BUYER`")->fetchColumn();
$count_transporters = $db->query("SELECT COUNT(*) FROM `TRANSPORTER`")->fetchColumn();
$count_crops = $db->query("SELECT COUNT(*) FROM `CROP`")->fetchColumn();
$count_active_del = $db->query("SELECT COUNT(*) FROM `DELIVERY` WHERE `status` != 'Delivered'")->fetchColumn();
$sum_revenue = $db->query("SELECT SUM(`price`) FROM `ORDER` WHERE `status` = 'Delivered'")->fetchColumn() ?: 0.00;

// Fetch Farmers list
$farmers = $db->query("SELECT * FROM `FARMER` ORDER BY `user_id` DESC")->fetchAll();

// Fetch Buyers list
$buyers = $db->query("SELECT * FROM `BUYER` ORDER BY `user_id` DESC")->fetchAll();

// Fetch Transporters list
$transporters = $db->query("SELECT * FROM `TRANSPORTER` ORDER BY `user_id` DESC")->fetchAll();

// Fetch All Crops
$crops = $db->query("SELECT c.*, f.name AS farmer_name FROM `CROP` c JOIN `FARMER` f ON c.user_id = f.user_id ORDER BY c.crop_id DESC")->fetchAll();

// Fetch All Deliveries
$deliveries = $db->query("SELECT d.*, o.quantity, c.crop_name, t.name AS transporter_name 
                          FROM `DELIVERY` d 
                          JOIN `ORDER` o ON d.order_id = o.order_id 
                          JOIN `HAS` h ON o.order_id = h.order_id 
                          JOIN `CROP` c ON h.crop_id = c.crop_id 
                          LEFT JOIN `TRANSPORTER` t ON d.user_id = t.user_id 
                          ORDER BY d.delivery_id DESC")->fetchAll();

require_once 'includes/header.php';
?>

<div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="user-profile">
            <div class="profile-avatar" style="background-color: var(--primary-dark);">A</div>
            <h4><?php echo htmlspecialchars(getUserName()); ?></h4>
            <span>Administrator</span>
        </div>
        <ul class="sidebar-menu">
            <li class="active"><a href="#stats"><ion-icon name="stats-chart-outline"></ion-icon> Stats Overview</a></li>
            <li><a href="#users"><ion-icon name="people-outline"></ion-icon> Manage Users</a></li>
            <li><a href="#crops-audit"><ion-icon name="leaf-outline"></ion-icon> Crop Audit</a></li>
            <li><a href="#logistics-audit"><ion-icon name="map-outline"></ion-icon> Logistics Tracking</a></li>
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

        <!-- Stats Overview -->
        <section id="stats" style="margin-bottom: 40px;">
            <h2 style="color: var(--primary-dark); margin-bottom: 20px;">System Oversight Panel</h2>
            
            <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: rgba(46,125,50,0.1);"><ion-icon name="logo-medium"></ion-icon></div>
                    <div class="stat-info">
                        <h3><?php echo $count_farmers; ?></h3>
                        <p>Total Farmers</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: rgba(255,152,0,0.1);"><ion-icon name="basket-outline"></ion-icon></div>
                    <div class="stat-info">
                        <h3><?php echo $count_buyers; ?></h3>
                        <p>Total Buyers</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: rgba(52,152,219,0.1);"><ion-icon name="bus-outline"></ion-icon></div>
                    <div class="stat-info">
                        <h3><?php echo $count_transporters; ?></h3>
                        <p>Transporters</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: rgba(155,89,182,0.1);"><ion-icon name="cash-outline"></ion-icon></div>
                    <div class="stat-info">
                        <h3><?php echo formatLKR($sum_revenue); ?></h3>
                        <p>Delivered Volume</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- User Administration -->
        <section id="users" style="margin-bottom: 40px;">
            <h2 style="color: var(--primary-dark); margin-bottom: 20px;">Relational Stakeholder Tables</h2>
            
            <div class="card">
                <h3 style="color: var(--primary-dark); margin-bottom: 15px; border-bottom: 2px solid var(--border-color); padding-bottom: 8px;">1. FARMER Table Records</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>NIC</th>
                                <th>Contact</th>
                                <th>District</th>
                                <th>Wallet (LKR)</th>
                                <th>Bank Account</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($farmers as $f): ?>
                                <tr>
                                    <td>#F-<?php echo htmlspecialchars($f['user_id']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($f['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($f['email']); ?></td>
                                    <td><?php echo htmlspecialchars($f['nic']); ?></td>
                                    <td><?php echo htmlspecialchars($f['contact_no']); ?></td>
                                    <td><?php echo htmlspecialchars($f['district']); ?></td>
                                    <td style="font-weight: bold; color: var(--primary-color);"><?php echo number_format($f['wallet'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($f['bank_ac_no']); ?></td>
                                    <td>⭐ <?php echo htmlspecialchars($f['rating']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card" style="margin-top: 30px;">
                <h3 style="color: var(--accent-color); margin-bottom: 15px; border-bottom: 2px solid var(--border-color); padding-bottom: 8px;">2. BUYER Table Records</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Business Name</th>
                                <th>Email</th>
                                <th>NIC</th>
                                <th>Contact</th>
                                <th>District</th>
                                <th>Business Reg</th>
                                <th>Market Location</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($buyers as $b): ?>
                                <tr>
                                    <td>#B-<?php echo htmlspecialchars($b['user_id']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($b['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($b['email']); ?></td>
                                    <td><?php echo htmlspecialchars($b['nic']); ?></td>
                                    <td><?php echo htmlspecialchars($b['contact_no']); ?></td>
                                    <td><?php echo htmlspecialchars($b['district']); ?></td>
                                    <td><?php echo htmlspecialchars($b['business_reg_no']); ?></td>
                                    <td style="font-size: 0.85rem;"><?php echo htmlspecialchars($b['market_location']); ?></td>
                                    <td>⭐ <?php echo htmlspecialchars($b['rating']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card" style="margin-top: 30px;">
                <h3 style="color: var(--info); margin-bottom: 15px; border-bottom: 2px solid var(--border-color); padding-bottom: 8px;">3. TRANSPORTER Table Records</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Transporter Name</th>
                                <th>Email</th>
                                <th>NIC</th>
                                <th>Contact</th>
                                <th>District</th>
                                <th>Vehicle Plate</th>
                                <th>Max Capacity</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transporters as $t): ?>
                                <tr>
                                    <td>#T-<?php echo htmlspecialchars($t['user_id']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($t['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($t['email']); ?></td>
                                    <td><?php echo htmlspecialchars($t['nic']); ?></td>
                                    <td><?php echo htmlspecialchars($t['contact_no']); ?></td>
                                    <td><?php echo htmlspecialchars($t['district']); ?></td>
                                    <td><span style="font-family: monospace; background: #e2e8f0; padding: 2px 6px; border-radius: 4px;"><?php echo htmlspecialchars($t['vehicle_plate_no']); ?></span></td>
                                    <td><?php echo htmlspecialchars($t['max_capacity']); ?> kg</td>
                                    <td>⭐ <?php echo htmlspecialchars($t['rating']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Crop Audit -->
        <section id="crops-audit" style="margin-bottom: 40px;">
            <h2 style="color: var(--primary-dark); margin-bottom: 20px;">Crop Inventory Suspension & Audit</h2>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Crop ID</th>
                            <th>Crop Name</th>
                            <th>Farmer</th>
                            <th>Pricing</th>
                            <th>Stock</th>
                            <th>Exp Date</th>
                            <th>Status</th>
                            <th>Oversight Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($crops as $crop): ?>
                            <tr>
                                <td>#CRP-<?php echo htmlspecialchars($crop['crop_id']); ?></td>
                                <td><strong><?php echo htmlspecialchars($crop['crop_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($crop['farmer_name']); ?></td>
                                <td><?php echo formatLKR($crop['price_per_kg']); ?>/kg</td>
                                <td><?php echo htmlspecialchars($crop['stock']); ?> kg</td>
                                <td><?php echo htmlspecialchars($crop['exp_date']); ?></td>
                                <td>
                                    <span class="badge <?php echo $crop['status'] == 'Available' ? 'badge-success' : 'badge-danger'; ?>">
                                        <?php echo htmlspecialchars($crop['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($crop['status'] !== 'Flagged'): ?>
                                        <form action="admin_dashboard.php#crops-audit" method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="flag_crop">
                                            <input type="hidden" name="crop_id" value="<?php echo $crop['crop_id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" style="padding: 4px 8px; font-size: 0.75rem;">Flag / Suspend</button>
                                        </form>
                                    <?php else: ?>
                                        <form action="admin_dashboard.php#crops-audit" method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="unflag_crop">
                                            <input type="hidden" name="crop_id" value="<?php echo $crop['crop_id']; ?>">
                                            <button type="submit" class="btn btn-primary btn-sm" style="padding: 4px 8px; font-size: 0.75rem; background-color: var(--success); border-color: var(--success);">Reinstate</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Logistics Audit -->
        <section id="logistics-audit" style="margin-bottom: 40px;">
            <h2 style="color: var(--primary-dark); margin-bottom: 20px;">Supply Chain Logistics & Route Tracking</h2>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Delivery ID</th>
                            <th>Crop Item</th>
                            <th>Transporter</th>
                            <th>Pickup Location</th>
                            <th>Delivery Location</th>
                            <th>Status</th>
                            <th>Order Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($deliveries as $d): ?>
                            <tr>
                                <td>#DEL-<?php echo htmlspecialchars($d['delivery_id']); ?></td>
                                <td><strong><?php echo htmlspecialchars($d['crop_name']); ?></strong> (<?php echo htmlspecialchars($d['quantity']); ?> kg)</td>
                                <td>
                                    <?php if ($d['transporter_name']): ?>
                                        <span style="font-weight: 500;"><?php echo htmlspecialchars($d['transporter_name']); ?></span>
                                    <?php else: ?>
                                        <em style="color: var(--text-muted);">Unassigned</em>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size: 0.8rem;"><?php echo htmlspecialchars($d['pickup_location']); ?></td>
                                <td style="font-size: 0.8rem;"><?php echo htmlspecialchars($d['delivery_location']); ?></td>
                                <td>
                                    <span class="badge <?php 
                                        if ($d['status'] === 'Delivered') echo 'badge-success';
                                        elseif ($d['status'] === 'Assigned') echo 'badge-warning';
                                        else echo 'badge-info';
                                    ?>">
                                        <?php echo htmlspecialchars($d['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($d['date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
