<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "auroma";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]));
}

// Get JSON data from request
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data["name"], $data["image"], $data["price"])) {
    $name = $conn->real_escape_string($data["name"]);
    $image = $conn->real_escape_string($data["image"]);
    $price = $conn->real_escape_string($data["price"]);

    $sql = "INSERT INTO wish (name, image, price) VALUES ('$name', '$image', '$price')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Product added to Wishlist!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid data received"]);
}

$conn->close();
?>
