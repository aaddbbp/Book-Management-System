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

// Get book ID from URL
if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    // Delete book query
    $query = "DELETE FROM books WHERE id = '$book_id' AND author_id = '" . $_SESSION['user_id'] . "'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        // Redirect back to manage books page with success message
        header('Location: manage_books.php?message=Book deleted successfully');
    } else {
        // Redirect back with error message
        header('Location: manage_books.php?message=Error deleting book');
    }
} else {
    // Redirect if no book ID is passed
    header('Location: manage_books.php');
}
?>
