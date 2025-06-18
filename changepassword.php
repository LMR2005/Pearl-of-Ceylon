<?php
session_start();

require_once('includes\db.php');
require_once('includes\functions.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Get user input from the form
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
        $user = $result->fetch_assoc();
        
        // Verify if the current password is correct
        if (password_verify($current_password, $user['password_hash'])) {
            // Check if the new passwords match
            if ($new_password == $confirm_password) {
                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update the password in the database
                $update_query = "UPDATE users SET password_hash = ? WHERE user_id = ?";
                $stmt_update = $conn->prepare($update_query);
                $stmt_update->bind_param('si', $hashed_password, $user_id);
                
                if ($stmt_update->execute()) {
                    echo "Password updated successfully!";
                } else {
                    echo "Error updating password!";
                }
            } else {
                echo "New password and confirm password do not match!";
            }
        } else {
            echo "Incorrect current password!";
        }
    } else {
        echo "User not found!";
    }

    $stmt->close();
    $stmt_update->close();
    $conn->close();
}