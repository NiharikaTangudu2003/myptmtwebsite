<?php
// Include database connection file (replace 'db_connections.php' with your actual file path)
include 'db_connections.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate the inputs
    $hodName = htmlspecialchars(trim($_POST['faculty-name']));
    $hodId = htmlspecialchars(trim($_POST['faculty-id']));
    $hodEmail = htmlspecialchars(trim($_POST['faculty-email']));
    $branch = htmlspecialchars(trim($_POST['branch']));

    if (empty($hodName) || empty($hodId) || empty($hodEmail) || empty($branch)) {
        echo "<p style='color: red;'>Please fill out all the fields.</p>";
    } elseif (!filter_var($hodEmail, FILTER_VALIDATE_EMAIL)) {
        echo "<p style='color: red;'>Invalid email format.</p>";
    } else {
        // Prepare the SQL query to insert the data into the table
        $sql = "INSERT INTO hod (name, faculty_id, email, branch) VALUES (?, ?, ?, ?)";
        
        // Prepare the statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters (s = string)
            $stmt->bind_param("ssss", $hodName, $hodId, $hodEmail, $branch);

            // Execute the query
            if ($stmt->execute()) {
                // Redirect to hod_login.php after successful registration
                header("Location: hod_login.php");
                exit(); // Ensure no further code is executed
            } else {
                echo "<p style='color: red;'>Error: Could not register HOD. Please try again later.</p>";
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "<p style='color: red;'>Error: Could not prepare the SQL statement.</p>";
        }
    }
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOD Registration</title>
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
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            width: 500px; /* Expanded width */
            height: 450px; /* Adjusted height */
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
                <h2>HOD Registration</h2>
            </div>
        </div>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Sanitize and validate the inputs
            $hodName = htmlspecialchars(trim($_POST['faculty-name']));
            $hodId = htmlspecialchars(trim($_POST['faculty-id']));
            $hodEmail = htmlspecialchars(trim($_POST['faculty-email']));
            $branch = htmlspecialchars(trim($_POST['branch']));

            if (empty($hodName) || empty($hodId) || empty($hodEmail) || empty($branch)) {
                echo "<p style='color: red;'>Please fill out all the fields.</p>";
            } elseif (!filter_var($hodEmail, FILTER_VALIDATE_EMAIL)) {
                echo "<p style='color: red;'>Invalid email format.</p>";
            } else {
                echo "<p style='color: green;'>HOD Registration Successful!</p>";
                echo "<p><strong>Name:</strong> $hodName</p>";
                echo "<p><strong>ID:</strong> $hodId</p>";
                echo "<p><strong>Email:</strong> $hodEmail</p>";
                echo "<p><strong>Branch:</strong> $branch</p>";

                // Uncomment the following lines to redirect after successful registration
                // header("Location: ../index.html");
                // exit();
            }
        }
        ?>
        <form method="POST" action="">
            <label for="faculty-name">Name:</label>
            <input type="text" id="faculty-name" name="faculty-name" placeholder="Enter HOD Name" required>
            
            <label for="faculty-id">ID:</label>
            <input type="text" id="faculty-id" name="faculty-id" placeholder="Enter HOD ID" required>
            
            <label for="faculty-email">Email:</label>
            <input type="email" id="faculty-email" name="faculty-email" placeholder="Enter HOD Email" required>
            
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
