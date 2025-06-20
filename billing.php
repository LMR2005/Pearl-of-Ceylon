<?php
session_start();

require_once('includes\db.php');


if (!isset($_SESSION['username'])) {
    // User not logged in, redirect to login page
    header("Location: login.php");
    exit();
}


$username = $_SESSION['username'];
date_default_timezone_set('Asia/Colombo');  
$currentTime = new DateTime();


// Fetch customer_id based on username (join users and customers)
$sql = "SELECT c.customer_id
        FROM users u
        JOIN customers c ON u.user_id = c.user_id
        WHERE u.username = ?";

// Prepare and execute the first query
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($customer_id);
$stmt->fetch();
$stmt->close();

// Check if customer_id exists
if ($customer_id) {
    // Fetch reservation_id based on customer_id
    $sql_reservation = "SELECT r.reservation_id
                        FROM reservations r
                        WHERE r.customer_id = ?";
    
    $stmt_reservation = $conn->prepare($sql_reservation);
    $stmt_reservation->bind_param("i", $customer_id);
    $stmt_reservation->execute();
    $stmt_reservation->bind_result($reservation_id);
    $stmt_reservation->fetch();
    $stmt_reservation->close();

    // Fetch billing data based on reservation_id
    if ($reservation_id) {
        $sql_billing = "SELECT b.billing_id, b.amount, b.paid, b.payment_method, b.billing_date
                        FROM billing b
                        WHERE b.reservation_id = ?";

        $stmt_billing = $conn->prepare($sql_billing);
        $stmt_billing->bind_param("i", $reservation_id);
        $stmt_billing->execute();
        $result_billing = $stmt_billing->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Reservation System | Billing History</title>
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
            <h2>Billing History </h2> 

            <!-- Upcoming Bookings -->
            <div class="dashboard-section">
                <h3>Upcoming Bookings</h3>
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Bill ID</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Billing Date</th>
                        </tr>
                    </thead>
                    <tbody>
                          <?php if ($result_billing->num_rows > 0) {
                            while ($row = $result_billing->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row["billing_id"] ?> </td>
                            <td><?php echo $row["amount"] ?></td>
                            <td><?php echo $row["payment_method"] ?></td>
                            <td><?php echo $row["billing_date"] ?></td>
                        </tr>
                         <?php }
                         $stmt_billing->close();
                             }
                        }
                     } ?>
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