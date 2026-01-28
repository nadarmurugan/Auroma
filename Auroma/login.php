<?php
session_start(); // Start session to store login info

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Check if the user exists
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Direct password comparison (no hashing)
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            
            $message = "Login successful! Redirecting to Home...";
            $redirect = "home.html";
            $icon = "success";
        } else {
            $message = "Incorrect password!";
            $redirect = "login.html";
            $icon = "error";
        }
    } else {
        $message = "No account found with this email!";
        $redirect = "login.html";
        $icon = "error";
    }
    
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
    Swal.fire({
        icon: '<?php echo $icon; ?>',
        title: '<?php echo $message; ?>',
        timer: 3000,
        showConfirmButton: false
    }).then(() => {
        window.location.href = '<?php echo $redirect; ?>';
    });
</script>

</body>
</html>
