<?php
// Start a PHP session if needed (e.g., for authentication)
// session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project Tracker Management Tool</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" 
  integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" 
  crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    body {
      margin: 0;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: white;
      padding: 10px 20px;
      border-bottom: 2px solid #ddd;
    }

    .logo-container {
      display: flex;
      align-items: center;
      gap: 30px;
      background-color: white;
    }

    .logo-container img {
      max-height: 80px;
      width: auto;
    }

    .dropdown {
      position: relative;
      display: inline-block;
      margin-right: 10px;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      background-color: white;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
      z-index: 1;
      width: 200px;
      margin-top: 10px;
    }

    .dropdown-content a {
      color: black;
      padding: 10px 15px;
      text-decoration: none;
      display: block;
      font-size: 14px;
    }

    .dropdown-content a:hover {
      background-color: #007bff;
      color: white;
    }

    .toggle-btn {
      font-size: 16px;
      text-decoration: none;
      color: #007bff;
      border: 1px solid #007bff;
      padding: 5px 10px;
      border-radius: 4px;
      background-color: white;
      cursor: pointer;
      transition: background-color 0.3s, color 0.3s;
    }

    .toggle-btn:hover {
      background-color: #007bff;
      color: white;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Georgia, 'Times New Roman', Times, serif;
      justify-content: center;
      background-color: #f0f0f0;
    }

    .logo-img {
      display: flex;
      justify-content: space-around;
      align-items: center;
      padding: 20px;
      background-color: white;
    }

    .logo-img img {
      height: 90px;
    }

    .titlename {
      display: flex;
      justify-content: space-around;
      align-items: center;
    }

    .title {
      font-size: 30px;
      color: rgb(93, 157, 186);
      margin-top: -100px;
    }

    footer {
      text-align: center;
      padding: 10px 0;
      bottom: 0;
      width: 100%;
    }

    footer p {
      color: #141313;
      
    }
  </style>
</head>
<body>
  <header class="header">
    <div class="logo-container">
      <img src="./images/aitamlogo.png" alt="AITAM Logo">
      <img src="./images/jntugv.jpeg" alt="Logo 2">
      <img src="./images/naac.jpeg" alt="NAAC Logo">
      <img src="./images/nba.png" alt="NBA Logo">
      <img src="./images/ugc.png" alt="UGC Logo">
      <img src="./images/nirf.jpeg" alt="NIRF Logo">
      <img src="./images/iic.png" alt="IIC Logo">
    </div>
  </header>
  <div class="titlename">
    <div>
      <img src="./images/front.jpg" alt="">
    </div>
    <div class="name1">
      <div class="title">
        <h1><center>Project Tracker Management Tool</center></h1>
        <div class="sign_in_up" style="display: flex; margin-top: 50px;">
          <div class="dropdown" style="margin-right:100px ;">
            <button class="toggle-btn">Sign In</button>
            <div class="dropdown-content">
              <a href="student_login.php">Student</a>
              <a href="guide_login.php">Guide</a>
              <a href="coordinator_login.php">Coordinator</a>
              <a href="hod_login.php">HOD</a>
              <a href="principal_login.php">Principal</a>
              <a href="director_login.php">Director</a>
            </div>
          </div>
          <div class="dropdown">
            <button class="toggle-btn">Sign Up</button>
            <div class="dropdown-content">
              <a href="student_registration.php">Student</a>
              <a href="guide_registration.php">Guide</a>
              <a href="coordinator_registration.php">Coordinator</a>
              <a href="hod_registration.php">HOD</a>
              <a href="principal_registration.php">Principal</a>
              <a href="director_registration.php">Director</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <footer>
    <p>&copy; Project Tracker Management Tool</p>
  </footer>
  <script>
    document.querySelectorAll('.dropdown .toggle-btn').forEach((btn) => {
      btn.addEventListener('click', (e) => {
        const dropdownContent = e.target.nextElementSibling;
        const isVisible = dropdownContent.style.display === 'block';
        document.querySelectorAll('.dropdown-content').forEach((content) => {
          content.style.display = 'none';
        });
        dropdownContent.style.display = isVisible ? 'none' : 'block';
      });
    });

    document.addEventListener('click', (e) => {
      if (!e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown-content').forEach((content) => {
          content.style.display = 'none';
        });
      }
    });
  </script>
</body>
</html>
