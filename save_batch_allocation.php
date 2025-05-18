<?php
include('db_connections.php');

// Get the JSON data sent from the JavaScript fetch API
$batchData = json_decode(file_get_contents('php://input'), true);

if (!empty($batchData)) {
    foreach ($batchData as $batch) {
        $batchNumber = $batch['batch_number'];
        $studentName = $batch['student_name'];
        $studentCgpa = $batch['student_cgpa'];
        $guideName = $batch['guide_name'];
        $branch = $batch['branch'];

        // Insert into the batch_allocations table
        $insertQuery = "INSERT INTO batch_allocations (batch_number, student_name, student_cgpa, guide_name, branch) 
                        VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($insertQuery);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sssss", $batchNumber, $studentName, $studentCgpa, $guideName, $branch);

        if ($stmt->execute()) {
            echo "Batch allocation saved successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
} else {
    echo "No data received.";
}

$conn->close();
?>
