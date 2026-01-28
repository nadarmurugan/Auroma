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

// Fetch reviews
$sql = "SELECT name, rating, message FROM reviews ORDER BY submitted_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $stars = str_repeat("★", $row["rating"]) . str_repeat("☆", 5 - $row["rating"]);
        echo "<div class='review'>
                <p>\"{$row['message']}\"</p>
                <div class='rating'>{$stars}</div>
                <h4>- {$row['name']}</h4>
              </div>";
    }
} else {
    echo "<p>No reviews yet. Be the first to leave one!</p>";
}

$conn->close();
?>
