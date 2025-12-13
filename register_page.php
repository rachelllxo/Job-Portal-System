<?php
require 'db_connection.php';

$message='';

if ($_SERVER['REQUEST_METHOD']=='POST'){
$email = trim($_POST['email']);
$password=$_POST['password'];
if (!filter_var($email,FILTER_VALIDATE_EMAIL)){
    $message="Invalid email format.";
} else {
$hashed_password=password_hash($password,PASSWORD_DEFAULT);

$sql="INSERT INTO users (email,password) VALUES (?,?)";

try{
$stmt=$pdo->prepare($sql);
$stmt->execute([$email,$hashed_password]);

$message="Registration successful! You can now log in,";
} catch (PDOException $e){
if($e->getCode()==23000){
$message="Email already exists.";
} else {
$message="An error occurred during registration.";
}
}
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Job Portal</title>
</head>
<body>
    <h2>Sign up</h2>
    <p style="color:red;"><?php echo $message;?></p>
    <form action="register_page.php" method="POST">
    <label for="email">Email: </label><br>
    <input type = "email" id="email" name="email" required><br><br>

    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" required<br><br>
    <input type="submit" value="Sign up">
    </form>

    <p>Already have an account? <a href="login_page.php">Sign In </a></p>
    </body>
    </html>



