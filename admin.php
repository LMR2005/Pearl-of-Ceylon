<?php
session_start();
require_once('includes\db.php');
require_once('includes\functions.php');


if (!isset($_SESSION['username'])) {
    // User not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Fetch reservations
$reservations_query = "SELECT * FROM `reservations`";
$reservations_result = $conn->query($reservations_query);

// Fetch Occupancy Rate
$occupancy_query = "SELECT (COUNT(r.assigned_room_id) / (SELECT COUNT(*) FROM rooms)) * 100 AS occupancy_rate 
                    FROM reservations r
                    WHERE r.status = 'confirmed'";


$occupancy_result = $conn->query($occupancy_query);
if ($occupancy_result->num_rows > 0) {
    $occupancy_rate = $occupancy_result->fetch_assoc()['occupancy_rate'];
} else {
    $occupancy_rate = 0;
}

// Fetch Total Revenue
$total_revenue_query = "SELECT SUM(b.amount) AS total_revenue
                        FROM billing b
                        WHERE MONTH(b.billing_date) = MONTH(CURRENT_DATE) 
                        AND YEAR(b.billing_date) = YEAR(CURRENT_DATE)";

$total_revenue_result = $conn->query($total_revenue_query);
if ($total_revenue_result->num_rows > 0) {
    $total_revenue = $total_revenue_result->fetch_assoc()['total_revenue'];
} else {
    $total_revenue = 0;
}

// Fetch Active Bookings
$active_bookings_query = "SELECT COUNT(*) AS active_bookings
                          FROM reservations r
                          WHERE r.status = 'checked_in' 
                          AND CURRENT_DATE BETWEEN r.arrival_date AND r.departure_date";

$active_bookings_result = $conn->query($active_bookings_query);
if ($active_bookings_result->num_rows > 0) {
    $active_bookings = $active_bookings_result->fetch_assoc()['active_bookings'];
} else {
    $active_bookings = 0;
}

$conn->close(); // Close the connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Reservation System | Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="style_admin.css">
</head>
<body>
    <!-- Navbar Placeholder -->
    <div id="nav-placeholder"></div>

<?php include("includes/sidebar.php"); ?>

    <!-- Admin Dashboard Section -->
    <div class="main-content">
        <section class="admin-dashboard">
            <h2>Admin Dashboard</h2>
            <!-- Quick Stats -->
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>Occupancy Rate</h3>
                    <p class="stat-value"><?php echo round($occupancy_rate, 2); ?>%</p>
                    <p class="stat-date">As of <?php echo date('F d, Y'); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Revenue</h3>
                    <p class="stat-value">$<?php echo number_format($total_revenue, 2); ?></p>
                    <p class="stat-date">This Month</p>
                </div>
                <div class="stat-card">
                    <h3>Active Bookings</h3>
                    <p class="stat-value"><?php echo $active_bookings; ?></p>
                    <p class="stat-date">Currently</p>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="recent-bookings">
                <h3>Recent Bookings</h3>
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
                        <tr>

                        <?php while ($row = $reservations_result->fetch_assoc()) { ?>
                        <tr>
                            <td class="border p-2"><?php echo $row['reservation_id']; ?></td>
                            <td class="border p-2"><?php echo $row['room_type']; ?></td>
                            <td class="border p-2"><?php echo $row['arrival_date']; ?></td>
                            <td class="border p-2"><?php echo $row['departure_date']; ?></td>
                            <td class="border p-2"><?php echo $row['status']; ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <a href="reports.php" class="view-reports-btn">View Detailed Reports</a>
            </div>
        </section>
    </div>
</body>
</html>