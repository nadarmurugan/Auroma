<?php
session_start(); // Start session

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "auroma";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access. Please <a href='login.php'>login</a> first.");
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$sql = "SELECT name, email, profile_pic FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Update user details when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = htmlspecialchars($_POST["name"]);
    $new_email = htmlspecialchars($_POST["email"]);
    $profile_pic = $user['profile_pic']; // Keep old profile picture if no new upload

    // Handle file upload
    if (!empty($_FILES["profile_pic"]["name"])) {
        $target_dir = "uploads/"; // Folder to store images
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Create folder if not exists
        }
        $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_extensions = ["jpg", "jpeg", "png", "gif"];

        // Validate file type
        if (!in_array($imageFileType, $allowed_extensions)) {
            echo "<script>alert('Only JPG, JPEG, PNG & GIF files are allowed.');</script>";
            exit();
        }

        // Move uploaded file
        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            $profile_pic = $target_file; // Update profile picture path
        } else {
            echo "<script>alert('Error uploading file.');</script>";
            exit();
        }
    }

    // Update user details in database
    $sql = "UPDATE users SET name = ?, email = ?, profile_pic = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $new_name, $new_email, $profile_pic, $user_id);
    
    if ($stmt->execute()) {
        echo "<script>
                alert('Profile updated successfully!');
                window.location.href = 'profile.php';
              </script>";
    } else {
        echo "<script>alert('Error updating profile: " . $conn->error . "');</script>";
    }
    
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css"> <!-- Link to CSS -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- Font Awesome -->
</head>
<style>
    /* Import Google Font */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #74ebd5, #ACB6E5);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.profile-container {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    width: 350px;
    text-align: center;
}

h2 {
    color: #333;
    margin-bottom: 20px;
}

.input-group {
    margin-bottom: 15px;
    text-align: left;
}

.input-group label {
    font-weight: 600;
    display: block;
    margin-bottom: 5px;
}

.input-group input {
    width: 100%;
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #ccc;
    transition: 0.3s;
}

.input-group input:focus {
    border-color: #74ebd5;
    outline: none;
}

.profile-img {
    border-radius: 50%;
    margin-bottom: 10px;
}

.file-input {
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.update-btn {
    background: #74ebd5;
    color: white;
    border: none;
    padding: 10px;
    width: 100%;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    transition: 0.3s;
}

.update-btn:hover {
    background: #57c7b7;
}

.back-btn {
    display: inline-block;
    margin-top: 10px;
    text-decoration: none;
    color: #333;
    font-weight: bold;
}

.back-btn i {
    margin-right: 5px;
}

.back-btn:hover {
    color: #555;
}

</style>
<body>

<div class="profile-container">
    <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>

    <form action="editprofile.php" method="post" enctype="multipart/form-data">
        
        <div class="input-group">
            <label><i class="fas fa-user"></i> Name:</label>
            <input type="text" name="name" value="<?php echo $user['name']; ?>" required>
        </div>

        <div class="input-group">
            <label><i class="fas fa-envelope"></i> Email:</label>
            <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
        </div>

        <div class="input-group profile-pic">
            <label><i class="fas fa-camera"></i> Profile Picture:</label><br>
            <img src="<?php echo !empty($user['profile_pic']) ? $user['profile_pic'] : 'uploads/default.jpg'; ?>" 
                 width="100" height="100" class="profile-img" alt="Profile Picture"><br>
            <input type="file" name="profile_pic" class="file-input">
        </div>

        <button type="submit" class="update-btn"><i class="fas fa-save"></i> Update Profile</button>
    </form>

    <a href="profile.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Profile</a>
</div>

</body>
</html>
