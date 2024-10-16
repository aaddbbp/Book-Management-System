<?php
session_start();
require('db.php'); // Database connection

// Check if the user is logged in and has a valid session
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'author') {
    header('Location: login.php');
    exit();
}

$user_role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Author Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header>
        <h1>Author Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> | <a href="logout.php">Logout</a></p>
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
        <h2>Welcome to Your Dashboard</h2>
        <p>As an author, you can manage your books here. Add new books to the catalog or manage your existing ones.</p>

        <section>
            <h3>Your Books</h3>
            <?php
            // Fetch books added by the current author
            $author_id = $_SESSION['user_id'];
            $book_query = "SELECT books.*, categories.category_name FROM books 
                           LEFT JOIN categories ON books.category_id = categories.id 
                           WHERE books.author_id = '$author_id'";
            $book_result = mysqli_query($conn, $book_query);

            if (mysqli_num_rows($book_result) > 0) {
                while ($book = mysqli_fetch_assoc($book_result)) {
                    echo "<div class='card'>";
                    echo "<img src='uploads/" . htmlspecialchars($book['image']) . "' alt='Book Image' class='card-img'>";
                    echo "<div class='card-body'>";
                    echo "<h4>" . htmlspecialchars($book['title']) . "</h4>";
                    echo "<p>" . htmlspecialchars($book['description']) . "</p>";
                    echo "<a href='view_book.php?id=" . $book['id'] . "' class='btn'>View Details</a>";
                    echo "</div></div>";
                }
            } else {
                echo "<p>You have not added any books yet.</p>";
            }
            ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Book Management System. All rights reserved.</p>
    </footer>
</body>

</html>