<?php
/**
 * Logout
 *
 * Destroys the session and redirects to login page.
 */
require_once __DIR__ . '/../includes/functions.php';
startSession();
session_unset();
session_destroy();
header('Location: /auth/login.php');
exit;
