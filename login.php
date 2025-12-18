<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}


require_once __DIR__ . "/includes/functions.php";
require_once __DIR__ . "/includes/db_connect.php";


try {
    if (!isset($pdo)) {
        throw new Exception("Database connection not available");
    }
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

$errors = [];
$success = "";
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'login';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'login') {
        $username = sanitize($_POST['username']);
        $password = $_POST['password'];

        if (empty($username) || empty($password)) {
            $errors[] = "Username and password are required.";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && verifyPassword($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];

                if ($_SESSION['role'] === 'Admin') {
                    header("Location: dashboard.php");
                } elseif ($_SESSION['role'] === 'Manager') {
                    header("Location: dashboard.php");
                } elseif ($_SESSION['role'] === 'Staff') {
                    header("Location: dashboard.php");
                } else {
                    header("Location: default_dashboard.php");
                }
                exit;
            }
        }
    } elseif ($action === 'register') {

        $username = sanitize_input($_POST['username']);
        $email = sanitize_input($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $full_name = sanitize_input($_POST['full_name']);
        $role = sanitize_input($_POST['role']);

        if (empty($username)) {
            $errors[] = "Username is required.";
        } elseif (strlen($username) < 3) {
            $errors[] = "Username must be at least 3 characters.";
        } elseif (username_exists($pdo, $username)) {
            $errors[] = "Username already exists.";
        }

        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!validate_email($email)) {
            $errors[] = "Invalid email format.";
        } elseif (email_exists($pdo, $email)) {
            $errors[] = "Email already exists.";
        }

        if (empty($password)) {
            $errors[] = "Password is required.";
        } elseif (!validate_password($password)) {
            $errors[] = "Password must be at least 8 characters.";
        } elseif ($password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        }

        if (empty($full_name)) {
            $errors[] = "Full name is required.";
        }

        $allowed_roles = ['Admin', 'Manager', 'Staff'];
        if (empty($role)) {
            $errors[] = "Role is required.";
        } elseif (!in_array($role, $allowed_roles)) {
            $errors[] = "Invalid role selected.";
        }

        if (empty($errors)) {
            $hashed_password = hashPassword($password);

            try {
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, ?)");

                if ($stmt->execute([$username, $email, $hashed_password, $full_name, $role])) {
                    $success = "Registration successful! You can now login.";
                    $mode = 'login';
                } else {
                    $errors[] = "Failed to create account. Please try again.";
                }
            } catch (PDOException $e) {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title><?= $mode === 'register' ? 'Register' : 'Login' ?> - User Authentication System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 8px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 30px;
            color: #333;
            font-size: 24px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            color: #555;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #333;
        }

        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            background: white;
            cursor: pointer;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #333;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background: #555;
        }

        .error {
            background: #fee;
            color: #c33;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .error ul {
            margin-left: 20px;
        }

        .success {
            background: #efe;
            color: #3a3;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .toggle {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 14px;
            color: #666;
        }

        .toggle a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
        }

        .toggle a:hover {
            text-decoration: underline;
        }

        .hint {
            font-size: 12px;
            color: #999;
            margin-top: 4px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2><?= $mode === 'register' ? 'Create Account' : 'Login' ?></h2>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($mode === 'login'): ?>
            <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>?mode=login">
                <input type="hidden" name="action" value="login">

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required autofocus>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <button type="submit">Sign In</button>
            </form>

            <div class="toggle">
                Don't have an account? <a href="?mode=register">Register</a>
            </div>
        <?php else: ?>
            <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>?mode=register">
                <input type="hidden" name="action" value="register">

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username"
                        value="<?= isset($_POST['username']) ? sanitize($_POST['username']) : '' ?>" required autofocus>
                    <div class="hint">Minimum 3 characters</div>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= isset($_POST['email']) ? sanitize($_POST['email']) : '' ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name"
                        value="<?= isset($_POST['full_name']) ? sanitize($_POST['full_name']) : '' ?>" required>
                </div>

                <div class="form-group">
                    <label>Role</label>
                    <select name="role" required>
                        <option value="">Select a role</option>
                        <option value="Admin" <?= (isset($_POST['role']) && $_POST['role'] === 'Admin') ? 'selected' : '' ?>>
                            Admin</option>
                        <option value="Manager" <?= (isset($_POST['role']) && $_POST['role'] === 'Manager') ? 'selected' : '' ?>>Manager</option>
                        <option value="Staff" <?= (isset($_POST['role']) && $_POST['role'] === 'Staff') ? 'selected' : '' ?>>
                            Staff</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                    <div class="hint">Minimum 8 characters</div>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required>
                </div>

                <button type="submit">Create Account</button>
            </form>

            <div class="toggle">
                Already have an account? <a href="?mode=login">Login</a>
            </div>
        <?php endif; ?>
    </div>

</body>

</html>