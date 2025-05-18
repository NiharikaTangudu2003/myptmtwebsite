 <?php
// Database connection
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "register"; // Updated database name
$port = 3307; // Custom port

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate the inputs
    $batchNumber = htmlspecialchars(trim($_POST['batch-number']));
    $studentName = htmlspecialchars(trim($_POST['student-name']));
    $rollNo = htmlspecialchars(trim($_POST['roll-no']));
    $studentEmail = htmlspecialchars(trim($_POST['student-email']));
    $studentContact = htmlspecialchars(trim($_POST['student-contact']));
    $branch = htmlspecialchars(trim($_POST['branch']));

    // Basic validation
    if (empty($batchNumber) || empty($studentName) || empty($rollNo) || empty($studentEmail) || empty($studentContact) || empty($branch)) {
        $message = "Please fill out all the fields.";
    } elseif (!filter_var($studentEmail, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } elseif (!preg_match("/^[0-9]{10}$/", $studentContact)) {
        $message = "Contact number must be 10 digits.";
    } elseif (strlen($rollNo) !== 10) {
        $message = "Roll number must be 10 digits.";
    } else {
        // Insert data into the student_register table
        $stmt = $conn->prepare("INSERT INTO student_register (batch_number, student_name, roll_no, email, contact, branch) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $batchNumber, $studentName, $rollNo, $studentEmail, $studentContact, $branch);

        if ($stmt->execute()) {
            // Redirect to index.php after successful registration
            header("Location: student_login.php");
            exit(); // Ensure no further code is executed
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden; /* Prevent scrolling */
        }

        .registration-container {
            background-color: white;
            padding: 15px; /* Reduced padding */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            width: 450px; /* Adjusted width */
            text-align: center;
        }

        .registration-container h2 {
            margin-bottom: 10px; /* Adjusted margin */
            color: #333;
        }

        .registration-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
            text-align: left;
        }

        .registration-container input[type="text"],
        .registration-container input[type="email"],
        .registration-container input[type="number"],
        .registration-container input[type="tel"],
        .registration-container select {
            width: 100%;
            padding: 6px; /* Reduced padding */
            margin: 6px 0 10px; /* Adjusted margins */
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .registration-container input[type="submit"] {
            width: 100%;
            padding: 8px; /* Reduced padding */
            background-color: #007bff;
            border: none;
            color: white;
            font-size: 14px; /* Reduced font size */
            cursor: pointer;
            border-radius: 5px;
        }

        .registration-container input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .logo {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 10px; /* Adjusted margin */
        }

        .logo img {
            height: 50px; /* Reduced logo size */
            margin-right: 30px;
        }

        .logo_name {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        h2 {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <div class="logo_name">
            <div class="logo">
                <img src="./images/aitamlogo.png" alt="AITAM Logo">
            </div>
            <div>
                <h2>Student Registration</h2>
            </div>
        </div>

        <?php if (!empty($message)): ?>
        <script>
            alert("<?php echo $message; ?>");
        </script>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="batch-number">Batch Number:</label>
            <input type="text" id="batch-number" name="batch-number" placeholder="Enter Batch Number" maxlength="10" required>

            <label for="student-name">Name:</label>
            <input type="text" id="student-name" name="student-name" placeholder="Enter Student Name" required>

            <label for="roll-no">Roll No:</label>
            <input type="text" id="roll-no" name="roll-no" placeholder="Enter Roll Number" minlength="10" maxlength="10" required>

            <label for="student-email">Email:</label>
            <input type="email" id="student-email" name="student-email" placeholder="Enter Student Email" required>

            <label for="student-contact">Contact:</label>
            <input type="tel" id="student-contact" maxlength="10" minlength="10" name="student-contact" placeholder="Enter Contact Number" pattern="[0-9]{10}" required>

            <label for="branch">Branch:</label>
            <select id="branch" name="branch" required>
                <option value="" disabled selected>Select Branch</option>
                <option value="CSE">CSE</option>
                <option value="IT">IT</option>
                <option value="ECE">ECE</option>
                <option value="CSM">CSM</option>
                <option value="EEE">EEE</option>
                <option value="CIVIL">CIVIL</option>
                <option value="MECH">MECH</option>
                <option value="CSD">CSD</option>
                <option value="OTHERS">OTHERS</option>
            </select>

            <input type="submit" value="Register">
        </form>
    </div>
</body>
</html>
