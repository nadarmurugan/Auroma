<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "auroma";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get JSON data
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data["id"])) {
    $id = $data["id"];
    $sql = "DELETE FROM cart WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Item removed from cart"]);
    } else {
        echo json_encode(["message" => "Error: " . $conn->error]);
    }
} else {
    echo json_encode(["message" => "Invalid request"]);
}

$conn->close();
?>
