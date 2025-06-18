<?php
session_start();

require_once('includes\db.php');
require_once('includes\functions.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, password_hash, role_name FROM users 
                            JOIN roles ON users.role_id = roles.role_id 
                            WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_id, $password_hash, $role_name);

    if ($stmt->fetch() && password_verify($password, $password_hash)) {
        // Login success
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role_name;

        if (isUser()){
            header("Location: customer-dashboard.php");
            exit;
        }
        else if (isManager()) {
            header("Location: admin.php");
        }
        else if (isClerk()) {
            header("Location: clerk.php");
        }

    } else {
        $error = "Invalid username or password.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="register_style.css">
    <title>Hotel Reservation System | Sign In</title>
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
            <div style="height:100px;"></div>
            <h2>Log In</h2>
            <div class="form-group">
                <label for="name">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <?php if (isset($error)): ?>
                <p style="color:red;"><?php echo $error; ?></p>
            <?php endif; ?>

            <?php if (isset($_SESSION['message'])): ?>
                <p style="color:green;"><?php echo $_SESSION['message'];
                                        unset($_SESSION['message']); ?></p>
            <?php endif; ?>

            <button type="submit">Log in</button>
            <div style="height:100px;"></div>
        </form>
    </div>

    <?php include("includes\\footer.php"); ?>
</body>
</html>