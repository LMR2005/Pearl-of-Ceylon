<?php
session_start();
require_once('includes\db.php');
require_once('includes\functions.php');
require('fpdf186\fpdf.php');


if (!isset($_SESSION['username'])) {
    // User not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

$sql = "SELECT COUNT(*) AS future_reservations FROM reservations WHERE arrival_date > CURDATE()";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    $future_reservations = $row['future_reservations'];
}

$sql = "SELECT COUNT(*) AS checkins FROM reservations WHERE MONTH(arrival_date) = MONTH(CURRENT_DATE) AND YEAR(arrival_date) = YEAR(CURRENT_DATE) AND arrival_date <= CURDATE() AND status = 'checked_in'";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    $checkins = $row['checkins'];
}

$sql = "SELECT COUNT(*) AS checkouts FROM reservations WHERE MONTH(departure_date) = MONTH(CURRENT_DATE) AND YEAR(departure_date) = YEAR(CURRENT_DATE) AND departure_date <= CURDATE() AND status = 'checked_out'";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    $checkouts = $row['checkouts'];
}

$sql = "SELECT SUM(amount) AS total_revenue FROM billing WHERE YEAR(billing_date)=YEAR(CURRENT_DATE)";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    $total_revenue = $row['total_revenue'];
}

$sql = "SELECT SUM(amount) AS additional_service_revenue FROM optional_charges ";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    $additional_service_revenue = $row['additional_service_revenue'];
}

$room_revenue = $total_revenue - $additional_service_revenue;


// Occupancy Rate
$sql = "SELECT COUNT(*) AS occupied_rooms FROM reservations WHERE status = 'checked_in'";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    $occupied_rooms = $row['occupied_rooms'];
}

$sql = "SELECT COUNT(*) AS total_rooms FROM rooms";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    $total_rooms = $row['total_rooms'];
}

$occupancy_rate = ($occupied_rooms / $total_rooms) * 100;

// Active Bookings
$sql = "SELECT COUNT(*) AS active_bookings FROM reservations WHERE status = 'active'";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    $active_bookings = $row['active_bookings'];
}

if (isset($_POST['download'])) {

    // Create instance of FPDF class
    $pdf = new FPDF();
    $pdf->AddPage();

    // Title
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(200, 10, 'Hotel Report - Pearl of Ceylon', 0, 1, 'C');

    // Financial Overview
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(200, 10, 'Financial Overview', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(200, 10, "Room Revenue: LKR " . number_format($room_revenue, 2), 0, 1);
    $pdf->Cell(200, 10, "Additional Service Revenue: LKR " . number_format($additional_service_revenue, 2), 0, 1);
    $pdf->Cell(200, 10, "Total Revenue: LKR " . number_format($total_revenue, 2), 0, 1);

    // Reservations Overview
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(200, 10, 'Reservations Overview', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(200, 10, "No. of Future Reservations: " . $future_reservations, 0, 1);
    $pdf->Cell(200, 10, "No. of Check-ins: " . $checkins, 0, 1);
    $pdf->Cell(200, 10, "No. of Check-outs: " . $checkouts, 0, 1);

    // Other Information
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(200, 10, 'Other Information', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(200, 10, "Occupancy Rate: " . number_format($occupancy_rate, 2) . "%", 0, 1);
    $pdf->Cell(200, 10, "Active Bookings: " . $active_bookings, 0, 1);

    // Output the PDF (force download)
    $pdf->Output('D', 'hotel_report.pdf');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Reservation System | Reports</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="style_admin.css">
    <style>
        .btn {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            background-color: #2575fc;
            color: white;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #1b54c9;
        }
    </style>
</head>
<body>
    <!-- Navbar Placeholder -->
    <div id="nav-placeholder"></div>

<?php include("includes/sidebar.php"); ?>

    <!-- Admin Dashboard Section -->
    <div class="main-content">
        <section class="admin-dashboard">
            <h2>View Reports</h2>

            <!-- Recent Bookings -->
            <div class="recent-bookings">
                <h3>Financial Overview</h3>
                <p>Room Revenue: <?php echo $room_revenue ?>.00</p>
                <p>Additional Service Revenue: <?php echo $additional_service_revenue ?> </p>
                <p>Total Revenue: <?php echo $total_revenue ?></p>

                <h3>Reservations</h3>
                <p>No. of future reservations: <?php echo $future_reservations ?></p>
                <p>No. of checkins: <?php echo $checkins ?></p> 
                <p>No. of checkouts: <?php echo $checkouts ?> </p>

                <h3>Other</h3>
                <p>Occupacy Rate: <?php echo round($occupancy_rate,2) ?></p>
                <p>Active Bookings: <?php echo $active_bookings ?></p>

                <button class="btn" id="downloadBtn">Download</button>
            </div>
        </section>
    </div>

    <script>
    // When the "Download" button is clicked
    document.getElementById("downloadBtn").addEventListener("click", function() {
        var form = document.createElement("form");
        form.method = "POST";
        form.action = "reports.php"; 

        var input = document.createElement("input");
        input.type = "hidden";
        input.name = "download";
        input.value = "1"; 

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    });
</script>
</body>
</html>