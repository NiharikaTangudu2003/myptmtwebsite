<?php
// Include the database connection file
include('db_connections.php');

// Start session to check if the guide is logged in
session_start();

// Check if the guide is logged in, else redirect to login page
if (!isset($_SESSION['guide_id'])) {
    header("Location: student_login.php");
    exit();
}

// Get the logged-in guide's ID
$guideID = $_SESSION['guide_id'];

// Fetch the guide's information from the database
$stmt = $conn->prepare("SELECT * FROM guides WHERE guide_id = ?");
$stmt->bind_param("s", $guideID);
$stmt->execute();
$result = $stmt->get_result();
$guide = $result->fetch_assoc();
$stmt->close();

// Extract guide details
$branch = htmlspecialchars($guide['branch']);
$guide_name = htmlspecialchars($guide['name']);
$attendance_submitted = false;

// Fetch unique batch numbers assigned to the guide (limit to 2)
$query_batches = "SELECT DISTINCT batch_number FROM batch_allocations WHERE guide_name = ?";
$stmt = $conn->prepare($query_batches);
$stmt->bind_param("s", $guide_name);
$stmt->execute();
$result_batches = $stmt->get_result();

$batches = [];
while ($row = $result_batches->fetch_assoc()) {
    $batches[] = $row['batch_number'];
}
$stmt->close();

// Limit batch selection to only two options
$batch_selection = array_slice($batches, 0, 2);
$selected_batch = isset($_POST['batch_number']) ? $_POST['batch_number'] : (count($batch_selection) > 0 ? $batch_selection[0] : "");

// Fetch students for the selected batch sorted by CGPA (highest first)
$students = [];
$query_students = "SELECT student_name, student_cgpa FROM batch_allocations 
                   WHERE guide_name = ? AND batch_number = ? 
                   ORDER BY student_cgpa DESC, student_name ASC";
$stmt = $conn->prepare($query_students);
$stmt->bind_param("ss", $guide_name, $selected_batch);
$stmt->execute();
$result_students = $stmt->get_result();

while ($row = $result_students->fetch_assoc()) {
    $students[] = [
        'name' => $row['student_name'],
        'cgpa' => $row['student_cgpa']
    ];
}
$stmt->close();

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submit_attendance'])) {
    foreach ($_POST['attendance'] as $student => $status) {
        $date = date('Y-m-d');

        // Insert attendance into the database
        $sql = "INSERT INTO attendance (student_name, batch, branch, attendance_date, status, guide_name)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $student, $selected_batch, $branch, $date, $status, $guide_name);
        $stmt->execute();
        $stmt->close();
    }

    // Set flag to show success message
    $attendance_submitted = true;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Guide Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(to right, #ffffff, #d4d9f5);
            padding: 10px 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header img {
            height: 90px;
            size: 100px;
        }

        .nav {
            display: flex;
            gap: 15px;
        }

        .nav a {
            text-decoration: none;
            color: #4e4e4e;
            font-weight: bold;
        }

        .guide-details {
            text-align: right;
            font-size: 0.9em;
            color: #333;
            font-weight: bold;
        }

        .content {
            padding: 20px;
        }

        .section {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .section h2 {
            margin-top: 0;
            color: #4a4a8a;
        }

        .section p {
            line-height: 1.6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }


        .dropdown {
            margin-top: 10px;
        }

        textarea {
            width: 100%;
            height: 100px;
            margin-top: 10px;
        }

        button {
            background-color: #4a4a8a;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #3c3c6b;
        }

        .guide-details{
            text-align: right;
            margin-right: 20px;
            color: #000;
            font-weight: bold;
        }

        .guide-details p{
            margin: 5px 0;
            font-weight: bold;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            bottom: 0;
            width: 100%;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(to right, #ffffff, #d4d9f5);
            padding: 10px 20px;
            border-bottom: 2px solid #ccc;
        }

        .header img {
            height: 80px; /* Adjust the size of the logo */
        }

        .header span {
            font-size: 1.2em;
            font-weight: bold;
        }

        .guide-details {
            text-align: right;
            margin-right: 20px;
            color: #000;
            font-weight: bold;
        }

        .guide-details p {
            margin: 5px 0;
        }

        .guide-details a {
            display: inline-block;
            padding: 8px 15px;
            background-color: #ff4d4d;
            color: white;
            font-size: 14px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .guide-details a:hover {
            background-color:  #ff1a1a;
        }

    </style>
</head>
<body>
    <div class="header">
        <img src="./images/aitamlogo.png" alt="Logo">
        <span><b>Guide Portal</b></span>
        <div class="guide-details">
            <strong>Guide Details:</strong><br>
            <p>Name: <?= htmlspecialchars($guide['name']); ?></p>
            <p>ID: <?= htmlspecialchars($guide['guide_id']); ?></p>
            <p>Branch: <?= htmlspecialchars($guide['branch']); ?></p> <!-- Display Branch -->
            <p>Email: <?= htmlspecialchars($guide['email']); ?></p>
            <a href="index.php" style="text-decoration: none; color: white; background: red; padding: 5px 10px; border-radius: 5px;">LOGOUT</a>
        </div>
    </div>

    <div class="content">
       
    <div class="section">
    <h2>Attendance (Branch: <?= htmlspecialchars($branch); ?>, Guide: <?= htmlspecialchars($guide_name); ?>)</h2>
    <!-- Batch Selection Form -->
    <form method="post">
        <label for="batch_number">Select Batch:</label>
        <select name="batch_number" id="batch_number" onchange="this.form.submit()">
            <?php foreach ($batch_selection as $batch) : ?>
                <option value="<?= htmlspecialchars($batch); ?>" <?= ($batch == $selected_batch) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($batch); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <noscript><button type="submit">Filter</button></noscript>
    </form>
    

   

    <div class="attendance">
        <?php if (!empty($students)) : ?>
            <form method="post">
                <input type="hidden" name="guide_name" value="<?= htmlspecialchars($guide_name); ?>">
                <input type="hidden" name="branch" value="<?= htmlspecialchars($branch); ?>">
                <input type="hidden" name="batch_number" value="<?= htmlspecialchars($selected_batch); ?>">

                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>   
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student) : ?>
                            <tr>
                                <td><?= htmlspecialchars($student['name']); ?></td>
                               
                                <td><?= date('Y-m-d'); ?></td>
                                <td>
                                    <label>
                                        <input type="radio" name="attendance[<?= htmlspecialchars($student['name']); ?>]" value="Present" required> Present
                                    </label>
                                    <label>
                                        <input type="radio" name="attendance[<?= htmlspecialchars($student['name']); ?>]" value="Absent"> Absent
                                    </label>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <br>
                <button type="submit" name="submit_attendance">Submit Attendance</button>
            </form>
            <br>
            <!-- <form method="post" action="download_attendance.php">
            <input type="hidden" name="batch_number" value="<?= htmlspecialchars($selected_batch); ?>">
            <button type="submit" name="download_attendance">Download Attendance</button>
            </form> -->
        <?php else : ?>
            <p>No students available in this batch.</p>
        <?php endif; ?>
    </div>
</div>




<div class="section">
    <h2>Progress Monitoring (Branch: <?= htmlspecialchars($guide['branch']); ?>)</h2>

    <table>
        <thead>
            <tr>
                <th>Batch Number</th>
                <th>Project Title</th>
                <th>Abstract</th>
                <th>Literature</th>
                <th>PPT</th>
                <th>Documentation</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($batch_selection)) {
                $placeholders = implode(',', array_fill(0, count($batch_selection), '?'));
                $query_submissions = "SELECT batch_number, submission_type, file_name, project_title 
                                      FROM project_submissions 
                                      WHERE batch_number IN ($placeholders) 
                                      ORDER BY batch_number ASC";

                $stmt = $conn->prepare($query_submissions);
                $stmt->bind_param(str_repeat('s', count($batch_selection)), ...$batch_selection);
                $stmt->execute();
                $result_submissions = $stmt->get_result();

                $projects = [];
                while ($row = $result_submissions->fetch_assoc()) {
                    $batch = $row['batch_number'];
                    $type = strtolower($row['submission_type']);
                    
                    // Check if the submission type corresponds to a file
                    if ($row['submission_type'] != 'project_title') {
                        $projects[$batch][$type] = "<a href='uploads/" . htmlspecialchars($row['file_name']) . "' download style='text-decoration: none; color: inherit;'>" . htmlspecialchars($row['file_name']) . "</a>";
                    } else {
                        $projects[$batch][$type] = htmlspecialchars($row['file_name']);
                    }
                                      
                }
                $stmt->close();

                foreach ($batch_selection as $batch) :
            ?>
                <tr>
                    <td><?= htmlspecialchars($batch); ?></td>
                    <td><?= isset($projects[$batch]['project_title']) ? $projects[$batch]['project_title'] : '-'; ?></td>
                    <td><?= isset($projects[$batch]['abstract']) ? $projects[$batch]['abstract'] : '-'; ?></td>
                    <td><?= isset($projects[$batch]['literature']) ? $projects[$batch]['literature'] : '-'; ?></td>
                    <td><?= isset($projects[$batch]['presentation']) ? $projects[$batch]['presentation'] : '-'; ?></td>
                    <td><?= isset($projects[$batch]['documentation']) ? $projects[$batch]['documentation'] : '-'; ?></td>
                </tr>
            <?php 
                endforeach;
            } else { 
                echo "<tr><td colspan='6'>No project submissions found.</td></tr>";
            } 
            ?>
        </tbody>
    </table>
</div>

        
<div class="section">
    <h2>Batch Reviews (Branch: <?= htmlspecialchars($guide['branch']); ?>)</h2> <!-- Show branch in batch reviews -->
    <p>Provide reviews for each student:</p>
    <div class="reviews">
        <form method="post" action="">
            <label for="batch-review">Select Batch:</label>
            <select id="batch-review" name="batch_review" required>
                <option value="">Select Batch</option>
                <?php foreach ($batch_selection as $batch): ?>
                    <option value="<?= htmlspecialchars($batch); ?>"> <?= htmlspecialchars($batch); ?></option>
                <?php endforeach; ?>
            </select>
            <textarea id="review-text" name="review_text" placeholder="Write your review..." required></textarea>
            <input type="hidden" name="reviewer_name" value="<?= htmlspecialchars($guide['name']); ?>">
            <input type="hidden" name="reviewer_role" value="Guide">
            <input type="hidden" name="reviewer_branch" value="<?= htmlspecialchars($guide['branch']); ?>">
            <input type="hidden" name="review_date" value="<?= date('Y-m-d'); ?>">
            <label for="review-type">Review Type:</label>
            <select name="review_type" required>
                <option value="">Select Review</option>
                <option value="review1">Review 1</option>
                <option value="review2">Review 2</option>
                <option value="review3">Review 3</option>
                <option value="review4">Review 4</option>
            </select>
            <br>
            <button type="submit" name="submit_review">Submit Review</button>
        </form>
    </div>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submit_review'])) {
    $batch_number = $_POST['batch_review'];
    $reviewer_name = $_POST['reviewer_name'];
    $reviewer_role = $_POST['reviewer_role'];
    $review_text = $_POST['review_text'];
    $review_date = $_POST['review_date'];
    $reviewer_branch = $_POST['reviewer_branch'];
    $review_type = $_POST['review_type'];

    $sql = "INSERT INTO review_submissions (batch_number, reviewer_name, reviewer_role, review_text, review_date, reviewer_branch, review_type) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $batch_number, $reviewer_name, $reviewer_role, $review_text, $review_date, $reviewer_branch, $review_type);
    $stmt->execute();
    $stmt->close();
    
    echo "<p style='color: green;'>Review submitted successfully!</p>";
}
?>

        
    </div>

    <footer>
        <p>&copy; Project Tracker Management Tool</p>
    </footer>
</body>
</html>
