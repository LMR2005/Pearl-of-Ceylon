<?php
session_start();
require_once('includes\db.php');
require_once('includes\functions.php');


if (!isset($_SESSION['username'])) {
    // User not logged in, redirect to login page
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Reservation System | Add Rooms</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="style_admin.css">
    <style>

.add-room-form {
    padding: 30px;
    margin: auto;
}

.add-room-form h3 {
    font-size: 1.5rem;
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

.add-room-form .form-group {
    margin-bottom: 20px;
}

.add-room-form .form-group label {
    font-weight: bold;
    margin-bottom: 5px;
    display: block;
}

.add-room-form .form-group input,
.add-room-form .form-group select,
.add-room-form .form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 1rem;
}

.add-room-form .form-group textarea {
    resize: vertical;
    height: 100px;
}

.add-room-form .submit-btn {
    width: 100%;
    padding: 12px;
    background-color: #2575fc;
    color: white;
    font-size: 1rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.add-room-form .submit-btn:hover {
    background-color: #1b54c9;
}

    </style>
</head>
<body>
    <!-- Navbar Placeholder -->
    <div id="nav-placeholder"></div>

<?php include("includes/sidebar.php"); ?>

    <!-- Admin Dashboard Section -->
    <div class="main-content">
        <section class="admin-dashboard">
          <h2>Add New Rooms</h2>
            <!-- Add Room Form -->
        <div class="recent-bookings">
            <form class="add-room-form" action="add_room.php" method="POST">
                <div class="form-group">
                    <label for="room_number">Room Number</label>
                    <input type="text" id="room_number" name="room_number" required>
                </div>
                <div class="form-group">
                    <label for="room_type">Room Type</label>
                    <select id="room_type" name="room_type" required>
                        <option value="">Select Room Type</option>
                        <option value="Single">Single</option>
                        <option value="Double">Double</option>
                        <option value="Suite">Suite</option>
                        <option value="Luxury">Luxury</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="price">Price per Night (LKR)</label>
                    <input type="number" id="price" name="price" required min="0">
                </div>
                <div class="form-group">
                    <label for="description">Room Description</label>
                    <textarea id="description" name="description"></textarea>
                </div>
                <div class="form-group">
                    <label for="status">Room Status</label>
                    <select id="status" name="status" required>
                        <option value="Available">Available</option>
                        <option value="Occupied">Occupied</option>
                    </select>
                </div>
                <button type="submit" class="submit-btn">Add Room</button>
            </form>
        </div>
    </div>
</body>
</html>