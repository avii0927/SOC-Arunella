<?php
// index.php
// Project Homepage

require_once 'includes/auth.php';
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero" style="border-radius: var(--border-radius); margin-bottom: 40px; padding: 60px 40px;">
    <div class="hero-grid">
        <div class="hero-content">
            <h1>Connecting Sri Lankan Agriculture <span>Digitally</span></h1>
            <p>
                <strong>Arunella</strong> is a smart agricultural supply chain platform connecting farmers, buyers, and transporters across Sri Lanka. Eliminate intermediaries, ensure fair crop pricing, coordinate logistics in real time, and audit operations through a secure administrative dashboard.
            </p>
            <div class="hero-actions">
                <?php if (isLoggedIn()): ?>
                    <?php $role = getRole(); ?>
                    <?php if ($role === 'Farmer'): ?>
                        <a href="farmer_dashboard.php" class="btn btn-primary">Go to Farmer Dashboard</a>
                    <?php elseif ($role === 'Buyer'): ?>
                        <a href="buyer_dashboard.php" class="btn btn-primary">Browse Marketplace</a>
                    <?php elseif ($role === 'Transporter'): ?>
                        <a href="transporter_dashboard.php" class="btn btn-primary">View Delivery Tasks</a>
                    <?php elseif ($role === 'Admin'): ?>
                        <a href="admin_dashboard.php" class="btn btn-primary">Admin Control Center</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">Sign In</a>
                    <a href="register.php" class="btn btn-secondary">Create Account</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="hero-image">
            <!-- Simulated premium illustration using CSS and SVGs to make it look professional without external asset dependency -->
            <div style="background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-dark) 100%); width: 100%; min-height: 300px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 40px; color: white; text-align: center; gap: 20px;">
                <ion-icon name="trending-up-outline" style="font-size: 5rem; color: var(--accent-color);"></ion-icon>
                <h3 style="font-size: 1.8rem; font-weight: 700;">Smart Supply Chain</h3>
                <p style="font-size: 0.95rem; opacity: 0.9; max-width: 320px;">
                    Real-time transaction log, integrated wallet transfers, crop expiry tracking, and optimized route coordination.
                </p>
                <div style="display: flex; gap: 10px;">
                    <span style="background: rgba(255,255,255,0.2); padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">Farmers</span>
                    <span style="background: rgba(255,255,255,0.2); padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">Buyers</span>
                    <span style="background: rgba(255,255,255,0.2); padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">Logistics</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- System Stakeholders -->
<section class="features">
    <div class="section-header">
        <h2>Key Stakeholder Services</h2>
        <p>A unified service-oriented environment providing tailored workflows for all participants.</p>
    </div>

    <div class="feature-grid">
        <!-- Farmers -->
        <div class="feature-card">
            <div class="feature-icon" style="background-color: var(--primary-color);">
                <ion-icon name="logo-medium"></ion-icon>
            </div>
            <h3>Farmers</h3>
            <p>Register crop harvests, manage stocks, define minimum reserve prices, and receive direct payments directly into a secure digital wallet.</p>
            <p style="margin-top: 15px; font-size: 0.9rem; font-weight: 600; color: var(--primary-color);">
                Features: Expiry warnings, inventory tracking, secure withdrawals.
            </p>
        </div>

        <!-- Buyers -->
        <div class="feature-card">
            <div class="feature-icon" style="background-color: var(--accent-color);">
                <ion-icon name="basket-outline"></ion-icon>
            </div>
            <h3>Buyers</h3>
            <p>Access a transparent, intermediary-free marketplace. Compare crop prices across different districts and place multi-crop orders.</p>
            <p style="margin-top: 15px; font-size: 0.9rem; font-weight: 600; color: var(--accent-color);">
                Features: Order tracking, cart checkout, price filtering.
            </p>
        </div>

        <!-- Transporters -->
        <div class="feature-card">
            <div class="feature-icon" style="background-color: var(--info);">
                <ion-icon name="bus-outline"></ion-icon>
            </div>
            <h3>Transporters</h3>
            <p>Claim available delivery orders. Coordinate pickup from farms and delivery to buyers, uploading digital delivery confirmations.</p>
            <p style="margin-top: 15px; font-size: 0.9rem; font-weight: 600; color: var(--info);">
                Features: Route assignments, cargo capacities, status updates.
            </p>
        </div>
    </div>
</section>

<!-- Technical Summary Section -->
<section class="card" style="margin-top: 20px; padding: 40px; border-left: 5px solid var(--primary-color);">
    <h3 style="color: var(--primary-dark); font-size: 1.5rem; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
        <ion-icon name="shield-checkmark-outline"></ion-icon>
        Service-Oriented Computing Integration
    </h3>
    <p style="color: var(--text-main); font-size: 1rem; margin-bottom: 15px;">
        This project illustrates <strong>Class/Concrete Table Inheritance</strong> mapping for user roles, relational schema constraints, and unified data views. It matches the complete structural specifications submitted in the <strong>CSC 313 Project Proposal</strong>.
    </p>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 20px;">
        <div style="background-color: var(--bg-main); padding: 15px; border-radius: 8px; text-align: center;">
            <strong style="color: var(--primary-dark); display: block; font-size: 1.1rem;">8 Core Entities</strong>
            <span style="font-size: 0.8rem; color: var(--text-muted);">Fully Normalized Schema</span>
        </div>
        <div style="background-color: var(--bg-main); padding: 15px; border-radius: 8px; text-align: center;">
            <strong style="color: var(--primary-dark); display: block; font-size: 1.1rem;">No Intermediaries</strong>
            <span style="font-size: 0.8rem; color: var(--text-muted);">Bypasses Middlemen</span>
        </div>
        <div style="background-color: var(--bg-main); padding: 15px; border-radius: 8px; text-align: center;">
            <strong style="color: var(--primary-dark); display: block; font-size: 1.1rem;">WampServer Ready</strong>
            <span style="font-size: 0.8rem; color: var(--text-muted);">Native PHP/MySQL Stack</span>
        </div>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>
