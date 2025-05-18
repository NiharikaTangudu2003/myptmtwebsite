<?php
session_start();
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
$error_message = ""; // Variable to store error messages
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password'])); // Assuming password is roll_no for now
    // Basic validation
    if (empty($username) || empty($password)) {
        $error_message = "Please fill out all fields.";
    } else {
        // Prepare SQL query to check login credentials
        $stmt = $conn->prepare("SELECT * FROM student_register WHERE email = ? AND roll_no = ?");
        $stmt->bind_param("ss", $username, $password);
        // Execute the query
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // Successful login
            $student = $result->fetch_assoc();
            $_SESSION['student_id'] = $student['id']; // Store student ID in session
            $_SESSION['student_name'] = $student['student_name']; // Store student name in session
            // Redirect to student dashboard page (student.php)
            header("Location: student.php");
            exit();
        } else {
            // Invalid credentials
            $error_message = "Invalid username or password.";
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
    <title>Student Login Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: white;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            width: 400px;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .login-container input[type="submit"] {
            width: 100%;
            padding: 15px;
            background-color: #007bff;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        .login-container input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .login-container .error-message {
            color: red;
            margin-top: 10px;
            font-size: 14px;
            display: block;
        }

        .logo_name {
            display: flex;
        }

        .logo img {
            height: 60px;
        }

        .logo_name h2 {
            margin-left: 20px;
            align-self: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo_name">
            <div class="logo">
                <img src="./images/aitamlogo.png" alt="AITAM Logo">
            </div>
            <div>
                <h2 style="margin-left: 50px;">Student Login</h2>
            </div>
        </div>
        <form method="POST" action="">
            <input type="text" id="username" name="username" placeholder="Username (Email)" required>
            <input type="password" id="password" name="password" placeholder="Password (Roll Number)" required>
            <input type="submit" value="Login">
            <?php
            if (!empty($error_message)) {
                echo "<p class='error-message'>$error_message</p>";
            }
            ?>
        </form>
    </div>
</body>
</html>
