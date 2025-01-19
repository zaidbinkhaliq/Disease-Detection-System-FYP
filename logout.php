<?php
session_start();

// Check if admin or user is logged in and log them out
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // Unset admin session variables
    unset($_SESSION['admin_user_id']);
    unset($_SESSION['admin_email']);
    unset($_SESSION['admin_password']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['admin_logged_in']);
} elseif (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    // Unset user session variables
    unset($_SESSION['user_id']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_password']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_logged_in']);
}

// Destroy the session if all variables are unset
session_destroy();

// Redirect to the login page or any other page
header("Location: ../log/login.php");
exit();
