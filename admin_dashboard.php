<?php
session_start();
require('db.php'); // Database connection

// Check if the user is logged in and has a valid session
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$user_role = $_SESSION['role'];
$username = $_SESSION['username'];

// Fetching statistics for admin dashboard (total users, books, and categories)
$total_users_query = "SELECT COUNT(*) AS total_users FROM users";
$total_books_query = "SELECT COUNT(*) AS total_books FROM books";
$total_categories_query = "SELECT COUNT(*) AS total_categories FROM categories";

$total_users_result = mysqli_query($conn, $total_users_query);
$total_books_result = mysqli_query($conn, $total_books_query);
$total_categories_result = mysqli_query($conn, $total_categories_query);

$total_users = mysqli_fetch_assoc($total_users_result)['total_users'];
$total_books = mysqli_fetch_assoc($total_books_result)['total_books'];
$total_categories = mysqli_fetch_assoc($total_categories_result)['total_categories'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Container for grid layout */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        /* Card style */
        .stat-card {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-10px);
        }

        .stat-card h3 {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }

        .stat-card p {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
        }

        /* Card header */
        .stat-card-header {
            font-size: 18px;
            color: #777;
        }
    </style>
</head>

<body>
    <header>
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($username); ?> | <a href="logout.php">Logout</a></p>
    </header>

    <nav>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="category.php">Manage Categories</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
        </ul>
    </nav>

    <main>
        <!-- Statistics Section (Card Layout) -->
        <section>
            <h2>Statistics</h2>
            <div class="stats">
                <!-- Total Users Card -->
                <div class="stat-card">
                    <div class="stat-card-header">Total Users</div>
                    <h3>Users</h3>
                    <p><?php echo $total_users; ?></p>
                </div>

                <!-- Total Books Card -->
                <div class="stat-card">
                    <div class="stat-card-header">Total Books</div>
                    <h3>Books</h3>
                    <p><?php echo $total_books; ?></p>
                </div>

                <!-- Total Categories Card -->
                <div class="stat-card">
                    <div class="stat-card-header">Total Categories</div>
                    <h3>Categories</h3>
                    <p><?php echo $total_categories; ?></p>
                </div>
            </div>
        </section>

        <!-- Other Sections... -->
        <section>
            <h2>Manage Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetching users for admin to manage
                    $users_query = "SELECT * FROM users";
                    $users_result = mysqli_query($conn, $users_query);

                    while ($user = mysqli_fetch_assoc($users_result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                        echo "<td><a href='edit_user.php?id=" . $user['id'] . "'>Edit</a> | <a href='delete_user.php?id=" . $user['id'] . "'>Delete</a></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <section>
            <h2>Manage Categories</h2>
            <table>
                <thead>
                    <tr>
                        <th>Category ID</th>
                        <th>Category Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetching categories for admin to manage
                    $categories_query = "SELECT * FROM categories";
                    $categories_result = mysqli_query($conn, $categories_query);

                    while ($category = mysqli_fetch_assoc($categories_result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($category['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($category['category_name']) . "</td>";
                        echo "<td><a href='edit_category.php?id=" . $category['id'] . "'>Edit</a> | <a href='delete_category.php?id=" . $category['id'] . "'>Delete</a></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <section>
            <h2>All Books</h2>
            <table>
                <thead>
                    <tr>
                        <th>Book ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetching books for admin to view
                    $books_query = "SELECT books.id, books.title, authors.name AS author_name, categories.category_name FROM books 
                                    JOIN authors ON books.author_id = authors.id 
                                    JOIN categories ON books.category_id = categories.id";
                    $books_result = mysqli_query($conn, $books_query);

                    while ($book = mysqli_fetch_assoc($books_result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($book['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($book['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($book['author_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($book['category_name']) . "</td>";
                        echo "<td><a href='edit_book1.php?id=" . $book['id'] . "'>Edit</a> | <a href='delete1_book.php?id=" . $book['id'] . "'>Delete</a></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Book Management System. All rights reserved.</p>
    </footer>
</body>

</html>