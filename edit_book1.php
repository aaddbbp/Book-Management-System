<?php
session_start();
require('db.php'); // Database connection

// Check if the user is logged in and has a valid session
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Check if the book ID is provided
if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    // Fetch the book details from the database
    $query = "SELECT * FROM books WHERE id = '$book_id'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $book = mysqli_fetch_assoc($result);
    } else {
        echo "Book not found.";
        exit();
    }

    // If the form is submitted, update the book details
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        
        // Handle file upload if an image is provided
        $image = $_FILES['image']['name'];
        if ($image) {
            $image_tmp = $_FILES['image']['tmp_name'];
            $image_dir = 'uploads/' . basename($image);
            move_uploaded_file($image_tmp, $image_dir);
        } else {
            // Keep existing image if no new image is uploaded
            $image = $book['image'];
        }

        // Update the book record in the database
        $update_query = "UPDATE books SET 
                            title = '$title', 
                            description = '$description', 
                            category_id = '$category', 
                            status = '$status', 
                            image = '$image'
                          WHERE id = '$book_id'";

        if (mysqli_query($conn, $update_query)) {
            header('Location: view_books.php');
            exit();
        } else {
            echo "Error updating book.";
        }
    }
} else {
    echo "Invalid request.";
    exit();
}

// Fetch categories for the category dropdown
$category_query = "SELECT * FROM categories";
$category_result = mysqli_query($conn, $category_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Edit Book</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> | <a href="logout.php">Logout</a></p>
    </header>

    <nav>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="category.php">Manage Categories</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
        </ul>
    </nav>

    <main>
        <h2>Edit Book Information</h2>

        <form action="edit_book.php?id=<?php echo $book_id; ?>" method="POST" enctype="multipart/form-data">
            <label for="title">Book Title:</label>
            <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($book['title']); ?>" required><br><br>

            <label for="description">Description:</label><br>
            <textarea name="description" id="description" rows="4" required><?php echo htmlspecialchars($book['description']); ?></textarea><br><br>

            <label for="category">Category:</label>
            <select name="category" id="category" required>
                <?php
                while ($category = mysqli_fetch_assoc($category_result)) {
                    $selected = $category['id'] == $book['category_id'] ? 'selected' : '';
                    echo "<option value='" . $category['id'] . "' $selected>" . htmlspecialchars($category['category_name']) . "</option>";
                }
                ?>
            </select><br><br>

            <label for="status">Status:</label>
            <select name="status" id="status" required>
                <option value="available" <?php echo $book['status'] == 'available' ? 'selected' : ''; ?>>Available</option>
                <option value="borrowed" <?php echo $book['status'] == 'borrowed' ? 'selected' : ''; ?>>Borrowed</option>
            </select><br><br>

            <label for="image">Book Image:</label>
            <input type="file" name="image" id="image"><br><br>
            <p>Current Image: <img src="uploads/<?php echo htmlspecialchars($book['image']); ?>" alt="Current Image" width="100"></p>

            <input type="submit" value="Update Book">
        </form>
    </main>

    <footer>
        <p>&copy; 2024 Book Management System. All rights reserved.</p>
    </footer>
</body>
</html>
