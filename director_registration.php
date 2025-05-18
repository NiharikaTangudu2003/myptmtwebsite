<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        .registration-container {
            background-color: white;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            width: 500px;
            text-align: center;
        }

        h2 {
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
            text-align: left;
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .logo_name {
            display: flex;
        }

        .logo img {
            height: 60px;
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
                <h2 style="margin-left: 60px;">Director Register</h2>
            </div>
        </div>
        <?php
        // Database connection
        $servername = "localhost";
        $username = "root"; // Replace with your MySQL username
        $password = ""; // Replace with your MySQL password
        $dbname = "register"; // Database name
        $port = 3307; // Custom port

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname, $port);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if the form has been submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $directorName = htmlspecialchars(trim($_POST['faculty-name']));
            $directorId = htmlspecialchars(trim($_POST['faculty-id']));
            $directorEmail = htmlspecialchars(trim($_POST['faculty-email']));

            // Validate fields
            if (empty($directorName) || empty($directorId) || empty($directorEmail)) {
                echo "<p style='color: red;'>Please fill out all the fields.</p>";
            } else {
                // Insert data into the database
                $stmt = $conn->prepare("INSERT INTO directors (name, director_id, email) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $directorName, $directorId, $directorEmail);

                if ($stmt->execute()) {
                    // Redirect to director_login.php after successful registration
                    header("Location: director_login.php");
                    exit();
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
            <input type="text" id="faculty-name" name="faculty-name" placeholder="Enter Director Name" required>
            
            <label for="faculty-id">ID:</label>
            <input type="text" id="faculty-id" name="faculty-id" placeholder="Enter Director ID" required>
            
            <label for="faculty-email">Email:</label>
            <input type="email" id="faculty-email" name="faculty-email" placeholder="Enter Director Email" required>
            
            <input type="submit" value="Register">
        </form>
    </div>
</body>
</html>
