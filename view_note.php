<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$note_id = mysqli_real_escape_string($conn, $_GET['id']);
$user_id = $_SESSION['user_id'];

// Check if the user has access to this note (owner or shared)
$note_query = "SELECT 
    notes.*,
    users.username as owner_name,
    CASE 
        WHEN notes.user_id = '$user_id' THEN 'owner'
        WHEN shared_notes.shared_with_id IS NOT NULL THEN 'shared'
        ELSE 'none'
    END as access_type,
    shared_notes.can_edit,
    DATE_FORMAT(notes.created_at, '%M %d, %Y at %h:%i %p') as formatted_date,
    DATE_FORMAT(notes.updated_at, '%M %d, %Y at %h:%i %p') as updated_date
    FROM notes 
    LEFT JOIN shared_notes ON notes.id = shared_notes.note_id AND shared_notes.shared_with_id = '$user_id'
    LEFT JOIN users ON notes.user_id = users.id
    WHERE notes.id = '$note_id' 
    AND (notes.user_id = '$user_id' OR shared_notes.shared_with_id = '$user_id')";

$result = mysqli_query($conn, $note_query);

if (mysqli_num_rows($result) == 0) {
    header('Location: dashboard.php');
    exit();
}

$note = mysqli_fetch_assoc($result);

// Get share information if user is owner
$shares = [];
if ($note['access_type'] == 'owner') {
    $shares_query = "SELECT 
        shared_notes.*,
        users.username,
        users.email
        FROM shared_notes 
        JOIN users ON shared_notes.shared_with_id = users.id
        WHERE note_id = '$note_id'";
    $shares_result = mysqli_query($conn, $shares_query);
    while ($share = mysqli_fetch_assoc($shares_result)) {
        $shares[] = $share;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Note - Note App</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .note-view-container {
            max-width: 800px;
            margin: 2rem auto;
            background: var(--surface);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .note-view-header {
            padding: 2rem;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            background: rgba(0,0,0,0.02);
        }

        .note-view-content {
            padding: 2rem;
            min-height: 300px;
            white-space: pre-wrap;
            line-height: 1.8;
        }

        .note-view-meta {
            padding: 1.5rem 2rem;
            background: rgba(0,0,0,0.02);
            border-top: 1px solid rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .share-list {
            margin-top: 2rem;
            background: var(--surface);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
        }

        .share-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .share-item:last-child {
            border-bottom: none;
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .badge-owner {
            background: rgba(46,204,113,0.1);
            color: #2ecc71;
        }

        .badge-shared {
            background: rgba(52,152,219,0.1);
            color: #3498db;
        }

        .note-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        @media (prefers-color-scheme: dark) {
            .note-view-header, .note-view-meta {
                background: rgba(255,255,255,0.03);
                border-color: rgba(255,255,255,0.1);
            }
            
            .share-item {
                border-color: rgba(255,255,255,0.1);
            }
        }
    </style>
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
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="note-view-container">
            <div class="note-view-header">
                <h1><?php echo htmlspecialchars($note['title']); ?></h1>
                <div class="note-actions">
                    <?php if ($note['access_type'] == 'owner' || $note['can_edit']): ?>
                        <a href="edit_note.php?id=<?php echo $note['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Note
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($note['access_type'] == 'owner'): ?>
                        <a href="share_note.php?id=<?php echo $note['id']; ?>" class="btn btn-secondary">
                            <i class="fas fa-share-alt"></i> Share Note
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="note-view-content">
                <?php echo nl2br(htmlspecialchars($note['content'])); ?>
            </div>

            <div class="note-view-meta">
                <div>
                    <p>Created: <?php echo $note['formatted_date']; ?></p>
                    <p>Last updated: <?php echo $note['updated_date']; ?></p>
                </div>
                <span class="badge <?php echo $note['access_type'] == 'owner' ? 'badge-owner' : 'badge-shared'; ?>">
                    <?php 
                    if ($note['access_type'] == 'owner') {
                        echo '<i class="fas fa-user"></i> Owner';
                    } else {
                        echo '<i class="fas fa-share-alt"></i> Shared by ' . htmlspecialchars($note['owner_name']);
                        echo $note['can_edit'] ? ' (Can Edit)' : ' (Read Only)';
                    }
                    ?>
                </span>
            </div>
        </div>

        <?php if ($note['access_type'] == 'owner' && !empty($shares)): ?>
        <div class="share-list">
            <h2>Shared With</h2>
            <?php foreach ($shares as $share): ?>
            <div class="share-item">
                <div>
                    <strong><?php echo htmlspecialchars($share['username']); ?></strong>
                    <p><?php echo htmlspecialchars($share['email']); ?></p>
                </div>
                <span class="badge <?php echo $share['can_edit'] ? 'badge-owner' : 'badge-shared'; ?>">
                    <?php echo $share['can_edit'] ? 'Can Edit' : 'Read Only'; ?>
                </span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>