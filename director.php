<?php 
// Include the database connection file
include 'db_connections.php';  

// Start the session to get director details
session_start();  

// Check if the director is logged in (session exists)
if (!isset($_SESSION['director_email'])) {     
    header("Location: director_login.php");     
    exit(); 
}  

// Retrieve director details from the session
$directorEmail = $_SESSION['director_email'];  

// Fetch director details from the database
$stmt = $conn->prepare("SELECT * FROM directors WHERE email = ?");
$stmt->bind_param("s", $directorEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {     
    $director = $result->fetch_assoc();     
    $directorName = $director['name'];     
    $directorId = $director['director_id']; 
} else {     
    echo "Director not found!";     
    exit(); 
}
?>
<!DOCTYPE html>
<html lang="en"> 
<head>     
    <meta charset="UTF-8">     
    <meta name="viewport" content="width=device-width, initial-scale=1.0">     
    <title>Director Dashboard</title>     
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(to right, #ffffff, #d4d9f5);
            padding: 10px 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header img {
            height: 90px;
        }
        .content {
            padding: 20px;
        }
        .header span {
            font-size: 1.2em;
            font-weight: bold;
        }
        .header_details {
            text-align: right;
            margin-right: 20px;
            color: #000;
            font-weight: bold;
        }
        .header_details p{
            margin: 5px 0;
        }
        .section {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }
                .filter-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .filter-container input {
            padding: 5px;
            border-radius: 5px;
            font-size: 14px;
            width: 100%;
            max-width: 300px;
        }
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            bottom: 0;
            width: 100%;
        }
    </style>
</head> 
<body>     
    <div class="header">         
        <img src="./images/aitamlogo.png" alt="Institution Logo">         
        <span><b>Director Portal</b></span>         
        <div class="header_details">             
            <strong>Director Details:</strong><br>             
            <p>Name: <?php echo htmlspecialchars($directorName); ?></p>             
            <p>ID: <?php echo htmlspecialchars($directorId); ?></p>             
            <p>Email: <?php echo htmlspecialchars($directorEmail); ?></p>  <br>           
            <a href="index.php" style="text-decoration: none; color: white; background: red; padding: 5px 10px; border-radius: 5px;">LOGOUT</a>         
        </div>     
    </div>      

    <div class="content">         
        <div class="section">
            <h2>Project Submissions</h2>
            
            <!-- Search Section -->
           <!-- Search Section -->
           <table class="filter-container" style="width: 100%; border-spacing: 10px;">
    <tr>
        <td>
            <label for="branchSearch">Search by Branch:</label>
        </td>
        <td>
            <input type="text" id="branchSearch" placeholder="Search Branch" style="width: 100%; padding: 5px;">
        </td>
    </tr>
    <tr style="height: 10px;"></tr> <!-- This row adds vertical spacing -->
    <tr>
        <td>
            <label for="submissionTypeSearch">Search by Submission Type:</label>
        </td>
        <td>
            <input type="text" id="submissionTypeSearch" placeholder="Search Submission Type" style="width: 100%; padding: 5px;">
        </td>
    </tr>
    <tr style="height: 10px;"></tr> 
</table>



            
            <table id="projectTable" class="display">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>Submission Type</th>
                        <th>File Name</th>
                        <th>Batch Number</th>
                        <th>Branch</th>
                    </tr>
                </thead>
                <tbody>
    <?php 
    // Fetch project submissions from the database
    $query = "SELECT * FROM project_submissions";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['submission_type']) . "</td>";

            // Check if submission type is 'project_title' (display as text without download)
            if (strtolower($row['submission_type']) === 'project_title') {
                echo "<td>" . htmlspecialchars($row['file_name']) . "</td>";
            } else {
                // Other file types trigger download on click
                echo "<td><a href='uploads/" . htmlspecialchars($row['file_name']) . "' download style='text-decoration: none; color: inherit;'>" . htmlspecialchars($row['file_name']) . "</a></td>";
            }

            echo "<td>" . htmlspecialchars($row['batch_number']) . "</td>";
            echo "<td>" . htmlspecialchars($row['branch']) . "</td>"; 
            echo "</tr>";
        }
    }
    ?>
</tbody>


            </table>
        </div>     
    </div>      

    <!-- Include jQuery and DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#projectTable').DataTable({
            "pageLength": 10,
            "stateSave": true,
            "ordering": true,
            "columnDefs": [
                {
                    // Disable sorting for all columns except the Branch column
                    "targets": "_all",
                    "orderable": false
                },
                {
                    // Enable sorting only for the Branch column
                    "targets": 5,
                    "orderable": true
                }
            ]
        });

        // Search by Branch
        $('#branchSearch').on('keyup', function() {
            table.columns(5).search(this.value).draw(); // Search only in the Branch column
        });

        // Search by Submission Type
        $('#submissionTypeSearch').on('keyup', function() {
            table.columns(2).search(this.value).draw(); // Search only in the Submission Type column
        });
    });
    </script>
    <footer>
        <p>&copy; Project Tracker Management Tool</p>
    </footer>
</body> 
</html>
