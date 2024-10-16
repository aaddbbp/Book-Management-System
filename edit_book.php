<?php
session_start();
require('db.php'); // Database connection

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Check user role (author)
$role = $_SESSION['role'];
if ($role !== 'author') {
    header('Location: dashboard.php'); // Redirect non-authors to their dashboard
    exit();
}

// Get the book ID from the URL
if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    // Fetch the book details from the database
    $query = "SELECT * FROM books WHERE id = '$book_id' AND author_id = '" . $_SESSION['user_id'] . "'";
    $result = mysqli_query($conn, $query);
    
    // Check if the book exists
    if (mysqli_num_rows($result) > 0) {
        $book = mysqli_fetch_assoc($result);
    } else {
        header('Location: manage_books.php');
        exit();
    }
} else {
    header('Location: manage_books.php');
    exit();
}

// Handle form submission for editing the book
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);

    // Handle image upload
    $image = $book['image']; // Keep the old image if no new image is uploaded

    if ($_FILES['image']['error'] == 0) {
        // New image uploaded
        $image = $_FILES['image']['name'];
        $target_dir = "images/";
        $target_file = $target_dir . basename($image);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // Image upload successful, update image in the database
        } else {
            $image = $book['image']; // Fallback to original image if upload fails
        }
    }

    // Update the book details in the database
    $update_query = "UPDATE books 
                     SET title = '$title', description = '$description', category_id = '$category_id', image = '$image' 
                     WHERE id = '$book_id' AND author_id = '" . $_SESSION['user_id'] . "'";
    
    if (mysqli_query($conn, $update_query)) {
        header('Location: manage_books.php?message=Book updated successfully');
        exit();
    } else {
        $error_message = 'Error updating book. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book - Book Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Edit Book Details</h1>
        <p>Welcome, <?php echo $_SESSION['username']; ?> | <a href="logout.php">Logout</a></p>
    </header>

    <nav>
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="add_book.php">Add New Book</a></li>
            <li><a href="category.php">Manage Categories</a></li>
            <li><a href="manage_books.php">Manage My Books</a></li>
        </ul>
    </nav>

    <main>
        <h2>Editing: <?php echo $book['title']; ?></h2>

        <?php if (isset($error_message)) { echo '<p class="error">' . $error_message . '</p>'; } ?>

        <form action="edit_book.php?id=<?php echo $book['id']; ?>" method="POST" enctype="multipart/form-data">
            <label for="title">Book Title:</label>
            <input type="text" id="title" name="title" value="<?php echo $book['title']; ?>" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" required><?php echo $book['description']; ?></textarea>

            <label for="category_id">Category:</label>
            <select id="category_id" name="category_id" required>
                <?php
                // Fetch categories from the database
                $category_query = "SELECT * FROM categories";
                $category_result = mysqli_query($conn, $category_query);
                while ($category = mysqli_fetch_assoc($category_result)) {
                    $selected = ($category['id'] == $book['category_id']) ? 'selected' : '';
                    echo "<option value='{$category['id']}' $selected>{$category['name']}</option>";
                }
                ?>
            </select>

            <label for="image">Book Image:</label>
            <input type="file" id="image" name="image">
            <p>Current Image: <img src="images/<?php echo $book['image']; ?>" alt="Current Book Image" width="100"></p>

            <button type="submit">Update Book</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 Book Management System. All rights reserved.</p>
    </footer>
</body>
</html>
