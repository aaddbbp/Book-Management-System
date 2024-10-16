<?php
session_start();
require('db.php'); // Database connection

// Check if the user is logged in and is an author
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'author') {
    header('Location: login.php');
    exit();
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author_id = $_SESSION['user_id']; // Use user_id from session
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category_id = intval($_POST['category']); // Ensure category ID is an integer

    // Check if the author exists in the authors table
    $author_check_query = "SELECT * FROM authors WHERE id = '$author_id'";
    $author_check_result = mysqli_query($conn, $author_check_query);

    if (mysqli_num_rows($author_check_result) == 0) {
        die("Error: Author not found in the authors table.");
    }

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];
        $image_type = mime_content_type($image_tmp);
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

        // Validate file type and size (e.g., max 2MB)
        if (in_array($image_type, $allowed_types) && $image_size <= 2 * 1024 * 1024) {
            $image_dir = 'uploads/' . basename($image);
            if (move_uploaded_file($image_tmp, $image_dir)) {
                // Insert book details into the database
                $query = "INSERT INTO books (title, author_id, category_id, description, image) 
                          VALUES ('$title', '$author_id', '$category_id', '$description', '$image')";
                $result = mysqli_query($conn, $query);

                if ($result) {
                    echo "<p>Book added successfully!</p>";
                } else {
                    echo "<p>Error: " . mysqli_error($conn) . "</p>";
                }
            } else {
                echo "<p>Error uploading image.</p>";
            }
        } else {
            echo "<p>Invalid file type or file size exceeds 2MB.</p>";
        }
    } else {
        echo "<p>Error: Please upload a valid image.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Add a New Book</h1>
        <p>Welcome, <?php echo $_SESSION['username']; ?> | <a href="logout.php">Logout</a></p>
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
        <h2>Book Information</h2>

        <form action="add_book.php" method="POST" enctype="multipart/form-data">
            <label for="title">Book Title:</label>
            <input type="text" name="title" id="title" required><br><br>

            <label for="description">Description:</label><br>
            <textarea name="description" id="description" rows="4" required></textarea><br><br>

            <label for="category">Category:</label>
            <select name="category" id="category" required>
                <?php
                // Fetch categories from the database
                $category_query = "SELECT * FROM categories";
                $category_result = mysqli_query($conn, $category_query);

                if (mysqli_num_rows($category_result) > 0) {
                    while ($category = mysqli_fetch_assoc($category_result)) {
                        echo "<option value='" . $category['id'] . "'>" . $category['category_name'] . "</option>";
                    }
                } else {
                    echo "<option>No categories available</option>";
                }
                ?>
            </select><br><br>

            <label for="image">Book Image:</label>
            <input type="file" name="image" id="image" accept="image/*" required><br><br>

            <input type="submit" value="Add Book">
        </form>
    </main>

    <footer>
        <p>&copy; 2024 Book Management System. All rights reserved.</p>
    </footer>
</body>
</html>
