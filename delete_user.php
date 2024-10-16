<?php
session_start();
require('db.php');

// Check if the user is logged in and has an admin role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Fetch user data for confirmation
    $query = "SELECT * FROM users WHERE id = '$user_id'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
    } else {
        echo "User not found!";
        exit();
    }

    // Handle delete action
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $delete_query = "DELETE FROM users WHERE id = '$user_id'";
        if (mysqli_query($conn, $delete_query)) {
            header('Location: admin_dashboard.php');
            exit();
        } else {
            echo "Error deleting user!";
        }
    }
} else {
    echo "Invalid user ID!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Delete User</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> | <a href="logout.php">Logout</a></p>
    </header>

    <p>Are you sure you want to delete the user "<?php echo htmlspecialchars($user['username']); ?>"?</p>

    <form action="delete_user.php?id=<?php echo $user_id; ?>" method="POST">
        <input type="submit" value="Delete">
        <a href="admin_dashboard.php">Cancel</a>
    </form>
</body>
</html>
