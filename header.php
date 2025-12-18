<!DOCTYPE html>
<html>
<head>
    <title><?= isset($page_title) ? $page_title : 'User Authentication System' ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; }
        .header {
            background-color: #007bff;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 { font-size: 24px; }
        .nav { background-color: #0056b3; padding: 10px 30px; }
        .nav a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
            padding: 5px 10px;
        }
        .nav a:hover { background-color: #003d82; }
        .user-info { text-align: right; font-size: 14px; }
        .user-info a {
            color: white;
            text-decoration: none;
            background-color: #dc3545;
            padding: 5px 15px;
            border-radius: 3px;
            margin-left: 10px;
        }
        .user-info a:hover { background-color: #c82333; }
        .container { max-width: 1200px; margin: 30px auto; padding: 20px; }
    </style>
</head>
<body>

<div class="header">
    <h1>Inventory & POS System</h1>
    <div class="user-info">
        <?php if (isLoggedIn()): ?>
            <div>Welcome, <strong><?= sanitize($_SESSION['full_name'] ?? $_SESSION['username']) ?></strong></div>
            <div>Role: <?= sanitize($_SESSION['role']) ?></div>
            <a href="<?= (basename(dirname($_SERVER['PHP_SELF'])) === 'users' || basename(dirname($_SERVER['PHP_SELF'])) === 'inventory' || basename(dirname($_SERVER['PHP_SELF'])) === 'pos') ? '../logout.php' : 'logout.php' ?>">Logout</a>
        <?php endif; ?>
    </div>
</div>

<?php if (isLoggedIn()): ?>
<nav class="nav">
    <a href="<?= (basename(dirname($_SERVER['PHP_SELF'])) === 'users' || basename(dirname($_SERVER['PHP_SELF'])) === 'inventory' || basename(dirname($_SERVER['PHP_SELF'])) === 'pos') ? '../dashboard.php' : 'dashboard.php' ?>">Dashboard</a>
    <a href="<?= (basename(dirname($_SERVER['PHP_SELF'])) === 'users' || basename(dirname($_SERVER['PHP_SELF'])) === 'inventory' || basename(dirname($_SERVER['PHP_SELF'])) === 'pos') ? '../inventory/index.php' : 'inventory/index.php' ?>">Inventory</a>
    <a href="<?= (basename(dirname($_SERVER['PHP_SELF'])) === 'users' || basename(dirname($_SERVER['PHP_SELF'])) === 'inventory' || basename(dirname($_SERVER['PHP_SELF'])) === 'pos') ? '../pos/pos.php' : 'pos/pos.php' ?>">POS</a>
    <?php if (checkRole(['admin'])): ?>
        <a href="<?= (basename(dirname($_SERVER['PHP_SELF'])) === 'users' || basename(dirname($_SERVER['PHP_SELF'])) === 'inventory' || basename(dirname($_SERVER['PHP_SELF'])) === 'pos') ? (basename(dirname($_SERVER['PHP_SELF'])) === 'users' ? 'index.php' : '../users/index.php') : 'users/index.php' ?>">Users</a>
    <?php endif; ?>
</nav>
<?php endif; ?>

<div class="container">
