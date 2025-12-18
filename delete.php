<?php
session_start();
require_once "../includes/auth_check.php";
require_once "../includes/rbac_check.php";
require_once "../includes/db_connect.php";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$user_id = (int)$_GET['id'];

if ($user_id === $_SESSION['user_id']) {
    $_SESSION['error_message'] = "You cannot delete your own account.";
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$user_id]);

header("Location: index.php");
exit;
?>