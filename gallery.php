<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Hotel Reservation System | Gallery</title>
</head>
<style>
    body{
        background-color: bisque;
    }
</style>
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

    <section class="decorate">
        <h1>Gallery</h1> 
    </section>

    <section class="gallery-section">
        <h3>Hotel Environment</h3>
        <div class="gallery-grid">
            <div class="gallery-card">
                <div class="gallery-image" style="background-image: url('Images/HotelEnv/1.jpg');"></div>
            </div>
            <div class="gallery-card">
                <div class="gallery-image" style="background-image: url('Images/HotelEnv/2.jpg');"></div>
            </div>
            <div class="gallery-card">
                <div class="gallery-image" style="background-image: url('Images/HotelEnv/3.jpg');"></div>
            </div>
            <div class="gallery-card">
                <div class="gallery-image" style="background-image: url('Images/HotelEnv/4.jpg');"></div>
            </div>
            <div class="gallery-card">
                <div class="gallery-image" style="background-image: url('Images/HotelEnv/5.jpg');"></div>
            </div>
        </div>
        <h3>Food</h3> 
        <div class="gallery-grid">
            <div class="gallery-card">
                <div class="gallery-image" style="background-image: url('Images/Food/1.jpg');"></div>
            </div>
            <div class="gallery-card">
                <div class="gallery-image" style="background-image: url('Images/Food/2.jpg');"></div>
            </div>
            <div class="gallery-card">
                <div class="gallery-image" style="background-image: url('Images/Food/3.jpg');"></div>
            </div>
            <div class="gallery-card">
                <div class="gallery-image" style="background-image: url('Images/Food/4.jpg');"></div>
            </div>
            <div class="gallery-card">
                <div class="gallery-image" style="background-image: url('Images/family.jpg');"></div>
            </div>
        </div>
        <h3>Rooms</h3>
        <div class="gallery-grid">
            <div class="gallery-card">
                <div class="gallery-image" style="background-image: url('Images/Rooms/1.jpg');"></div>
            </div>
            <div class="gallery-card">
                <div class="gallery-image" style="background-image: url('Images/Rooms/2.jpg');"></div>
            </div>
            <div class="gallery-card">
                <div class="gallery-image" style="background-image: url('Images/Rooms/3.jpg');"></div>
            </div>
            <div class="gallery-card">
                <div class="gallery-image" style="background-image: url('Images/Rooms/4.jpg');"></div>
            </div>
            <div class="gallery-card">
                <div class="gallery-image" style="background-image: url('Images/Rooms/5.jpg');"></div>
            </div>
        </div>
    </section>

    <?php include("includes\\footer.php"); ?>

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

</body>
</html>