<?php
// farmer_dashboard.php
// Dashboard for Farmers

require_once 'includes/auth.php';
require_once 'config/db.php';

// Check if user is a Farmer
checkAccess(['Farmer']);

$farmer_id = getUserId();
$database = new Database();
$db = $database->getConnection();

$error = '';
$success = '';

// Handle Add Crop Listing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_crop') {
    $crop_name = trim($_POST['crop_name'] ?? '');
    $price_per_kg = trim($_POST['price_per_kg'] ?? '');
    $stock = trim($_POST['stock'] ?? '');
    $exp_date = trim($_POST['exp_date'] ?? '');
    $min_price = trim($_POST['min_price'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $crop_type = trim($_POST['crop_type'] ?? 'vegetable');

    // Default crop images based on selected category
    $image = 'vegetables.jpg';
    if ($crop_type === 'carrot') $image = 'carrot.jpg';
    elseif ($crop_type === 'potato') $image = 'potato.jpg';
    elseif ($crop_type === 'leeks') $image = 'leeks.jpg';
    elseif ($crop_type === 'onion') $image = 'onion.jpg';
    elseif ($crop_type === 'cabbage') $image = 'cabbage.jpg';
    elseif ($crop_type === 'paddy') $image = 'paddy.jpg';
    elseif ($crop_type === 'coconut') $image = 'coconut.jpg';

    if (empty($crop_name) || empty($price_per_kg) || empty($stock) || empty($exp_date) || empty($min_price)) {
        $error = "Please fill in all required fields.";
    } elseif (!is_numeric($price_per_kg) || !is_numeric($stock) || !is_numeric($min_price)) {
        $error = "Prices and stock levels must be numeric.";
    } else {
        $sql = "INSERT INTO `CROP` (`user_id`, `crop_name`, `price_per_kg`, `stock`, `status`, `uploaded_date`, `exp_date`, `min_price`, `description`, `image`) 
                VALUES (:user_id, :crop_name, :price_per_kg, :stock, 'Available', CURDATE(), :exp_date, :min_price, :description, :image)";
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            'user_id' => $farmer_id,
            'crop_name' => $crop_name,
            'price_per_kg' => $price_per_kg,
            'stock' => $stock,
            'exp_date' => $exp_date,
            'min_price' => $min_price,
            'description' => $description,
            'image' => $image
        ]);
        if ($result) {
            $success = "Crop listing added successfully!";
        } else {
            $error = "Failed to add crop. Please try again.";
        }
    }
}

// Handle Wallet Cash Out
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'withdraw') {
    $withdraw_amount = trim($_POST['amount'] ?? '');
    
    // Get current wallet balance
    $stmt = $db->prepare("SELECT `wallet` FROM `FARMER` WHERE `user_id` = :id");
    $stmt->execute(['id' => $farmer_id]);
    $current_wallet = $stmt->fetchColumn();

    if (empty($withdraw_amount) || !is_numeric($withdraw_amount) || $withdraw_amount <= 0) {
        $error = "Please enter a valid withdrawal amount.";
    } elseif ($withdraw_amount > $current_wallet) {
        $error = "Insufficient funds in your wallet.";
    } else {
        $new_wallet = $current_wallet - $withdraw_amount;
        $stmt = $db->prepare("UPDATE `FARMER` SET `wallet` = :new_w WHERE `user_id` = :id");
        $result = $stmt->execute(['new_w' => $new_wallet, 'id' => $farmer_id]);
        if ($result) {
            $success = "Successfully withdrawn " . formatLKR($withdraw_amount) . " to your bank account.";
        } else {
            $error = "Withdrawal failed. Please try again.";
        }
    }
}

// Fetch Farmer Details (Wallet, Bank details, rating)
$stmt = $db->prepare("SELECT * FROM `FARMER` WHERE `user_id` = :id LIMIT 1");
$stmt->execute(['id' => $farmer_id]);
$farmer = $stmt->fetch();

// Fetch Farmer Crops
$stmt = $db->prepare("SELECT * FROM `CROP` WHERE `user_id` = :id ORDER BY `crop_id` DESC");
$stmt->execute(['id' => $farmer_id]);
$crops = $stmt->fetchAll();

// Fetch Sales / Orders for this Farmer's crops
$sql_sales = "SELECT o.*, c.crop_name, b.name AS buyer_name, b.contact_no, b.district AS buyer_district 
              FROM `ORDER` o 
              JOIN `HAS` h ON o.order_id = h.order_id 
              JOIN `CROP` c ON h.crop_id = c.crop_id 
              JOIN `BUYER` b ON o.user_id = b.user_id 
              WHERE c.user_id = :farmer_id 
              ORDER BY o.date DESC";
$stmt_sales = $db->prepare($sql_sales);
$stmt_sales->execute(['farmer_id' => $farmer_id]);
$sales = $stmt_sales->fetchAll();

require_once 'includes/header.php';
?>

<div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="user-profile">
            <div class="profile-avatar">F</div>
            <h4><?php echo htmlspecialchars($farmer['name']); ?></h4>
            <span>Farmer</span>
            <div style="margin-top: 10px; font-size: 0.85rem; color: var(--text-muted);">
                Rating: <strong>⭐ <?php echo htmlspecialchars($farmer['rating']); ?>/5</strong>
            </div>
        </div>
        <ul class="sidebar-menu">
            <li class="active"><a href="#overview"><ion-icon name="grid-outline"></ion-icon> Overview</a></li>
            <li><a href="#listings"><ion-icon name="list-circle-outline"></ion-icon> My Crop Listings</a></li>
            <li><a href="#add-listing"><ion-icon name="add-circle-outline"></ion-icon> Add New Crop</a></li>
            <li><a href="#sales"><ion-icon name="cash-outline"></ion-icon> Sales History</a></li>
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

        <!-- Overview Section -->
        <section id="overview" style="margin-bottom: 40px;">
            <h2 style="color: var(--primary-dark); margin-bottom: 20px;">Farmer Overview</h2>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><ion-icon name="wallet-outline"></ion-icon></div>
                    <div class="stat-info">
                        <h3><?php echo formatLKR($farmer['wallet']); ?></h3>
                        <p>Wallet Balance</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><ion-icon name="leaf-outline"></ion-icon></div>
                    <div class="stat-info">
                        <h3><?php echo count($crops); ?></h3>
                        <p>Total Crops Listed</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><ion-icon name="cart-outline"></ion-icon></div>
                    <div class="stat-info">
                        <h3><?php echo count($sales); ?></h3>
                        <p>Completed Orders</p>
                    </div>
                </div>
            </div>

            <!-- Wallet Cashout Widget -->
            <div class="card" style="background: linear-gradient(135deg, #ffffff 0%, #f1f8e9 100%);">
                <h3 style="color: var(--primary-dark); margin-bottom: 10px;">Withdraw Earnings</h3>
                <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 15px;">
                    Send funds instantly to your registered bank account: <strong><?php echo htmlspecialchars($farmer['bank_ac_no']); ?></strong>.
                </p>
                <form action="farmer_dashboard.php" method="POST" style="display: flex; gap: 15px; max-width: 450px; align-items: flex-end;">
                    <input type="hidden" name="action" value="withdraw">
                    <div class="form-group" style="flex: 1; margin-bottom: 0;">
                        <label for="withdraw-amount">Amount (Rs.)</label>
                        <input type="number" name="amount" id="withdraw-amount" class="form-control" placeholder="e.g. 5000" min="1" max="<?php echo $farmer['wallet']; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="height: 45px;">Withdraw to Bank</button>
                </form>
            </div>
        </section>

        <!-- Crop Listings -->
        <section id="listings" style="margin-bottom: 40px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: var(--primary-dark);">My Active Crop Listings</h2>
                <a href="#add-listing" class="btn btn-primary btn-sm">Add Crop</a>
            </div>

            <?php if (count($crops) === 0): ?>
                <div class="card" style="text-align: center; padding: 40px; color: var(--text-muted);">
                    <ion-icon name="information-circle-outline" style="font-size: 3rem; color: var(--secondary-color);"></ion-icon>
                    <p style="margin-top: 10px;">You haven't listed any crops yet. Use the form below to create your first crop listing!</p>
                </div>
            <?php else: ?>
                <div class="crop-grid">
                    <?php foreach ($crops as $crop): ?>
                        <div class="crop-card">
                            <div class="crop-img">
                                <ion-icon name="image-outline" style="font-size: 3rem; opacity: 0.3;"></ion-icon>
                                <span class="crop-tag <?php echo $crop['status'] == 'Available' ? '' : 'danger'; ?>">
                                    <?php echo htmlspecialchars($crop['status']); ?>
                                </span>
                            </div>
                            <div class="crop-body">
                                <h3><?php echo htmlspecialchars($crop['crop_name']); ?></h3>
                                <div class="crop-details">
                                    <span class="crop-price"><?php echo formatLKR($crop['price_per_kg']); ?>/kg</span>
                                    <span class="crop-stock">Stock: <?php echo htmlspecialchars($crop['stock']); ?> kg</span>
                                </div>
                                <p class="crop-desc"><?php echo htmlspecialchars($crop['description'] ?: 'No description provided.'); ?></p>
                                <div class="crop-footer">
                                    <span>Uploaded: <?php echo htmlspecialchars($crop['uploaded_date']); ?></span>
                                    <span style="color: var(--danger); font-weight: 500;">Expires: <?php echo htmlspecialchars($crop['exp_date']); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Add Crop Listing Form -->
        <section id="add-listing" class="card" style="margin-bottom: 40px;">
            <h2 style="color: var(--primary-dark); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <ion-icon name="add-circle-outline" style="font-size: 1.8rem;"></ion-icon>
                List a New Crop Harvest
            </h2>
            <form action="farmer_dashboard.php" method="POST">
                <input type="hidden" name="action" value="add_crop">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label for="crop_name">Crop Name</label>
                        <input type="text" name="crop_name" id="crop_name" class="form-control" placeholder="e.g. Red Carrots (Welimada)" required>
                    </div>
                    <div class="form-group">
                        <label for="crop_type">Crop Category / Image Template</label>
                        <select name="crop_type" id="crop_type" class="form-control" required>
                            <option value="vegetable">General Vegetable</option>
                            <option value="carrot">Carrot</option>
                            <option value="potato">Potato</option>
                            <option value="leeks">Leeks</option>
                            <option value="onion">Big Onion</option>
                            <option value="cabbage">Cabbage</option>
                            <option value="paddy">Rice / Paddy</option>
                            <option value="coconut">Coconut</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label for="price_per_kg">Price per kg (LKR)</label>
                        <input type="number" name="price_per_kg" id="price_per_kg" class="form-control" placeholder="e.g. 250" min="1" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">Available Stock (kg)</label>
                        <input type="number" name="stock" id="stock" class="form-control" placeholder="e.g. 500" min="1" step="0.1" required>
                    </div>
                    <div class="form-group">
                        <label for="min_price">Minimum Reserve Price (per kg)</label>
                        <input type="number" name="min_price" id="min_price" class="form-control" placeholder="e.g. 220" min="1" step="0.01" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="exp_date">Expected Expiry Date</label>
                    <input type="date" name="exp_date" id="exp_date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Harvest Description</label>
                    <textarea name="description" id="description" class="form-control" placeholder="Provide description about quality, soil type, packaging, or pick-up location details..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="padding: 12px 30px;">Publish Harvest Listing</button>
            </form>
        </section>

        <!-- Sales / Order History -->
        <section id="sales" style="margin-bottom: 40px;">
            <h2 style="color: var(--primary-dark); margin-bottom: 20px;">Sales & Crop Orders</h2>

            <?php if (count($sales) === 0): ?>
                <div class="card" style="text-align: center; padding: 40px; color: var(--text-muted);">
                    <ion-icon name="cash-outline" style="font-size: 3rem; color: var(--secondary-color);"></ion-icon>
                    <p style="margin-top: 10px;">No sales orders recorded yet. Once a buyer purchases your crops, orders will appear here!</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Crop Name</th>
                                <th>Buyer Details</th>
                                <th>Quantity Ordered</th>
                                <th>Total Revenue</th>
                                <th>Date Placed</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sales as $sale): ?>
                                <tr>
                                    <td>#ORD-<?php echo htmlspecialchars($sale['order_id']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($sale['crop_name']); ?></strong></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($sale['buyer_name']); ?></strong><br>
                                        <small style="color: var(--text-muted);"><?php echo htmlspecialchars($sale['contact_no']); ?> | <?php echo htmlspecialchars($sale['buyer_district']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($sale['quantity']); ?> kg</td>
                                    <td style="font-weight: 700; color: var(--primary-dark);"><?php echo formatLKR($sale['price']); ?></td>
                                    <td><?php echo htmlspecialchars($sale['date']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $sale['status'] == 'Delivered' ? 'badge-success' : 'badge-warning'; ?>">
                                            <?php echo htmlspecialchars($sale['status']); ?>
                                        </span>
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
