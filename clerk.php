<?php
session_start();

require_once('includes\db.php');


if (!isset($_SESSION['username'])) {
    // User not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Fetch reservations
$reservations_query = "SELECT * FROM `reservations`";
$reservations_result = $conn->query($reservations_query);

$reservations = $conn->query("SELECT reservation_id, customer_id, arrival_date, departure_date, room_type, status FROM reservations WHERE status='active'");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get the reservation ID and new dates from the POST data
    $reservation_id = $_POST['reservationId'];
    $new_checkin_date = $_POST['new_checkin_date'];
    $new_checkout_date = $_POST['new_checkout_date'];

    // Validate the inputs (ensure the new dates are valid)
    if (empty($new_checkin_date) || empty($new_checkout_date)) {
        echo "Both check-in and check-out dates are required!";
    } else {
        // Prepare the SQL query to update the check-in and check-out dates
        $sql = "UPDATE reservations 
                SET arrival_date = ?, departure_date = ? 
                WHERE reservation_id = ?";

        // Prepare the statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind the parameters to the SQL query
            $stmt->bind_param("ssi", $new_checkin_date, $new_checkout_date, $reservation_id);

            // Execute the statement
            if ($stmt->execute()) {
                echo "Reservation dates updated successfully!";
            } else {
                echo "Error: Could not update the reservation dates.";
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Error: Could not prepare the SQL statement.";
        }
    }
} else {
    echo "Invalid request method!";
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Reservation System | Clerk Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="style_admin.css">
    <style>
    .clerk-section {
        margin-bottom: 20px;
    }
    .clerk-form {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }
    .clerk-form .form-group {
        margin-bottom: 15px;
    }
    .clerk-form label {
        display: block;
        margin-bottom: 5px;
        color: #2c3e50;
    }
    .clerk-form input, .clerk-form select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
    }
    .clerk-form .btn {
        background-color: #3498db;
        color: #ffffff;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .clerk-form .btn:hover {
        background-color: #2980b9;
    }
    .reservation-table {
        margin-top: 20px;
    }
    .no-reservations {
        text-align: center;
        color: #666;
        padding: 20px;
    }
    </style>
</head>
<body>
    <!-- Navbar Placeholder -->
    <div id="nav-placeholder"></div>

    <!--Sidebar Section-->
    <?php include("includes/sidebar.php"); ?>

    <!-- Admin Dashboard Section -->
    <div class="main-content">
        <section class="admin-dashboard">
            <h2>Clerk Dashboard</h2>

            <!-- Clerk Sections -->
            <div class="clerk-section">
                <div class="clerk-form">
                    <h3>Check Out change</h3>
                    <form method="POST" action="">
                    <div class="form-group">
                        <select name="reservationId">
                            <option value="">-- Select Reservation --</option>
                            <?php while ($res = $reservations->fetch_assoc()): ?>
                                <option value="<?php echo $res['reservation_id']; ?>">
                                    ID: <?php echo $res['reservation_id']; ?> - Room Type: <?php echo $res['room_type']; ?> 
                                </option>
                            <?php endwhile; ?>
                            </select>
                    </div>
                    <div class="form-group">
                        <label for="newCheckoutDate">New Checkin Date (if changing):</label>
                        <input type="date" id="newCheckinDate" name="new_checkin_date">
                    </div>
                    <div class="form-group">
                        <label for="newCheckoutDate">New Checkout Date (if changing):</label>
                        <input type="date" id="newCheckoutDate" name="new_checkout_date">
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Proces" class="btn">
                    </div>
                    </form>
                </div>
            </div>

            <div class="clerk-section">
                <div class="clerk-form">
                    <h3>Reservations</h3>
                    <div class="reservation-table">
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
                        <div id="noReservations" class="no-reservations" style="display: none;">No reservations found.</div>
                    </div>
                </div>
            </div>

        </section>
    </div>

    <script>
        // JavaScript to handle dynamic active state on sidebar links
        document.addEventListener('DOMContentLoaded', function() {
            const links = document.querySelectorAll('.sidebar a');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    links.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });

        // Fetch and display reservations
        function loadReservations() {
            fetch('get_reservations.php')
                .then(response => response.json())
                .then(data => {
                    const table = document.getElementById('reservationTable');
                    const noReservations = document.getElementById('noReservations');
                    table.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(reservation => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${reservation.reservation_id}</td>
                                <td>${reservation.customer_name}</td>
                                <td>${reservation.room_type}</td>
                                <td>${reservation.arrival_date}</td>
                                <td>${reservation.departure_date}</td>
                                <td>${reservation.status}</td>
                            `;
                            table.appendChild(row);
                        });
                        noReservations.style.display = 'none';
                    } else {
                        noReservations.style.display = 'block';
                    }
                })
                .catch(error => console.error('Error loading reservations:', error));
        }

        // Process Check-In/Check-Out
        function processCheckInOut() {
            const reservationId = document.getElementById('reservationId').value;
            const newCheckoutDate = document.getElementById('newCheckoutDate').value;
            fetch('process_check_in_out.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `reservationId=${encodeURIComponent(reservationId)}&newCheckoutDate=${encodeURIComponent(newCheckoutDate)}`
            })
            .then(response => response.json())
            .then(data => alert(data.message))
            .catch(error => alert('Error: ' + error));
        }

        // Process Billing
        function processBilling() {
            const reservationId = document.getElementById('billingReservationId').value;
            const paymentMethod = document.getElementById('paymentMethod').value;
            fetch('process_billing.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `reservationId=${encodeURIComponent(reservationId)}&paymentMethod=${encodeURIComponent(paymentMethod)}`
            })
            .then(response => response.json())
            .then(data => alert(data.message))
            .catch(error => alert('Error: ' + error));
        }

        // Load reservations on page load
        window.onload = loadReservations;
    </script>
</body>
</html>