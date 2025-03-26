<?php

session_start();
header('Content-Type: application/json'); // Ensure response is JSON

include 'config.php'; // Connect to the database

// Ensure the user is logged in before saving results
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "User not authenticated"]);
    exit();
}


// Read JSON input from JavaScript
$input = json_decode(file_get_contents("php://input"), true);

// Validate received data
if (!$input || !isset($input['score']) || !isset($input['categories']) || !isset($input['time_taken'])) {
    echo json_encode(["success" => false, "error" => "Invalid input"]);
    exit;
}

// Extract values from JSON
$score = intval($input['score']);
$categories = $input['categories'];
$timeTaken = intval($input['time_taken']);

// Database connection (Change these details if needed)
$servername = "localhost"; // Change if your database is hosted elsewhere
$username = "root"; // Change if you use a different DB username
$password = ""; // Change if your database has a password
$dbname = "quiz_database"; // Make sure this matches your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check database connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Prepare and execute query
$stmt = $conn->prepare("INSERT INTO quiz_results (score, categories, time_taken) VALUES (?, ?, ?)");
$stmt->bind_param("isi", $score, $categories, $timeTaken);

if ($stmt->execute()) {
    error_log("✅ Quiz result saved: Score=$score, Categories=$categories, Time Taken=$timeTaken", 0);
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Database error: " . $stmt->error]);
}

// Close database connection
$stmt->close();
$conn->close();
