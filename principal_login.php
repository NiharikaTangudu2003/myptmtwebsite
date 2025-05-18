<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Principal Login Page</title>
    <style>
        /* Keep the same styling as before */
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

        .logo_name{
            display: flex;
        }
        .logo img{
            height: 60px;
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
                <h2 style="margin-left: 60px;">Principal Login</h2>
            </div>
        </div>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Email" required>
            <input type="password" name="password" placeholder="Faculty ID" required>
            <input type="submit" value="Login">
        </form>
        <?php
        session_start();
        include 'db_connections.php'; // Ensure this file contains your DB connection logic

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Sanitize and validate user inputs
            $username = htmlspecialchars(trim($_POST['username'])); // Email as username
            $password = htmlspecialchars(trim($_POST['password'])); // Faculty ID as password

            // Query to check credentials
            $sql = "SELECT * FROM principals WHERE email = ? AND faculty_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                // Valid credentials
                $row = $result->fetch_assoc();
                $_SESSION['principal_name'] = $row['name'];
                $_SESSION['principal_id'] = $row['faculty_id'];
                $_SESSION['principal_email'] = $row['email'];
                header("Location: principal.php"); // Redirect to the principal dashboard
                exit();
            } else {
                echo "<p class='error-message'>Invalid Email or Faculty ID.</p>";
            }
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
