<?php
// register.php
// User Registration

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
    $role = $_POST['role'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $nic = trim($_POST['nic'] ?? '');
    $contact_no = trim($_POST['contact_no'] ?? '');
    $district = trim($_POST['district'] ?? '');

    if (empty($role) || empty($name) || empty($email) || empty($password) || empty($nic) || empty($contact_no) || empty($district)) {
        $error = "Please fill in all common fields.";
    } elseif (!in_array($role, ['Farmer', 'Buyer', 'Transporter'])) {
        $error = "Invalid user role selected.";
    } else {
        $database = new Database();
        $db = $database->getConnection();

        // Check if email already exists in any user table
        $emailExists = false;
        foreach (['FARMER', 'BUYER', 'TRANSPORTER', 'ADMIN'] as $tbl) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM `$tbl` WHERE `email` = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->fetchColumn() > 0) {
                $emailExists = true;
                break;
            }
        }

        // Check if NIC already exists (except for Admin)
        $nicExists = false;
        foreach (['FARMER', 'BUYER', 'TRANSPORTER'] as $tbl) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM `$tbl` WHERE `nic` = :nic");
            $stmt->execute(['nic' => $nic]);
            if ($stmt->fetchColumn() > 0) {
                $nicExists = true;
                break;
            }
        }

        if ($emailExists) {
            $error = "An account with this email already exists.";
        } elseif ($nicExists) {
            $error = "An account with this NIC already exists.";
        } else {
            // Hash Password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Fetch default admin_id to assign for management (assign to admin_id = 1)
            $admin_id = 1;

            if ($role === 'Farmer') {
                $location = trim($_POST['location'] ?? '');
                $bank_ac_no = trim($_POST['bank_ac_no'] ?? '');

                if (empty($location) || empty($bank_ac_no)) {
                    $error = "Please fill in all Farmer-specific fields.";
                } else {
                    $sql = "INSERT INTO `FARMER` (`admin_id`, `role`, `name`, `email`, `password`, `nic`, `contact_no`, `district`, `location`, `bank_ac_no`, `wallet`, `rating`) 
                            VALUES (:admin_id, 'Farmer', :name, :email, :password, :nic, :contact_no, :district, :location, :bank_ac_no, 0.00, 5.00)";
                    $stmt = $db->prepare($sql);
                    $result = $stmt->execute([
                        'admin_id' => $admin_id,
                        'name' => $name,
                        'email' => $email,
                        'password' => $hashedPassword,
                        'nic' => $nic,
                        'contact_no' => $contact_no,
                        'district' => $district,
                        'location' => $location,
                        'bank_ac_no' => $bank_ac_no
                    ]);
                    if ($result) {
                        $success = "Farmer registered successfully. Please login.";
                    } else {
                        $error = "Registration failed. Please try again.";
                    }
                }
            } elseif ($role === 'Buyer') {
                $business_reg_no = trim($_POST['business_reg_no'] ?? '');
                $market_location = trim($_POST['market_location'] ?? '');

                if (empty($business_reg_no) || empty($market_location)) {
                    $error = "Please fill in all Buyer-specific fields.";
                } else {
                    $sql = "INSERT INTO `BUYER` (`admin_id`, `role`, `name`, `email`, `password`, `nic`, `contact_no`, `district`, `business_reg_no`, `market_location`, `rating`) 
                            VALUES (:admin_id, 'Buyer', :name, :email, :password, :nic, :contact_no, :district, :business_reg_no, :market_location, 5.00)";
                    $stmt = $db->prepare($sql);
                    $result = $stmt->execute([
                        'admin_id' => $admin_id,
                        'name' => $name,
                        'email' => $email,
                        'password' => $hashedPassword,
                        'nic' => $nic,
                        'contact_no' => $contact_no,
                        'district' => $district,
                        'business_reg_no' => $business_reg_no,
                        'market_location' => $market_location
                    ]);
                    if ($result) {
                        $success = "Buyer registered successfully. Please login.";
                    } else {
                        $error = "Registration failed. Please try again.";
                    }
                }
            } elseif ($role === 'Transporter') {
                $vehicle_plate_no = trim($_POST['vehicle_plate_no'] ?? '');
                $max_capacity = trim($_POST['max_capacity'] ?? '');

                if (empty($vehicle_plate_no) || empty($max_capacity) || !is_numeric($max_capacity)) {
                    $error = "Please fill in valid Transporter-specific fields.";
                } else {
                    // Check if vehicle plate number exists
                    $stmt = $db->prepare("SELECT COUNT(*) FROM `TRANSPORTER` WHERE `vehicle_plate_no` = :v");
                    $stmt->execute(['v' => $vehicle_plate_no]);
                    if ($stmt->fetchColumn() > 0) {
                        $error = "Vehicle Plate Number already registered.";
                    } else {
                        $sql = "INSERT INTO `TRANSPORTER` (`admin_id`, `role`, `name`, `email`, `password`, `nic`, `contact_no`, `district`, `vehicle_plate_no`, `max_capacity`, `rating`) 
                                VALUES (:admin_id, 'Transporter', :name, :email, :password, :nic, :contact_no, :district, :vehicle_plate_no, :max_capacity, 5.00)";
                        $stmt = $db->prepare($sql);
                        $result = $stmt->execute([
                            'admin_id' => $admin_id,
                            'name' => $name,
                            'email' => $email,
                            'password' => $hashedPassword,
                            'nic' => $nic,
                            'contact_no' => $contact_no,
                            'district' => $district,
                            'vehicle_plate_no' => $vehicle_plate_no,
                            'max_capacity' => $max_capacity
                        ]);
                        if ($result) {
                            $success = "Transporter registered successfully. Please login.";
                        } else {
                            $error = "Registration failed. Please try again.";
                        }
                    }
                }
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card" style="max-width: 600px;">
        <div class="auth-header">
            <h2>Join Arunella</h2>
            <p>Create a new account to participate in the supply chain</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label>Select User Role</label>
                <div class="role-selector">
                    <div class="role-option">
                        <input type="radio" name="role" id="role-farmer" value="Farmer" checked>
                        <label for="role-farmer" class="role-label">Farmer</label>
                    </div>
                    <div class="role-option">
                        <input type="radio" name="role" id="role-buyer" value="Buyer">
                        <label for="role-buyer" class="role-label">Buyer</label>
                    </div>
                    <div class="role-option">
                        <input type="radio" name="role" id="role-transporter" value="Transporter">
                        <label for="role-transporter" class="role-label">Transporter</label>
                    </div>
                </div>
            </div>

            <!-- Common Fields -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="e.g. Ruwan Kumara" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="e.g. ruwan@email.com" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>
                <div class="form-group">
                    <label for="nic">NIC Number</label>
                    <input type="text" name="nic" id="nic" class="form-control" placeholder="e.g. 199123456789" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label for="contact_no">Contact Number</label>
                    <input type="text" name="contact_no" id="contact_no" class="form-control" placeholder="e.g. 0771234567" required>
                </div>
                <div class="form-group">
                    <label for="district">District</label>
                    <select name="district" id="district" class="form-control" required>
                        <option value="">-- Select District --</option>
                        <option value="Colombo">Colombo</option>
                        <option value="Gampaha">Gampaha</option>
                        <option value="Kalutara">Kalutara</option>
                        <option value="Kandy">Kandy</option>
                        <option value="Matale">Matale</option>
                        <option value="Nuwara Eliya">Nuwara Eliya</option>
                        <option value="Galle">Galle</option>
                        <option value="Matara">Matara</option>
                        <option value="Hambantota">Hambantota</option>
                        <option value="Jaffna">Jaffna</option>
                        <option value="Mannar">Mannar</option>
                        <option value="Vavuniya">Vavuniya</option>
                        <option value="Mullaitivu">Mullaitivu</option>
                        <option value="Kilinochchi">Kilinochchi</option>
                        <option value="Batticaloa">Batticaloa</option>
                        <option value="Ampara">Ampara</option>
                        <option value="Trincomalee">Trincomalee</option>
                        <option value="Kurunegala">Kurunegala</option>
                        <option value="Puttalam">Puttalam</option>
                        <option value="Anuradhapura">Anuradhapura</option>
                        <option value="Polonnaruwa">Polonnaruwa</option>
                        <option value="Badulla">Badulla</option>
                        <option value="Moneragala">Moneragala</option>
                        <option value="Ratnapura">Ratnapura</option>
                        <option value="Kegalle">Kegalle</option>
                    </select>
                </div>
            </div>

            <!-- Farmer specific fields -->
            <div id="farmer-fields" style="display: none;">
                <div class="form-group">
                    <label for="location">Farm Address / Location</label>
                    <input type="text" name="location" id="location" class="form-control" placeholder="e.g. 45/A, Keppetipola Rd, Welimada">
                </div>
                <div class="form-group">
                    <label for="bank_ac_no">Bank Account Number (LKR)</label>
                    <input type="text" name="bank_ac_no" id="bank_ac_no" class="form-control" placeholder="e.g. BOC Acc 10045612">
                </div>
            </div>

            <!-- Buyer specific fields -->
            <div id="buyer-fields" style="display: none;">
                <div class="form-group">
                    <label for="business_reg_no">Business Registration Number</label>
                    <input type="text" name="business_reg_no" id="business_reg_no" class="form-control" placeholder="e.g. PV-204561">
                </div>
                <div class="form-group">
                    <label for="market_location">Market Delivery Address</label>
                    <input type="text" name="market_location" id="market_location" class="form-control" placeholder="e.g. Manning Market, Peliyagoda">
                </div>
            </div>

            <!-- Transporter specific fields -->
            <div id="transporter-fields" style="display: none;">
                <div class="form-group">
                    <label for="vehicle_plate_no">Vehicle Plate Number</label>
                    <input type="text" name="vehicle_plate_no" id="vehicle_plate_no" class="form-control" placeholder="e.g. WP-LY-1234">
                </div>
                <div class="form-group">
                    <label for="max_capacity">Max Cargo Capacity (kg)</label>
                    <input type="number" name="max_capacity" id="max_capacity" class="form-control" placeholder="e.g. 1500" min="1">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 15px; padding: 12px;">Create Account</button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="login.php">Sign In</a>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
