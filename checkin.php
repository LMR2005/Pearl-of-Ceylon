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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservation_id = intval($_POST['reservation_id']);
    $room_id = intval($_POST['room_id']);

    // Check if room is available
    $stmt = $conn->prepare("SELECT status FROM rooms WHERE room_id=?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $stmt->bind_result($status);
    $stmt->fetch();
    $stmt->close();

    if ($status !== 'available') {
        $error = "Selected room is not available.";
    } else {
        // Assign room and update reservation
        $stmt = $conn->prepare("UPDATE reservations SET assigned_room_id=?, status='checked_in' WHERE reservation_id=?");
        $stmt->bind_param("ii", $room_id, $reservation_id);
        $stmt->execute();
        $stmt->close();

        // Mark room as occupied
        $stmt = $conn->prepare("UPDATE rooms SET status='occupied' WHERE room_id=?");
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $stmt->close();

        // Insert checkin record
        $stmt = $conn->prepare("INSERT INTO checkins (reservation_id, room_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $reservation_id, $room_id);
        $stmt->execute();
        $stmt->close();

        $success = "Check-in successful.";
    }
}

// Fetch reservations eligible for check-in
$reservations = $conn->query("SELECT reservation_id, customer_id, arrival_date, departure_date, room_type, status FROM reservations WHERE status='active'");

// Fetch available rooms
$rooms = $conn->query("SELECT room_id, room_number, room_type FROM rooms WHERE status='available'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Reservation System | Check in</title>
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
            <h2>Check-in Customer</h2>

                <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
                <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

                <div class="dashboard-section">
                <form class="form-container" method="POST">
                    <div class="form-group">
                    <label>Select Reservation</label>
                    <select name="reservation_id" required>
                        <option value="">-- Select Reservation --</option>
                        <?php while ($res = $reservations->fetch_assoc()): ?>
                            <option value="<?php echo $res['reservation_id']; ?>">
                                ID: <?php echo $res['reservation_id']; ?> - Room Type: <?php echo $res['room_type']; ?> - Arrival: <?php echo $res['arrival_date']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    </div>

                    <div class="form-group">
                    <label>Select Room</label>
                    <select name="room_id" required>
                        <option value="">-- Select Room --</option>
                        <?php while ($room = $rooms->fetch_assoc()): ?>
                            <option value="<?php echo $room['room_id']; ?>">
                                <?php echo $room['room_number']; ?> (<?php echo ucfirst($room['room_type']); ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                    </div>

                    <div class="form-group">
                    <button type="submit" class="submit-btn">Check In</button>
                    </div>
                </form>
                </div>
        </section>
</body>
</html>

