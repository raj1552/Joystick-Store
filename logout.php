<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: ./customer-login.php");
    exit();
}

// Clear all session variables
$_SESSION = array();

// If it's desired to kill the session cookie as well
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session
session_destroy();

// Set a success message (optional - you can remove this if not needed)
session_start(); // Start a new session for the message
$_SESSION['logout_message'] = "You have been successfully logged out.";

// Redirect to home page or login page
header("Location: ./index.php");
exit();
?>