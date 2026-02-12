<?php
/**
 * Reusable Helper Functions
 */

/**
 * Start session if not already started.
 */
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Check if a user is logged in.
 */
function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']);
}

/**
 * Get the logged-in user's type ('customer' or 'agency').
 */
function getUserType() {
    startSession();
    return isset($_SESSION['user_type']) ? $_SESSION['user_type'] : null;
}

/**
 * Get the logged-in user's ID.
 */
function getUserId() {
    startSession();
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

/**
 * Get the logged-in user's display name.
 */
function getUserName() {
    startSession();
    return isset($_SESSION['full_name']) ? $_SESSION['full_name'] : null;
}

/**
 * Redirect to a URL and stop script execution.
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Escape output for safe HTML display (XSS prevention).
 */
function escape($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Format a number as currency.
 */
function formatCurrency($amount) {
    return '₹' . number_format((float)$amount, 2);
}

/**
 * Format a date string.
 */
function formatDate($dateString) {
    return date('d M Y', strtotime($dateString));
}
