<?php
session_start();
require('db.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the borrowed books for the logged-in user
$query = "
    SELECT b.id, b.title, b.author_id, b.category_id, bb.borrow_date, b.description, b.image
    FROM borrowed_books bb
    JOIN books b ON bb.book_id = b.id
    WHERE bb.user_id = $user_id
    ORDER BY bb.borrow_date DESC
";
$result = mysqli_query($conn, $query);

// Check if the user has borrowed any books
if (mysqli_num_rows($result) > 0) {
    $borrowed_books = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $borrowed_books = [];
}

// Handle return book action
if (isset($_GET['return_book_id'])) {
    $book_id_to_return = $_GET['return_book_id'];
    $return_query = "DELETE FROM borrowed_books WHERE user_id = $user_id AND book_id = $book_id_to_return";
    $return_result = mysqli_query($conn, $return_query);

    if ($return_result) {
        $message = "Book successfully returned!";
    } else {
        $message = "Failed to return the book.";
    }

    header("Location: view_borrowed_books.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Borrowed Books</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header>
        <h1>My Borrowed Books</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> | <a href="logout.php">Logout</a></p>
    </header>

    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="search_books.php">Search Books</a></li>
            <li><a href="my_books.php">My Borrowed Books</a></li>
            <li><a href="authors.php">Browse Authors</a></li>
        </ul>
    </nav>

    <main>
        <h2>Borrowed Books List</h2>

        <?php if (isset($message)): ?>
            <p style="color:green;"><?php echo $message; ?></p>
        <?php endif; ?>

        <?php if (count($borrowed_books) > 0): ?>
            <?php foreach ($borrowed_books as $book): ?>
                <?php
                // Fetch author and category details
                $author_query = "SELECT name FROM authors WHERE id = {$book['author_id']}";
                $author_result = mysqli_query($conn, $author_query);
                $author = mysqli_fetch_assoc($author_result);

                $category_query = "SELECT category_name FROM categories WHERE id = {$book['category_id']}";
                $category_result = mysqli_query($conn, $category_query);
                $category = mysqli_fetch_assoc($category_result);
                ?>

                <div class="book-item">
                    <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p><strong>Author:</strong> <?php echo htmlspecialchars($author['name']); ?></p>
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($category['category_name']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($book['description']); ?></p>
                    <p><strong>Borrowed On:</strong> <?php echo date('F j, Y', strtotime($book['borrow_date'])); ?></p>

                    <?php if ($book['image']): ?>
                        <p><strong>Book Image:</strong><br><img src="uploads/<?php echo htmlspecialchars($book['image']); ?>" alt="Book Image" width="200"></p>
                    <?php endif; ?>

                    <!-- Return Book Button -->
                    <a href="view_borrowed_books.php?return_book_id=<?php echo $book['id']; ?>" class="return-btn" onclick="return confirm('Are you sure you want to return this book?')">Return Book</a>
                </div>

                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>You have not borrowed any books yet.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2024 Book Management System. All rights reserved.</p>
    </footer>
</body>

</html>
