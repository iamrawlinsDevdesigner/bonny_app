<?php
session_start();

// Remove all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to home or login
header("Location: ../../index.php");
exit;
