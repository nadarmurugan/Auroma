<?php
$servername = "localhost";
$username = "root"; // Update if needed
$password = ""; // Update if needed
$dbname = "auroma";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Check if payment data is received
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $payment_method = $_POST["payment_method"];
    $total_price = $_POST["total_price"];
    $date_time = $_POST["date_time"];

    // Validate inputs
    if (empty($payment_method) || empty($total_price) || empty($date_time)) {
        echo json_encode(["status" => "error", "message" => "Invalid input data."]);
        exit;
    }

    // Prepare and execute query
    $stmt = $conn->prepare("INSERT INTO payments (payment_method, total_price, date_time) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $payment_method, $total_price, $date_time);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Payment stored successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to store payment."]);
    }

    $stmt->close();
}

$conn->close();
?>
