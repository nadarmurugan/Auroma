<?php
$servername = "localhost";
$username = "root"; // Change this if you have a different database username
$password = ""; // Add your database password if applicable
$dbname = "auroma";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $message = trim($_POST["message"]);

    // Validate fields
    if (!empty($name) && !empty($email) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            echo "<script>alert('Message sent successfully!'); window.location.href='contact.html';</script>";
        } else {
            echo "<script>alert('Error sending message. Try again!');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('All fields are required!');</script>";
    }
}

$conn->close();
?>
