<?php
session_start();

// Unset all student-related session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to student login page
header('Location: ../authentications/student_login.html');
exit();
?>


