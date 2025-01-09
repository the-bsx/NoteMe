<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $user_id = $_SESSION['user_id'];

    $query = "INSERT INTO notes (user_id, title, content) VALUES ('$user_id', '$title', '$content')";
    if (mysqli_query($conn, $query)) {
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Failed to create note";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Create Note - Note App</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <nav class="nav">
        <a href="dashboard.php">Back to Dashboard</a>
    </nav>

    <div class="container">
        <div class="form-container">
            <h2>Create New Note</h2>
            <?php if (isset($error)) echo "<p style='color: red'>$error</p>"; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" required>
                </div>
                <div class="form-group">
                    <label>Content</label>
                    <textarea name="content" rows="10" required></textarea>
                </div>
                <button type="submit" class="btn">Create Note</button>
            </form>
        </div>
    </div>
</body>

</html>