<?php
session_start(); // Start session
session_unset(); // Clear session variables
session_destroy(); // Destroy session
header("Location: login.php"); // Send user back to login page
exit();
?>
