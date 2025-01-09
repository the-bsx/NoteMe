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
$check_query = "SELECT * FROM notes WHERE id = '$note_id' AND user_id = '$user_id'";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) > 0) {
    // Delete shared notes first
    $delete_shares = "DELETE FROM shared_notes WHERE note_id = '$note_id'";
    mysqli_query($conn, $delete_shares);
    
    // Delete the note
    $delete_query = "DELETE FROM notes WHERE id = '$note_id'";
    mysqli_query($conn, $delete_query);
}

header('Location: dashboard.php');
// exit();
?> 