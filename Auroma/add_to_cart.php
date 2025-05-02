<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "auroma";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["message" => "Database connection failed: " . $conn->connect_error]));
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data["name"], $data["image"], $data["price"], $data["quantity"])) {
    $name = $conn->real_escape_string($data["name"]);
    $image = $conn->real_escape_string($data["image"]);
    $price = $conn->real_escape_string($data["price"]);
    $quantity = $conn->real_escape_string($data["quantity"]);

    $sql = "INSERT INTO cart (name, image, price, quantity) VALUES ('$name', '$image', '$price', '$quantity')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Product added to cart successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid data received"]);
}

$conn->close();
?>
