<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Reservation System | Home Page</title>
    <link href="style.css" rel="stylesheet">
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

    <!-- Homepage Content -->
    <header class="hero">
        <div class="hero-content">
            <h1>Welcome to Pearl of Ceylon</h1>
            <p>Experience comfort and luxury at its finest. Book your stay with us today!</p>
            <a href="register.php" class="cta-button">Book Now</a>
        </div>
    </header>

    <section class="intro">
        <h1>Experience Luxury in Colombo Elegance</h1>
        <p>Welcome to Pearl of Ceylon, Colombo's premier destination for discerning travelers seeking a blend of luxury, culture, 
            and comfort. Nestled in the heart of Western Province, our resort offers an unparalleled experience that harmoniously 
            combines Sri Lanka's rich heritage with modern elegance. From plush accommodations and curated culinary delights to 
            personalized service and world-class amenities, Pearl of Ceylon is dedicated to providing an unforgettable retreat for 
            international guests. Discover the vibrant essence of Colombo while enjoying a tranquil sanctuary at Pearl of Ceylon.</p>
    </section>

    <section class="features">
        <h2>Why Choose Pearl of Ceylon?</h2>
        <div class="feature-grid">
            <div class="feature-item">
                <img src="Images/rooms.jpg">
                <h3>Luxurious Rooms & Suites</h3>
                <p>From cozy standard rooms to spacious residential suites for extended stays, we have it all.</p>
            </div>
            <div class="feature-item">
                <img src="Images/reserved.jpg">
                <h3>Seamless Reservations</h3>
                <p>Book, modify, or cancel your stay effortlessly online or through our dedicated staff.</p>
            </div>
            <div class="feature-item">
                <img src="Images/pool.jpg">
                <h3>Exceptional Services</h3>
                <p>Enjoy amenities like room service, swimming pool, dining, laundry, and exclusive club facilities.</p>
            </div>
            <div class="feature-item">
                <img src="Images/travel.jpg">
                <h3>Group Travel Solutions</h3>
                <p>Special rates for travel companies booking multiple rooms for groups.</p>
            </div>
        </div>
    </section>

    <section class="middle-image-section">
        <div class="middle-image"></div>
    </section>

    <section class="details">
        <div class="details-text">
            <h1>Experience Unmatched Comfort in Sri Lanka</h1>
            <p>Discover unparalleled luxury and comfort at Pearl of Ceylon, your gateway to the exquisite beauty of Colombo, Sri Lanka.</p>
            <div class="details-button">
                <a href="contacts.php">Contact us Now</a>
            </div>
        </div>
        <div class="details-image"></div>
    </section>

    <section class="promotions">
        <h2>Special Offers</h2>
        <div class="promo-grid">
            <div class="promo-item">
                <h3>Stay Longer, Save More</h3>
                <p>Book a residential suite for a week or month and enjoy exclusive discounts.</p>
                <a href="reservation.php" class="promo-link">Learn More</a>
            </div>
            <div class="promo-item">
                <h3>Group Booking Discount</h3>
                <p>Travel companies: Reserve 3+ rooms and get up to 20% off.</p>
                <a href="travel-booking.php" class="promo-link">Get Started</a>
            </div>
        </div>
    </section>

    <?php include("includes\\footer.php"); ?>
</body>
</html>