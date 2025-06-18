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
    $company_name = sanitize($_POST['company_name']);
    $contact_person = sanitize($_POST['contact_person']);
    $phone = sanitize($_POST['phone']);
    $email = sanitize($_POST['email']);
    $discount_rate = floatval($_POST['discount_rate']);
    $num_rooms = intval($_POST['num_rooms']);
    $nights = intval($_POST['nights']);

    if ($num_rooms < 3) {
        $error = "Must book at least 3 rooms.";
    } else {
        // Insert or update travel company
        $stmt = $conn->prepare("INSERT INTO travel_companies (name, contact_person, phone, email, discount_rate) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssd", $company_name, $contact_person, $phone, $email, $discount_rate);

        if ($stmt->execute()) {
            $company_id = $stmt->insert_id;

            // Create block bookings
            // Simplified: insert multiple reservations under company_id
            $arrival_date = date('Y-m-d');
            $departure_date = date('Y-m-d', strtotime("+$nights days"));
            for ($i = 0; $i < $num_rooms; $i++) {
                $stmt2 = $conn->prepare("INSERT INTO reservations (customer_id, room_type, occupants, arrival_date, departure_date, credit_card_provided, status, travel_company_id) VALUES (NULL, 'double', 2, ?, ?, 1, 'active', ?)");
                $stmt2->bind_param("ssi", $arrival_date, $departure_date, $company_id);
                $stmt2->execute();
                $stmt2->close();
            }
            $success = "Travel company block booking created.";
        } else {
            $error = "Failed to add travel company.";
        }
    }
}

try {
    // Create PDO connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL query to count the number of available rooms
    $sql = "SELECT COUNT(*) AS available_rooms FROM rooms WHERE status = 'Available'";

    // Prepare and execute the query
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Store the number of available rooms in a variable
    $available_rooms = $result['available_rooms'];

    // Output the number of available rooms
    echo "Number of available rooms: " . $available_rooms;

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn = null;
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
            <h2>Travel Company Block Booking</h2>

            <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
            <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

            <div class="dashboard-section">
            <form method="POST" class="form-container">
                <div class="form-group">
                <label>Company Name</label>
                <input type="text" name="company_name" required>
                </div>

                <div class="form-group">
                <label>Contact Person</label>
                <input type="text" name="contact_person" required>
                </div>

                <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" required>
                </div>

                <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
                </div>

                <div class="form-group">
                <label>Discount Rate (%)</label>
                <input type="number" name="discount_rate" step="0.01" min="0" max="100" required>
                </div>

                <div class="form-group">
                <label>Number of Rooms (3 or more)</label>
                <input type="number" name="num_rooms" min="3" max="<?php echo $available_rooms ?>" required>
                </div>

                <div class="form-group">
                <label>Number of Nights</label>
                <input type="number" name="nights" min="1" required>
                </div>

                <div class="form-group">
                <button type="submit" class="submit-btn">Create Block Booking</button>
                </div>
            </form>
        </section>
</body>
</html>

