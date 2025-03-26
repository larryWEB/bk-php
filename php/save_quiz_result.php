<?php
session_start();
include 'config.php'; // Connect to the database

// Ensure the user is logged in before saving results
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "User not authenticated"]);
    exit();
}

// If the quiz form is submitted, save results
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id']; // Get logged-in user ID
    $score = $_POST['score']; // Get quiz score
    $categories = $_POST['categories']; // Get quiz categories
    $time_taken = $_POST['time_taken']; // Get time taken to complete quiz
    $timestamp = date('Y-m-d H:i:s'); // Store the current date and time

    // Insert quiz result into database
    $stmt = $conn->prepare("INSERT INTO quiz_results (user_id, score, categories, time_taken, timestamp) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $score, $categories, $time_taken, $timestamp);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }
    $stmt->close();
    $conn->close();
}
?>
