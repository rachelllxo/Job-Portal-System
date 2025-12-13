<?php
session_start();
require 'db_connection.php';

$message='';
if ($_SERVER['REQUEST_METHOD']=='POST'){
    $email=trim($_POST['email']);
    $password=$_POST['password'];

    $sql="SELECT id,email, password FROM users WHERE email=?";
    $stmt=$pdo->prepare($sql);
    $stmt->execute([$email]);

    $user=$stmt->fetch();

    if($user){
    if(password_verify($password,$user['password'])){
    $_SESSION['email']=$user['email'];
    }
    header ('Location: dashboard.php');
    exit;
    } else {
    $message="Invalid email or password.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Job Portal</title>
    </head>
    <body>
    <h2>Sign In</h2>
    <p style="color:red;"<?php echo $message;?></p>
    <form action="login_page.php" method="POST">
        <label for="email">Email</label><br>
        <input type="email" id="email" name="email" required><br>
        <label for="password">Password: </label><br>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="login">
        </form>

        <p>Don't have an account? Register! <a href="register_page.php">Sign Up</a></p>
        </body>
        </html>
