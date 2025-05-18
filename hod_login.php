<?php
// Include database connection file (replace 'db_connections.php' with your actual file path)
include 'db_connections.php';

session_start(); // Start a session to manage user login status

$error_message = ''; // Variable to store error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and trim the inputs
    $username = htmlspecialchars(trim($_POST['username'])); // Email (username)
    $password = htmlspecialchars(trim($_POST['password'])); // Faculty ID (password)

    // Check if fields are empty
    if (empty($username) || empty($password)) {
        $error_message = "Please enter both email and password.";
    } else {
        // Prepare SQL query to check the user credentials
        $sql = "SELECT * FROM hod WHERE email = ? AND faculty_id = ?";
        
        // Prepare the statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters (s = string, s = string for email and faculty_id)
            $stmt->bind_param("ss", $username, $password);

            // Execute the query
            $stmt->execute();

            // Get the result
            $result = $stmt->get_result();

            // Check if a user exists with the provided credentials
            if ($result->num_rows > 0) {
                // User found, start session and redirect to the dashboard
                $_SESSION['username'] = $username; // Store username in session (optional)
                header("Location: hod.php"); // Redirect to the dashboard
                exit();
            } else {
                // Credentials are incorrect
                $error_message = "Invalid email or password.";
            }

            // Close the statement
            $stmt->close();
        } else {
            // Error preparing the SQL statement
            $error_message = "Database error, please try again later.";
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
    <title>HOD Login Page</title>
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

        .login-container .role-selection {
            margin: 20px 0;
        }

        .login-container .role-selection select {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .login-container p {
            margin: 20px 0 0;
            color: #666;
        }

        .logo_name {
            display: flex;
        }

        .logo img {
            height: 60px;
        }

        .error-message {
            color: red;
            margin-top: 10px;
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
                <h2 style="margin-left: 70px;">HOD Login</h2>
            </div>
        </div>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Email" required>
            <input type="password" name="password" placeholder="Faculty ID" required>
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
