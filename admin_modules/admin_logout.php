<?php
session_start();

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to admin login page
header('Location: ../authentications/admin_login.html');
exit();
?>
