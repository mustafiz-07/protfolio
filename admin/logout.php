<?php
session_start();

// Clear session
session_destroy();

// Clear remember me cookie
setcookie('admin_remember', '', time() - 3600, '/');

// Redirect to login page
header('Location: login.php?logged_out=1');
exit();
?>
