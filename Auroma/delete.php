<?php
// Database Connection
$host = 'localhost';
$user = 'root'; // Change if needed
$password = ''; // Change if needed
$database = 'auroma';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Get user ID and ensure it's an integer

    // Fetch user details to delete the profile picture if necessary
    $query = "SELECT profile_pic FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Delete the user from the database
        $deleteQuery = "DELETE FROM users WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $id);

        if ($deleteStmt->execute()) {
            // Delete the profile picture if it exists
            if (!empty($user['profile_pic']) && file_exists($user['profile_pic'])) {
                unlink($user['profile_pic']);
            }
            echo "<script>alert('User deleted successfully!'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Error deleting user.'); window.location.href='dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('User not found.'); window.location.href='dashboard.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href='dashboard.php';</script>";
}
?>
