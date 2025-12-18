<?php
session_start();
require_once "../includes/auth_check.php";
require_once "../includes/rbac_check.php";
require_once "../includes/db_connect.php";
require_once "../includes/functions.php";

$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { color: #333; }
        .actions { margin-bottom: 20px; }
        .btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
        }
        .btn:hover { background-color: #0056b3; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td { border: 1px solid #ddd; }
        th {
            background-color: #007bff;
            color: white;
            padding: 12px;
            text-align: left;
        }
        td { padding: 10px; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .action-links a {
            margin-right: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .action-links a:hover { text-decoration: underline; }
        .delete-link { color: #dc3545; }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<h2>Manage Users</h2>

<div class="actions">
    <a href="add.php" class="btn">Add New User</a>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Full Name</th>
            <th>Role</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= sanitize($user['username']) ?></td>
            <td><?= sanitize($user['full_name']) ?></td>
            <td><?= sanitize($user['role']) ?></td>
            <td><?= date('Y-m-d H:i:s', strtotime($user['created_at'])) ?></td>
            <td class="action-links">
                <a href="edit.php?id=<?= $user['id'] ?>">Edit</a>
                <a href="change_password.php?id=<?= $user['id'] ?>">Change Password</a>
                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                    <a href="delete.php?id=<?= $user['id'] ?>" class="delete-link" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="../dashboard.php" class="back-link">← Back to Dashboard</a>

</body>
</html>
