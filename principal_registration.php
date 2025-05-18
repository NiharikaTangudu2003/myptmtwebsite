<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Principal Registration</title>
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
            width: 500px;
            height: 400px;
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
        .registration-container input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
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
                <h2>Principal Registration</h2>
            </div>
        </div>
        <?php
        // MySQL Connection
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "register";
        $port = 3307;

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname, $port);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Sanitize and validate the inputs
            $principalName = htmlspecialchars(trim($_POST['faculty-name']));
            $principalId = htmlspecialchars(trim($_POST['faculty-id']));
            $principalEmail = htmlspecialchars(trim($_POST['faculty-email']));

            if (empty($principalName) || empty($principalId) || empty($principalEmail)) {
                echo "<p style='color: red;'>Please fill out all the fields.</p>";
            } elseif (!filter_var($principalEmail, FILTER_VALIDATE_EMAIL)) {
                echo "<p style='color: red;'>Invalid email format.</p>";
            } else {
                // Prepare and bind
                $stmt = $conn->prepare("INSERT INTO principals (name, faculty_id, email) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $principalName, $principalId, $principalEmail);

                // Execute the statement
                if ($stmt->execute()) {
                    // Redirect to principal_login.php after successful registration
                    header("Location: principal_login.php");
                    exit(); // Ensure no further code is executed
                } else {
                    echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
                }

                $stmt->close();
            }
        }

        $conn->close();
        ?>
        <form method="POST" action="">
            <label for="faculty-name">Name:</label>
            <input type="text" id="faculty-name" name="faculty-name" placeholder="Enter Principal Name" required>
            
            <label for="faculty-id">ID:</label>
            <input type="text" id="faculty-id" name="faculty-id" placeholder="Enter Principal ID" required>
            
            <label for="faculty-email">Email:</label>
            <input type="email" id="faculty-email" name="faculty-email" placeholder="Enter Principal Email" required>
            
            <input type="submit" value="Register">
        </form>
    </div>
</body>
</html>
