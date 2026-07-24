<?php
// login.php
// User Authentication

require_once 'includes/auth.php';
require_once 'config/db.php';

// Redirect if already logged in
if (isLoggedIn()) {
    $role = getRole();
    if ($role === 'Farmer') header("Location: farmer_dashboard.php");
    elseif ($role === 'Buyer') header("Location: buyer_dashboard.php");
    elseif ($role === 'Transporter') header("Location: transporter_dashboard.php");
    elseif ($role === 'Admin') header("Location: admin_dashboard.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $database = new Database();
        $db = $database->getConnection();

        // 1. Check ADMIN Table
        $stmt = $db->prepare("SELECT * FROM `ADMIN` WHERE `email` = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['admin_id'];
            $_SESSION['role'] = 'Admin';
            $_SESSION['name'] = $user['name'];
            header("Location: admin_dashboard.php");
            exit();
        }

        // 2. Check FARMER Table
        $stmt = $db->prepare("SELECT * FROM `FARMER` WHERE `email` = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = 'Farmer';
            $_SESSION['name'] = $user['name'];
            header("Location: farmer_dashboard.php");
            exit();
        }

        // 3. Check BUYER Table
        $stmt = $db->prepare("SELECT * FROM `BUYER` WHERE `email` = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = 'Buyer';
            $_SESSION['name'] = $user['name'];
            header("Location: buyer_dashboard.php");
            exit();
        }

        // 4. Check TRANSPORTER Table
        $stmt = $db->prepare("SELECT * FROM `TRANSPORTER` WHERE `email` = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = 'Transporter';
            $_SESSION['name'] = $user['name'];
            header("Location: transporter_dashboard.php");
            exit();
        }

        // If not found in any table
        $error = "Invalid email or password.";
    }
}

require_once 'includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Welcome Back</h2>
            <p>Login to your Arunella account</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="enter your email (e.g. bandara@farmer.lk)" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px; padding: 12px;">Sign In</button>
        </form>

        <div style="background-color: #f8fafc; border: 1px solid var(--border-color); border-radius: 8px; padding: 15px; margin-top: 25px; font-size: 0.85rem;">
            <strong style="color: var(--primary-dark); display: block; margin-bottom: 5px;">Viva / Testing Credentials:</strong>
            <span style="display: block; margin-bottom: 4px;">🔐 Password for all default accounts is: <strong>password123</strong></span>
            <ul style="padding-left: 15px; color: var(--text-muted);">
                <li><strong>Admin:</strong> admin@arunella.lk</li>
                <li><strong>Farmer:</strong> bandara@farmer.lk</li>
                <li><strong>Buyer:</strong> procure@keells.lk</li>
                <li><strong>Transporter:</strong> ruwan@transporter.lk</li>
            </ul>
        </div>

        <div class="auth-footer">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
