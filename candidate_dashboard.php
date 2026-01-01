<?php
session_start();
require 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_page.php"); // Updated to match your login filename
    exit();
}

// Fetch only active job openings (where deadline hasn't passed)
// This pulls exactly what the Admin added via post_job.php
$stmt = $pdo->query("SELECT * FROM jobs WHERE last_date_to_apply >= CURDATE() ORDER BY posted_at DESC");
$jobs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Candidate Dashboard - CSIR 4PI</title>
    <style>
        .container { max-width: 1000px; margin: 40px auto; padding: 20px; }
        .header-flex { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 2px solid var(--primary-light); 
            padding-bottom: 15px; 
            margin-bottom: 30px; 
        }
        .job-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .job-card { 
            background: #fff; border: 1px solid var(--primary-light); border-radius: 8px; 
            padding: 20px; transition: 0.3s; display: flex; flex-direction: column;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .job-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .job-title { color: var(--primary-dark); font-size: 1.2em; margin-bottom: 5px; font-weight: bold; }
        .dept { color: var(--primary-color); font-size: 0.85em; font-weight: bold; text-transform: uppercase; margin-bottom: 10px; }
        .description-preview { font-size: 0.9em; color: #666; margin-bottom: 15px; line-height: 1.4; }
        .job-meta { font-size: 0.85em; margin-bottom: 8px; color: #444; display: flex; align-items: center; gap: 8px; }
        
        .apply-btn { 
            margin-top: auto; background: var(--primary-color); color: white; 
            text-align: center; padding: 10px; text-decoration: none; border-radius: 5px; font-weight: bold;
        }
        .apply-btn:hover { background: var(--primary-dark); }
        .logout-btn { color: #d9534f; text-decoration: none; font-size: 0.9em; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-flex">
            <div>
                <h1 style="margin:0; color: var(--primary-dark);">Available Positions</h1>
                <p style="margin:5px 0 0 0;">Logged in as: <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Candidate'); ?></strong></p>
            </div>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>

        <div class="job-grid">
            <?php if (count($jobs) > 0): ?>
                <?php foreach ($jobs as $job): ?>
                <div class="job-card">
                    <div class="dept"><?php echo htmlspecialchars($job['department']); ?></div>
                    <div class="job-title"><?php echo htmlspecialchars($job['job_title']); ?></div>
                    
                    <div class="description-preview">
                        <?php 
                            // Show first 100 characters of description
                            $desc = htmlspecialchars($job['job_description']);
                            echo (strlen($desc) > 100) ? substr($desc, 0, 100) . '...' : $desc;
                        ?>
                    </div>
                    
                    <div class="job-meta">📍 <span><?php echo htmlspecialchars($job['location']); ?></span></div>
                    <div class="job-meta">💼 <span><?php echo htmlspecialchars($job['job_type']); ?></span></div>
                    <div class="job-meta">📅 <span style="color: #d9534f;">Deadline: <?php echo date('d M, Y', strtotime($job['last_date_to_apply'])); ?></span></div>
                    
                    <a href="apply.php?job_id=<?php echo $job['id']; ?>" class="apply-btn">Apply Now</a>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 50px; background: #f9f9f9; border-radius: 8px;">
                    <p style="color: #777; font-size: 1.1em;">Currently, there are no active job openings at CSIR-4PI.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>