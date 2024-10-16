<?php
session_start();
require('db.php'); // Database connection

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Check if author ID is provided in the URL
if (!isset($_GET['id'])) {
    header('Location: authors.php');
    exit();
}

$author_id = (int) $_GET['id']; // Convert to integer for safety

// Fetch author details from the database
$author_query = "SELECT * FROM authors WHERE id = $author_id";
$author_result = mysqli_query($conn, $author_query);

if (mysqli_num_rows($author_result) == 1) {
    $author = mysqli_fetch_assoc($author_result); // Fetch author details
} else {
    // Redirect if author not found
    header('Location: authors.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($author['name']); ?> - Author Details</title>
    <link rel="stylesheet" href="styles.css">

    <!-- Internal CSS for author details and books list -->
    <style>
        .container {
            width: 80%;
            margin: 0 auto;
        }

        .author-details {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .author-details img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            float: left;
            margin-right: 20px;
        }

        .author-details h2 {
            font-size: 28px;
            color: #333;
        }

        .author-details p {
            font-size: 16px;
            color: #666;
        }

        .book-list {
            display: flex;
            flex-wrap: wrap;
        }

        .book-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 250px;
            margin: 15px;
            padding: 20px;
            text-align: center;
        }

        .book-card img {
            width: 100px;
            height: 100px;
            margin-bottom: 10px;
        }

        .book-card h3 {
            font-size: 18px;
            color: #333;
        }

        .book-card p {
            font-size: 14px;
            color: #666;
        }
    </style>
</head>

<body>
    <header>
        <h1>Author Details</h1>
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

    <div class="container">
        <!-- Author Details Section -->
        <div class="author-details">
            <!-- Assuming there is an image field for the author. Replace with a placeholder if not available -->

            <h2><?php echo htmlspecialchars($author['name']); ?></h2>
            <p><strong>Joined on:</strong> <?php echo date('F j, Y', strtotime($author['created_at'])); ?></p>
        </div>

        <!-- Books List Section -->
        <h2>Books by <?php echo htmlspecialchars($author['name']); ?></h2>

        <div class="book-list">
            <?php
            // Fetch books by this author from the database
            $books_query = "SELECT * FROM books WHERE author_id = $author_id";
            $books_result = mysqli_query($conn, $books_query);

            if (mysqli_num_rows($books_result) > 0) {
                while ($book = mysqli_fetch_assoc($books_result)) {
                    echo "<div class='book-card'>";
                    // Update image path to point to 'uploads/' directory
                    echo '<img src="uploads/' . htmlspecialchars($book['image']) . '" alt="' . htmlspecialchars($book['title']) . '" width="100px" height="50px">';
                    echo "<h3>" . htmlspecialchars($book['title']) . "</h3>";
                    echo "<p>" . htmlspecialchars($book['description']) . "</p>";
                    echo "<a href='view_book.php?id=" . $book['id'] . "' class='btn'>View Details</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>No books available by this author.</p>";
            }
            ?>
        </div>

    </div>

    <footer>
        <p>&copy; 2024 Book Management System. All rights reserved.</p>
    </footer>
</body>

</html>