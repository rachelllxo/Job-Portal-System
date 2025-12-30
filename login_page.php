<?php
session_start();
require 'db_connection.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 1. Fetch user by email
    $sql = "SELECT id, email, password FROM users WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // 2. Verify password
        if (password_verify($password, $user['password'])) {
            
            // 3. SET THE SESSION DATA CORRECTLY
            $_SESSION['user_id'] = $user['id']; 
            $_SESSION['email'] = $user['email'];
            
            // 4. Redirect to apply.php (or dashboard.php if you prefer)
            header('Location: apply.php');
            exit;
        } else {
            $message = "Invalid email or password.";
        }
    } else {
        $message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <title>Job Portal</title>
    </head>
    <body>
    <div class="container">
    <h2>Sign In</h2>
    <p style="color:red;"<?php echo $message;?></p>
    <form action="login_page.php" method="POST">
        <label for="email">Email</label><br>
        <input type = "email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required><br><br>
        <label for="password">Password: </label><br>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="login">
        </form>

        <p>Don't have an account? Register! <a href="register_page.php">Sign Up</a></p>
        </body>
        </html>
