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

// Fetch Data from Tables
$tables = ['cart', 'contact_messages', 'payments', 'reviews', 'users', 'wish'];
$data = [];

foreach ($tables as $table) {
    $result = $conn->query("SELECT * FROM $table");
    $data[$table] = $result->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Auroma</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="#cart"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                <li><a href="#contact_messages"><i class="fas fa-envelope"></i> Contact Messages</a></li>
                <li><a href="#payments"><i class="fas fa-credit-card"></i> Payments</a></li>
                <li><a href="#reviews"><i class="fas fa-star"></i> Reviews</a></li>
                <li><a href="#users"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="#wish"><i class="fas fa-heart"></i> Wishlists</a></li>
            </ul>
        </aside>

        <main class="content">
        <header style="display: flex; justify-content: space-between; align-items: center; background: #2c3e50; color: white; padding: 15px 20px; border-radius: 8px;">
    <h1 style="margin: 0;">Admin Dashboard</h1>
    <button class="logout-btn" onclick="confirmLogout()">
    <i class="fas fa-sign-out-alt"></i> Logout
</button>
</header>

<?php foreach ($data as $table => $rows): ?>
    <section id="<?php echo $table; ?>">
        <h2><?php echo ucfirst(str_replace('_', ' ', $table)); ?></h2>
        <?php if ($table === 'users'): ?>
            <a href="add.php" class="button button-add">Add User</a>
        <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <?php if (!empty($rows)): ?>
                        <?php foreach (array_keys($rows[0]) as $col): ?>
                            <th><?php echo ucfirst(str_replace('_', ' ', $col)); ?></th>
                        <?php endforeach; ?>
                        <?php if ($table === 'users'): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($rows)): ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <?php foreach ($row as $key => $value): ?>
                                <td><?php echo htmlspecialchars($value); ?></td>
                            <?php endforeach; ?>
                            <?php if ($table === 'users'): ?>
                                <td>
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="button button-edit">Edit</a>
                                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="button button-delete" onclick="return confirm('Are you sure?');">Delete</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="100%">No data available</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
<?php endforeach; ?>


    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }
        .dashboard-container {
            display: flex;
            width: 100%;
        }
        .sidebar {
            width: 250px;
            background: #222;
            color: white;
            padding: 20px;
            height: 100vh;
            position: fixed;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            margin: 15px 0;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .sidebar ul li a i {
            margin-right: 10px;
        }
        .content {
            margin-left: 270px;
            padding: 20px;
            width: calc(100% - 270px);
        }
        h1 {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #333;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .button {
    display: inline-block;
    padding: 8px 12px;
    font-size: 14px;
    font-weight: bold;
    text-decoration: none;
    border-radius: 5px;
    transition: 0.3s;
}

/* Add Button */
.button-add {
    background-color: #28a745; /* Green */
    color: white;
}

.button-add:hover {
    background-color: #218838;
}

/* Edit Button */
.button-edit {
    background-color: #007bff; /* Blue */
    color: white;
}

.button-edit:hover {
    background-color: #0056b3;
}

/* Delete Button */
.button-delete {
    background-color: #dc3545; /* Red */
    color: white;
}

.button-delete:hover {
    background-color: #c82333;
}




        .logout-btn {
    background: #e74c3c;
    color: white;
    border: none;
    padding: 10px 15px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px;
    display: flex;
    align-items: center;
    transition: 0.3s;
}

.logout-btn i {
    margin-right: 8px;
}

.logout-btn:hover {
    background: #c0392b;
}
    </style>
</body>
<script>
    function confirmLogout() {
    Swal.fire({
        title: "Are you sure?",
        text: "You will be logged out of your session.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, Logout",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "logout.php"; // Redirect to logout page
        }
    });
}
    </script>
</html>
