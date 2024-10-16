<?php
session_start();
require('db.php'); // Include database connection

// Check user role if logged in
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Management System</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS for styling -->
</head>
<body>
    <header>
        <h1>Welcome to the Book Management System</h1>

        <?php if (isset($_SESSION['username'])): ?>
            <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?> (Role: <?php echo ucfirst($role); ?>) | <a href="logout.php">Logout</a></p>
        <?php else: ?>
            <p><a href="login.php">Login</a> | <a href="register.php">Register</a></p>
        <?php endif; ?>
    </header>

    <nav>
        <ul>
            <li><a href="index.php">Dashboard</a></li>


            <?php if ($role === 'author'): ?>
                <!-- Author-specific links -->
                <li><a href="add_book.php">Add Book</a></li>

            <?php elseif ($role === 'user'): ?>
                <!-- User-specific links -->
                <li><a href="search_books.php">Search Books</a></li>
                <li><a href="authors.php">Browse Authors</a></li>
                <li><a href="my_books.php">My Borrowed Books</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main>
        <h2>Book List</h2>

        <?php
        // Fetch all books from the database
        $query = "SELECT books.title, books.image, books.description, authors.name AS author_name FROM books
                  JOIN authors ON books.author_id = authors.id";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            echo '<div class="book-list">';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="book-item">';
                echo '<img src="uploads/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['title']) . '">';
                echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
                echo '<p>by ' . htmlspecialchars($row['author_name']) . '</p>';
                echo '<p>' . nl2br(htmlspecialchars($row['description'])) . '</p>'; // Ensure the description is safe
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<p>No books available.</p>';
        }
        ?>
    </main>

    <footer>
        <p>&copy; 2024 Book Management System. All rights reserved.</p>
    </footer>
</body>
</html>
