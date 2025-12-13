<?php
session_start();

if(!isset($_SESSION['email'])){
header('Location: login_page.php');
exit;
}

$email=$_SESSION['email'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Dashboard</title>
</head>
<body>
<h2>Welcome to the Job Portal, <?php echo htmlspecialchars($email);?>!</h2>
<p>You can now start building your job application from here</p>
<p><a href="logout.php">Log Out</a></p>
</body>
</html>
