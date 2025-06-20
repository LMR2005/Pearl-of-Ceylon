<?php
session_start();

// Include the db.php file to get the database connection
require_once 'includes/db.php';
require_once 'includes/functions.php';  // Assuming db.php contains the database connection with $conn

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if the reservation ID is provided via POST
    if (isset($_POST['reservation_id'])) {
        $reservation_id = $_POST['reservation_id'];

        // SQL query to update the reservation status to 'cancelled'
        $sql = "UPDATE reservations SET status = 'cancelled' WHERE reservation_id = ?";

         // Prepare the SQL statement and check for errors
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $reservation_id);

        // Execute the query
        if ($stmt->execute()) {
            // Successfully cancelled the reservation
            $_SESSION['sucess'] = "Reservation with ID " . $reservation_id . " has been cancelled.";
            // Redirect to the reservations page or show a success message
            header("Location: view_reserve.php"); 
        } else {
            $_SESSION['error'] = "There was an error cancelling the reservation.";
            header("Location: view_reserve.php");
        }
    } 
}
?>
