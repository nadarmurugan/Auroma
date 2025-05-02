<?php
$servername = "localhost"; // Change if needed
$username = "root"; // Your DB username
$password = ""; // Your DB password
$dbname = "auroma";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validate input
if (empty($name) || empty($message) || $rating < 1 || $rating > 5) {
    echo "<script>alert('Invalid input data. Please try again.'); window.history.back();</script>";
    exit();
}

// Prepare SQL statement to prevent SQL injection
$stmt = $conn->prepare("INSERT INTO reviews (name, rating, message) VALUES (?, ?, ?)");
$stmt->bind_param("sis", $name, $rating, $message);

// Execute query and show alert
if ($stmt->execute()) {
    echo "<script>
            alert('Review submitted successfully!');
            window.location.href = 'review.html';
          </script>";
} else {
    echo "<script>
            alert('Error submitting review. Please try again.');
            window.history.back();
          </script>";
}

// Close connections
$stmt->close();
$conn->close();
?>
