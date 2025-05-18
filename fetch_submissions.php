<?php 
// Include the database connection file
include 'db_connections.php';

// Get the selected branch from the request
$branch = isset($_GET['branch']) ? $_GET['branch'] : '';

// If no branch is selected, return empty response
if (!$branch) {
    echo json_encode([]);
    exit();
}

// Prepare SQL to fetch project submissions based on the selected branch
$query = "SELECT ps.batch_number, ps.title, ps.abstract, ps.literature, ps.presentation, ps.documentation, d.name AS coordinator
          FROM project_submissions ps
          JOIN directors d ON d.director_id = ps.director_id
          WHERE ps.branch = ?"; // Ensure you have a 'branch' column in the project_submissions table

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $branch);
$stmt->execute();
$result = $stmt->get_result();

// Check if results are found
if ($result->num_rows > 0) {
    $projects = [];
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }

    // Respond with project details in JSON format
    echo json_encode(['coordinator' => $projects[0]['coordinator'], 'projects' => $projects]);
} else {
    // No projects found for the selected branch
    echo json_encode([]);
}

exit();
