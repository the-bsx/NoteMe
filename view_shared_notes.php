<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];



// Fetch the user profile  details
$user_query = "SELECT username, profile_photo FROM users WHERE id = '" . $_SESSION['user_id'] . "'";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

// Fetch shared notes with detailed information
$shared_notes_query = "SELECT 
    notes.*, 
    users.username as owner_name, 
    shared_notes.can_edit,
    shared_notes.shared_at,
    CASE 
        WHEN shared_notes.can_edit = 1 THEN 'Read & Write'
        ELSE 'Read Only'
    END as permission_type
    FROM shared_notes 
    JOIN notes ON shared_notes.note_id = notes.id 
    JOIN users ON shared_notes.owner_id = users.id 
    WHERE shared_notes.shared_with_id = '$user_id'
    ORDER BY shared_notes.shared_at DESC";
$shared_notes_result = mysqli_query($conn, $shared_notes_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shared Notes - Note App</title>
    <link rel="stylesheet" href="./assets/css/style.css">
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
        <div class="page-header">
            <h1><i class="fas fa-share-alt"></i> Shared Notes</h1>
            <p>Notes that others have shared with you</p>
        </div>

        <?php if (mysqli_num_rows($shared_notes_result) == 0): ?>
            <div class="empty-state">
                <i class="fas fa-share-alt fa-3x"></i>
                <h2>No Shared Notes Yet</h2>
                <p>When someone shares a note with you, it will appear here.</p>
            </div>
        <?php else: ?>
            <div class="notes-grid">
                <?php while ($note = mysqli_fetch_assoc($shared_notes_result)): ?>
                    <div class="note-card shared">
                        <div class="note-header">
                            <span class="permission-badge <?php echo $note['can_edit'] ? 'edit' : 'read'; ?>">
                                <i class="fas <?php echo $note['can_edit'] ? 'fa-edit' : 'fa-eye'; ?>"></i>
                                <?php echo $note['permission_type']; ?>
                            </span>
                        </div>
                        <div class="note-content">
                            <h3><?php echo htmlspecialchars($note['title']); ?></h3>
                            <div class="note-meta">
                                <span><i class="fas fa-user"></i> Shared by: <?php echo htmlspecialchars($note['owner_name']); ?></span>
                                <span><i class="fas fa-clock"></i> <?php echo date('M d, Y', strtotime($note['shared_at'])); ?></span>
                            </div>
                            <div class="note-preview">
                                <?php echo nl2br(htmlspecialchars(substr($note['content'], 0, 150)) . '...'); ?>
                            </div>
                        </div>
                        <div class="note-actions">
                            <a href="view_note.php?id=<?php echo $note['id']; ?>" class="btn btn-view">
                                <i class="fas fa-eye"></i> Read
                            </a>
                            <?php if ($note['can_edit']): ?>
                                <a href="edit_note.php?id=<?php echo $note['id']; ?>" class="btn btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 