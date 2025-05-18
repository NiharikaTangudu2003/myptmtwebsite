<?php
// Include the database connection file
include('db_connections.php');

// Initialize the error message variable
$error_message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize the user inputs
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Validate inputs (ensure no empty fields)
    if (empty($email) || empty($password)) {
        $error_message = "Please enter both email and password.";
    } else {
        // Query the guides table using email
        $stmt = $conn->prepare("SELECT * FROM guides WHERE email = ?");
        $stmt->bind_param("s", $email);

        // Execute the query
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch the guide data
            $guide = $result->fetch_assoc();

            // Check if the password matches the guide_id
            if ($password == $guide['guide_id']) {
                // Login successful, start a session and redirect
                session_start();
                $_SESSION['guide_id'] = $guide['guide_id'];
                $_SESSION['guide_name'] = $guide['name'];
                $_SESSION['guide_email'] = $guide['email'];

                // Redirect to the dashboard or homepage
                header("Location: guide.php");
                exit();
            } else {
                $error_message = "Invalid password.";
            }
        } else {
            $error_message = "No guide found with this email.";
        }

        // Close the statement
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
    <title>Guide Login Page</title>
    <style>

        /* CSS styling */
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

        .login-container input[type="email"],
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

        .error-message {
            color: red;
            margin-top: 10px;
        }

        .logo img {
            height: 60px;
        }

        .logo_name h2 {
            margin-left: 20px;
            align-self: center;
        }
        .logo_name {
            display: flex;
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
                <h2 style="margin-left: 60px;">Guide Login</h2>
            </div>
        </div>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password (Guide ID)" required>
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
