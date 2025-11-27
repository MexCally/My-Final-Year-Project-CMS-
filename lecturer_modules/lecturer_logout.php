<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login page
header('Location: ../authentications/lecturer_login.html');
exit();

?>