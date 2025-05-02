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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); // Removed hashing

    // Check if email already exists
    $check_email = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($check_email);
    
    if ($result->num_rows > 0) {
        $message = "Email already exists!";
        $redirect = "signup.html";
        $icon = "error";
    } else {
        // Insert user data into database
        $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
        if ($conn->query($sql) === TRUE) {
            $message = "Registration successful! Redirecting to login...";
            $redirect = "login.html";
            $icon = "success";
        } else {
            $message = "Error: " . $conn->error;
            $redirect = "signup.html";
            $icon = "error";
        }
    }

    // Close the connection before outputting HTML
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
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
