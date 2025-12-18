<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/functions.php';

if (!isLoggedIn()) {
    $login_path = (basename(dirname($_SERVER['PHP_SELF'])) === 'users' || 
                   basename(dirname($_SERVER['PHP_SELF'])) === 'inventory' || 
                   basename(dirname($_SERVER['PHP_SELF'])) === 'pos') 
                   ? 'user_auth_app/login.php' : 'login.php';
    redirect($login_path);
}
?>