<?php
// includes/auth.php
// Session management and authorization checks

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getRole() {
    return $_SESSION['role'] ?? null; // 'Farmer', 'Buyer', 'Transporter', 'Admin'
}

function getUserName() {
    return $_SESSION['name'] ?? '';
}

function checkAccess($allowedRoles) {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
    
    $role = getRole();
    if (!in_array($role, $allowedRoles)) {
        // Redirect to their own dashboard or login
        if ($role === 'Farmer') {
            header("Location: farmer_dashboard.php");
        } elseif ($role === 'Buyer') {
            header("Location: buyer_dashboard.php");
        } elseif ($role === 'Transporter') {
            header("Location: transporter_dashboard.php");
        } elseif ($role === 'Admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: login.php");
        }
        exit();
    }
}

// Helper to format currency
function formatLKR($amount) {
    return "Rs. " . number_format($amount, 2);
}

// Shopping Cart helper for Buyers
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

function getCartCount() {
    return count($_SESSION['cart']);
}
?>
