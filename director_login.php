<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Director Login Page</title>
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
                <h2 style="margin-left: 60px;">Director Login</h2>
            </div>
        </div>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Email" required>
            <input type="password" name="password" placeholder="Director ID" required>
            <input type="submit" value="Login">
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

            session_start();

            // Process form submission
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $email = htmlspecialchars(trim($_POST['username']));
                $director_id = htmlspecialchars(trim($_POST['password']));

                // Check if user exists
                $stmt = $conn->prepare("SELECT * FROM directors WHERE email = ? AND director_id = ?");
                $stmt->bind_param("ss", $email, $director_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // User exists, start session and redirect
                    $director = $result->fetch_assoc();
                    $_SESSION['director_email'] = $director['email']; // Store email in session
                    header("Location: director.php");
                    exit();
                } else {
                    echo "<p class='error-message'>Invalid Email or Director ID.</p>";
                }

                $stmt->close();
            }

            $conn->close();
            ?>
        </form>
    </div>
</body>
</html>
