<?php
session_start();

require_once('includes\db.php');


if (!isset($_SESSION['username'])) {
    // User not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Fetch reservations
$reservations_query = "SELECT * FROM `reservations` WHERE arrival_date > CURDATE() ORDER BY `arrival_date` ASC;";
$reservations_result = $conn->query($reservations_query);


$username = $_SESSION['username'];
date_default_timezone_set('Asia/Colombo');  
$currentTime = new DateTime();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Reservation System | Customer Dashboard</title>
    <link rel="stylesheet" href="style_admin.css">
    <link rel="stylesheet" href="customer-style.css">
</head>
<body>

    <!-- Sidebar -->
    <?php include("includes/sidebar.php"); ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Customer Dashboard Section -->
        <section class="customer-dashboard">
            <h2>Welcome, <?php echo htmlspecialchars($username); ?> </h2> 
            <p class="dashboard-date"> <?php echo $currentTime->format('Y-m-d H:i:s'); ?> </p>

            <!-- Upcoming Bookings -->
            <div class="dashboard-section">
                <h3>Upcoming Bookings</h3>
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Room Type</th>
                            <th>Check-In</th>
                            <th>Check-Out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                          <?php while ($row = $reservations_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row["reservation_id"] ?> </td>
                            <td><?php echo $row["room_type"] ?></td>
                            <td><?php echo $row["arrival_date"] ?></td>
                            <td><?php echo $row["departure_date"] ?></td>
                            <td><?php echo $row["status"] ?></td>
                        </tr>
                         <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>


    <!-- Role-Based Sidebar Logic -->
    <script>
        const userRole = 'customer'; // Set to 'customer' for this dashboard
        const managerLinks = document.querySelectorAll('.manager-only');
        const travelLinks = document.querySelectorAll('.travel-only');

        if (userRole !== 'manager') {
            managerLinks.forEach(link => link.style.display = 'none');
        }
        if (userRole !== 'travel') {
            travelLinks.forEach(link => link.style.display = 'none');
        }
    </script>
</body>
</html>