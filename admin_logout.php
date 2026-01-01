<?php
session_start();
session_destroy(); // Clears all session data
header("Location: admin_login.php");
exit();
?>