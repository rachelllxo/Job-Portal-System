<?php
session_start();
require 'db_connection.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 1. Fetch user by email
    // Added 'full_name' to the query so we can greet them on the dashboard
    $sql = "SELECT id, email, password, full_name FROM users WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // 2. Verify password
        if (password_verify($password, $user['password'])) {
            
            // 3. SET THE SESSION DATA CORRECTLY
            $_SESSION['user_id'] = $user['id']; 
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_name'] = $user['full_name']; // Storing name for the dashboard
            
            // 4. Redirect to Candidate Dashboard
            header('Location: candidate_dashboard.php');
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
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Candidate Login - CSIR 4PI</title>
</head>
<body>
    <div class="container" style="max-width: 400px; margin: 100px auto; text-align: left;">
        <h2 style="color: var(--primary-dark); border-bottom: 2px solid var(--primary-light); padding-bottom: 10px;">Sign In</h2>
        
        <?php if($message): ?>
            <p style="color:red; background: #fdeaea; padding: 10px; border-radius: 4px;"><?php echo $message;?></p>
        <?php endif; ?>

        <form action="login_page.php" method="POST">
            <label for="email" style="font-weight: bold;">Email Address</label><br>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required style="width: 100%; padding: 8px; margin-top: 5px;"><br><br>
            
            <label for="password" style="font-weight: bold;">Password</label><br>
            <input type="password" id="password" name="password" required style="width: 100%; padding: 8px; margin-top: 5px;"><br><br>
            
            <input type="submit" value="Login" style="background: var(--primary-color); color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px; font-weight: bold; width: 100%;">
        </form>

        <p style="margin-top: 20px; font-size: 0.9em; text-align: center;">
            Don't have an account? <a href="register_page.php" style="color: var(--primary-color); text-decoration: none; font-weight: bold;">Sign Up</a>
        </p>
    </div>
</body>
</html>
