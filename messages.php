<?php
session_start();
require_once('includes\db.php');
require_once('includes\functions.php');


if (!isset($_SESSION['username'])) {
    // User not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

$query = "SELECT * FROM `messages` WHERE status='not_read'";
$result = $conn->query($query);
$sucess = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'];

        // Prepare the SQL query to update the check-in and check-out dates
        $sql = "UPDATE messages 
                SET status = 'read' 
                WHERE message_ID = ?";

        // Prepare the statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind the parameters to the SQL query
            $stmt->bind_param("i", $id);

            // Execute the statement
            if ($stmt->execute()) {
                echo "Message set as Read";

                header("Location: messages.php");
            } else {
                echo "Error: Could not Complete action";
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Error: Could not prepare the SQL statement.";
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
    <title>Hotel Reservation System | Messages</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="style_admin.css">
    <style>
    .card {
      background: #f9f9f9;
      border: 1px solid #ddd;
      border-radius: 5px;
      padding: 15px;
      margin-bottom: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .card .btn {
      background-color: #3498db;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 3px;
      cursor: pointer;
    }
    .card .btn:hover {
      background-color: #2980b9;
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
            <h2>Messages</h2>

            <?php if (isset($success)): ?>
                <p style="color:green;"><?php echo $sucess; ?></p>
            <?php endif; ?>

            <?php if (isset($_SESSION['message'])): ?>
                <p style="color:green;"><?php echo $_SESSION['message'];
                                        unset($_SESSION['message']); ?></p>
            <?php endif; ?>


            <div class="recent-bookings">
                <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="card">
                <div>
                    <p><strong>Name:</strong> <?php echo $row["Name"] ?></p>
                    <p><strong>Email:</strong> <?php echo $row['Email'] ?></p>
                    <p><strong>Message:</strong> <?php echo $row['Message'] ?></p>  
                </div>
                <form action="" method="POST">
                <input type="text" value="<?php echo $row['message_ID']; ?>" name="id" hidden>
                <input type="submit" class="btn" value="Mark As Read">
                </form>
                </div>
                <?php } ?>
            </div>
        </section>
    </div>
</body>
</html>