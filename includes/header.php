<?php
// includes/header.php
require_once __DIR__ . '/auth.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arunella - Smart Agricultural Supply Chain</title>
    <link rel="stylesheet" href="assets/css/style.css?v=1.1">
    <!-- Ionicons for beautiful dashboard icons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">
                    <ion-icon name="leaf" style="color: var(--primary-color); font-size: 1.8rem;"></ion-icon>
                    Arunella<span>.</span>
                </a>
                
                <ul class="nav-links">
                    <li><a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                    
                    <?php if (isLoggedIn()): ?>
                        <?php $role = getRole(); ?>
                        <?php if ($role === 'Farmer'): ?>
                            <li><a href="farmer_dashboard.php" class="<?php echo $current_page == 'farmer_dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                        <?php elseif ($role === 'Buyer'): ?>
                            <li><a href="buyer_dashboard.php" class="<?php echo $current_page == 'buyer_dashboard.php' ? 'active' : ''; ?>">Marketplace</a></li>
                        <?php elseif ($role === 'Transporter'): ?>
                            <li><a href="transporter_dashboard.php" class="<?php echo $current_page == 'transporter_dashboard.php' ? 'active' : ''; ?>">Deliveries</a></li>
                        <?php elseif ($role === 'Admin'): ?>
                            <li><a href="admin_dashboard.php" class="<?php echo $current_page == 'admin_dashboard.php' ? 'active' : ''; ?>">Admin Panel</a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>

                <div class="nav-auth">
                    <?php if (isLoggedIn()): ?>
                        <span style="align-self: center; font-weight: 500; margin-right: 10px;">
                            Hello, <strong><?php echo htmlspecialchars(getUserName()); ?></strong> 
                            <span style="font-size: 0.8rem; background-color: var(--secondary-color); color: white; padding: 2px 8px; border-radius: 12px; margin-left: 5px;">
                                <?php echo getRole(); ?>
                            </span>
                        </span>
                        
                        <?php if (getRole() === 'Buyer'): ?>
                            <a href="buyer_dashboard.php?view=cart" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 5px; position: relative;">
                                <ion-icon name="cart-outline" style="font-size: 1.2rem;"></ion-icon>
                                Cart
                                <?php if (getCartCount() > 0): ?>
                                    <span style="background-color: var(--accent-color); color: white; font-size: 0.75rem; border-radius: 50%; width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center; font-weight: bold; margin-left: 2px;">
                                        <?php echo getCartCount(); ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        <?php endif; ?>

                        <a href="logout.php" class="btn btn-danger btn-sm" style="display: inline-flex; align-items: center; gap: 5px;">
                            <ion-icon name="log-out-outline" style="font-size: 1.1rem;"></ion-icon>
                            Logout
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-secondary btn-sm">Login</a>
                        <a href="register.php" class="btn btn-primary btn-sm">Register</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>
    <main class="container" style="min-height: calc(100vh - 200px); padding-top: 20px; padding-bottom: 40px;">
