<?php
session_start();

require_once('includes\db.php');
require_once('includes\functions.php');


if (!isset($_SESSION['username'])) {
    // User not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submission for saving card details
    $reservationId = $_POST['reservationId']; // Get the reservation ID from the form
    $cardNumber = $_POST['cardNumber'];
    $cardHolder = $_POST['cardHolder'];
    $expiryDate = $_POST['expiryDate'];
    $cvv = $_POST['cvv'];
    $price = $_POST['totprice']; // Get the total price from the form

    $cardDetails = "Card Number: $cardNumber, Card Holder: $cardHolder, Expiry Date: $expiryDate, CVV: $cvv";

    // SQL query to update the reservation with the card details and set have_credit_card to 1
    $sql = "UPDATE reservations 
            SET credit_card_details = ?, credit_card_provided = 1
            WHERE reservation_id = ?";

    // Prepare and execute the query
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $cardDetails, $reservationId);

    if ($stmt->execute()) {
        // Set a session success message or show a confirmation
        $_SESSION['success'] = "Payment done successfully!";
    } else {
        // Set an error message if the query fails
        $_SESSION['error'] = "Error processing payment. Please try again.";
    }

    $stmt->close();
    // Insert into billing table
    $sql = "INSERT INTO billing (reservation_id, amount, paid, payment_method, billing_date, additional_charges) VALUES (?, ?, 1, 'credit_card', NOW(), NULL)";

    // Prepare and execute the query
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $reservationId, $price);
    $stmt->execute();
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
            $sql = "SELECT * FROM reservations WHERE customer_id = ? AND credit_card_details IS NULL ORDER BY arrival_date ASC";
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
    <title>Pay Now - Pearl of Ceylon</title>
    <link rel="stylesheet" href="customer-style.css">
    <link rel="stylesheet" href="style_admin.css">
    <style>
        .add-card-btn {
            margin-left: 1050px;
            background-color: #1a3c5e;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .add-card-btn:hover {
            background-color: #2a5c8e;
        }
        .pay-btn {
            background-color: #1a3c5e;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .pay-btn:hover {
            background-color: #2a5c8e;
        }
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
            <button class="add-card-btn" onclick="openModal()" hidden>Add Card</button>
            <h2>Pay Now</h2> 
            <p class="dashboard-date"> Make payments for your reservations</p>

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
                <h3>Reservations list</h3>
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Room No.</th>
                            <th>Room Type</th>
                            <th>Price</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php // Check if there are any reservations
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) { 

                                $sql = "SELECT room_number, price_per_night FROM rooms WHERE room_id = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $row['assigned_room_id']);
                                $stmt->execute();
                                $stmt->bind_result($room_no , $price_per_night);
                                $stmt->fetch();
                                $stmt->close(); ?>

                                <?php
                                $check_in_date = $row['arrival_date'];
                                $check_out_date = $row['departure_date'];
                                $reservationID = $row['reservation_id'];

                                // Convert the string dates to DateTime objects
                                $check_in = new DateTime($check_in_date);
                                $check_out = new DateTime($check_out_date);

                                // Calculate the difference between check-in and check-out dates
                                $interval = $check_in->diff($check_out);

                                // Get the number of nights
                                $number_of_nights = $interval->days;
                                $total_price = $price_per_night * $number_of_nights;
                                ?>
                        <tr>
                            <td><?php echo $room_no ?></td>
                            <td><?php echo $row['room_type']; ?></td>
                            <td><?php echo $total_price ?></td>
                            <td><?php echo $row['arrival_date']; ?></td>
                            <td><?php echo $row['departure_date'] ?></td>
                            <td><?php echo $row['status'] ?></td>
                            <td>
                                <button class='btn btn-primary' onclick="openModal(<?php echo($reservationID) ?> , <?php echo($total_price) ?>)">Pay Now</button>
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


    <div class="modal" id="cardModal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">×</span>
            <h2>Add Card Details</h2>
            <form action="" method="POST">
                <input type="text" id="reservationId" name="reservationId" placeholder="Reservation Number" hidden>
                <input type="text" id="totprice" name="totprice" placeholder="Total Price" hidden>
                <input type="text" id="cardNumber" name="cardNumber" placeholder="Card Number" required>
                <input type="text" id="cardHolder" name="cardHolder" placeholder="Card Holder Name" required>
                <input type="text" id="expiryDate" name="expiryDate" placeholder="Expiry Date (MM/YY)" required>
                <input type="text" id="cvv" name="cvv" placeholder="CVV" required>
                <button type="submit">Save Card & Pay Now</button>
            </form>
        </div>
    </div>

    <script>
        let hasCard = false;

        function openModal(reservationId, totalPrice) {
            document.getElementById('reservationId').value = reservationId;
            document.getElementById('totprice').value = totalPrice;
            document.getElementById('cardModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('cardModal').style.display = 'none';
        }
    </script>
</body>
</html>