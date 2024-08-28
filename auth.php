<?php
session_start(); // Start a session to track connected users

// Checks if a user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Redirects user to login page if not logged in
function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Disconnects the user, destroying the session
function logout() {
    session_unset(); // Deletes all session variables
    session_destroy(); // Destroys the current session
    header("Location: login.php");
    exit();
}
