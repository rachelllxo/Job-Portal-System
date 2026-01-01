<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
require 'db_connection.php';
require 'fpdf.php'; 
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$message = "";
// Load .env variables (Ensure you have the loadEnv function in db_connection.php)

// 1. SECURITY CHECK: Prevent "user_id cannot be null" error
if (!isset($_SESSION['user_id'])) {
    die("Error: Your session has expired or you are not logged in. Please log in again to submit the form.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    // 1. Handle File Uploads
    $sig_name = time() . "_sig_" . basename($_FILES["signature"]["name"]);
    $sig_path = $target_dir . $sig_name;
    $photo_name = time() . "_photo_" . basename($_FILES["passport_photo"]["name"]);
    $photo_path = $target_dir . $photo_name;

    if (move_uploaded_file($_FILES["signature"]["tmp_name"], $sig_path) && 
        move_uploaded_file($_FILES["passport_photo"]["tmp_name"], $photo_path)) {
        
        // 2. Handle Dynamic Employment
        $jobs = [];
        if (isset($_POST['emp_org'])) {
            foreach ($_POST['emp_org'] as $key => $val) {
                if (!empty($val)) {
                    $jobs[] = [
                        'org' => $val, 
                        'from' => $_POST['emp_from'][$key],
                        'to' => $_POST['emp_to'][$key], 
                        'pos' => $_POST['emp_pos'][$key],
                        'type' => $_POST['emp_type'][$key]
                    ];
                }
            }
        }

        // 3. Prepare Data Array
        $data = [
            'user_id'           => $_SESSION['user_id'] ?? NULL,
            'photo_path'        => $photo_path,
            'full_name'         => $_POST['full_name'],
            'father_name'       => $_POST['father_name'],
            'sex'               => $_POST['sex'],
            'nationality'       => $_POST['nationality'],
            'mailing_address'   => $_POST['mailing_address'],
            'mobile_number'     => $_POST['mobile_number'],
            'email_id'          => $_POST['email_id'],
            'permanent_address' => $_POST['permanent_address'],
            'dob'               => $_POST['dob'],
            'age'               => $_POST['age'],
            'category'          => $_POST['category'],
            'edu10_board' => $_POST['edu10_board'], 'edu10_to' => $_POST['edu10_to'], 'edu10_perc' => $_POST['edu10_perc'],
            'edu12_board' => $_POST['edu12_board'], 'edu12_to' => $_POST['edu12_to'], 'edu12_perc' => $_POST['edu12_perc'],
            'ug_board' => $_POST['graduation_board'], 'ug_to' => $_POST['ug_to'], 'ug_perc' => $_POST['graduation_perc'],
            'pg_board' => $_POST['post_graduation_board'] ?? '', 
            'pg_to' => $_POST['pg_to'] ?? '', 
            'pg_perc' => $_POST['post_graduation_perc'] ?? '',
            'mphil_name' => $_POST['mphil_name'], 'mphil_board' => $_POST['mphil_board'], 'mphil_to' => $_POST['mphil_to'], 'mphil_perc' => $_POST['mphil_perc'],
            'phd_name' => $_POST['phd_name'], 'phd_board' => $_POST['phd_board'], 'phd_to' => $_POST['phd_to'], 'phd_perc' => $_POST['phd_perc'],
            'employment_json'   => json_encode($jobs),
            'rel_name' => $_POST['rel_name'], 'rel_relation' => $_POST['rel_relation'],
            'under_bond' => $_POST['under_bond'], 'dismissed' => $_POST['dismissed'],
            'app_place' => $_POST['app_place'], 'app_date' => $_POST['app_date'],
            'signature_path'    => $sig_path
        ];

        // 4. Database Insert
        // Capture the job_id from the URL or a hidden input field
$data['job_id'] = $_POST['job_id'] ?? NULL;

        // 5. Dynamic Database Insert (The clean way)
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $sql = "INSERT INTO applications ($columns) VALUES ($placeholders)";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_values($data));

            // 5. Generate PDF
            $pdf = new FPDF();
            $pdf->AddPage();
            
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(190, 10, 'CSIR - 4th Paradigm Institute, Bengaluru', 0, 1, 'C');
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(190, 7, 'Application Summary - 2025', 0, 1, 'C');
            $pdf->Line(10, 28, 200, 28);
            
            if(file_exists($photo_path)) $pdf->Image($photo_path, 160, 32, 30, 35);
            
            $pdf->Ln(15);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetFillColor(230, 230, 230);
            $pdf->Cell(190, 8, ' PERSONAL DETAILS', 0, 1, 'L', true);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Ln(2);
            $pdf->Cell(40, 7, 'Full Name:', 0); $pdf->Cell(100, 7, strtoupper($data['full_name']), 0, 1);
            $pdf->Cell(40, 7, 'Father/Husband Name:', 0); $pdf->Cell(100, 7, strtoupper($data['father_name']), 0, 1);
            $pdf->Cell(40, 7, 'Sex:', 0); $pdf->Cell(100, 7, strtoupper($data['sex']), 0, 1);
            $pdf->Cell(40, 7, 'Nationality:', 0); $pdf->Cell(100, 7, strtoupper($data['nationality']), 0, 1);
            $pdf->Cell(40, 7,'Mailing Address:', 0); $pdf->Cell(100, 7, strtoupper($data['mailing_address']), 0, 1);
            $pdf->Cell(40, 7, 'Mobile Number:', 0); $pdf->Cell(100, 7, strtoupper($data['mobile_number']), 0, 1);
            $pdf->Cell(40, 7, 'Email:', 0); $pdf->Cell(100, 7, $data['email_id'], 0, 1);
            $pdf->Cell(40, 7, 'Permanent Address:', 0); $pdf->Cell(100, 7, strtoupper($data['permanent_address']), 0, 1);
            $pdf->Cell(40, 7, 'Date of Birth:', 0); $pdf->Cell(100, 7, strtoupper($data['dob']), 0, 1);
            $pdf->Cell(40, 7, 'Age:', 0); $pdf->Cell(100, 7, strtoupper($data['age']), 0, 1);
            $pdf->Cell(40, 7, 'Category:', 0); $pdf->Cell(100, 7, strtoupper($data['category']), 0, 1);

            // Education Table
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(190, 8, ' EDUCATIONAL QUALIFICATIONS', 0, 1, 'L', true);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(30, 7, 'Exam', 1); $pdf->Cell(80, 7, 'Board/Univ', 1); $pdf->Cell(30, 7, 'Year', 1); $pdf->Cell(50, 7, 'Result', 1); $pdf->Ln();
            
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(30, 7, '10th', 1); $pdf->Cell(80, 7, $data['edu10_board'], 1); $pdf->Cell(30, 7, $data['edu10_to'], 1); $pdf->Cell(50, 7, $data['edu10_perc'].'%', 1); $pdf->Ln();
            $pdf->Cell(30, 7, '12th', 1); $pdf->Cell(80, 7, $data['edu12_board'], 1); $pdf->Cell(30, 7, $data['edu12_to'], 1); $pdf->Cell(50, 7, $data['edu12_perc'].'%', 1); $pdf->Ln();
$pdf->Cell(30, 7, 'UG', 1); 
$pdf->MultiCell(90, 5, $data['ug_board'], 1); // This moves the cursor to the next line
$pdf->Cell(30, 7, $data['ug_to'], 1); // This starts on a new line            
            if(!empty($data['pg_board'])) {
                $pdf->Cell(30, 7, 'PG', 1); $pdf->Cell(80, 7, $data['pg_board'], 1); $pdf->Cell(30, 7, $data['pg_to'], 1); $pdf->Cell(50, 7, $data['pg_perc'].'%', 1); $pdf->Ln();
            }
            if(!empty($data['mphil_board'])) {
                $pdf->Cell(30, 7, 'MPHil', 1); $pdf->Cell(80, 7, $data['mphil_board'], 1); $pdf->Cell(30, 7, $data['mphil_to'], 1); $pdf->Cell(50, 7, $data['mphil_perc'].'%', 1); $pdf->Ln();
            }
            if(!empty($data['phd_board'])) {
                $pdf->Cell(30, 7, 'PhD', 1); $pdf->Cell(80, 7, $data['phd_board'], 1); $pdf->Cell(30, 7, $data['phd_to'], 1); $pdf->Cell(50, 7, $data['phd_perc'].'%', 1); $pdf->Ln();
            }

            // Employment Table
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(190, 8, ' EMPLOYMENT HISTORY', 0, 1, 'L', true);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(60, 7, 'Organization', 1); $pdf->Cell(40, 7, 'Duration', 1); $pdf->Cell(50, 7, 'Designation', 1); $pdf->Cell(40, 7, 'Type', 1); $pdf->Ln();
            $pdf->SetFont('Arial', '', 9);
            foreach ($jobs as $job) {
                $pdf->Cell(60, 7, $job['org'], 1);
                $pdf->Cell(40, 7, $job['from'] . ' to ' . $job['to'], 1);
                $pdf->Cell(50, 7, $job['pos'], 1);
                $pdf->Cell(40, 7, $job['type'], 1);
                $pdf->Ln();
            }
            $pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 8, ' 4. ADDITIONAL DETAILS', 0, 1, 'L', true);
$pdf->SetFont('Arial', '', 9);

// Question 1: Bond Obligation
$pdf->MultiCell(190, 5, "Are you under any Bond/ Contractual obligation to serve Central /State Government /PSU /Autonomous or any other body /organization?: " . $data['under_bond'], 1, 'L');

// Question 2: Dismissal
$pdf->MultiCell(190, 5, "Whether dismissed from service from any other institution /office or debarred by the Public Service Commission? If Yes, give details: " . $data['dismissed'], 1, 'L');

$pdf->Ln(5);

// --- Section 5: Final Declaration ---
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 8, ' 5. DECLARATION', 0, 1, 'L', true);
$pdf->SetFont('Arial', '', 9);

$declarationText = "I hereby declare that all the statements made in this application are true, complete and correct to the best of my knowledge and belief.\n\nI understand that in the event of any information being found false or incorrect at any stage, my candidature/appointment shall be liable to be cancelled / terminated summarily without notice.";

// Use MultiCell for the declaration text to handle wrapping [cite: 25, 26]
$pdf->MultiCell(190, 5, $declarationText, 0, 'L');

// --- Place and Date ---
$pdf->Ln(5);
$pdf->Cell(95, 7, 'Place: ' . strtoupper($data['app_place']), 0, 0, 'L');
$pdf->Cell(95, 7, 'Date: ' . $data['app_date'], 0, 1, 'R');
            // Signature
            $pdf->Ln(15);
            if(file_exists($sig_path)) $pdf->Image($sig_path, 150, $pdf->GetY(), 40, 15);
            $pdf->Ln(16);
            $pdf->Cell(140, 7, '', 0); $pdf->Cell(50, 7, 'Candidate Signature', 0, 1, 'C');

            $pdf_content = $pdf->Output('S'); 

            // 6. Send Email
            // 6. Send Email
            $mail = new PHPMailer(true);

            // Add a check to see if email is actually there
            if (empty($data['email_id'])) {
                throw new Exception("Email address is missing from the form submission.");
            }

            // --- Server Settings ---
            $mail->SMTPDebug = 0; // Keep this until you confirm it works, then change to 0
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USER']; 
            $mail->Password   = $_ENV['SMTP_PASS'];    
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $_ENV['SMTP_PORT'];
            $mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
            // --- Recipients & Content ---
            $mail->setFrom($_ENV['SMTP_USER'], 'CSIR-4PI Recruitment');
            $mail->addAddress($data['email_id'], $data['full_name']);
            $mail->addStringAttachment($pdf_content, 'Application_Summary.pdf');

            $mail->isHTML(true);
            $mail->Subject = 'Confirmation: Application Submitted Successfully';
            $mail->Body    = "Dear <b>{$data['full_name']}</b>,<br><br>Your application has been received. Please find your summary attached.";

            // --- Send It ---
            $mail->send();

            // Store data for success page
            $_SESSION['submitted_name'] = $data['full_name'];
            $_SESSION['submitted_email'] = $data['email_id'];

            header("Location: success.php");
            exit();

        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        $message = "Error: Failed to upload signature or photo.";
    } 
} 
?>
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>CSIR - 4th Paradigm Institute Application</title>
    <style>
        .container { max-width: 850px; margin: auto; }
        .section-title { color: var(--primary-dark); border-bottom: 2px solid var(--primary-light); padding-bottom: 5px; margin-top: 30px; text-align: left; }
        .grid-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 10px; }
        fieldset { border: 1px solid var(--primary-light); border-radius: 8px; margin-bottom: 20px; padding: 15px; text-align: left; }
        legend { font-weight: bold; color: var(--primary-color); padding: 0 10px; }
        input, select, textarea { width: 100%; padding: 8px; box-sizing: border-box; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Job Application Form</h2>
        <?php if($message) echo "<p style='color:green; font-weight:bold;'>$message</p>"; ?>

        <form action="apply.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($_GET['job_id'] ?? ''); ?>">
            <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($_GET['job_id'] ?? ''); ?>">
            <h3 class="section-title">1. Personal Information</h3>
            <div class="grid-row">
                <input type="text" name="full_name" placeholder="Name in full (Block Letters)" required>
                <input type="text" name="father_name" placeholder="Father's / Husband's Name" required>
            </div>
            <div class="grid-row">
                <select name="sex" required>
                    <option value="">Select Sex</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <input type="text" name="nationality" placeholder="Nationality">
            </div>
            <div class="grid-row">
                <textarea name="mailing_address" placeholder="Mailing Address with PINCODE" rows="3"></textarea>
                <textarea name="permanent_address" placeholder="Permanent address with PINCODE" rows="3"></textarea>
            </div>
            <div class="grid-row">
                <input type="text" name="mobile_number" placeholder="Valid Mobile Number">
                <input type="email" name="email_id" placeholder="Valid Email ID">
            </div>
            <div class="grid-row">
                <input type="date" name="dob" title="Date of Birth">
                <input type="number" name="age" placeholder="Age as on last date">
            </div>
            <div class="grid-row">
                <select name="category" required>
                    <option value="">Select Category</option>
                    <option value="SC">SC</option>
                    <option value="ST">ST</option>
                    <option value="OBC">OBC</option>
                    <option value="GEN">GEN</option>
                </select>
            </div>

            <h3 class="section-title">2. Educational Qualifications</h3>
            
            <fieldset>
                <legend>10th Details (Mandatory)</legend>
                <input type="text" name="edu10_name" placeholder="School Name">
                <div class="grid-row">
                    <input type="text" name="edu10_from" placeholder="From (Year)">
                    <input type="text" name="edu10_to" placeholder="To (Year)">
                </div>
                <div class="grid-row">
                    <input type="number" name="edu10_marks" placeholder="Marks Obtained">
                    <input type="number" name="edu10_total" placeholder="Total Marks">
                    <input type="text" name="edu10_perc" placeholder="Percentage/GPA">
                    <input type="text" name="edu10_board" placeholder="Board/University">
                </div>
            </fieldset>

            <fieldset>
                <legend>12th Details (Mandatory)</legend>
                <input type="text" name="edu12_name" placeholder="School Name">
                <div class="grid-row">
                    <input type="text" name="edu12_from" placeholder="From">
                    <input type="text" name="edu12_to" placeholder="To">
                </div>
                <div class="grid-row">
                    <input type="number" name="edu12_marks" placeholder="Marks Obtained">
                    <input type="number" name="edu12_total" placeholder="Total Marks">
                    <input type="text" name="edu12_perc" placeholder="Percentage/GPA">
                    <input type="text" name="edu12_board" placeholder="Board/University">
                </div>
            </fieldset>

            <fieldset>
                <legend>Graduation</legend>
                <input type="text" name="graduation_name" placeholder="Course Name">
                <div class="grid-row">
                    <input type="text" name="ug_from" placeholder="From">
                    <input type="text" name="ug_to" placeholder="To">
                    <input type="number" name="graduation_marks" placeholder="Marks Obtained">
                    <input type="number" name="graduation_total" placeholder="Total">
                    <input type="text" name="graduation_perc" placeholder="%">
                    <input type="text" name="graduation_board" placeholder="University">
                </div>
            </fieldset>
                    <fieldset>
            <legend>Post Graduation</legend>
            <input type="text" name="post_graduation_name" placeholder="Course Name (e.g. M.Sc, M.Tech, MCA)">
            <div class="grid-row" style="margin-top: 10px;">
                <input type="text" name="pg_from" placeholder="From (Year)">
                <input type="text" name="pg_to" placeholder="To (Year)">
            </div>
            <div class="grid-row">
                <input type="number" name="post_graduation_marks" placeholder="Marks Obtained">
                <input type="number" name="post_graduation_total" placeholder="Total Marks">
                <input type="text" name="post_graduation_perc" placeholder="Percentage / CGPA">
                <input type="text" name="post_graduation_board" placeholder="University Name">
            </div>
        </fieldset>

                            <fieldset>
                    <legend>Higher Education </legend>
                    <div class="grid-row">
                        <input type="text" name="mphil_name" placeholder="M.Phil Course Name">
                        <input type="text" name="mphil_board" placeholder="University">
                        <input type="text" name="mphil_perc" placeholder="M.Phil %">
                        <input type="text" name="mphil_to" placeholder="Year of Passing">
                    </div>
                    <div class="grid-row" style="margin-top: 10px;">
                        <input type="text" name="phd_name" placeholder="Ph.D Course Name">
                        <input type="text" name="phd_board" placeholder="University">
                        <input type="text" name="phd_perc" placeholder="Ph.D %">
                        <input type="text" name="phd_to" placeholder="Year of Passing">
                    </div>
                </fieldset>

            <h3 class="section-title">3. Details Of Employment</h3>
            <div id="employment-container">
                <fieldset class="employment-entry">
                    <legend>Employment Entry</legend>
                    <input type="text" name="emp_org[]" placeholder="Organization">
                    <div class="grid-row">
                        <input type="text" name="emp_from[]" placeholder="From">
                        <input type="text" name="emp_to[]" placeholder="To">
                    </div>
                    <div class="grid-row">
                        <input type="text" name="emp_pos[]" placeholder="Position">
                        <input type="text" name="emp_type[]" placeholder="Basis (Regular/Contract)">
                    </div>
                </fieldset>
            </div>
            <button type="button" onclick="addJob()" style="background: var(--primary-color); color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 20px;">+ Add Another Job</button>

            <h3 class="section-title">4.  & Declaration</h3>
            <div class="grid-row">
                <input type="text" name="rel_name" placeholder="Relations in CSIR (Name)">
                <input type="text" name="rel_relation" placeholder="Relationship">
                <input type="text" name="rel_design" placeholder="Designation">
                <input type="text" name="rel_div" placeholder="Division">
            </div>
            <input type="text" name="other-details" placeholder="Any other details" style="margin-bottom:10px;">
            <textarea name="under_bond" placeholder="Are you under any Bond/ Contractual obligation to serve Central /State Government /PSU /Autonomous or any other body /organization"></textarea>
            <textarea name="dismissed" placeholder="Whether dismissed from service from any other institution /office or debarred by the Public Service Commission If Yes, give details"></textarea>

            <h3 class="section-title">5. Declaration, Photo & Signature Submission</h3>
            <p style="font-size: 0.9em; text-align: left;">I hereby declare that all the statements made in this application are true, complete and correct to the best of my knowledge and belief.
I understand that in the event of any information being found false or incorrect at any stage, my candidature/appointment shall be liable to be cancelled / terminated summarily without notice or any compensation in lieu thereof. 
</p>
            <div class="grid-row">
                <input type="text" name="app_place" placeholder="Place" required>
                <input type="date" name="app_date" value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="grid-row">
                <div>
                    <label>Upload Passport Photo [less than 500kb] </label>
                    <input type="file" name="passport_photo" accept="image/*" required>
                </div>
                <div>
                    <label>Upload Signature [less than 500kb]</label>
                    <input type="file" name="signature" accept="image/*" required>
                </div>
            </div>

            <input type="submit" value="Submit Application" style="background: var(--primary-dark); color: white; padding: 15px; margin-top: 20px; cursor: pointer;">
        </form>
    </div>

    <script>
    function addJob() {
        const container = document.getElementById('employment-container');
        const firstEntry = document.querySelector('.employment-entry');
        const newEntry = firstEntry.cloneNode(true);
        newEntry.querySelectorAll('input').forEach(input => input.value = '');
        container.appendChild(newEntry);
    }
    </script>
</body>
</html>

        
