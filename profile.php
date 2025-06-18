<?php
session_start();

require_once('includes/db.php');
require_once('includes/functions.php');

$error = $sucess = "";

if (!isset($_SESSION['username'])) {
    // User not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the user details based on the logged-in user ID
$users_query = "SELECT * FROM `users` WHERE `user_id` = ?";
$stmt = $conn->prepare($users_query);
$stmt->bind_param('i', $user_id);  // Bind the user_id to the prepared statement
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "No user found!";
    exit();
}

$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $email = $_POST['email'];

    // Update profile details
    $update_query = "UPDATE `users` SET `email` = ? WHERE `user_id` = ?";
    $stmt_update = $conn->prepare($update_query);
    $stmt_update->bind_param('si', $email, $user_id);
    
    if ($stmt_update->execute()) {
        $_SESSION['success'] = "Profile updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating profile!";
    }
    $stmt_update->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch the current password hash from the database
    $query = "SELECT password_hash FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        
        // Verify if the current password is correct
        if (password_verify($current_password, $user_data['password_hash'])) {
            // Check if the new passwords match
            if ($new_password == $confirm_password) {
                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update the password in the database
                $update_query = "UPDATE users SET password_hash = ? WHERE user_id = ?";
                $stmt_update = $conn->prepare($update_query);
                $stmt_update->bind_param('si', $hashed_password, $user_id);
                
                if ($stmt_update->execute()) {
                    $_SESSION['success'] = "Password changed successfully!";
                } else {
                    $_SESSION['error'] = "Error changing password!";
                }
            } else {
                $_SESSION['error'] = "New password and confirm password do not match!";
            }
        } else {
            $_SESSION['error'] = "Incorrect current password!";
        }
    } else {
        $_SESSION['error'] = "User not found!";
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Reservation System | Profile Page</title>
    <link rel="stylesheet" href="style_admin.css">
    <link rel="stylesheet" href="customer-style.css">
    <style>
        /* Error and Success Messages */
.error-message {
    background-color: #f8d7da;
    color: #721c24;
    padding: 15px;
    border: 1px solid #f5c6cb;
    border-radius: 5px;
    margin-bottom: 20px;
    font-size: 14px;
}

.success-message {
    background-color: #d4edda;
    color: #155724;
    padding: 15px;
    border: 1px solid #c3e6cb;
    border-radius: 5px;
    margin-bottom: 20px;
    font-size: 14px;
}

    </style>
</head>
<body>
    <?php include("includes/sidebar.php"); ?>
    <div class="main-content">
        <section class="admin-dashboard">
            <h2>Profile Settings</h2>

       <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="error-message">'.$_SESSION['error'].'</div>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<div class="success-message">'.$_SESSION['success'].'</div>';
            unset($_SESSION['success']);
        }
        ?>

            <div class="recent-bookings">
                <h3></h3>
                <form method="POST" action="" class="form-container">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>

        <div class="form-group">
            <button type="submit" class="submit-btn" name="update_profile">Update Profile</button>
        </div>
    </form>
</div>

<div style="height:30px"></div>
            <div class="recent-bookings">
            <h3>Change Password</h3>
                <!-- Change Password Form -->
                <form method="POST" action="changepassword.php" class="form-container">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="submit-btn" name="change_password">Change Password</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</body>
</html>