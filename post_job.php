<?php
session_start();
require 'db_connection.php';

// Security Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['job_title']);
    $desc  = trim($_POST['job_description']);
    $dept  = trim($_POST['department']);
    $loc   = trim($_POST['location']);
    $type  = $_POST['job_type'];
    $exp   = trim($_POST['experience_required']);
    $date  = $_POST['last_date_to_apply'];

    try {
        $sql = "INSERT INTO jobs (job_title, job_description, department, location, job_type, experience_required, last_date_to_apply) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $desc, $dept, $loc, $type, $exp, $date]);
        
        $message = "Job posted successfully!";
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Post New Job - Admin</title>
    <style>
        .container { max-width: 800px; margin: 30px auto; padding: 25px; background: #fff; border: 1px solid var(--primary-light); border-radius: 8px; }
        .form-group { margin-bottom: 15px; }
        label { font-weight: bold; display: block; margin-bottom: 5px; color: var(--primary-dark); }
        input[type="text"], input[type="date"], select, textarea { 
            width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; 
        }
        textarea { height: 120px; resize: vertical; }
        .submit-btn { 
            background: var(--primary-color); color: white; padding: 12px 25px; border: none; 
            border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; 
        }
        .submit-btn:hover { background: var(--primary-dark); }
        .nav-link { text-decoration: none; color: var(--primary-color); font-weight: bold; }
        .success-msg { background: #dff0d8; color: #3c763d; padding: 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #d6e9c6; }
    </style>
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="color: var(--primary-dark);">Post New Job Opening</h2>
            <a href="admin_dashboard.php" class="nav-link">&larr; Back to Dashboard</a>
        </div>
        <hr style="border: 0; border-top: 2px solid var(--primary-light); margin: 20px 0;">

        <?php if($message): ?>
            <div class="success-msg"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="grid-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Job Title</label>
                    <input type="text" name="job_title" placeholder="e.g. Project Associate" required>
                </div>
                <div class="form-group">
                    <label>Department</label>
                    <input type="text" name="department" placeholder="e.g. Data Science">
                </div>
            </div>

            <div class="grid-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" placeholder="e.g. Bengaluru" required>
                </div>
                <div class="form-group">
                    <label>Job Type</label>
                    <select name="job_type" required>
                        <option value="Permanent">Permanent</option>
                        <option value="Contract">Contract</option>
                        <option value="Temporary">Temporary</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Job Description</label>
                <textarea name="job_description" placeholder="Enter roles, responsibilities, and requirements..." required></textarea>
            </div>

            <div class="grid-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Experience Required</label>
                    <input type="text" name="experience_required" placeholder="e.g. 0-2 Years">
                </div>
                <div class="form-group">
                    <label>Last Date to Apply</label>
                    <input type="date" name="last_date_to_apply" required>
                </div>
            </div>

            <div style="text-align: right; margin-top: 20px;">
                <button type="submit" class="submit-btn">Publish Job Opening</button>
            </div>
        </form>
    </div>
</body>
</html>