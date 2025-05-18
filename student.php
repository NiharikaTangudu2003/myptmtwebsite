<?php
session_start();
include('db_connections.php');

// Check if student is logged in, if not, redirect to login page
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student details from the database
$sql = "SELECT * FROM student_register WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
    $batch_number = $student['batch_number'];
    $name = $student['student_name'];
    $roll_no = $student['roll_no'];
    $email = $student['email'];
} else {
    // If student not found, redirect to login
    header('Location: login.php');
    exit();
}

// Handle file uploads for project submissions
// Handle file uploads for project submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uploads_dir = 'uploads';
    $batch_number = $_POST['batch_number'] ?? null; // Get the batch number from the form

    if (isset($_FILES['abstract'])) {
        $file = $_FILES['abstract'];
        handleFileUpload($file, $uploads_dir, 'abstract', $student_id, $batch_number);
    }
    if (isset($_FILES['literature'])) {
        $file = $_FILES['literature'];
        handleFileUpload($file, $uploads_dir, 'literature', $student_id, $batch_number);
    }
    if (isset($_FILES['presentation'])) {
        $file = $_FILES['presentation'];
        handleFileUpload($file, $uploads_dir, 'presentation', $student_id, $batch_number);
    }
    if (isset($_FILES['documentation'])) {
        $file = $_FILES['documentation'];
        handleFileUpload($file, $uploads_dir, 'documentation', $student_id, $batch_number);
    }
    if (isset($_POST['title'])) {
        $project_title = $_POST['title'];
        handleTextSubmission($project_title, $student_id, $batch_number);
    }
}

// Handle file uploads for project submissions
function handleFileUpload($file, $uploads_dir, $submission_type, $student_id, $batch_number) {
    if ($file['error'] == UPLOAD_ERR_OK) {
        $tmp_name = $file['tmp_name'];
        $name = basename($file['name']);
        $target_path = "$uploads_dir/$name";

        if (move_uploaded_file($tmp_name, $target_path)) {
            // Insert into project submissions table
            global $conn;
            $branch = $_POST['branch']; // Get the branch from the form
            $stmt = $conn->prepare("INSERT INTO project_submissions (student_id, batch_number, branch, submission_type, file_name) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $student_id, $batch_number, $branch, $submission_type, $name);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Handle project title text submission
function handleTextSubmission($project_title, $student_id, $batch_number) {
    global $conn;
    
    $branch = $_POST['branch']; // Get the branch from the form
    $submission_type = 'project_title';
    $stmt = $conn->prepare("INSERT INTO project_submissions (student_id, batch_number, branch, submission_type, file_name) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $student_id, $batch_number, $branch, $submission_type, $project_title);
    $stmt->execute();
    $stmt->close();
}




// Fetch the submissions and their respective reviews
$sql = "SELECT * FROM project_submissions WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$submissions_result = $stmt->get_result();

// Fetch the reviews for each submission


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal</title>
   <style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f9;
}

header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(to right, #ffffff, #9ed7ae);
    padding: 10px 20px;
    color: #333;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
.header span {
    font-size: 1.2em;
    font-weight: bold;
}
.logo {
    display: flex;
    align-items: center;
}

.logo img {
    height: 80px;
    margin-right: 10px;
}

.logo span {
    font-size: 20px;
    font-weight: bold;
}

.student-summary {
    text-align: right;
    margin-right: 20px;
    color: #000;
    font-weight: bold;
}

.student-summary p {
    margin: 5px 0;
}

.submission-section,
.review-section,
.milestone-section {
    margin: 20px;
    padding: 15px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.submission-section h3, .review-section h3, .milestone-section h3 {
    margin-bottom: 15px;
}

.submission-section form {
    display: flex;
    flex-direction: column;
    margin-bottom: 15px;
}

.submission-section label {
    margin-bottom: 5px;
    font-weight: bold;
}

.submission-section .batch-wrapper {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.submission-section .batch-wrapper input[type="text"],
.submission-section .batch-wrapper select {
    flex: 1;
    margin-right: 10px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.submission-section .batch-wrapper select {
    cursor: pointer;
    background-color: #ffffff;
}

.submission-section .batch-wrapper input[type="file"] {
    flex: 1;
    margin-right: 10px;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.submission-section button {
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    align-self: flex-start;
}

.submission-section button:hover {
    background-color: #45a049;
}

footer {
    background: linear-gradient(to right, #ffffff, #9ed7ae);
    background-color: #333;
    color: black;
    text-align: center;
    padding: 10px 0;
    bottom: 0;
    width: 100%;
}

.submission-section .batch-wrapper select option {
    padding: 10px;
}
.student_summmary a{

}
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="./images/aitamlogo.png" alt="Logo">
            <span style="margin-left: 340px;"><b>Student Portal</b></span>
        </div>
        <div class="student-summary">
            <p>Student Details</p>
            <p>Batch Number: <?php echo $batch_number; ?></p>
            <p>Name: <?php echo $name; ?></p>
            <p>Roll No: <?php echo $roll_no; ?></p>
            <p>Email: <?php echo $email; ?></p><br>
            <a href="logout_student.php" style="text-decoration: none; color: white; background: red; padding: 5px 10px; border-radius: 5px;">LOGOUT</a>
        </div>
    </header>

    <div class="submission-section">
        <h3>Submit Your Project Work</h3>
        <!-- Submit Project Title -->
        <form method="POST">
            <div class="batch-wrapper">
                <input type="text" id="title" name="title" placeholder="Project Title" required>
                <input type="text" class="batch-number" name="batch_number" placeholder="Batch Number" required>
                <select name="branch" class="branch-dropdown" required>
                <option value="" disabled selected>Select Branch</option>
                <option value="IT">IT</option>
                <option value="CSE">CSE</option>
                <option value="ECE">ECE</option>
                <option value="EEE">EEE</option>
                <option value="MECH">MECH</option>
                <option value="CIVIL">CIVIL</option>
                <option value="CSM">CSM</option>
                <option value="CSD">CSD</option>
            </select>
            </div>
            <button type="submit">Submit Project Title</button>
        </form>

        <!-- Submit Abstract -->
        <form method="POST" enctype="multipart/form-data">
            <div class="batch-wrapper">
                <input type="file" id="abstract" name="abstract" required>
                <input type="text" class="batch-number" name="batch_number" placeholder="Batch Number" required>
                <select name="branch" class="branch-dropdown" required>
                <option value="" disabled selected>Select Branch</option>
                <option value="IT">IT</option>
                <option value="CSE">CSE</option>
                <option value="ECE">ECE</option>
                <option value="EEE">EEE</option>
                <option value="MECH">MECH</option>
                <option value="CIVIL">CIVIL</option>
                <option value="CSM">CSM</option>
                <option value="CSD">CSD</option>
            </select>
            </div>
            <button type="submit">Submit Abstract</button>
        </form>

        <!-- Submit Literature Paper -->
        <form method="POST" enctype="multipart/form-data">
            <div class="batch-wrapper">
                <input type="file" id="literature" name="literature" required>
                <input type="text" class="batch-number" name="batch_number" placeholder="Batch Number" required>
                 <select name="branch" class="branch-dropdown" required>
                <option value="" disabled selected>Select Branch</option>
                <option value="IT">IT</option>
                <option value="CSE">CSE</option>
                <option value="ECE">ECE</option>
                <option value="EEE">EEE</option>
                <option value="MECH">MECH</option>
                <option value="CIVIL">CIVIL</option>
                <option value="CSM">CSM</option>
                <option value="CSD">CSD</option>
            </select>
            </div>
            <button type="submit">Submit Literature Paper</button>
        </form>

        <!-- Submit Presentation -->
        <form method="POST" enctype="multipart/form-data">
            <div class="batch-wrapper">
                <input type="file" id="presentation" name="presentation" required>
                <input type="text" class="batch-number" name="batch_number" placeholder="Batch Number" required>
                <select name="branch" class="branch-dropdown" required>
                <option value="" disabled selected>Select Branch</option>
                <option value="IT">IT</option>
                <option value="CSE">CSE</option>
                <option value="ECE">ECE</option>
                <option value="EEE">EEE</option>
                <option value="MECH">MECH</option>
                <option value="CIVIL">CIVIL</option>
                <option value="CSM">CSM</option>
                <option value="CSD">CSD</option>
            </select>
            </div>
            <button type="submit">Submit Presentation</button>
        </form>

        <!-- Submit Final Documentation -->
        <form method="POST" enctype="multipart/form-data">
            <div class="batch-wrapper">
                <input type="file" id="documentation" name="documentation" required>
                <input type="text" class="batch-number" name="batch_number" placeholder="Batch Number" required>
                <select name="branch" class="branch-dropdown" required>
                <option value="" disabled selected>Select Branch</option>
                <option value="IT">IT</option>
                <option value="CSE">CSE</option>
                <option value="ECE">ECE</option>
                <option value="EEE">EEE</option>
                <option value="MECH">MECH</option>
                <option value="CIVIL">CIVIL</option>
                <option value="CSM">CSM</option>
                <option value="CSD">CSD</option>
            </select>
            </div>
            <button type="submit">Submit Final Documentation</button>
        </form>
    </div>

    <div class="review-section">
        <h3>Review Details</h3>
        <label for="review">Select Review:</label>
        <select id="review" name="review" onchange="showReviewDetails()">
            <option value="">-- Select Review --</option>
            <?php
            // Fetch reviews dynamically based on student's batch number
            $review_sql = "SELECT id, review_text, batch_number, reviewer_name, reviewer_role, review_date, reviewer_branch, review_type FROM review_submissions WHERE batch_number = ?";
            $stmt = $conn->prepare($review_sql);
            $stmt->bind_param("s", $batch_number);
            $stmt->execute();
            $review_result = $stmt->get_result();
            
            $reviews = [];
            while ($row = $review_result->fetch_assoc()) {
                $reviews[$row['id']] = $row;
                echo "<option value='{$row['id']}'>Review by {$row['reviewer_name']} :- {$row['review_type']}</option>";
            }
            ?>
            
        </select>
        
        <div id="review-details" class="review-details">
            <p>Please select a review to see the details.</p>
        </div>
        
        
    </div>

<script>
    const reviews = <?php echo json_encode($reviews); ?>;

    function showReviewDetails() {
        const reviewId = document.getElementById("review").value;
        const reviewDetails = document.getElementById("review-details");

        if (reviewId && reviews[reviewId]) {
            const selectedReview = reviews[reviewId];
            reviewDetails.innerHTML = `
                <table border="1" cellspacing="0" cellpadding="5" style="margin-top:25px;margin-left:350px;">
                    <tr>
                        <th>Reviewer</th>
                        <th>Review Type</th>
                        <th>Date</th>
                        <th>Branch</th>
                        <th>Review</th>

                    </tr>
                    <tr>
                        <td>${selectedReview.reviewer_name} (${selectedReview.reviewer_role})</td>
                        <td>${selectedReview.review_type}</td>
                        <td>${selectedReview.review_date}</td>
                        <td>${selectedReview.reviewer_branch}</td>
                        <td>${selectedReview.review_text}</td>
                    </tr>
                </table>
            `;
        } else {
            reviewDetails.innerHTML = "<p>Please select a review to see the details.</p>";
        }
    }
</script>   
    <footer>
        <p>&copy; Project Tracker Management Tool</p>
    </footer>
</body>
</html>
