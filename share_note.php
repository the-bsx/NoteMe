<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$note_id = mysqli_real_escape_string($conn, $_GET['id']);
$user_id = $_SESSION['user_id'];

// Verify note ownership
$note_query = "SELECT * FROM notes WHERE id = '$note_id' AND user_id = '$user_id'";
$note_result = mysqli_query($conn, $note_query);

if (mysqli_num_rows($note_result) == 0) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $share_with_email = mysqli_real_escape_string($conn, $_POST['email']);
    $can_edit = isset($_POST['can_edit']) ? 1 : 0;
    
    // Get user ID of the person to share with
    $user_query = "SELECT id FROM users WHERE email = '$share_with_email'";
    $user_result = mysqli_query($conn, $user_query);
    
    if ($share_user = mysqli_fetch_assoc($user_result)) {
        $share_with_id = $share_user['id'];
        
        // Check if already shared
        $check_query = "SELECT * FROM shared_notes WHERE note_id = '$note_id' AND shared_with_id = '$share_with_id'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) == 0) {
            $share_query = "INSERT INTO shared_notes (note_id, owner_id, shared_with_id, can_edit) 
                           VALUES ('$note_id', '$user_id', '$share_with_id', '$can_edit')";
            if (mysqli_query($conn, $share_query)) {
                $success = "Note shared successfully";
            } else {
                $error = "Failed to share note";
            }
        } else {
            $error = "Note already shared with this user";
        }
    } else {
        $error = "User not found";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Share Note - Note App</title>
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
            <h2>Share Note</h2>
            <?php 
            if (isset($error)) echo "<p style='color: red'>$error</p>";
            if (isset($success)) echo "<p style='color: green'>$success</p>";
            ?>
            <form method="POST">
                <div class="form-group">
                    <label>Share with (email)</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="can_edit">
                        Allow editing
                    </label>
                </div>
                <button type="submit" class="btn">Share Note</button>
            </form>
        </div>
    </div>
</body>
</html> 