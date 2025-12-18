<?php
session_start();
require_once "../includes/auth_check.php";
require_once "../includes/rbac_check.php";
require_once "../includes/db_connect.php";
require_once "../includes/functions.php";

$errors = [];
$success = "";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$user_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize($_POST['username']);
    $full_name = sanitize($_POST['full_name']);
    $role = sanitize($_POST['role']);
    
    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$username, $user_id]);
        if ($stmt->fetch()) {
            $errors[] = "Username already exists.";
        }
    }
    
    if (empty($full_name)) {
        $errors[] = "Full name is required.";
    }
    
    if (!in_array($role, ['admin', 'cashier'])) {
        $errors[] = "Invalid role.";
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, full_name = ?, role = ? WHERE id = ?");
        
        if ($stmt->execute([$username, $full_name, $role, $user_id])) {
            $success = "User updated successfully!";
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $errors[] = "Failed to update user. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        h2 {
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        select {
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
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
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
        .actions {
            margin-top: 20px;
        }
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

<h2>Edit User</h2>

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
    <strong>Note:</strong> To change the password, use the <a href="change_password.php?id=<?= $user_id ?>">Change Password</a> page.
</div>

<form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>?id=<?= $user_id ?>">
    <div class="form-group">
        <label>User ID:</label>
        <input type="text" value="<?= $user['id'] ?>" disabled>
    </div>
    
    <div class="form-group">
        <label>Username: *</label>
        <input type="text" name="username" value="<?= sanitize($user['username']) ?>" required>
    </div>
    
    <div class="form-group">
        <label>Full Name: *</label>
        <input type="text" name="full_name" value="<?= sanitize($user['full_name']) ?>" required>
    </div>
    
    <div class="form-group">
        <label>Role: *</label>
        <select name="role" required>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="cashier" <?= $user['role'] === 'cashier' ? 'selected' : '' ?>>Cashier</option>
        </select>
    </div>
    
    <div class="form-group">
        <label>Created At:</label>
        <input type="text" value="<?= date('F d, Y h:i A', strtotime($user['created_at'])) ?>" disabled>
    </div>
    
    <div class="actions">
        <button type="submit" class="btn btn-primary">Update User</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>

</body>
</html>