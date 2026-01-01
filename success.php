<?php
session_start();

// Redirect back to form if they haven't submitted anything
if (!isset($_SESSION['submitted_name'])) {
    header("Location: apply.php");
    exit();
}

$name = $_SESSION['submitted_name'];
$email = $_SESSION['submitted_email'];

// Clear session so they can't refresh and see this specific data forever
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submitted - CSIR-4PI</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #f4f7f6; font-family: 'Segoe UI', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .success-card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; max-width: 500px; width: 90%; }
        .icon { font-size: 60px; color: #28a745; margin-bottom: 20px; }
        h2 { color: #333; margin-bottom: 10px; }
        p { color: #666; line-height: 1.6; }
        .email-highlight { font-weight: bold; color: #0056b3; }
        .btn { display: inline-block; margin-top: 25px; padding: 12px 25px; background: #0056b3; color: white; text-decoration: none; border-radius: 5px; transition: 0.3s; }
        .btn:hover { background: #004494; }
        .note { margin-top: 20px; font-size: 0.85em; color: #999; border-top: 1px solid #eee; padding-top: 15px; }
    </style>
</head>
<body>

<div class="success-card">
    <div class="icon">!</div>
    <h2>Submission Successful!</h2>
    <p>Dear <strong><?php echo htmlspecialchars($name); ?></strong>,</p>
    <p>Your application for the <strong>CSIR - 4th Paradigm Institute</strong> has been received successfully.</p>
    
    <p>A confirmation email with your <strong>Application Summary PDF</strong> has been sent to:<br>
    <span class="email-highlight"><?php echo htmlspecialchars($email); ?></span></p>

    <a href="apply.php" class="btn">Submit Another Application</a>

    <div class="note">
        If you don't see the email within 5 minutes, please check your <strong>Spam/Junk folder</strong>.
    </div>
</div>

</body>
</html>