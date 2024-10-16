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

    // Delete the book from the database
    $query = "DELETE FROM books WHERE id = '$book_id'";

    if (mysqli_query($conn, $query)) {
        // Set a success message in the session
        $_SESSION['message'] = "Book deleted successfully!";
        // header('Location: view_books.php');
        exit();
    } else {
        // Set an error message in case of a failure
        $_SESSION['message'] = "Error deleting book.";
        header('Location: view_books.php');
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}
