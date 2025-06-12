<?php
session_start();

// Clear all session data
session_unset();
session_destroy();

// Redirect to login page or home page
header('Location: login.php');
exit();
?>
