<?php
session_start();
require 'db_connection.php';

// Security Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle Job Deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $del_stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
    if ($del_stmt->execute([$delete_id])) {
        $msg = "Job deleted successfully.";
    }
}

// Fetch all jobs
$stmt = $pdo->query("SELECT * FROM jobs ORDER BY posted_at DESC");
$jobs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Manage Jobs - Admin</title>
    <style>
        .container { max-width: 1100px; margin: 30px auto; padding: 25px; background: #fff; border: 1px solid var(--primary-light); border-radius: 8px; }
        .job-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .job-table th { background: var(--primary-dark); color: white; padding: 12px; text-align: left; }
        .job-table td { padding: 12px; border-bottom: 1px solid #ddd; font-size: 0.95em; }
        .job-table tr:hover { background: #f9f9f9; }
        
        .btn-delete { color: #d9534f; text-decoration: none; font-weight: bold; padding: 5px 10px; border: 1px solid #d9534f; border-radius: 4px; }
        .btn-delete:hover { background: #d9534f; color: white; }
        
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold; }
        .status-active { background: #dff0d8; color: #3c763d; }
        
        .nav-header { display: flex; justify-content: space-between; align-items: center; }
        .add-btn { background: var(--primary-color); color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav-header">
            <h2 style="color: var(--primary-dark);">Manage Job Openings</h2>
            <div>
                <a href="post_job.php" class="add-btn">+ Post New Job</a>
                <a href="admin_dashboard.php" style="margin-left:15px; text-decoration:none; color:var(--primary-dark);">Dashboard &rarr;</a>
            <a href="view_applications.php?job_id=<?php echo $job['id']; ?>" 
   style="color: var(--primary-color); text-decoration: none; font-weight: bold; margin-right: 10px;">
   View Applicants
</a>
</div>
        </div>


        <?php if(isset($msg)): ?>
            <div style="background:#dff0d8; color:#3c763d; padding:10px; margin-top:15px; border-radius:4px;"><?php echo $msg; ?></div>
        <?php endif; ?>

        <table class="job-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Department</th>
                    <th>Location</th>
                    <th>Type</th>
                    <th>Last Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($jobs) > 0): ?>
                    <?php foreach ($jobs as $job): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($job['job_title']); ?></strong></td>
                        <td><?php echo htmlspecialchars($job['department']); ?></td>
                        <td><?php echo htmlspecialchars($job['location']); ?></td>
                        <td><?php echo htmlspecialchars($job['job_type']); ?></td>
                        <td><?php echo date('d-M-Y', strtotime($job['last_date_to_apply'])); ?></td>
                        <td>
                            <a href="view_jobs.php?delete_id=<?php echo $job['id']; ?>" 
                               class="btn-delete" 
                               onclick="return confirm('Are you sure you want to delete this job listing?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:30px; color:#777;">No jobs found. Click "Post New Job" to start.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>