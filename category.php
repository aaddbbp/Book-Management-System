<?php
session_start();
require('db.php'); // Database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Handle category addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);

    if (!empty($category_name)) {
        $query = "INSERT INTO categories (category_name) VALUES ('$category_name')";
        $result = mysqli_query($conn, $query);

        if ($result) {
            echo "<p>Category added successfully!</p>";
        } else {
            echo "<p>Error: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p>Please enter a category name.</p>";
    }
}

// Handle category deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = "DELETE FROM categories WHERE id = '$delete_id'";
    if (mysqli_query($conn, $delete_query)) {
        echo "<p>Category deleted successfully!</p>";
    } else {
        echo "<p>Error deleting category: " . mysqli_error($conn) . "</p>";
    }
}

// Fetch all categories
$category_query = "SELECT * FROM categories";
$category_result = mysqli_query($conn, $category_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header>
        <h1>Manage Categories</h1>
        <p>Welcome, <?php echo $_SESSION['username']; ?> | <a href="logout.php">Logout</a></p>
    </header>

    <nav>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="category.php">Manage Categories</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
        </ul>
    </nav>

    <main>
        <h2>Add a New Category</h2>
        <form action="category.php" method="POST">
            <label for="category_name">Category Name:</label>
            <input type="text" name="category_name" id="category_name" required><br><br>
            <input type="submit" name="add_category" value="Add Category">
        </form>

        <h2>Existing Categories</h2>
        <?php
        if (mysqli_num_rows($category_result) > 0) {
            echo "<table>";
            echo "<tr><th>Category Name</th><th>Actions</th></tr>";
            while ($category = mysqli_fetch_assoc($category_result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($category['category_name']) . "</td>";
                echo "<td><a href='category.php?delete_id=" . $category['id'] . "' onclick='return confirm(\"Are you sure you want to delete this category?\")'>Delete</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No categories found.</p>";
        }
        ?>
    </main>

    <footer>
        <p>&copy; 2024 Book Management System. All rights reserved.</p>
    </footer>
</body>

</html>