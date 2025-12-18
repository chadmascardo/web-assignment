<?php
session_start();
require_once "../includes/auth_check.php";
require_once "../includes/rbac_check.php";
require_once "../includes/db_connect.php";
require_once "../includes/functions.php";

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_input($_POST['username']);
    $full_name = sanitize_input($_POST['full_name']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $user_role = sanitize_input($_POST['user_role']);
    
    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters.";
    } elseif (username_exists($pdo, $username)) {
        $errors[] = "Username already exists.";
    }
    
    if (empty($full_name)) {
        $errors[] = "Full name is required.";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (!validate_password($password)) {
        $errors[] = "Password must be at least 8 characters.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    
    if (!in_array($user_role, ['Admin', 'Manager', 'Staff'])) {
        $errors[] = "Invalid user role.";
    }
    
    if (empty($errors)) {
        $hashed_password = hash_password($password);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)");
        
        if ($stmt->execute([$username, $hashed_password, $full_name, $user_role])) {
            $success = "User created successfully!";
            $_POST = [];
        } else {
            $errors[] = "Failed to create user. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New User</title>
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
        input[type="password"],
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
    </style>
</head>
<body>

<h2>Add New User</h2>

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

<form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
    <div class="form-group">
        <label>Username: *</label>
        <input type="text" name="username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" required>
    </div>
    
    <div class="form-group">
        <label>Full Name: *</label>
        <input type="text" name="full_name" value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '' ?>" required>
    </div>
    
    <div class="form-group">
        <label>Password: * (minimum 8 characters)</label>
        <input type="password" name="password" required>
    </div>
    
    <div class="form-group">
        <label>Confirm Password: *</label>
        <input type="password" name="confirm_password" required>
    </div>
    
    <div class="form-group">
        <label>User Role: *</label>
        <select name="user_role" required>
            <option value="">-- Select Role --</option>
            <option value="Admin" <?= (isset($_POST['user_role']) && $_POST['user_role'] === 'Admin') ? 'selected' : '' ?>>Admin</option>
            <option value="Manager" <?= (isset($_POST['user_role']) && $_POST['user_role'] === 'Manager') ? 'selected' : '' ?>>Manager</option>
            <option value="Staff" <?= (isset($_POST['user_role']) && $_POST['user_role'] === 'Staff') ? 'selected' : '' ?>>Staff</option>
        </select>
    </div>
    
    <div class="actions">
        <button type="submit" class="btn btn-primary">Create User</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>

</body>
</html>