<?php
session_start();
require 'db_connection.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_id = trim($_POST['employee_id']);
    $full_name = trim($_POST['full_name']);
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($pass !== $confirm_pass) {
        $error = "Passwords do not match.";
    } else {
        $check = $pdo->prepare("SELECT id FROM admins WHERE employee_id = ?");
        $check->execute([$emp_id]);
        
        if ($check->fetch()) {
            $error = "This Employee ID is already registered.";
        } else {
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
            try {
                $sql = "INSERT INTO admins (employee_id, password, full_name) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$emp_id, $hashed_pass, $full_name]);
                $message = "Registration successful! You can now log in.";
            } catch (PDOException $e) {
                $error = "Registration failed: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>CSIR - Admin Registration</title>
    <style>
        .container { max-width: 500px; margin: 50px auto; padding: 30px; border: 1px solid var(--primary-light); border-radius: 8px; background: #fff; }
        .section-title { color: var(--primary-dark); border-bottom: 2px solid var(--primary-light); padding-bottom: 5px; margin-bottom: 20px; text-align: center; }
        input { width: 100%; padding: 10px; margin-bottom: 15px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .signup-btn { background: var(--primary-dark); color: white; padding: 12px; width: 100%; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .error-msg { color: #a94442; background: #f2dede; padding: 10px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #ebccd1; }
        .success-msg { color: #3c763d; background: #dff0d8; padding: 10px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #d6e9c6; }
        .redirect-link { text-align: center; margin-top: 20px; font-size: 0.9em; }
        .redirect-link a { color: var(--primary-color); text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="text-align: center; color: var(--primary-dark);">CSIR - 4th Paradigm Institute</h2>
        <h3 class="section-title">Employee Registration</h3>
        
        <?php if($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if($message): ?>
            <div class="success-msg"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label style="font-weight: bold; color: var(--primary-color);">Full Name</label>
            <input type="text" name="full_name" required placeholder="Enter Full Name">

            <label style="font-weight: bold; color: var(--primary-color);">Employee ID</label>
            <input type="text" name="employee_id" required placeholder="Example: EMP123">

            <label style="font-weight: bold; color: var(--primary-color);">Password</label>
            <input type="password" name="password" required placeholder="Create Password">

            <label style="font-weight: bold; color: var(--primary-color);">Confirm Password</label>
            <input type="password" name="confirm_password" required placeholder="Repeat Password">

            <button type="submit" class="signup-btn">Register Employee</button>
        </form>
        
        <div class="redirect-link">
            Already have an account? <a href="admin_login.php">Login here</a>
        </div>
    </div>
</body>
</html>