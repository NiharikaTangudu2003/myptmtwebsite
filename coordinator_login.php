
<?php
// Start the session at the very beginning of the script
session_start();

// Include the database connection
include('db_connections.php'); // Ensure this path is correct

// Initialize error message variable
$error_message = "";



// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the submitted username (email) and password (faculty_id)
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Check if both fields are filled
    if (empty($username) || empty($password)) {
        $error_message = "Please enter both username and password.";
    } else {
        // Query the database to find a coordinator with the given username (email)
        $stmt = $conn->prepare("SELECT * FROM coordinators WHERE faculty_email = ? AND faculty_id = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the username and password match
        if ($result->num_rows > 0) {
            // Fetch the coordinator data
            $coordinator = $result->fetch_assoc();

            // Store the user's data in the session
            $_SESSION['faculty_email'] = $coordinator['faculty_email'];
            $_SESSION['faculty_id'] = $coordinator['faculty_id'];  // You can store more data as needed

            // If successful, redirect to the dashboard or another page
            header("Location: co-ordinator.php"); // Replace with your actual dashboard page
            exit();
        } else {
            $error_message = "Invalid email or password.";
        }

        $stmt->close();
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
    <title>Coordinator Login Page</title>
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
                <h2 style="margin-left: 30px;">Coordinator Login</h2>
            </div>
        </div>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">

            <!-- Display error message if login fails -->
            <?php
            if (!empty($error_message)) {
                echo "<p class='error-message'>$error_message</p>";
            }
            ?>
        </form>
    </div>
</body>
</html>
