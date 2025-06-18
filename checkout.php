<?php
session_start();

require_once('includes\auth.php');
require_once('includes\db.php');
require_once('includes\functions.php');

if (!isset($_SESSION['username'])) {
    // User not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

$error = $success = "";

// Handle checkout form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservation_id = intval($_POST['reservation_id']);
    $payment_method = $_POST['payment_method'];
    $additional_charges = sanitize($_POST['additional_charges']);
    $additional_amount = floatval($_POST['additional_amount']);

    // Fetch reservation and assigned room
    $stmt = $conn->prepare("SELECT assigned_room_id FROM reservations WHERE reservation_id=? AND status='checked_in'");
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $stmt->bind_result($room_id);
    if (!$stmt->fetch()) {
        $error = "Invalid reservation or not checked in.";
    }
    $stmt->close();

    if (!$error) {
        // Calculate total amount (room price + additional charges)
        $stmt = $conn->prepare("SELECT price_per_night FROM rooms WHERE room_id=?");
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $stmt->bind_result($room_price);
        $stmt->fetch();
        $stmt->close();

        $total_amount = $room_price + $additional_amount;

        // Insert billing record
        $stmt = $conn->prepare("INSERT INTO billing (reservation_id, amount, paid, payment_method, additional_charges) VALUES (?, ?, 1, ?, ?)");
        $stmt->bind_param("idss", $reservation_id, $total_amount, $payment_method, $additional_charges);
        if ($stmt->execute()) {
            // Update reservation status and free room
            $stmt2 = $conn->prepare("UPDATE reservations SET status='checked_out' WHERE reservation_id=?");
            $stmt2->bind_param("i", $reservation_id);
            $stmt2->execute();
            $stmt2->close();

            $stmt3 = $conn->prepare("UPDATE rooms SET status='available' WHERE room_id=?");
            $stmt3->bind_param("i", $room_id);
            $stmt3->execute();
            $stmt3->close();

            $success = "Checkout successful. Total billed: $total_amount";
        } else {
            $error = "Error processing billing.";
        }
        $stmt->close();
    }
}

// Fetch checked-in reservations
$checkedInReservations = $conn->query("SELECT reservation_id, customer_id, assigned_room_id FROM reservations WHERE status='checked_in'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Reservation System | Check Out</title>
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
            <h2>Check-Out Customer</h2>

                <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
                <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

                <div class="dashboard-section">
                <form class="form-container" method="POST">
                    <div class="form-group">
                    <label>Select Reservation</label>
                    <select name="reservation_id" required>
                        <option value="">-- Select Reservation --</option>
                        <?php while ($res = $checkedInReservations->fetch_assoc()): ?>
                            <option value="<?php echo $res['reservation_id']; ?>">
                                Reservation ID: <?php echo $res['reservation_id']; ?> - Room ID: <?php echo $res['assigned_room_id']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    </div>

                    <div class="form-group">
                    <label>Payment Method</label>
                    <select name="payment_method" required>
                        <option value="cash">Cash</option>
                        <option value="credit_card">Credit Card</option>
                    </select>
                    </div>

                    <div class="form-group">
                    <label>Additional Charges Description (optional)</label>
                    <textarea name="additional_charges" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                    <label>Additional Charges Amount</label>
                    <input type="number" name="additional_amount" step="0.01" min="0" value="0">
                    </div>

                    <div class="form-group">
                    <button type="submit" class="submit-btn">Check Out</button>
                    </div>
                </form>
                </div>
        </section>
</body>
</html>

