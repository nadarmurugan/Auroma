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
    $id = $_GET['id'];
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $profile_pic = $user['profile_pic'];

    // Handle file upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
        move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file);
        $profile_pic = $target_file;
    }

    // Update user details
    $update_query = "UPDATE users SET name=?, email=?, password=?, profile_pic=? WHERE id=?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssi", $name, $email, $password, $profile_pic, $id);

    if ($stmt->execute()) {
        header("Location: dashboard.php?success=User updated successfully");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="font-family: Arial, sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh;">

<div style="background: #f8f9fa; padding: 25px; border-radius: 10px; width: 450px; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1); font-family: Arial, sans-serif;">
    <h2 style="text-align: center; color: #343a40; margin-bottom: 20px;">Edit User</h2>
    
    <form action="edit.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">

        <label style="font-weight: bold; color: #495057;">Name:</label>
        <input type="text" name="name" value="<?php echo $user['name']; ?>" required 
            style="width: 100%; padding: 12px; margin-bottom: 12px; border: 1px solid #ced4da; border-radius: 6px; font-size: 16px;">

        <label style="font-weight: bold; color: #495057;">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required 
            style="width: 100%; padding: 12px; margin-bottom: 5px; border: 1px solid #ced4da; border-radius: 6px; font-size: 16px;">
        <small id="emailError" style="color: red; display: none; font-size: 14px;">Invalid email format</small>

        <label style="font-weight: bold; color: #495057;">Password:</label>
        <div style="position: relative;">
            <input type="password" id="password" name="password" value="<?php echo $user['password']; ?>" required 
                style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 6px; font-size: 16px;">
            <i id="togglePassword" class="fas fa-eye" 
                style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6c757d;"></i>
        </div>
        <small id="passwordError" style="color: red; display: none; font-size: 14px;">Password must contain at least 8 characters, 1 uppercase, 1 lowercase, 1 number, and 1 special character.</small>

        <label style="font-weight: bold; color: #495057;">Profile Picture:</label>
        <input type="file" name="profile_pic" 
            style="width: 100%; padding: 12px; margin-bottom: 12px; border: 1px solid #ced4da; border-radius: 6px;">
        <img src="<?php echo $user['profile_pic']; ?>" width="120" alt="Profile Picture" 
            style="display: block; margin: 10px auto; border-radius: 8px; border: 1px solid #dee2e6;">

        <button type="submit" 
            style="width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 6px; font-size: 18px; font-weight: bold; cursor: pointer; transition: 0.3s;">
            Update
        </button>

        <a href="dashboard.php" 
            style="display: block; text-align: center; margin-top: 15px; color: #007bff; text-decoration: none; font-size: 16px; font-weight: bold;">
            Back to Dashboard
        </a>
    </form>
</div>

    <script>
        // Show/Hide Password
        document.getElementById("togglePassword").addEventListener("click", function () {
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

        // Email Validation
        function validateEmail(email) {
            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            return emailPattern.test(email);
        }

        document.getElementById("email").addEventListener("input", function () {
            const emailError = document.getElementById("emailError");
            if (!validateEmail(this.value)) {
                emailError.style.display = "block";
            } else {
                emailError.style.display = "none";
            }
        });

        // Password Validation
        function validatePassword(password) {
            const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            return passwordPattern.test(password);
        }

        document.getElementById("password").addEventListener("input", function () {
            const passwordError = document.getElementById("passwordError");
            if (!validatePassword(this.value)) {
                passwordError.style.display = "block";
            } else {
                passwordError.style.display = "none";
            }
        });

        // Prevent Form Submission if Validation Fails
        document.querySelector("form").addEventListener("submit", function (event) {
            if (!validateEmail(document.getElementById("email").value)) {
                document.getElementById("emailError").style.display = "block";
                event.preventDefault();
            }
            if (!validatePassword(document.getElementById("password").value)) {
                document.getElementById("passwordError").style.display = "block";
                event.preventDefault();
            }
        });
    </script>

</body>
</html>
