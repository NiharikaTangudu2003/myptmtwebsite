<?php
// Include the database connection
include('db_connections.php'); // Adjust the path to where your db_connections.php file is

// Ensure the 'branch' column exists in the database table
$addColumnQuery = "ALTER TABLE `coordinators` ADD COLUMN IF NOT EXISTS `branch` VARCHAR(255)";
$conn->query($addColumnQuery);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $facultyName = htmlspecialchars(trim($_POST['faculty-name']));
    $facultyId = htmlspecialchars(trim($_POST['faculty-id']));
    $facultyEmail = htmlspecialchars(trim($_POST['faculty-email']));
    $branch = htmlspecialchars(trim($_POST['branch']));

    if (empty($facultyName) || empty($facultyId) || empty($facultyEmail) || empty($branch)) {
        $errorMessage = "Please fill out all the fields.";
    } else {
        // Prepare and execute the INSERT query
        $stmt = $conn->prepare("INSERT INTO coordinators (faculty_name, faculty_id, faculty_email, branch) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $facultyName, $facultyId, $facultyEmail, $branch);

        if ($stmt->execute()) {
            // Redirect to coordinator_login.php after successful registration
            header("Location: coordinator_login.php");
            exit(); // Ensure no further code is executed
        } else {
            $errorMessage = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coordinator Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        .registration-container {
            background-color: white;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            width: 500px;
            height: 450px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .registration-container h2 {
            margin-bottom: 20px;
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
        .registration-container select {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
            background-color: #fff;
            color: #333;
        }

        .registration-container input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            border: none;
            color: white;
            font-size: 16px;
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
            margin-bottom: 20px;
        }

        .logo img {
            height: 60px;
            margin-right: 10px;
        }

        .logo_name {
            display: flex;
        }

        h2 {
            margin-left: 50px;
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
                <h2>Coordinator Registration</h2>
            </div>
        </div>
        
        <?php
        // Display error or success message
        if (isset($errorMessage)) {
            echo "<p style='color: red;'>$errorMessage</p>";
        }

        if (isset($successMessage)) {
            echo "<p style='color: green;'>$successMessage</p>";
            echo "<p><strong>Name:</strong> " . $facultyDetails['Name'] . "</p>";
            echo "<p><strong>ID:</strong> " . $facultyDetails['ID'] . "</p>";
            echo "<p><strong>Email:</strong> " . $facultyDetails['Email'] . "</p>";
            echo "<p><strong>Branch:</strong> " . $facultyDetails['Branch'] . "</p>";
        }
        ?>

        <!-- Registration Form -->
        <form method="POST" action="">
            <label for="faculty-name">Name:</label>
            <input type="text" id="faculty-name" name="faculty-name" placeholder="Enter Coordinator Name" required>
            
            <label for="faculty-id">ID:</label>
            <input type="text" id="faculty-id" name="faculty-id" placeholder="Enter Coordinator ID" required>
            
            <label for="faculty-email">Email:</label>
            <input type="email" id="faculty-email" name="faculty-email" placeholder="Enter Coordinator Email" required>
            
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
