<?php
session_start();

require_once('includes\functions.php');
require_once('includes\db.php');
require_once('includes\auth.php');

require 'mailer/PHPMailer.php';
require 'mailer/SMTP.php';
require 'mailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['username'])) {
    // User not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT customer_id, first_name, last_name, email, phone, address FROM customers WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

$stmt = $conn->prepare("SELECT * FROM rooms WHERE status='available'");
$stmt->execute();
$result = $stmt->get_result();
$rooms = $result->fetch_assoc();


$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = sanitize($_POST['first_name']);
    $last_name = sanitize($_POST['last_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $room_type = $_POST['room_type'];
    $occupants = intval($_POST['occupants']);
    $arrival_date = $_POST['arrival_date'];
    $departure_date = $_POST['departure_date'];
    $credit_card_provided = isset($_POST['credit_card_provided']) ? 1 : 0;
    $credit_card_details = $credit_card_provided ? sanitize($_POST['credit_card_details']) : NULL;

    if (!$customer) {
        $stmt = $conn->prepare("INSERT INTO customers (user_id, first_name, last_name, email, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $user_id, $first_name, $last_name, $email, $phone, $address);
        $stmt->execute();
        $customer_id = $stmt->insert_id;
    } else {
        $stmt = $conn->prepare("UPDATE customers SET first_name=?, last_name=?, email=?, phone=?, address=? WHERE user_id=?");
        $stmt->bind_param("sssssi", $first_name, $last_name, $email, $phone, $address, $user_id);
        $stmt->execute();
        $customer_id = $customer['customer_id'];
    }

    $stmt = $conn->prepare("INSERT INTO reservations (customer_id, room_type, occupants, arrival_date, departure_date, credit_card_provided, credit_card_details) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isissis", $customer_id, $room_type, $occupants, $arrival_date, $departure_date, $credit_card_provided, $credit_card_details);

    if ($stmt->execute()) {
        $success = "Reservation successful.";
    } else {
        $error = "Failed to make reservation.";
    }

    $mail = new PHPMailer();
    // Server settings
    $mail->isSMTP();  // Use SMTP
    $mail->Host = 'smtp.gmail.com';  // Set the SMTP server to send through
    $mail->SMTPAuth = true;  // Enable SMTP authentication
    $mail->Username = 'lmrjayasinghe@gmail.com';  // SMTP username
    $mail->Password = 'sfsj pvtt pbjd gulc';  // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Enable TLS encryption
    $mail->Port = 587;  // TCP port to connect to

    // Recipients
    $mail->setFrom('lmrjayasinghe@gmail.com', 'Pearl of Ceylon');
    $mail->addAddress($_POST['email']);  // Add a recipient

    // Content
    $mail->isHTML(true);  // Set email format to HTML
    $mail->Subject = 'Reservation Confirmation';
    $mail->Body    = 'Dear ' . $first_name . ', <br><br>

        Thank you for choosing Hotel Pearl of Ceylon for your stay. We are delighted to confirm your reservation at our hotel. Below are the details of your booking: <br><br>

        Reservation Details: <br>

        Guest Name: ' . $first_name . ' ' . $last_name . ' <br>

        Check-in Date: ' . $arrival_date . ' <br>

        Check-out Date: ' . $departure_date . ' <br>

        Room Type: ' . $room_type . ' <br>

        Number of Guests: ' . $occupants . ' <br><br>

        We look forward to welcoming you to Hotel Pearl of Ceylon. Our team is committed to ensuring that your stay is comfortable and memorable. <br> 
        If you have any special requests or require further assistance before your arrival, please feel free to contact us at 0711735543 or pearlofceylon@gmail.com. <br><br>

        Hotel Address: <br>
        Hotel Pearl of Ceylon, <br>
        Galle Road, <br>
        Colombo 03, <br>
        Sri Lanka. <br><br>

        Once again, thank you for choosing us. We look forward to hosting you soon! <br><br>

        Warm regards, <br>
        Hotel Pearl of Ceylon Team <br>
        [pearlofceylon@gmail.com] <br>
        [0711735543] <br>
        [www.pearlofceylon.com]';

    // Send the email
    if ($mail->send()) {
        echo 'Message has been sent';
    } else {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Hotel Reservation System | Make Reservation</title>
    <link rel="stylesheet" href="style_admin.css">
    <link rel="stylesheet" href="customer-style.css">
          <style>

.form-container form {
    padding: 30px;
    border-radius: 8px;
    margin: auto;
}

.form-container form h3 {
    font-size: 1.5rem;
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

.form-container .form-group {
    margin-bottom: 20px;
}

.form-container .form-group label {
    font-weight: bold;
    margin-bottom: 5px;
    display: block;
}

.form-container .form-group input,
.form-container .form-group select,
.form-container .form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 1rem;
}

.form-container .form-group textarea {
    resize: vertical;
    height: 100px;
}

.form-container .form-group input[type="date"] {
    padding: 10px;
}

.form-container .submit-btn {
    width: 100%;
    padding: 12px;
    background-color: #2575fc;
    color: white;
    font-size: 1rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.form-container .submit-btn:hover {
    background-color: #1b54c9;
}

.form-container .form-group .checkbox-label {
    display: inline-flex; 
    align-items: center; 
    margin-left: 10px;
}

.form-container .form-group .checkbox-label input {
    margin-right: 5px;
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
            <h2>Make Reservations</h2>
            <div class="dashboard-section">
                <?php if ($error): ?>
                <p style="color:red;"><?php echo $error; ?></p>
                <?php endif; ?>

                <?php if ($success): ?>
                    <p style="color:green;"><?php echo $success; ?></p>
                <?php endif; ?>

                <div class="form-container">
                    <form method="POST">
                        <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" required value="<?php echo isset($customer['first_name']) ? htmlspecialchars($customer['first_name']) : ''; ?>">
                        </div>

                        <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" required value="<?php echo isset($customer['last_name']) ? htmlspecialchars($customer['last_name']) : ''; ?>">
                        </div>

                        <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required value="<?php echo isset($customer['email']) ? htmlspecialchars($customer['email']) : ''; ?>">
                        </div>

                        <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" required value="<?php echo isset($customer['phone']) ? htmlspecialchars($customer['phone']) : ''; ?>">
                        </div>

                        <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" required><?php echo isset($customer['address']) ? htmlspecialchars($customer['address']) : ''; ?></textarea>
                        </div>

                        <div class="form-group">
                        <label>Room Type</label>
                        <select name="room_type" required>
                            <option value="single">Single</option>
                            <option value="double">Double</option>
                            <option value="suite">Suite</option>
                            <option value="residential_suite">Residential Suite</option>
                        </select>
                        </div>

                        <div class="form-group">
                        <label>Number of Occupants</label>
                        <input type="number" name="occupants" min="1" required>
                        </div>

                        <div class="form-group">
                        <label>Arrival Date</label>
                        <input type="date" name="arrival_date" required>
                        </div>

                        <div class="form-group">
                        <label>Departure Date</label>
                        <input type="date" name="departure_date" required>
                        </div>

                        <div class="form-group">
                        <label class="checkbox-label"><input type="checkbox" id="cc_provided" name="credit_card_provided"> Credit Card Provided?</label>
                        </div>

                        <div class="form-group">
                        <div id="credit_card_details" style="display:none;">
                            <label>Credit Card Details</label>
                            <textarea name="credit_card_details"></textarea>
                        </div>
                        </div>

                        <div class="form-group">
                        <button type="submit" class="submit-btn">Book Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    <script>
    document.getElementById('cc_provided').addEventListener('change', function() {
        document.getElementById('credit_card_details').style.display = this.checked ? 'block' : 'none';
    });
</script>
</body>
</html>