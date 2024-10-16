<?php
session_start();
require('db.php'); // Database connection

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Check user role (author)
$role = $_SESSION['role'];
if ($role !== 'author') {
    header('Location: dashboard.php'); // Redirect non-authors to their dashboard
    exit();
}

// Fetch the books added by the logged-in author
$author_id = $_SESSION['user_id']; // Assuming user_id is stored in the session
$query = "SELECT * FROM books WHERE author_id = '$author_id'";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books - Book Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Manage Your Books</h1>
        <p>Welcome, <?php echo $_SESSION['username']; ?> | <a href="logout.php">Logout</a></p>
    </header>

    <nav>
        <ul>
            <!-- Common link -->
            <li><a href="author_dashboard.php">Dashboard</a></li>

            <!-- Author-specific links -->
            <li><a href="add_book.php">Add New Book</a></li>
            <li><a href="manage_books.php">Manage My Books</a></li>
        </ul>
    </nav>

    <main>
        <h2>Your Books</h2>

        <?php
        if (mysqli_num_rows($result) > 0) {
            echo '<table>';
            echo '<tr><th>Title</th><th>Category</th><th>Image</th><th>Actions</th></tr>';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . $row['title'] . '</td>';
                echo '<td>' . $row['category_id'] . '</td>';
                echo '<td><img src="uploads/' . $row['image'] . '" alt="' . $row['title'] . '" width="100"></td>';
                echo '<td>
                        <a href="edit_book.php?id=' . $row['id'] . '">Edit</a> | 
                        <a href="delete_book.php?id=' . $row['id'] . '" onclick="return confirm(\'Are you sure you want to delete this book?\')">Delete</a>
                      </td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p>You have not added any books yet.</p>';
        }
        ?>
    </main>

    <footer>
        <p>&copy; 2024 Book Management System. All rights reserved.</p>
    </footer>
</body>
</html>
