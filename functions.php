<?php
/**
 * Hash password using Argon2i
 * @param string $password
 * @return string
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_ARGON2I);
}

/**
 * Verify password against hash
 * @param string $password
 * @param string $hash
 * @return bool
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Sanitize user input
 * @param string $input
 * @return string
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Redirect to a URL
 * @param string $url
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Sanitize user input (alternative name for sanitize for consistency with existing code)
 * @param string $input
 * @return string
 */
function sanitize_input($input) {
    return sanitize($input);
}

/**
 * Hash password (alias for hashPassword for consistency with existing code)
 * @param string $password
 * @return string
 */
function hash_password($password) {
    return hashPassword($password);
}

/**
 * Validate email format
 * @param string $email
 * @return bool
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 * @param string $password
 * @return bool
 */
function validate_password($password) {
    return strlen($password) >= 8;
}

/**
 * Check if username already exists
 * @param PDO $pdo
 * @param string $username
 * @return bool
 */
function username_exists($pdo, $username) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetchColumn() > 0;
}

/**
 * Check if email already exists
 * @param PDO $pdo
 * @param string $email
 * @return bool
 */
function email_exists($pdo, $email) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetchColumn() > 0;
}
?>