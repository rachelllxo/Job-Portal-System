<?php
session_start();
require 'db_connection.php'; 

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_id = trim($_POST['employee_id']);
    $pass = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE employee_id = ?");
    $stmt->execute([$emp_id]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($pass, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['emp_id'] = $admin['employee_id'];
        $_SESSION['admin_name'] = $admin['full_name'];
        
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid Employee ID or Password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>CSIR - 4PI Admin Login</title>
    <style>
        .container { max-width: 450px; margin: 80px auto; padding: 30px; border: 1px solid var(--primary-light); border-radius: 8px; background: #fff; }
        .section-title { color: var(--primary-dark); border-bottom: 2px solid var(--primary-light); padding-bottom: 5px; margin-bottom: 20px; text-align: center; }
        input { width: 100%; padding: 10px; margin-bottom: 15px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .login-btn { background: var(--primary-dark); color: white; padding: 12px; width: 100%; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .error-msg { color: #d9534f; background: #f2dede; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center; border: 1px solid #ebccd1; }
        .redirect-link { text-align: center; margin-top: 20px; font-size: 0.9em; }
        .redirect-link a { color: var(--primary-color); text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="text-align: center; color: var(--primary-dark);">CSIR - 4th Paradigm Institute</h2>
        <h3 class="section-title">Admin Portal Login</h3>
        
        <?php if($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label style="font-weight: bold; color: var(--primary-color);">Employee ID</label>
            <input type="text" name="employee_id" required placeholder="Enter Employee ID">

            <label style="font-weight: bold; color: var(--primary-color);">Password</label>
            <input type="password" name="password" required placeholder="Enter Password">

            <button type="submit" class="login-btn">Login to Dashboard</button>
        </form>
        
        <div class="redirect-link">
            New Admin? <a href="admin_signup.php">Register / Sign Up here</a>
        </div>
    </div>
</body>
</html>