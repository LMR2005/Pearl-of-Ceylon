<?php
session_start();

require_once('includes\db.php');


if (!isset($_SESSION['username'])) {
    // User not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT customer_id FROM customers WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($customer_id);
    $stmt->fetch();
    $stmt->close();

    if (!$customer_id) {
        $error = "Please create a customer profile first.";
    } else {
        $occupants = intval($_POST['occupants']);
        $stay_type = $_POST['stay_type']; // 'weekly' or 'monthly'
        $start_date = $_POST['start_date'];
        $weeks = $stay_type === 'weekly' ? intval($_POST['duration']) : 0;
        $months = $stay_type === 'monthly' ? intval($_POST['duration']) : 0;

        $end_date = date('Y-m-d', strtotime($start_date . " + $weeks week + $months month"));

        $price_per_unit = $stay_type === 'weekly' ? 700 : 2500; // example prices
        $total_amount = ($weeks * $price_per_unit) + ($months * $price_per_unit);

        // Insert reservation for residential suite
        $stmt = $conn->prepare("INSERT INTO reservations (customer_id, room_type, occupants, arrival_date, departure_date, credit_card_provided) VALUES (?, 'residential_suite', ?, ?, ?, 0)");
        $stmt->bind_param("isss", $customer_id, $occupants, $start_date, $end_date);

        if ($stmt->execute()) {
            $success = "Residential suite booked from $start_date to $end_date.";
        } else {
            $error = "Failed to book suite.";
        }
    }
}

// Fetch reservations
$reservations_query = "SELECT * FROM `reservations` WHERE arrival_date > CURDATE();";
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
    <title>Hotel Reservation System | Residential Suites</title>
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
            <h2>Book Residential Suites</h2> 

            <!-- Upcoming Bookings -->
            <div class="dashboard-section">
                <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
                <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

                <div class="form-container">
                <form method="POST">
                    <div class="form-group">
                    <label>Number of Occupants</label>
                    <input type="number" name="occupants" min="1" required>
                    </div>
                    
                    <div class="form-group">
                    <label>Stay Type</label>
                    <select name="stay_type" id="stay_type" required>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                    </div>

                    <div class="form-group">
                    <label>Duration (<span id="duration_label">Weeks</span>)</label>
                    <input type="number" name="duration" min="1" value="1" required>
                    </div>

                    <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" name="start_date" required>
                    </div>

                    <div class="form-group">
                    <button type="submit" class="submit-btn">Book Suite</button>
                    </div>
                </form>
                </div>
            </div>
        </section>
    </div>

    <script>
    document.getElementById('stay_type').addEventListener('change', function() {
        const label = document.getElementById('duration_label');
        label.textContent = this.value === 'weekly' ? 'Weeks' : 'Months';
    });
    </script>

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