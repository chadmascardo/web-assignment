<?php
session_start();
require_once "../includes/auth_check.php";
require_once "../includes/rbac_check.php";
require_once "../includes/db_connect.php";
require_once "../includes/functions.php";

$errors = [];
$success = "";

// Get user ID from URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$user_id = (int)$_GET['id'];

// Check if user can change this password
// Admin can change any password, users can only change their own
$is_admin = checkRole(['admin']);
if (!$is_admin && $user_id !== $_SESSION['user_id']) {
    die("You do not have permission to change this user's password.");
}

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate new password
    if (empty($new_password)) {
        $errors[] = "New password is required.";
    } elseif (strlen($new_password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    
    // Validate password confirmation
    if ($new_password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    
    // If no errors, update password
    if (empty($errors)) {
        $hashed_password = hashPassword($new_password);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        
        if ($stmt->execute([$hashed_password, $user_id])) {
            $success = "Password updated successfully!";
        } else {
            $errors[] = "Failed to update password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        h2 { color: #333; }
        .form-group { margin-bottom: 15px; }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-primary:hover { background-color: #0056b3; }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-secondary:hover { background-color: #545b62; }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .actions { margin-top: 20px; }
        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<h2>Change Password</h2>

<?php if (!empty($errors)): ?>
    <div class="error">
        <ul style="margin: 0; padding-left: 20px;">
            <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="success"><?= $success ?></div>
<?php endif; ?>

<div class="info">
    <strong>Changing password for:</strong> <?= sanitize($user['username']) ?> (<?= sanitize($user['full_name']) ?>)
</div>

<form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>?id=<?= $user_id ?>">
    <div class="form-group">
        <label>New Password: *</label>
        <input type="password" name="new_password" required>
    </div>
    
    <div class="form-group">
        <label>Confirm New Password: *</label>
        <input type="password" name="confirm_password" required>
    </div>
    
    <div class="actions">
        <button type="submit" class="btn btn-primary">Update Password</button>
        <a href="<?= $is_admin ? 'index.php' : '../dashboard.php' ?>" class="btn btn-secondary">Cancel</a>
    </div>
</form>

</body>
</html>
