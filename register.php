<?php
session_start();
require('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $email = mysqli_real_escape_string($conn, $_POST['email']); // Capture email

    // Insert the new user into the database
    $query = "INSERT INTO users (username, password, email, role) VALUES ('$username', '$password', '$email', '$role')";

    if (mysqli_query($conn, $query)) {
        // If the user is an author, insert them into the 'authors' table
        if ($role === 'author') {
            // Insert the author into the 'authors' table with the same username
            $author_query = "INSERT INTO authors (name) VALUES ('$username')";
            if (mysqli_query($conn, $author_query)) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;
                header('Location: index.php');
                exit();
            } else {
                $error = "Error adding author to authors table!";
            }
        } else {
            // If user is not an author, just log them in as a regular user
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            header('Location: index.php');
            exit();
        }
    } else {
        $error = "Error registering user!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h1>Register</h1>

    <?php if (isset($error)): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="role">Role:</label><br>
        <select name="role" required>
            <option value="user">User</option>
            <option value="author">Author</option>
        </select><br><br>

        <button type="submit">Register</button>
    </form>

    <p class="ready">Already have an account? <a href="login.php">Login here</a></p>

    <footer>
        <p>&copy; 2024 Book Management System. All rights reserved.</p>
    </footer>

</body>

</html>