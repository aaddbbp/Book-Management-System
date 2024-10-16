<?php
session_start();
require('db.php'); // Database connection

// Initialize variables
$category = '';
$author = '';
$search_results = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the search criteria from the form
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $author = isset($_POST['author']) ? $_POST['author'] : '';

    // Construct the query
    $query = "SELECT books.*, authors.name AS author_name, categories.category_name FROM books
              JOIN authors ON books.author_id = authors.id
              JOIN categories ON books.category_id = categories.id
              WHERE 1=1"; // Start with a simple query

    // Add conditions based on the user's selection
    if ($category) {
        $query .= " AND books.category_id = '$category'";
    }
    if ($author) {
        $query .= " AND authors.id = '$author'";
    }

    // Execute the query and fetch results
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $search_results[] = $row;
        }
    }
}

// Fetch categories and authors for the search form
$categories_query = "SELECT * FROM categories";
$categories_result = mysqli_query($conn, $categories_query);

$authors_query = "SELECT * FROM authors";
$authors_result = mysqli_query($conn, $authors_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Books</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    /* Search Form Styling */
    form {
        margin-bottom: 30px;
    }

    form label {
        font-size: 16px;
        margin-right: 10px;
    }

    form select {
        padding: 8px;
        font-size: 14px;
        margin-right: 10px;
    }

    form button {
        background-color: #007bff;
        color: white;
        padding: 8px 12px;
        border: none;
        cursor: pointer;
    }

    form button:hover {
        background-color: #0056b3;
    }

    /* Book List Styling */
    .book-list {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .book-item {
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        width: 30%;
        margin-bottom: 20px;
    }

    .book-item h3 {
        font-size: 20px;
        margin-top: 0;
    }

    .book-item p {
        font-size: 16px;
        margin: 8px 0;
    }

    .book-item .borrow-btn {
        background-color: #28a745;
        color: white;
        padding: 10px 15px;
        text-decoration: none;
        border-radius: 5px;
        margin-top: 10px;
        display: inline-block;
        font-weight: bold;
    }

    .book-item .borrow-btn:hover {
        background-color: #218838;
    }

    /* Pagination (if needed) */
    .pagination {
        text-align: center;
        margin-top: 30px;
    }

    .pagination a {
        display: inline-block;
        padding: 10px 15px;
        margin: 0 5px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
    }

    .pagination a:hover {
        background-color: #0056b3;
    }
</style>

<body>

    <header>
        <h1>Search Books</h1>
        <?php if (isset($_SESSION['username'])): ?>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> | <a href="logout.php">Logout</a></p>
        <?php endif; ?>
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
        <form action="search_books.php" method="POST">
            <label for="category">Select Category:</label>
            <select name="category" id="category">
                <option value="">--All Categories--</option>
                <?php
                while ($category_row = mysqli_fetch_assoc($categories_result)) {
                    echo "<option value='" . $category_row['id'] . "' " . ($category == $category_row['id'] ? 'selected' : '') . ">" . $category_row['category_name'] . "</option>";
                }
                ?>
            </select>

            <label for="author">Select Author:</label>
            <select name="author" id="author">
                <option value="">--All Authors--</option>
                <?php
                while ($author_row = mysqli_fetch_assoc($authors_result)) {
                    echo "<option value='" . $author_row['id'] . "' " . ($author == $author_row['id'] ? 'selected' : '') . ">" . $author_row['name'] . "</option>";
                }
                ?>
            </select>

            <button type="submit">Search</button>
        </form>

        <?php if (!empty($search_results)): ?>
            <h2>Search Results:</h2>
            <div class="book-list">
                <?php foreach ($search_results as $book): ?>
                    <div class="book-item">
                        <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                        <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author_name']); ?></p>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($book['category_name']); ?></p>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($book['description']); ?></p>
                        <a href="borrow_book.php?id=<?php echo $book['id']; ?>" class="borrow-btn">Borrow</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <p>No results found for the selected criteria.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2024 Book Management System. All rights reserved.</p>
    </footer>

</body>

</html>