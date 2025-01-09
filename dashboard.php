<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Add this at the top of files with navbar, after session_start()
$user_query = "SELECT username, profile_photo FROM users WHERE id = '" . $_SESSION['user_id'] . "'";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

// Fetch user's notes
$user_id = $_SESSION['user_id'];
$notes_query = "SELECT * FROM notes WHERE user_id = '$user_id' ORDER BY created_at DESC";
$notes_result = mysqli_query($conn, $notes_query);

// Fetch shared notes
$shared_notes_query = "SELECT notes.*, users.username as owner_name, shared_notes.can_edit 
                      FROM shared_notes 
                      JOIN notes ON shared_notes.note_id = notes.id 
                      JOIN users ON shared_notes.owner_id = users.id 
                      WHERE shared_notes.shared_with_id = '$user_id'";
$shared_notes_result = mysqli_query($conn, $shared_notes_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Note App</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<nav class="nav">
    <div class="nav-container">
        <a href="dashboard.php" class="nav-brand">
            <i class="fas fa-book-reader"></i>
            NoteApp
        </a>
        <div class="nav-links">
            <a href="dashboard.php">
                <i class="fas fa-home"></i>
                Dashboard
            </a>
            <a href="create_note.php">
                <i class="fas fa-plus-circle"></i>
                New Note
            </a>
            <a href="view_shared_notes.php">
                <i class="fas fa-share-alt"></i>
                Shared Notes
            </a>
            <div class="user-badge">
                <?php if ($user_data['profile_photo']): ?>
                    <img src="<?php echo htmlspecialchars($user_data['profile_photo']); ?>" alt="Profile Photo">
                <?php else: ?>
                    <div class="default-avatar">
                        <?php echo strtoupper(substr($user_data['username'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
                <span class="username"><?php echo htmlspecialchars($user_data['username']); ?></span>
            </div>
            <a href="logout.php">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
    </div>
</nav>
    
    <div class="container">
        <h2>My Notes</h2>
        <div class="notes-grid">
            <?php while ($note = mysqli_fetch_assoc($notes_result)) { ?>
                <div class="note-card">
                    <h3><?php echo htmlspecialchars($note['title']); ?></h3>
                    <p><?php echo substr(htmlspecialchars($note['content']), 0, 100) . '...'; ?></p>
                    <div class="note-actions">
                        <a href="edit_note.php?id=<?php echo $note['id']; ?>" class="btn">Edit</a>
                        <a href="share_note.php?id=<?php echo $note['id']; ?>" class="btn">Share</a>
                        <a href="view_note.php?id=<?php echo $note['id']; ?>" class="btn">Read</a>
                        <a href="delete_note.php?id=<?php echo $note['id']; ?>" class="btn" onclick="return confirm('Are you sure?')">Delete</a>
                    </div>
                </div>
            <?php } ?>
        </div>

        <h2>Shared With Me</h2>
        <div class="notes-grid">
            <?php while ($note = mysqli_fetch_assoc($shared_notes_result)) { ?>
                <div class="note-card">
                    <h3><?php echo htmlspecialchars($note['title']); ?></h3>
                    <p>Shared by: <?php echo htmlspecialchars($note['owner_name']); ?></p>
                    <p><?php echo substr(htmlspecialchars($note['content']), 0, 100) . '...'; ?></p>
                    <?php if ($note['can_edit']) { ?>
                        <a href="edit_note.php?id=<?php echo $note['id']; ?>" class="btn">Edit</a>
                    <?php } ?>
                    <a href="view_note.php?id=<?php echo $note['id']; ?>" class="btn btn-view">
                                <i class="fas fa-eye"></i> Read
                            </a>    
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html> 