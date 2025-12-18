<?php
/**
 * Check if current user's role is in the allowed roles
 * @param array $allowed_roles Array of allowed role names
 * @return bool
 */
function checkRole($allowed_roles) {
    if (!isset($_SESSION['role'])) {
        return false;
    }
    
    return in_array($_SESSION['role'], $allowed_roles);
}

/**
 * Require specific roles, deny access if not authorized
 * @param array $allowed_roles Array of allowed role names
 */
function requireRole($allowed_roles) {
    if (!checkRole($allowed_roles)) {
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Access Denied</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; margin-top: 100px; }
                .error { color: #dc3545; font-size: 24px; }
                a { color: #007bff; text-decoration: none; }
            </style>
        </head>
        <body>
            <h1 class='error'>Access Denied</h1>
            <p>You do not have permission to access this page.</p>
            <p><a href='../dashboard.php'>Return to Dashboard</a></p>
        </body>
        </html>";
        exit;
    }
}

if (!checkRole(['Admin'])) {
    requireRole(['Admin']);
}
?>