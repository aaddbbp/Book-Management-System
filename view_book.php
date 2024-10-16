<?php
session_start();
require('db.php'); // Database connection

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Get the book ID from the URL
$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($book_id <= 0) {
    echo "Invalid book ID.";
    exit();
}

// Fetch the book details from the database
$book_query = "SELECT books.*, categories.category_name, authors.name AS author_name 
               FROM books 
               LEFT JOIN categories ON books.category_id = categories.id
               LEFT JOIN authors ON books.author_id = authors.id
               WHERE books.id = $book_id";

$book_result = mysqli_query($conn, $book_query);

if (mysqli_num_rows($book_result) == 1) {
    $book = mysqli_fetch_assoc($book_result);
} else {
    echo "Book not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['title']); ?> - Book Details</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($book['title']); ?></h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> | <a href="logout.php">Logout</a></p>
    </header>

    <nav>
        <ul>
            <li><a href="user_dashboard.php">Dashboard</a></li>
            <li><a href="search_books.php">Search Books</a></li>
            <li><a href="my_books.php">My Borrowed Books</a></li>
            <li><a href="authors.php">Browse Authors</a></li>
        </ul>
    </nav>

    <main>
        <div class="book-details">
            <h2>Book Information</h2>
            <p><strong>Title:</strong> <?php echo htmlspecialchars($book['title']); ?></p>
            <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author_name']); ?></p>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($book['category_name']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($book['description']); ?></p>

            <!-- If the book has an image -->
            <?php if (!empty($book['image'])): ?>
                <p><img src="uploads/<?php echo htmlspecialchars($book['image']); ?>" alt="Book Image" style="max-width:200px;"></p>
            <?php endif; ?>

            <p><strong>Date Added:</strong> <?php echo htmlspecialchars($book['created_at']); ?></p>
            
            <!-- Borrow book button (if applicable) -->
            <form action="borrow_book.php" method="POST">
                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                <button type="submit">Borrow Book</button>
            </form>

        
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Book Management System. All rights reserved.</p>
    </footer>
</body>
</html>
