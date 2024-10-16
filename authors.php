<?php
session_start();
require('db.php'); // Database connection

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Authors</title>
    <link rel="stylesheet" href="styles.css">

    <!-- Internal CSS for card styling -->
    <style>
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            margin: 15px;
            padding: 20px;
            text-align: center;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 15px;
        }

        .card h2 {
            font-size: 24px;
            color: #333;
        }

        .card p {
            font-size: 14px;
            color: #666;
        }

        .card a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .card a:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <header>
        <h1>Browse Authors</h1>
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
        <?php
        // Fetch authors from the database
        $authors_query = "SELECT * FROM authors";
        $authors_result = mysqli_query($conn, $authors_query);

        if (mysqli_num_rows($authors_result) > 0) {
            while ($author = mysqli_fetch_assoc($authors_result)) {
                echo "<div class='card'>";
                echo "<h2>" . htmlspecialchars($author['name']) . "</h2>";
                echo "<p>" . htmlspecialchars($author['bio']) . "</p>";
                echo "<a href='view_author.php?id=" . $author['id'] . "'>View More</a>";
                echo "</div>";
            }
        } else {
            echo "<p>No authors available.</p>";
        }
        ?>
    </div>

    <footer>
        <p>&copy; 2024 Book Management System. All rights reserved.</p>
    </footer>
</body>

</html>