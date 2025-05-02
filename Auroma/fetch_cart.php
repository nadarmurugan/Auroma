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

// Fetch all cart items
$sql = "SELECT * FROM cart";
$result = $conn->query($sql);

$cart_items = [];
$total_price = 0; // Initialize total price

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Remove ₹ symbol and commas, then convert to float
        $clean_price = floatval(str_replace([',', '₹'], '', $row['price']));

        $total_price += $clean_price; // Add each item's price directly

        $row['price'] = $clean_price; // Store clean price in response
        $cart_items[] = $row;
    }
}

// Add total price to response
$response = [
    "items" => $cart_items,
    "total_price" => $total_price
];

echo json_encode($response);

$conn->close();
?>
