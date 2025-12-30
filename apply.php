<?php
session_start();
require 'db_connection.php';



$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    // Handle Signature Upload
    $sig_name = time() . "_sig_" . basename($_FILES["signature"]["name"]);
    $sig_path = $target_dir . $sig_name;
    
    // Handle Photo Upload
    $photo_name = time() . "_photo_" . basename($_FILES["passport_photo"]["name"]);
    $photo_path = $target_dir . $photo_name;

    if (move_uploaded_file($_FILES["signature"]["tmp_name"], $sig_path) && 
        move_uploaded_file($_FILES["passport_photo"]["tmp_name"], $photo_path)) {
        
        // Handle Dynamic Employment (Converting arrays to JSON string)
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

        $data = [
            'user_id'           => $_SESSION['user_id'],
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
            
            // Education
            'edu10_name' => $_POST['edu10_name'], 'edu10_from' => $_POST['edu10_from'], 'edu10_to' => $_POST['edu10_to'],
            'edu10_marks' => $_POST['edu10_marks'], 'edu10_total' => $_POST['edu10_total'], 'edu10_perc' => $_POST['edu10_perc'], 'edu10_board' => $_POST['edu10_board'],
            
            'edu12_name' => $_POST['edu12_name'], 'edu12_from' => $_POST['edu12_from'], 'edu12_to' => $_POST['edu12_to'],
            'edu12_marks' => $_POST['edu12_marks'], 'edu12_total' => $_POST['edu12_total'], 'edu12_perc' => $_POST['edu12_perc'], 'edu12_board' => $_POST['edu12_board'],
            
            'ug_name' => $_POST['graduation_name'], 'ug_from' => $_POST['ug_from'], 'ug_to' => $_POST['ug_to'],
            'ug_marks' => $_POST['graduation_marks'], 'ug_total' => $_POST['graduation_total'], 'ug_perc' => $_POST['graduation_perc'], 'ug_board' => $_POST['graduation_board'],
            
            'pg_name' => $_POST['post_graduation_name'], 'pg_from' => $_POST['pg_from'], 'pg_to' => $_POST['pg_to'],
            'pg_marks' => $_POST['post_graduation_marks'], 'pg_total' => $_POST['post_graduation_total'], 'pg_perc' => $_POST['post_graduation_perc'], 'pg_board' => $_POST['post_graduation_board'],
            
            'mphil_name' => $_POST['mphil_name'], 'mphil_from' => $_POST['mphil_from'], 'mphil_to' => $_POST['mphil_to'],
            'mphil_marks' => $_POST['mphil_marks'], 'mphil_total' => $_POST['mphil_total'], 'mphil_perc' => $_POST['mphil_perc'], 'mphil_board' => $_POST['mphil_board'],
            
            'phd_name' => $_POST['phd_name'], 'phd_from' => $_POST['phd_from'], 'phd_to' => $_POST['phd_to'],
            'phd_marks' => $_POST['phd_marks'], 'phd_total' => $_POST['phd_total'], 'phd_perc' => $_POST['phd_perc'], 'phd_board' => $_POST['phd_board'],
            
            // Employment stored as JSON
            'employment_json' => json_encode($jobs),
            
            // Relatives & Misc
            'rel_name' => $_POST['rel_name'], 'rel_design' => $_POST['rel_design'], 'rel_div' => $_POST['rel_div'], 'rel_relation' => $_POST['rel_relation'],
            'other_details' => $_POST['other-details'],
            'under_bond' => $_POST['under_bond'], 'dismissed' => $_POST['dismissed'],
            'app_place' => $_POST['app_place'], 'app_date' => $_POST['app_date'],
            'signature_path' => $sig_path
        ];

        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $sql = "INSERT INTO applications ($columns) VALUES ($placeholders)";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_values($data));
            $message = "Application submitted successfully!";
        } catch (PDOException $e) {
            $message = "Database Error: " . $e->getMessage();
        }
    } else {
        $message = "Failed to upload files.";
    }
}
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