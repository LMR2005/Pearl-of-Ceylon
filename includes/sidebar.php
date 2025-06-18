<?php 
require_once("includes/functions.php");
?>

<div class="sidebar">
        <div class="sidebar-logo">
            <a href="index.php">Pearl of Ceylon</a>
        </div>
        <ul class="sidebar-menu">
            <?php if(isManager()): ?>
            <li class="sidebar-item"><a href="admin.php" class="sidebar-link">Admin Dashboard</a></li>
            <li class="sidebar-item"><a href="reports.php" class="sidebar-link">Reports</a></li>
            <li class="sidebar-item"><a href="travel-booking.php" class="sidebar-link">Group Booking</a></li>
            <li class="sidebar-item"><a href="rooms.php" class="sidebar-link">Add Rooms</a></li>
            <?php endif; ?>
            <?php if(isClerk()): ?>
            <li class="sidebar-item"><a href="clerk.php" class="sidebar-link">Dashboard</a></li>
            <?php endif; ?>
            <?php if(isUser()): ?>
            <li class="sidebar-item"><a href="customer-dashboard.php" class="sidebar-link">My Dashboard</a></li>
            <?php endif; ?>

            <li class="sidebar-item"><a href="view_reserve.php" class="sidebar-link">View Reservation</a></li>
            <li class="sidebar-item"><a href="reservation.php" class="sidebar-link">Make Reservation</a></li>
            <li class="sidebar-item"><a href="residentialsuites.php" class="sidebar-link">Residential Suites</a></li>
            
            <?php if(isManager()): ?>
                <li class="sidebar-item"><a href="messages.php" class="sidebar-link">Messages From users</a></li>
            <?php endif; ?>

            <?php if(isUser()): ?>
            <li class="sidebar-item"><a href="paynow.php" class="sidebar-link">Pay Now</a></li>
            <li class="sidebar-item"><a href="billing.php" class="sidebar-link">Billing History</a></li>
            <?php endif; ?>
            <?php if(isManager() || isClerk()): ?>
            <li class="sidebar-item"><a href="checkin.php" class="sidebar-link ">Check-in</a></li>
            <li class="sidebar-item"><a href="checkout.php" class="sidebar-link ">Check-out</a></li>
            <?php endif; ?>
            <li class="sidebar-item"><a href="profile.php" class="sidebar-link">Profile</a></li>
            <li class="sidebar-item"><a href="logout.php" class="sidebar-link">Logout</a></li>
        </ul>
    </div>