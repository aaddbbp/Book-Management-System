<?php
session_start();
require('db.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Fetch all books
$books_query = "SELECT books.id, books.title, books.description, books.image, authors.name as author, categories.category_name 
                FROM books 
                LEFT JOIN authors ON books.author_id = authors.id
                LEFT JOIN categories ON books.category_id = categories.id";
$books_result = mysqli_query($conn, $books_query);

// Fetch authors and categories for filtering
$authors_query = "SELECT id, name FROM authors";
$authors_result = mysqli_query($conn, $authors_query);

$categories_query = "SELECT id, category_name FROM categories";
$categories_result = mysqli_query($conn, $categories_query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    .book-list {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .card {
        width: 250px;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card-img {
        width: 100px;
        height: 100px;
        object-fit: cover;
    }

    .card-body {
        padding: 10px;
    }

    .card-body h4 {
        margin: 0;
        font-size: 1.2em;
    }

    .card-body p {
        font-size: 0.9em;
    }

    .btn {
        display: inline-block;
        margin-top: 10px;
        padding: 8px 12px;
        background-color: #5cb85c;
        color: white;
        text-align: center;
        text-decoration: none;
        border-radius: 4px;
    }

    .btn:hover {
        background-color: #4cae4c;
    }
</style>

<body>
    <header>
        <h1>User Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> | <a href="logout.php">Logout</a></p>
    </header>

    <nav>
        <ul>
            <li><a href="user_dashboard.php">Dashboard</a></li>
            <li><a href="search_books.php">Search Books</a></li>
            <li><a href="user_books.php">My Borrowed Books</a></li>
            <li><a href="authors.php">Browse Authors</a></li>
        </ul>
    </nav>

    <main>
        <h2>Available Books</h2>

        <!-- Filter Form -->
        <form method="GET" action="user_dashboard.php">
            <label for="author">Filter by Author:</label>
            <select name="author" id="author">
                <option value="">Select Author</option>
                <?php while ($author = mysqli_fetch_assoc($authors_result)): ?>
                    <option value="<?php echo $author['id']; ?>" <?php echo isset($_GET['author']) && $_GET['author'] == $author['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($author['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="category">Filter by Category:</label>
            <select name="category" id="category">
                <option value="">Select Category</option>
                <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                    <option value="<?php echo $category['id']; ?>" <?php echo isset($_GET['category']) && $_GET['category'] == $category['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['category_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Filter</button>
        </form>

        <h3>Books List</h3>

        <!-- Displaying books with images -->
        <div class="book-list">
            <?php
            // Filter query based on selected author and category
            $filter_query = "SELECT books.id, books.title, books.description, books.image, authors.name as author, categories.category_name 
                             FROM books 
                             LEFT JOIN authors ON books.author_id = authors.id
                             LEFT JOIN categories ON books.category_id = categories.id";

            if (isset($_GET['author']) && $_GET['author'] != '') {
                $author_id = mysqli_real_escape_string($conn, $_GET['author']);
                $filter_query .= " WHERE books.author_id = $author_id";
            }

            if (isset($_GET['category']) && $_GET['category'] != '') {
                $category_id = mysqli_real_escape_string($conn, $_GET['category']);
                $filter_query .= isset($author_id) ? " AND books.category_id = $category_id" : " WHERE books.category_id = $category_id";
            }

            $filtered_books_result = mysqli_query($conn, $filter_query);

            if (mysqli_num_rows($filtered_books_result) > 0) {
                while ($book = mysqli_fetch_assoc($filtered_books_result)) {
                    echo "<div class='card'>";
                    echo "<img src='uploads/" . htmlspecialchars($book['image']) . "' alt='Book Image' class='card-img'>";
                    echo "<div class='card-body'>";
                    echo "<h4>" . htmlspecialchars($book['title']) . "</h4>";
                    echo "<p><strong>Author:</strong> " . htmlspecialchars($book['author']) . "</p>";
                    echo "<p><strong>Category:</strong> " . htmlspecialchars($book['category_name']) . "</p>";
                    echo "<p>" . htmlspecialchars($book['description']) . "</p>";
                    echo "<a href='view_book.php?id=" . $book['id'] . "' class='btn'>View Details</a>";
                    echo "</div></div>";
                }
            } else {
                echo "<p>No books available.</p>";
            }
            ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Book Management System. All rights reserved.</p>
    </footer>
</body>

</html>