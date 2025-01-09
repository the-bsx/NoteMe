<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$note_id = mysqli_real_escape_string($conn, $_GET['id']);
$user_id = $_SESSION['user_id'];

// Check if user has permission to edit
$permission_query = "SELECT notes.*, shared_notes.can_edit 
                    FROM notes 
                    LEFT JOIN shared_notes ON notes.id = shared_notes.note_id 
                    WHERE notes.id = '$note_id' 
                    AND (notes.user_id = '$user_id' 
                    OR (shared_notes.shared_with_id = '$user_id' AND shared_notes.can_edit = 1))";
$permission_result = mysqli_query($conn, $permission_query);

if (mysqli_num_rows($permission_result) == 0) {
    header('Location: dashboard.php');
    exit();
}

$note = mysqli_fetch_assoc($permission_result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    
    $update_query = "UPDATE notes SET title = '$title', content = '$content' WHERE id = '$note_id'";
    if (mysqli_query($conn, $update_query)) {
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Failed to update note";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Note - Note App</title>
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
            <h2>Edit Note</h2>
            <?php if (isset($error)) echo "<p style='color: red'>$error</p>"; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($note['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Content</label>
                    <textarea name="content" rows="10" required><?php echo htmlspecialchars($note['content']); ?></textarea>
                </div>
                <button type="submit" class="btn">Update Note</button>
            </form>
        </div>
    </div>
</body>
</html> 