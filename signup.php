<?php
session_start();
include 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $profile_photo = null;
    
    // Handle photo upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_photo']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            $temp_name = $_FILES['profile_photo']['tmp_name'];
            $new_filename = uniqid() . '.' . $filetype;
            $upload_path = 'uploads/profiles/' . $new_filename;
            
            // Create directory if it doesn't exist
            if (!file_exists('uploads/profiles')) {
                mkdir('uploads/profiles', 0777, true);
            }
            
            if (move_uploaded_file($temp_name, $upload_path)) {
                $profile_photo = $upload_path;
            }
        }
    }
    
    $check_query = "SELECT * FROM users WHERE email = '$email' OR username = '$username'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error = "Email or username already exists";
    } else {
        $query = "INSERT INTO users (username, email, password, profile_photo) VALUES ('$username', '$email', '$password', " . ($profile_photo ? "'$profile_photo'" : "NULL") . ")";
        if (mysqli_query($conn, $query)) {
            header('Location: login.php');
            exit();
        } else {
            $error = "Registration failed";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up - Note App</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Sign Up</h2>
            <?php if (isset($error)) echo "<p style='color: red'>$error</p>"; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Profile Photo (Optional)</label>
                    <div class="file-input-container">
                        <input type="file" name="profile_photo" accept="image/*" id="profile_photo">
                        <label for="profile_photo" class="file-label">
                            <i class="fas fa-upload"></i> Choose Photo
                        </label>
                        <span id="file-name">No file chosen</span>
                    </div>
                </div>
                <button type="submit" class="btn">Sign Up</button>
            </form>
            <p>Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>

    <script>
        document.getElementById('profile_photo').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'No file chosen';
            document.getElementById('file-name').textContent = fileName;
        });
    </script>
</body>
</html>