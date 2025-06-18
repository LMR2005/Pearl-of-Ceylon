<?php
session_start();
require_once('includes/db.php');
require_once('includes/functions.php');

$error = $sucess = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['uname']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if userna  e or email already exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or email already exists.";
        } else {
            // Get role_id for 'user' by default
            $role_stmt = $conn->prepare("SELECT role_id FROM roles WHERE role_name = 'user'");
            $role_stmt->execute();
            $role_stmt->bind_result($role_id);
            $role_stmt->fetch();
            $role_stmt->close();

            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $insert_stmt = $conn->prepare("INSERT INTO users (username, password_hash, email, role_id) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("sssi", $username, $password_hash, $email, $role_id);
            if ($insert_stmt->execute()) {
                $_SESSION['message'] = "Registration successful! Please login.";
                header("Location: login.php");
                exit;
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="register_style.css">
    <title>Hotel Reservation System | Sign Up</title>
</head>
<body>
    <!--Navigation bar Code Starts-->
    <nav class="navbar">
        <div class="logo">
            <a href="index.php">Pearl of Ceylon</a>
        </div>
        <div class="menu-toggle" id="mobile-menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
            <li class="nav-item"><a href="gallery.php" class="nav-link">Gallery</a></li>
            <li class="nav-item"><a href="contacts.php" class="nav-link">Contact Us</a></li>
            <li class="nav-item"><a href="register.php" class="nav-link login-btn">Sign up</a></li>
            <li class="nav-item"><a href="login.php" class="nav-link login-btn">Sign in</a></li>
        </ul>
    </nav>
    <!--Navigation bar Code Ends-->

    <div class="container">
        <div class="image-section"></div>
        <form class="form-section" action="" method="POST">
            <h2>Register</h2>

            <div class="form-group">
                <label for="name">Username</label>
                <input type="text" id="uname" name="uname" placeholder="Enter username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" required>
            </div>

            <?php if (isset($error)): ?>
                <p style="color:red;"><?php echo $error; ?></p>
            <?php endif; ?>


            <button type="submit">Register</button>
        </form>
    </div>

    <?php include("includes\\footer.php"); ?>
</body>
</html>