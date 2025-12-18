<?php
session_start();
require_once "includes/auth_check.php";
require_once "includes/db_connect.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    $_SESSION['error_message'] = "You must log in to access the dashboard.";
    header("Location: login.php");
    exit;
}

// Normalize role to lowercase for consistent checking
$user_role = strtolower($_SESSION['role']);
$allowed_roles = ['admin', 'manager', 'staff'];

if (!in_array($user_role, $allowed_roles)) {
    $_SESSION['error_message'] = "Access denied. Invalid user role.";
    header("Location: login.php");
    exit;
}

function renderModuleCards($role) {
    // Normalize role to lowercase
    $role = strtolower($role);
    
    $modules = [
        'admin' => [
            [
                "title" => " User Management",
                "description" => "Admin Access: Create, edit, and manage user accounts and permissions.",
                "link" => "users/index.php",
            ],
            [
                "title" => " Inventory Management",
                "description" => "Full access to manage inventory, add/edit/delete products, and control stock levels.",
                "link" => "inventory/index.php",
            ],
            [
                "title" => "Point of Sale (POS)",
                "description" => "Process sales transactions and manage the checkout system.",
                "link" => "pos/pos.php",
            ],

        ],
        'manager' => [
            [
                "title" => " Inventory Management",
                "description" => "View and edit inventory, monitor stock levels, and update product information.",
                "link" => "inventory/index.php",
            ],
            [
                "title" => "Point of Sale (POS)",
                "description" => "Process sales transactions and assist with customer checkouts.",
                "link" => "pos/pos.php",
            ],

        ],
        'staff' => [
            [
                "title" => " View Inventory",
                "description" => "View current inventory and check product availability.",
                "link" => "inventory/index.php",
            ],
            [
                "title" => " Point of Sale (POS)",
                "description" => "Process customer sales and complete transactions.",
                "link" => "pos/pos.php",
            ],
            [
                "title" => " My Profile",
                "description" => "View and update your personal information.",
                "link" => "#",
            ],
        ],
    ];
    
    return $modules[$role] ?? [];
}

$role_title = ucfirst($user_role);
$modules = renderModuleCards($_SESSION['role']);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Inventory & POS System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .user-info {
            text-align: right;
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }
        .modules {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .module-card {
            border: 1px solid #ddd;
            padding: 30px;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .module-card h3 {
            color: #007bff;
            margin-top: 0;
        }
        .module-card a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .module-card a:hover {
            background-color: #0056b3;
        }
        .logout-btn {
            background-color: #dc3545;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Inventory & POS System</h1>
    <div class="user-info">
        <div>Welcome, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></div>
        <div style="font-size: 14px; margin-top: 5px;">Role: <?= htmlspecialchars($role_title) ?></div>
        <a href="logout.php" class="logout-btn" style="display: inline-block; margin-top: 10px;">Logout</a>
    </div>
</div>

<div class="container">
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="error-message">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    
    <h2 style="color: #333; margin-bottom: 20px;"><?= htmlspecialchars($role_title) ?> Dashboard</h2>

    <div class="modules">
        <?php foreach ($modules as $module): ?>
            <div class="module-card">
                <h3><?= htmlspecialchars($module['title']) ?></h3>
                <p><?= htmlspecialchars($module['description']) ?></p>
                <a href="<?= htmlspecialchars($module['link']) ?>">Open Module</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>