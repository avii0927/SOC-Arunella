<?php
// buyer_dashboard.php
// Marketplace and Order management for Buyers

require_once 'includes/auth.php';
require_once 'config/db.php';

// Check if user is a Buyer
checkAccess(['Buyer']);

$buyer_id = getUserId();
$database = new Database();
$db = $database->getConnection();

$error = '';
$success = '';
$view = $_GET['view'] ?? 'market'; // 'market', 'cart', 'orders'

// 1. Add to Cart Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $crop_id = intval($_POST['crop_id']);
    $qty = floatval($_POST['quantity']);

    // Check crop details and stock
    $stmt = $db->prepare("SELECT * FROM `CROP` WHERE `crop_id` = :id");
    $stmt->execute(['id' => $crop_id]);
    $crop = $stmt->fetch();

    if (!$crop) {
        $error = "Crop not found.";
    } elseif ($qty <= 0) {
        $error = "Please enter a valid quantity.";
    } elseif ($qty > $crop['stock']) {
        $error = "Insufficient stock. Only " . $crop['stock'] . " kg available.";
    } else {
        $_SESSION['cart'][$crop_id] = $qty;
        $success = "Successfully added " . htmlspecialchars($crop['crop_name']) . " to cart!";
    }
}

// 2. Remove from Cart Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove_from_cart') {
    $crop_id = intval($_POST['crop_id']);
    if (isset($_SESSION['cart'][$crop_id])) {
        unset($_SESSION['cart'][$crop_id]);
        $success = "Item removed from cart.";
    }
}

// 3. Checkout Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'checkout') {
    if (empty($_SESSION['cart'])) {
        $error = "Your cart is empty.";
    } else {
        // Fetch Buyer details (specifically delivery address / market_location)
        $stmt_buyer = $db->prepare("SELECT * FROM `BUYER` WHERE `user_id` = :id");
        $stmt_buyer->execute(['id' => $buyer_id]);
        $buyer = $stmt_buyer->fetch();

        try {
            $db->beginTransaction();

            // We will loop through the cart items. 
            // For each crop item, we will:
            // a) Create an ORDER record.
            // b) Create a HAS record.
            // c) Create a DELIVERY record referencing the order.
            // d) Deduct the crop stock.
            // e) Credit the farmer's wallet (total order price minus some commission or full price).
            foreach ($_SESSION['cart'] as $crop_id => $qty) {
                // Fetch crop details
                $stmt_c = $db->prepare("SELECT c.*, f.location AS farmer_location, f.user_id AS farmer_id 
                                        FROM `CROP` c 
                                        JOIN `FARMER` f ON c.user_id = f.user_id 
                                        WHERE c.crop_id = :cid FOR UPDATE");
                $stmt_c->execute(['cid' => $crop_id]);
                $crop = $stmt_c->fetch();

                if (!$crop || $crop['stock'] < $qty) {
                    throw new Exception("Stock mismatch or crop no longer available: " . ($crop['crop_name'] ?? 'Unknown'));
                }

                $total_price = $crop['price_per_kg'] * $qty;

                // a) Insert into ORDER table
                $stmt_order = $db->prepare("INSERT INTO `ORDER` (`user_id`, `price`, `quantity`, `date`, `status`) 
                                            VALUES (:user_id, :price, :qty, CURDATE(), 'Pending')");
                $stmt_order->execute([
                    'user_id' => $buyer_id,
                    'price' => $total_price,
                    'qty' => $qty
                ]);
                $new_order_id = $db->lastInsertId();

                // b) Insert into HAS table
                $stmt_has = $db->prepare("INSERT INTO `HAS` (`order_id`, `crop_id`) VALUES (:oid, :cid)");
                $stmt_has->execute([
                    'oid' => $new_order_id,
                    'cid' => $crop_id
                ]);

                // c) Insert into DELIVERY table
                $stmt_delivery = $db->prepare("INSERT INTO `DELIVERY` (`user_id`, `order_id`, `pickup_location`, `delivery_location`, `status`, `date`) 
                                               VALUES (NULL, :oid, :pickup, :delivery, 'Pending', CURDATE())");
                $stmt_delivery->execute([
                    'oid' => $new_order_id,
                    'pickup' => $crop['farmer_location'],
                    'delivery' => $buyer['market_location']
                ]);

                // d) Deduct stock
                $new_stock = $crop['stock'] - $qty;
                $status = ($new_stock <= 0) ? 'Out of Stock' : 'Available';
                $stmt_stock = $db->prepare("UPDATE `CROP` SET `stock` = :stock, `status` = :status WHERE `crop_id` = :cid");
                $stmt_stock->execute([
                    'stock' => $new_stock,
                    'status' => $status,
                    'cid' => $crop_id
                ]);

                // e) Add funds to Farmer's wallet
                $stmt_wallet = $db->prepare("UPDATE `FARMER` SET `wallet` = `wallet` + :amt WHERE `user_id` = :fid");
                $stmt_wallet->execute([
                    'amt' => $total_price,
                    'fid' => $crop['farmer_id']
                ]);
            }

            $db->commit();
            $_SESSION['cart'] = []; // Clear Cart
            $success = "Checkout successful! Your orders have been placed and deliveries are being scheduled.";
            $view = 'orders';
        } catch (Exception $e) {
            $db->rollBack();
            $error = "Transaction failed: " . $e->getMessage();
        }
    }
}

// Fetch all available crops for market view
$crops = [];
if ($view === 'market') {
    $sql_market = "SELECT c.*, f.name AS farmer_name, f.district 
                   FROM `CROP` c 
                   JOIN `FARMER` f ON c.user_id = f.user_id 
                   WHERE c.status = 'Available' AND c.stock > 0 AND c.exp_date >= CURDATE()";
    $stmt_m = $db->prepare($sql_market);
    $stmt_m->execute();
    $crops = $stmt_m->fetchAll();
}

// Fetch orders for order view
$orders = [];
if ($view === 'orders') {
    $sql_orders = "SELECT o.*, c.crop_name, c.image, f.name AS farmer_name, d.status AS delivery_status, d.delivery_id 
                   FROM `ORDER` o
                   JOIN `HAS` h ON o.order_id = h.order_id
                   JOIN `CROP` c ON h.crop_id = c.crop_id
                   JOIN `FARMER` f ON c.user_id = f.user_id
                   JOIN `DELIVERY` d ON o.order_id = d.order_id
                   WHERE o.user_id = :bid 
                   ORDER BY o.order_id DESC";
    $stmt_o = $db->prepare($sql_orders);
    $stmt_o->execute(['bid' => $buyer_id]);
    $orders = $stmt_o->fetchAll();
}

require_once 'includes/header.php';
?>

<div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="user-profile">
            <div class="profile-avatar" style="background-color: var(--accent-color);">B</div>
            <h4><?php echo htmlspecialchars(getUserName()); ?></h4>
            <span>Buyer</span>
        </div>
        <ul class="sidebar-menu">
            <li class="<?php echo $view === 'market' ? 'active' : ''; ?>"><a href="buyer_dashboard.php?view=market"><ion-icon name="basket-outline"></ion-icon> Crop Marketplace</a></li>
            <li class="<?php echo $view === 'cart' ? 'active' : ''; ?>"><a href="buyer_dashboard.php?view=cart"><ion-icon name="cart-outline"></ion-icon> Cart (<?php echo getCartCount(); ?>)</a></li>
            <li class="<?php echo $view === 'orders' ? 'active' : ''; ?>"><a href="buyer_dashboard.php?view=orders"><ion-icon name="cube-outline"></ion-icon> My Orders & Track</a></li>
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

        <!-- 1. VIEW: Marketplace -->
        <?php if ($view === 'market'): ?>
            <div class="market-header">
                <h2 style="color: var(--primary-dark);">Browse Farm Fresh Harvests</h2>
                
                <div class="filters-wrapper">
                    <input type="text" id="market-search" class="form-control" placeholder="Search crop..." style="width: 220px; padding: 8px 12px; font-size: 0.9rem;">
                    
                    <select id="market-district" class="form-control" style="width: 180px; padding: 8px 12px; font-size: 0.9rem;">
                        <option value="">All Districts</option>
                        <option value="Nuwara Eliya">Nuwara Eliya</option>
                        <option value="Badulla">Badulla</option>
                        <option value="Kandy">Kandy</option>
                        <option value="Jaffna">Jaffna</option>
                        <option value="Gampaha">Gampaha</option>
                        <option value="Colombo">Colombo</option>
                    </select>
                </div>
            </div>

            <?php if (count($crops) === 0): ?>
                <div class="card" style="text-align: center; padding: 45px; color: var(--text-muted);">
                    <ion-icon name="basket-outline" style="font-size: 3rem; color: var(--secondary-color);"></ion-icon>
                    <p style="margin-top: 10px;">No fresh crops are currently listed for sale. Please check back later.</p>
                </div>
            <?php else: ?>
                <div class="crop-grid">
                    <?php foreach ($crops as $crop): ?>
                        <div class="crop-card">
                            <div class="crop-img">
                                <ion-icon name="leaf-outline" style="font-size: 3.5rem; opacity: 0.3;"></ion-icon>
                                <span class="crop-tag">Available</span>
                            </div>
                            <div class="crop-body">
                                <h3><?php echo htmlspecialchars($crop['crop_name']); ?></h3>
                                <p class="crop-farmer">Listed by: <strong><?php echo htmlspecialchars($crop['farmer_name']); ?></strong> (<span class="crop-location"><?php echo htmlspecialchars($crop['district']); ?></span>)</p>
                                <div class="crop-details">
                                    <span class="crop-price"><?php echo formatLKR($crop['price_per_kg']); ?>/kg</span>
                                    <span class="crop-stock">Stock: <?php echo htmlspecialchars($crop['stock']); ?> kg</span>
                                </div>
                                <p class="crop-desc"><?php echo htmlspecialchars($crop['description'] ?: 'No description provided.'); ?></p>
                                
                                <form action="buyer_dashboard.php?view=market" method="POST" style="margin-top: auto; display: flex; gap: 8px;">
                                    <input type="hidden" name="action" value="add_to_cart">
                                    <input type="hidden" name="crop_id" value="<?php echo $crop['crop_id']; ?>">
                                    <input type="number" name="quantity" class="form-control" style="width: 80px;" value="10" min="1" max="<?php echo $crop['stock']; ?>" step="1" required>
                                    <button type="submit" class="btn btn-primary btn-sm" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 5px;">
                                        <ion-icon name="cart-outline"></ion-icon> Buy
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        <!-- 2. VIEW: Cart -->
        <?php elseif ($view === 'cart'): ?>
            <h2 style="color: var(--primary-dark); margin-bottom: 20px;">Shopping Cart</h2>
            
            <?php if (empty($_SESSION['cart'])): ?>
                <div class="card" style="text-align: center; padding: 40px; color: var(--text-muted);">
                    <ion-icon name="cart-outline" style="font-size: 3.5rem; color: var(--secondary-color);"></ion-icon>
                    <p style="margin-top: 15px;">Your shopping cart is empty.</p>
                    <a href="buyer_dashboard.php?view=market" class="btn btn-primary btn-sm" style="margin-top: 15px;">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Crop Name</th>
                                <th>Unit Price</th>
                                <th>Quantity (kg)</th>
                                <th>Total Cost</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $grand_total = 0;
                            foreach ($_SESSION['cart'] as $crop_id => $qty): 
                                $stmt_c = $db->prepare("SELECT * FROM `CROP` WHERE `crop_id` = :id");
                                $stmt_c->execute(['id' => $crop_id]);
                                $crop = $stmt_c->fetch();
                                $item_total = $crop['price_per_kg'] * $qty;
                                $grand_total += $item_total;
                            ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($crop['crop_name']); ?></strong></td>
                                    <td><?php echo formatLKR($crop['price_per_kg']); ?></td>
                                    <td><?php echo htmlspecialchars($qty); ?> kg</td>
                                    <td style="font-weight: 700;"><?php echo formatLKR($item_total); ?></td>
                                    <td>
                                        <form action="buyer_dashboard.php?view=cart" method="POST">
                                            <input type="hidden" name="action" value="remove_from_cart">
                                            <input type="hidden" name="crop_id" value="<?php echo $crop_id; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" style="padding: 4px 10px;">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr style="background-color: #f8fafc; font-size: 1.1rem; font-weight: 700;">
                                <td colspan="3" style="text-align: right;">Grand Total:</td>
                                <td style="color: var(--accent-color);"><?php echo formatLKR($grand_total); ?></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 15px;">
                    <a href="buyer_dashboard.php?view=market" class="btn btn-secondary">Add More Items</a>
                    <form action="buyer_dashboard.php?view=cart" method="POST">
                        <input type="hidden" name="action" value="checkout">
                        <button type="submit" class="btn btn-primary" style="padding: 12px 30px;">Proceed to Checkout (Order)</button>
                    </form>
                </div>
            <?php endif; ?>

        <!-- 3. VIEW: My Orders -->
        <?php elseif ($view === 'orders'): ?>
            <h2 style="color: var(--primary-dark); margin-bottom: 20px;">My Orders & Logistics Status</h2>

            <?php if (count($orders) === 0): ?>
                <div class="card" style="text-align: center; padding: 40px; color: var(--text-muted);">
                    <ion-icon name="cube-outline" style="font-size: 3rem; color: var(--secondary-color);"></ion-icon>
                    <p style="margin-top: 10px;">No purchase orders placed yet.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Crop Info</th>
                                <th>Farmer Name</th>
                                <th>Quantity</th>
                                <th>Total Cost</th>
                                <th>Order Date</th>
                                <th>Delivery Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#ORD-<?php echo htmlspecialchars($order['order_id']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($order['crop_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($order['farmer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['quantity']); ?> kg</td>
                                    <td style="font-weight: 700; color: var(--primary-dark);"><?php echo formatLKR($order['price']); ?></td>
                                    <td><?php echo htmlspecialchars($order['date']); ?></td>
                                    <td>
                                        <span class="badge <?php 
                                            if ($order['delivery_status'] === 'Delivered') echo 'badge-success';
                                            elseif ($order['delivery_status'] === 'In Transit' || $order['delivery_status'] === 'Picked Up') echo 'badge-info';
                                            else echo 'badge-warning';
                                        ?>">
                                            <ion-icon name="car-outline" style="vertical-align: middle; margin-right: 3px;"></ion-icon>
                                            <?php echo htmlspecialchars($order['delivery_status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
