<?php
session_start();

require_once('includes\db.php');
require_once('includes\functions.php');


if (!isset($_SESSION['username'])) {
    // User not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_date'])) {
    // Handle the form submission for updating reservation dates
    $reservation_id = $_POST['reservation_id'];
    $room_type = $_POST['room_type'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    // Update the reservation in the database
    $sql = "UPDATE reservations SET room_type = ?, arrival_date = ?, departure_date = ? WHERE reservation_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $room_type, $check_in, $check_out, $reservation_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Reservation updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating reservation: " . $stmt->error;
    }
    $stmt->close();
}

$username_to_search = $_SESSION['username'];  

$sql = "SELECT user_id FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username_to_search);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// Check if user was found
if ($user_id) {
    $sql = "SELECT customer_id FROM customers WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($customer_id);
    $stmt->fetch();
    $stmt->close();

    // Check if customer was found
    if ($customer_id) {

        // Query for reservations related to customer_id
        if(isUser()) {
            $sql = "SELECT * FROM reservations WHERE customer_id = ? ORDER BY arrival_date ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $customer_id);
        } else {
            // If no customer_id found, fetch all reservations
            $sql = "SELECT * FROM reservations ORDER BY arrival_date ASC";
            $stmt = $conn->prepare($sql);
        }
        $stmt->execute();
        $result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Reservation System | Customer Dashboard</title>
    <link rel="stylesheet" href="customer-style.css">
    <link rel="stylesheet" href="style_admin.css">
    <style>
        .btn { 
            padding: 8px 16px; 
            border-radius: 25px; 
            border: none; 
            cursor: pointer; 
            font-weight: 600; 
        }

        .btn-primary { 
            background: #2575fc; 
            color: white; 
        }

        .btn-primary:hover { 
            background: #1b54c9; 
        }

        .btn-danger { 
            background: #ff4d4d; 
            color: white; 
        }

        .btn-danger:hover { 
            background: #d93636; 
        }

        /* The Modal (background) */
    .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            position: relative;
        }
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            cursor: pointer;
        }
        .modal-content h2 {
            margin-top: 0;
        }
        .modal-content input {
            width: 95%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .modal-content button {
            background-color: #1a3c5e;
            color: white;
            margin-top: 20px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            width: 100%;
            cursor: pointer;
        }
        .modal-content button:hover {
            background-color: #2a5c8e;
        }

    /* Form Inputs */
    input[type="text"], input[type="date"], select {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        box-sizing: border-box;
    }

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

    <!-- Sidebar -->
    <?php include("includes/sidebar.php"); ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Customer Dashboard Section -->
        <section class="customer-dashboard">
            <h2>All Reservations</h2> 
            <p class="dashboard-date"> Modify and Cancel your reservations</p>

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


            <!-- Upcoming Bookings -->
            <div class="dashboard-section">
                <h3>All Bookings</h3>
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Reservation ID</th>
                            <th>Room Type</th>
                            <th>Check-In</th>
                            <th>Check-Out</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php // Check if there are any reservations
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) { 

                                    $sql = "SELECT room_number FROM rooms WHERE room_id = ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("i", $row['assigned_room_id']);
                                    $stmt->execute();
                                    $stmt->bind_result($room_no);
                                    $stmt->fetch();
                                    $stmt->close(); ?>
                                    <tr>
                                        <td><?php echo $row["reservation_id"]; ?></td>
                                        <td><?php echo $row["room_type"]; ?></td>
                                        <td><?php echo $row["arrival_date"]; ?></td>
                                        <td><?php echo $row["departure_date"]; ?></td>
                                        <td><?php echo $row["status"]; ?></td>
                                        <td>
                                            <button class='btn btn-primary modify-btn' onclick='openModal(<?php echo $row["reservation_id"]; ?>,"<?php echo $row["room_type"]?>", "<?php echo $row["arrival_date"] ?>", "<?php echo $row["departure_date"]?>")'>Modify</button>
                                            <form action='cancel_reservation.php' method='POST' style='display:inline;'>
                                                <input type='hidden' name='reservation_id' value='" . $row["reservation_id"] . "' />
                                                <button type='submit' class='btn btn-danger'>Cancel</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                }
                            }
                        } ?>
                    </tbody>
                </table>
            </div>
        </section>


        <div id="modifyModal" class="modal">
        <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h3>Modify Reservation</h3>
        <form id="modifyForm" method="POST" action="">
            <input type="hidden" name="reservation_id" id="reservation_id" />
            <label for="room_type">Room Type:</label>
            <select name="room_type" id="room_type">
                <option value="single">Single</option>
                <option value="double">Double</option>
                <option value="residential_suite">Residential_suite</option>
            </select>

            <label for="check_in">Check-In Date:</label>
            <input type="date" id="check_in" name="check_in" required />

            <label for="check_out">Check-Out Date:</label>
            <input type="date" id="check_out" name="check_out" required />

            <button type="submit" class="btn btn-success" name="update_date">Save Changes</button>
        </form>
    </div>
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

        function openModal(reservationId, roomType, checkIn, checkOut) {
            document.getElementById('reservation_id').value = reservationId; 
            document.getElementById('room_type').value = roomType;
            document.getElementById('check_in').value = checkIn;
            document.getElementById('check_out').value = checkOut;
            document.getElementById('modifyModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('modifyModal').style.display = 'none';
        }
    </script>

</body>
</html>