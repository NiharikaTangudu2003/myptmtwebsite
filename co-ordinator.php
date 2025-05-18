<?php
session_start();

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['faculty_email'])) {
    header("Location: co-ordinator_login.php"); // Redirect to the login page
    exit();
}

// Include the database connection
include('db_connections.php');

// Fetch coordinator data from the database based on logged-in email
$email = $_SESSION['faculty_email'];
$stmt = $conn->prepare("SELECT * FROM coordinators WHERE faculty_email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Check if coordinator exists
if ($result->num_rows > 0) {
    $coordinator = $result->fetch_assoc();
    $coordinatorName = $coordinator['faculty_name'];
    $coordinatorId = $coordinator['faculty_id'];
    $coordinatorBranch = $coordinator['branch'];
} else {
    die("Coordinator not found.");
}
$stmt->close();

// Fetch project submissions **only for the coordinator's branch**
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

$submissionStmt = $conn->prepare($submissionQuery);
$submissionStmt->bind_param("s", $coordinatorBranch);
$submissionStmt->execute();
$submissionResult = $submissionStmt->get_result();
$submissionStmt->close();

// Handle review submission
// Handle review submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $batchNumber = $_POST['batch_number'];
    $reviewText = $_POST['review_text'];
    $reviewType = $_POST['review_type'];
    $reviewerRole = 'Coordinator';

    
    date_default_timezone_set('Asia/Kolkata');

    // Get the current time in UTC format
    $reviewDate = date('Y-m-d H:i:s');

    if (!empty($batchNumber) && !empty($reviewText) && !empty($reviewType)) {
        $insertReviewQuery = "INSERT INTO review_submissions (batch_number, reviewer_name, reviewer_role, review_text, review_date, reviewer_branch, review_type) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertReviewQuery);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("sssssss", $batchNumber, $coordinatorName, $reviewerRole, $reviewText, $reviewDate, $coordinatorBranch, $reviewType);
        if ($stmt->execute()) {
            echo "<p style='color:green;'>Review submitted successfully.</p>";
        } else {
            echo "<p style='color:red;'>Error submitting review: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color:red;'>All fields are required.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Co-ordinator Dashboard</title>
    <style>
        /* Add styles similar to the previous version */
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
        .batch-display ul {
            list-style: none;
            padding: 0;
        }
        .batch-display li {
            background: #f9f9f9;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
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

        .co_ordinator-summary {
            text-align: right;
            margin-right: 20px;
            color: #000;
            font-weight: bold;
        }

        .co_ordinator-summary p {
            margin: 5px 0;
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
        .dropdown {
            margin-top: 10px;
        }

        textarea {
            width: 100%;
            height: 100px;
            margin-top: 10px;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            bottom: 0;
            width: 100%;
        }
            

        .co_ordinator-summary p {
            font-size: 16px;
            margin: 10px 0;
            color: #333;
        }

        .co_ordinator-summary p strong {
            font-weight: bold;
        }

        .co_ordinator-summary a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ff4d4d;
            color: white;
            font-size: 16px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-top: 15px;
        }

        .co_ordinator-summary a:hover {
            background-color: #ff1a1a; /* Darker blue on hover */
        }

        #batch-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        #batch-table th, #batch-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        #batch-table th {
            background: #4a4a8a;
            color: white;
        }
        #batch-table tr:nth-child(even) {
            background: #f4f4f9;
        }
        #batch-table tr:hover {
            background: #e0e0ff;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="./images/aitamlogo.png" alt="Logo">
            <span style="margin-left: 340px;">Co-ordinator Portal</span>
        </div>
        <div class="co_ordinator-summary">
            <p>Coordinator Details</p>
            <p>Name: <?php echo htmlspecialchars($coordinator['faculty_name']); ?></p>
            <p>ID: <?php echo htmlspecialchars($coordinator['faculty_id']); ?></p>
            <p>Email: <?php echo htmlspecialchars($coordinator['faculty_email']); ?></p>
            <p>Branch: <?php echo htmlspecialchars($coordinator['branch']); ?></p>
            <a href="index.php" style="text-decoration: none;color: #000;">Logout</a>
        </div>

    </header>
 <div class="content">
        <div class="section">
            <h2>Batch Division Based on CGPA</h2>
            <p>Upload an Excel sheet of students with their CGPAs:</p>
            <input type="file" id="file-input" accept=".xlsx, .xls">
            <button id="process-file">Process File</button>
        </div>

        <div class="section">
            <h2>Guide Allocation</h2>
            <p>Upload an Excel sheet for guide allocation:</p>
            <input type="file" id="guide-file-input" accept=".xlsx, .xls">
        </div>

        <div class="section batch-display" id="batch-display" style="display: none;">
            <h3>Allocated Batches with Guides</h3>
            <table id="batch-table">
                <thead>
                    <tr>
                        <th>Batch Number</th>
                        <th>Section</th>
                        <th>Students</th>
                        <th>Guide</th>
                    </tr>
                </thead>
                <tbody id="batch-list">
                    <!-- Batches will be displayed here -->
                </tbody>
            </table>
        </div>
    </div>
    <div class="section">
        <h2>Progress Monitoring (<?php echo htmlspecialchars($coordinatorBranch); ?>)</h2>
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
            echo "<td>" . (!empty($row['project_title']) ? htmlspecialchars($row['project_title']) : "Not Submitted") . "</td>";

            // Download links for specific project details
            foreach (['abstract', 'literature', 'presentation', 'documentation'] as $type) {
                if (!empty($row[$type])) {
                    echo "<td><a href='uploads/" . htmlspecialchars($row[$type]) . "' download style='text-decoration: none; color: inherit;'>" . htmlspecialchars($row[$type]) . "</a></td>";
                } else {
                    echo "<td>Not Submitted</td>";
                }
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
    <div class="section">
        <h2>Batch Reviews</h2>
        <form method="POST">
            <label for="batch_number">Select Batch:</label>
            <select name="batch_number" required>
                <option value="">Select Batch</option>
                <?php
                $batchQuery = "SELECT DISTINCT batch_number FROM project_submissions WHERE branch = ?";
                $stmt = $conn->prepare($batchQuery);
                $stmt->bind_param("s", $coordinatorBranch);
                $stmt->execute();
                $batchResult = $stmt->get_result();
                while ($batch = $batchResult->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($batch['batch_number']) . "'>" . htmlspecialchars($batch['batch_number']) . "</option>";
                }
                $stmt->close();
                ?>
            </select>
            
            <label for="review_type">Select Review Type:</label>
            <select name="review_type" required>
                <option value="">Select Review</option>
                <option value="review1">Review 1</option>
                <option value="review2">Review 2</option>
                <option value="review3">Review 3</option>
                <option value="review4">Review 4</option>
            </select>
            
            <textarea name="review_text" placeholder="Write your review..." required></textarea>
            <button type="submit">Submit Review</button>
        </form>
    </div>
    <footer>
        <p>&copy; Project Tracker Management Tool</p>
    </footer>
</body>
</html>


<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>

<script>
document.getElementById("process-file").addEventListener("click", () => {
    const studentFileInput = document.getElementById("file-input").files[0];
    const guideFileInput = document.getElementById("guide-file-input").files[0];

    if (!studentFileInput) {
        alert("Please upload a student file.");
        return;
    }

    if (!guideFileInput) {
        alert("Please upload a guide allocation file.");
        return;
    }

    const studentReader = new FileReader();
    studentReader.onload = function (e) {
        const studentData = new Uint8Array(e.target.result);
        const studentWorkbook = XLSX.read(studentData, { type: "array" });
        const studentSheet = studentWorkbook.Sheets[studentWorkbook.SheetNames[0]];
        const studentRows = XLSX.utils.sheet_to_json(studentSheet, { header: 1 });

        // Extract student data with Section info
        const students = studentRows.slice(1).map(row => ({
            name: row[0],
            cgpa: parseFloat(row[1]),
            section: row[2]  // Assuming Section is in column 3
        })).filter(s => s.name && !isNaN(s.cgpa) && s.section);

        // Group students by section
        const sectionWiseStudents = {};
        students.forEach(student => {
            if (!sectionWiseStudents[student.section]) {
                sectionWiseStudents[student.section] = [];
            }
            sectionWiseStudents[student.section].push(student);
        });

        let batchCounter = 1; // Track batch numbers across sections
        const allBatches = [];

        // Process each section separately
        Object.keys(sectionWiseStudents).forEach(section => {
            const sortedStudents = sectionWiseStudents[section].sort((a, b) => b.cgpa - a.cgpa);

            const totalStudents = sortedStudents.length;
            const numBatches = Math.max(1, Math.floor(totalStudents / 4));
            const batches = Array.from({ length: numBatches }, () => []);

            let direction = 1;
            let batchIndex = 0;
            sortedStudents.forEach(student => {
                batches[batchIndex].push(student);
                batchIndex += direction;
                if (batchIndex === numBatches || batchIndex === -1) {
                    direction *= -1;
                    batchIndex += direction;
                }
            });

            allBatches.push({ section, batches, numBatches });
        });

        // Process Guide Allocation
        const guideReader = new FileReader();
        guideReader.onload = function (e) {
            const guideData = new Uint8Array(e.target.result);
            const guideWorkbook = XLSX.read(guideData, { type: "array" });
            const guideSheet = guideWorkbook.Sheets[guideWorkbook.SheetNames[0]];
            const guideRows = XLSX.utils.sheet_to_json(guideSheet, { header: 1 });

            const guides = guideRows.slice(1).map(row => row[0]).filter(g => g);
            let guideIndex = 0;
            const numGuides = guides.length;

            const batchDisplay = document.getElementById("batch-display");
            const batchList = document.getElementById("batch-list");
            batchList.innerHTML = "";

            const batchData = [];

            allBatches.forEach(({ section, batches }) => {
                const numBatches = batches.length;

                batches.forEach((batch) => {
                    let guide;

                    if (numGuides >= numBatches) {
                        // If guides are equal to or more than batches, assign one-to-one
                        guide = guides[batchCounter - 1];
                    } else {
                        // If guides are fewer, repeat the guides in a round-robin fashion
                        guide = guides[guideIndex % numGuides];
                        guideIndex++;
                    }

                    const batchStudents = batch.map(s => `${s.name} (${s.cgpa})`).join("<br>");

                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>Batch ${batchCounter}</td>
                        <td>${section}</td>
                        <td>${batchStudents}</td>
                        <td>${guide}</td>
                    `;
                    batchList.appendChild(row);

                    batch.forEach(student => {
                        batchData.push({
                            batch_number: batchCounter,
                            section: section,
                            student_name: student.name,
                            student_cgpa: student.cgpa,
                            guide_name: guide,
                            branch: "<?php echo $coordinatorBranch; ?>",
                        });
                    });

                    batchCounter++;
                });
            });

            batchDisplay.style.display = "block";

            let submitButton = document.getElementById("submit-batch-data");
            if (!submitButton) {
                submitButton = document.createElement("button");
                submitButton.id = "submit-batch-data";
                submitButton.textContent = "Submit Batch Allocations";
                submitButton.style.display = "block";
                submitButton.style.marginTop = "20px";
                submitButton.style.padding = "10px 15px";
                submitButton.style.backgroundColor = "#4CAF50";
                submitButton.style.color = "#fff";
                submitButton.style.border = "none";
                submitButton.style.borderRadius = "5px";
                submitButton.style.cursor = "pointer";

                submitButton.addEventListener("click", () => {
                    fetch("save_batch_allocation.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify(batchData)
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log(data);
                        alert("Batch allocations saved successfully!");
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("Error saving batch allocations.");
                    });
                });

                batchDisplay.appendChild(submitButton);
            }
        };

        guideReader.readAsArrayBuffer(guideFileInput);
    };

    studentReader.readAsArrayBuffer(studentFileInput);
});


</script>
