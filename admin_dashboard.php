<?php
session_start();
require 'db_connection.php';

// Security Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch applications with Job Titles
// We use a LEFT JOIN to ensure we see applications even if a job was recently deleted
$query = "SELECT a.*, j.job_title 
          FROM applications a 
          LEFT JOIN jobs j ON a.job_id = j.id 
          ORDER BY a.app_date DESC";
$stmt = $pdo->query($query);
$applications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Candidate Applications - Admin</title>
    <style>
        .container { max-width: 1100px; margin: 30px auto; padding: 25px; background: #fff; border-radius: 8px; border: 1px solid var(--primary-light); }
        .app-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .app-table th { background: var(--primary-dark); color: white; padding: 12px; text-align: left; }
        .app-table td { padding: 12px; border-bottom: 1px solid #eee; font-size: 0.9em; }
        .app-table tr:hover { background: #f9f9fb; }
        
        .status-badge { background: #e8f0fe; color: #1a73e8; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 0.8em; }
        .pdf-link { color: #d9534f; text-decoration: none; font-weight: bold; border: 1px solid #d9534f; padding: 4px 10px; border-radius: 4px; }
        .pdf-link:hover { background: #d9534f; color: white; }
        
        .header-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-nav">
            <h2>Received Applications</h2>
            <a href="admin_dashboard.php" style="text-decoration:none; color:var(--primary-color); font-weight:bold;">&larr; Back to Dashboard</a>
        </div>

        <table class="app-table">
            <thead>
                <tr>
                    <th>Candidate Name</th>
                    <th>Job Applied For</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Date Applied</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($applications) > 0): ?>
                    <?php foreach ($applications as $app): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($app['full_name']); ?></strong></td>
                        <td><span class="status-badge"><?php echo htmlspecialchars($app['job_title'] ?? 'N/A'); ?></span></td>
                        <td><?php echo htmlspecialchars($app['email_id']); ?></td>
                        <td><?php echo htmlspecialchars($app['mobile_number']); ?></td>
                        <td><?php echo date('d-M-Y', strtotime($app['app_date'])); ?></td>
                        <td>
                            <a href="generate_pdf.php?id=<?php echo $app['id']; ?>" class="pdf-link" target="_blank">View PDF</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:30px; color:#888;">No applications found in the system.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>