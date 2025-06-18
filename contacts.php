<?php
require_once("includes/db.php");

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $status = "not_read";

    // Validate form data (e.g., check if email is valid)
    if (empty($name) || empty($email) || empty($message)) {
        echo "Please fill in all the fields.";
    } else {
        // Prepare SQL query to insert the data into the database
        $query = "INSERT INTO messages (name, email, message,status) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        
        // Bind the parameters and execute the query
        $stmt->bind_param("ssss", $name, $email, $message, $status);
        
        if ($stmt->execute()) {
            // Redirect or display success message
            echo "Message sent successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="contacts-style.css">
    <title>Hotel Reservation System | Contact Us</title>
</head>
<body>
    <!--Navigation bar Code Starts-->
    <nav class="navbar">
        <div class="logo">
            <a href="index.php">Pearl of Ceylon</a>
        </div>
        <div class="menu-toggle" id="mobile-menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
            <li class="nav-item"><a href="gallery.php" class="nav-link">Gallery</a></li>
            <li class="nav-item"><a href="contacts.php" class="nav-link">Contact Us</a></li>
            <li class="nav-item"><a href="register.php" class="nav-link login-btn">Sign up</a></li>
            <li class="nav-item"><a href="login.php" class="nav-link login-btn">Sign in</a></li>
        </ul>
    </nav>
    <!--Navigation bar Code Ends-->

    <script>
        // Toggle mobile menu
        const menuToggle = document.getElementById('mobile-menu');
        const navMenu = document.querySelector('.nav-menu');
        
        menuToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });

        // Basic user role simulation (for demo purposes)
        const userRole = 'customer'; // Change to 'manager', 'travel', 'clerk', or 'customer' for testing
        const managerLinks = document.querySelectorAll('.manager-only');
        const travelLinks = document.querySelectorAll('.travel-only');
        
        if (userRole !== 'manager') {
            managerLinks.forEach(link => link.style.display = 'none');
        }
        if (userRole !== 'travel') {
            travelLinks.forEach(link => link.style.display = 'none');
        }
    </script>

    <section class="decorate" style="background: linear-gradient(rgba(152, 86, 86, 0.5), rgba(0, 0, 0, 0.5)), url('Images/contact.jpg'); background-size: cover;">
        <h1>Contact Us</h1> 
    </section>

    <section class="contact-section">
        <form id="contact-form" action="" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="email">Name:</label>
                    <input type="text" id="name" name="name" required placeholder="Enter your Name">
                </div>
                <div class="form-group">   
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>
            </div>
            <div class="form-group">
                <label for="message">Message:</label>
                <textarea id="message" name="message" required placeholder="Type your message here"></textarea>
            </div>
            <button type="submit" class="submit-button">Send Message</button>
        </form>
    </section>

    <?php include("includes\\footer.php"); ?>
</body>
</html>