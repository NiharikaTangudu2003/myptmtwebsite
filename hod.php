<?php
// Start session and include database connection
session_start();
include 'db_connections.php'; // Include the database connection file

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: hod_login.php"); // Redirect to login if not logged in
    exit();
}

// Retrieve HOD info from session
$username = $_SESSION['username'];
$sql = "SELECT * FROM hod WHERE email = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$hod = $result->fetch_assoc();
$stmt->close();

// Check if the HOD details were fetched successfully
if (!$hod) {
    echo "HOD details not found!";
    exit();
}

$hodName = $hod['name'];
$hodId = $hod['faculty_id'];
$hodEmail = $hod['email'];
$hodBranch = $hod['branch'];

// Fetch project submissions for the HOD's branch
$submissionQuery = "
    SELECT batch_number, 
           MAX(CASE WHEN submission_type = 'project_title' THEN file_name END) AS project_title,
           MAX(CASE WHEN submission_type = 'abstract' THEN file_name END) AS abstract,
           MAX(CASE WHEN submission_type = 'literature' THEN file_name END) AS literature,
           MAX(CASE WHEN submission_type = 'presentation' THEN file_name END) AS presentation,
           MAX(CASE WHEN submission_type = 'documentation' THEN file_name END) AS documentation
    FROM project_submissions
    WHERE branch = ?
    GROUP BY batch_number
";
$stmt = $conn->prepare($submissionQuery);
$stmt->bind_param("s", $hodBranch);
$stmt->execute();
$submissionResult = $stmt->get_result();
$stmt->close();

// Handle review submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $batchNumber = $_POST['batch_number'];
    $reviewText = $_POST['review_text'];
    $reviewType = $_POST['review_type']; // Get the selected review type
    $reviewerRole = 'HOD';

    if (!empty($batchNumber) && !empty($reviewText) && !empty($reviewType)) {
        $insertReviewQuery = "INSERT INTO review_submissions (batch_number, reviewer_name, reviewer_role, review_text, reviewer_branch, review_type) 
                              VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertReviewQuery);
        $stmt->bind_param("ssssss", $batchNumber, $hodName, $reviewerRole, $reviewText, $hodBranch, $reviewType);
        $stmt->execute();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOD Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }
        .header {
            background: linear-gradient(to right, #ffffff, #d4d9f5);
            padding: 10px 20px;
        }
        .section {
            margin: 20px 0;
            background: #fff;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .section h2 {
            margin-top: 0;
            color: #4a4a8a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
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
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        textarea {
            width: 100%;
            height: 100px;
            margin-top: 10px;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(to right, #ffffff, #d4d4f7);
            padding: 10px 20px;
            color: #333;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .hod_details p{
            margin: 5px 0;
        }
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            bottom: 0;
            width: 100%;
        }
        .file-link {
        color: black; /* Ensures text appears as normal */
        text-decoration: none; /* Removes underline */
        font-weight: normal;
        display: inline-block; /* Keeps it inline */
    }

    .file-link:hover {
        text-decoration: none; /* Ensures underline does not appear on hover */
        color: black; /* Keeps color unchanged */
    }
    </style>
</head>
<body>

    <header>
        <div class="logo">
            <img src="./images/aitamlogo.png" alt="Logo" style="height: 80px; margin-right: 10px;"> 
            <span style="font-size: 1.2em;font-weight: bold;margin-left:350px;"><b>HOD Portal<b></span>
        </div>
        <div class="hod-summary" style="text-align: right;
            margin-right: 20px;
            color: #000;
            font-weight: bold;">
            <div class="hod_details">
            <p>HOD Details</p>
            <p>Name: <?php echo htmlspecialchars($hodName); ?></p>
            <p>ID: <?php echo htmlspecialchars($hodId); ?></p>
            <p>Email: <?php echo htmlspecialchars($hodEmail); ?></p>
            <p>Branch: <?php echo htmlspecialchars($hodBranch); ?></p><br>
            <a href="index.php" style="color: white; background-color: #ff4d4d; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Logout</a>
        </div>
    </header>

    <div class="content">
        <div class="section">
            <h2>Progress Monitoring</h2>
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
    if ($submissionResult->num_rows > 0) {
        while ($row = $submissionResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['batch_number']) . "</td>";
            echo "<td>" . htmlspecialchars($row['project_title']) . "</td>";

            // Modify to make file name clickable for download with styles
            $submissionTypes = ['abstract', 'literature', 'presentation', 'documentation'];
            foreach ($submissionTypes as $type) {
                echo "<td>";
                if (!empty($row[$type])) {
                    echo "<a href='uploads/" . htmlspecialchars($row[$type]) . "' download='" . htmlspecialchars($row[$type]) . "' class='file-link'>" . htmlspecialchars($row[$type]) . "</a>";
                } else {
                    echo "No file";
                }
                echo "</td>";
            }

            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No submissions found for your branch.</td></tr>";
    }
    ?>
</tbody>


            </table>
        </div>
    </div>

    <div class="section">
        <h2>Batch Reviews</h2>
        <form method="POST">
            <div>
                <label for="batch_number">Select Batch:</label>
                <select name="batch_number" required>
                    <option value="">Select Batch</option>
                    <?php
                    $batchQuery = "SELECT DISTINCT batch_number FROM project_submissions WHERE branch = ?";
                    $stmt = $conn->prepare($batchQuery);
                    $stmt->bind_param("s", $hodBranch);
                    $stmt->execute();
                    $batchResult = $stmt->get_result();
                    while ($batch = $batchResult->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($batch['batch_number']) . "'>" . htmlspecialchars($batch['batch_number']) . "</option>";
                    }
                    $stmt->close();
                    ?>
                </select>

                <select name="review_type" required>
                    <option value="">Select Review</option>
                    <option value="review1">Review 1</option>
                    <option value="review2">Review 2</option>
                    <option value="review3">Review 3</option>
                    <option value="review4">Review 4</option>
                </select>
            </div>
            <br>
            <div>
            <textarea name="review_text" placeholder="Write your review..." required></textarea>
            <br>
            <button type="submit" style="background-color: #4a4a8a;
                color: #fff;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;">
                Submit Review
            </button>
        </form>
    </div>

    <footer>
        <p>&copy; Project Tracker Management Tool</p>
    </footer>

</body>
</html>
