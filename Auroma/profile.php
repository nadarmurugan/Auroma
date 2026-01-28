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

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access. Please <a href='login.php'>login</a> first.");
}

$user_id = $_SESSION['user_id']; 

// Fetch user details
$sql = "SELECT id, name, email, profile_pic, created_at FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id = $row["id"];
    $name = htmlspecialchars($row["name"]);
    $email = htmlspecialchars($row["email"]);
    $profile_pic = !empty($row["profile_pic"]) ? $row["profile_pic"] : "uploads/default.jpg";
    $member_since = date("F Y", strtotime($row["created_at"]));
} else {
    die("User not found.");
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
       body {
            font-family: 'Dancing Script', cursive;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
            color: black;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }


  

 
 /* Header Styles */
.header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(0, 0, 0, 0.9);
    padding: 15px 20px;
    color: white;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    position: fixed; /* Fix the header at the top */
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
}

.logo {
    font-size: 32px;
    font-weight: bold;
    color: #fffefe;
    letter-spacing: 2px;
    display: flex;
    align-items: center;
}

.logo i {
    margin-right: 10px;
    color: #b76e79;
}

/* Navigation Icons */
.nav-icons {
    display: flex;
    align-items: center;
    gap: 15px;
}

.nav-icon {
    color: white;
    font-size: 20px;
    text-decoration: none;
    transition: transform 0.3s, color 0.3s;
}

.nav-icon:hover {
    transform: scale(1.2);
    color: #f7f1e3;
}

/* Back Button */
.back-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    font-size: 16px;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    display: flex;
    align-items: center;
    transition: background 0.3s, transform 0.2s;
}

.back-btn i {
    margin-right: 5px;
}

.back-btn:hover {
    background: rgba(255, 255, 255, 0.4);
    transform: scale(1.1);
}

        .profile-container {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .profile-container h2 {
            color: #e63946;
            font-size: 26px;
        }
        .profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid #e63946;
        }
        .profile-info p {
            font-size: 16px;
            margin: 8px 0;
            color: #555;
        }
        .profile-info p span {
            font-weight: bold;
            color: #333;
        }
        .btn {
            background: #e63946;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 15px;
            transition: 0.3s;
        }
        .btn:hover {
            background: #d62828;
        }

        .edit-btn, .logout-btn {
    display: block;
    background: #ff758c;
    color: white;
    padding: 10px;
    margin: 10px 0;
    text-decoration: none;
    border-radius: 5px;
    transition: 0.3s;
}

.edit-btn:hover, .logout-btn:hover {
    background: #ff5c7a;
}
</style>
<body>
<header class="header">
        <div class="logo">
            <i class="fas fa-spray-can"></i> Auroma
        </div>
    
        <nav class="nav-icons">
            <button class="back-btn" onclick="goBack()">
                <i class="fas fa-arrow-left"></i> Back
            </button>
    
            <a href="home.html" class="nav-icon" title="Home">
                <i class="fas fa-home"></i>
            </a>
            <a href="products.html" class="nav-icon" title="Products">
                <i class="fas fa-box-open"></i>
            </a>
            <a href="cart.html" class="nav-icon" title="Cart">
                <i class="fas fa-shopping-cart"></i>
            </a>
            <a href="wish.html" class="nav-icon" title="Wishlist">
                <i class="fas fa-heart"></i>
            </a>
            <a href="profile.html" class="nav-icon" title="Profile">
                <i class="fas fa-user"></i>
            </a>
        </nav>
    </header>

<div class="profile-container">
    <div class="profile-card">
        <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" class="profile-pic">
        <h2><i class="fas fa-user"></i> <?php echo $name; ?></h2>
        <p><i class="fas fa-envelope"></i> <?php echo $email; ?></p>
        <p><i class="fas fa-calendar-alt"></i> Member Since: <?php echo $member_since; ?></p>
        <a href="editprofile.php" class="edit-btn"><i class="fas fa-edit"></i> Edit Profile</a>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

</body>
<script>
    
        // Go back to the previous page
function goBack() {
    window.history.back();
}
</script>
</html>
