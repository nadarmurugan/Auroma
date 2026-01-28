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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Image Upload Handling
    $target_dir = "uploads/";
    $profile_pic = "";
    if (!empty($_FILES["profile_pic"]["name"])) {
        $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                $profile_pic = $target_file;
            } else {
                die("Error uploading file.");
            }
        } else {
            die("Only JPG, JPEG, PNG & GIF files are allowed.");
        }
    }
    
    // Server-side validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }
    
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $_POST['password'])) {
        die("Password must be at least 8 characters, contain an uppercase letter, a lowercase letter, a number, and a special character.");
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO users (name, email, profile_pic, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $profile_pic, $password);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
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
    <title>Add User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <style>
body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    font-family: "Poppins", Arial, sans-serif;
    background: linear-gradient(135deg, #eef2f3, #d7dde8);
    color: #333;
    margin: 0;
}

.container {
    background: #fff;
    padding: 35px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
    width: 420px;
    transition: 0.3s ease-in-out;
    text-align: center;
}

.container:hover {
    box-shadow: 0 14px 35px rgba(0, 0, 0, 0.18);
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #222;
    font-size: 26px;
    font-weight: 700;
}

label {
    font-weight: 600;
    display: block;
    margin-bottom: 6px;
    color: #555;
    text-align: left;
}

.input-group {
    margin-bottom: 18px;
    text-align: left;
}

input[type="text"],
input[type="email"],
input[type="password"],
input[type="file"] {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    outline: none;
    font-size: 15px;
    transition: 0.3s ease;
    background: #f9f9f9;
    box-sizing: border-box;
}

input:focus {
    border-color: #28a745;
    box-shadow: 0 0 8px rgba(40, 167, 69, 0.3);
    background: #fff;
}

.password-container {
    position: relative;
}

.toggle-password {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #777;
    font-size: 18px;
    transition: 0.3s ease;
}

.toggle-password:hover {
    color: #333;
}

.info {
    font-size: 13px;
    color: #777;
    margin-top: 5px;
    text-align: left;
}

/* Button Container */
.button-group {
    display: flex;
    justify-content: space-between;
    gap: 120px;
    margin-top: 20px;
}

.button,
.back-button {
    flex: 1;
    padding: 14px;
    border: none;
    border-radius: 8px;
    color: white;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    text-align: center;
    transition: 0.3s ease;
}

/* Primary Button */
.button {
    background: linear-gradient(135deg, #28a745, #218838);
}

.button:hover {
    background: linear-gradient(135deg, #218838, #1e7e34);
    transform: translateY(-2px);
}

/* Secondary Button */
.back-button {
    background: linear-gradient(135deg, #6c757d, #5a6268);
    text-decoration: none;
}

.back-button:hover {
    background: linear-gradient(135deg, #5a6268, #495057);
    transform: translateY(-2px);
}

    </style>
</head>
<body>
    <div class="container">
        <h2>Add User</h2>
        <form id="addUserForm" method="POST" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <span class="info">Example: user@example.com</span>
            
            <label for="profile_pic">Profile Picture:</label>
            <input type="file" id="profile_pic" name="profile_pic" accept="image/*">
            
            <label for="password">Password:</label>
            <div class="password-container">
                <input type="password" id="password" name="password" required>
                <i class="fa fa-eye toggle-password" id="togglePassword"></i>
            </div>
            <span class="info">Must contain at least 8 characters, one uppercase, one lowercase, one number, and one special character.</span>
            
            <button type="submit" class="button">Add User</button>
            <a href="dashboard.php" class="back-button">Back</a>
        </form>
    </div>

    <script>
        document.getElementById("togglePassword").addEventListener("click", function() {
            const passwordField = document.getElementById("password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                this.classList.remove("fa-eye");
                this.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                this.classList.remove("fa-eye-slash");
                this.classList.add("fa-eye");
            }
        });
    </script>
</body>
</html>
