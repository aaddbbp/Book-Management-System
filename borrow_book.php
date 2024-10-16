<?php
session_start();
require('db.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = intval($_POST['book_id']);
    $user_id = $_SESSION['user_id'];

    // Check if the book is already borrowed by the user
    $check_query = "SELECT * FROM borrowed_books WHERE user_id = $user_id AND book_id = $book_id";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) == 0) {
        // Insert the borrowing record into the borrowed_books table
        $borrow_query = "INSERT INTO borrowed_books (user_id, book_id, borrow_date) VALUES ($user_id, $book_id, NOW())";
        if (mysqli_query($conn, $borrow_query)) {
            // Successfully borrowed the book
            echo "<script>alert('You have successfully borrowed the book.'); window.location.href='view_book.php?id=$book_id';</script>";
        } else {
            // Error occurred
            echo "<script>alert('Failed to borrow the book.'); window.location.href='view_book.php?id=$book_id';</script>";
        }
    } else {
        // Already borrowed the book
        echo "<script>alert('You have already borrowed this book.'); window.location.href='view_book.php?id=$book_id';</script>";
    }
} else {
    // If the form is not submitted correctly, redirect to the view_book page
    header('Location: view_book.php?id=' . $_GET['id']);
    exit();
}
?>
