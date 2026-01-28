<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json'); // Ensure response is JSON

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "auroma";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Get JSON data from request
$data = json_decode(file_get_contents("php://input"), true);

// Validate data
if (!isset($data["id"]) || !is_numeric($data["id"])) {
    echo json_encode(["success" => false, "message" => "Invalid item ID"]);
    exit();
}

$id = intval($data["id"]); // Convert ID to integer to prevent SQL injection

// Prepare and execute delete query
$sql = "DELETE FROM wish WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Item removed successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to remove item: " . $stmt->error]);
}

// Close resources
$stmt->close();
$conn->close();
?>
