<?php
session_start();
require 'db_connection.php';

// Security Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get Job ID from URL if it exists
$job_id = isset($_GET['job_id']) ? $_GET['job_id'] : null;

if ($job_id) {
    // Fetch applications for a specific job
    $stmt = $pdo->prepare("SELECT a.*, j.job_title FROM applications a 
                           JOIN jobs j ON a.job_id = j.id 
                           WHERE a.job_id = ? ORDER BY a.submitted_at DESC");
    $stmt->execute([$job_id]);
    $job_title_stmt = $pdo->prepare("SELECT job_title FROM jobs WHERE id = ?");
    $job_title_stmt->execute([$job_id]);
    $current_job_title = $job_title_stmt->fetchColumn();
} else {
    // Fetch all applications
    $stmt = $pdo->query("SELECT a.*, j.job_title FROM applications a 
                         LEFT JOIN jobs j ON a.job_id = j.id 
                         ORDER BY a.submitted_at DESC");
    $current_job_title = "All Positions";
}

$applications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>View Applications - CSIR 4PI</title>
    <style>
        .container { max-width: 1200px; margin: 30px auto; padding: 25px; background: #fff; border: 1px solid var(--primary-light); border-radius: 8px; }
        .app-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .app-table th { background: var(--primary-dark); color: white; padding: 12px; text-align: left; }
        .app-table td { padding: 12px; border-bottom: 1px solid #ddd; font-size: 0.9em; }
        .app-table tr:hover { background: #f2f7ff; }
        
        .btn-view { color: var(--primary-color); text-decoration: none; font-weight: bold; border: 1px solid var(--primary-color); padding: 5px 10px; border-radius: 4px; }
        .btn-view:hover { background: var(--primary-color); color: white; }
        
        .header-flex { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--primary-light); padding-bottom: 10px; }
        .badge { background: #eee; padding: 4px 8px; border-radius: 12px; font-size: 0.8em; color: #555; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-flex">
            <div>
                <h2 style="color: var(--primary-dark);">Applications for: <?php echo htmlspecialchars($current_job_title); ?></h2>
                <p>Total Candidates: <strong><?php echo count($applications); ?></strong></p>
            </div>
            <a href="admin_dashboard.php" style="text-decoration:none; color:var(--primary-dark); font-weight:bold;">&larr; Dashboard</a>
        </div>

        <table class="app-table">
            <thead>
                <tr>
                    <th>Candidate Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Job Title</th>
                    <th>Applied Date</th>
                    <th>Summary</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($applications) > 0): ?>
                    <?php foreach ($applications as $app): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($app['full_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($app['email']); ?></td>
                        <td><?php echo htmlspecialchars($app['mobile']); ?></td>
                        <td><span class="badge"><?php echo htmlspecialchars($app['job_title']); ?></span></td>
                        <td><?php echo date('d M Y', strtotime($app['submitted_at'])); ?></td>
                        <td>
                            <a href="generate_pdf.php?id=<?php echo $app['id']; ?>" class="btn-view" target="_blank">PDF Summary</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:40px; color:#999;">No applications found for this position.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>