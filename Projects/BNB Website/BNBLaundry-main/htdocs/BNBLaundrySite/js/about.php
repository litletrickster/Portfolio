<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | BnB Laundry</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding-top: 120px;
            height: 200%;
            background-color: #f4fbff;
        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 50px;
            background-color: white;
            position: fixed;
            top: 0;
            width: 95%;
            z-index: 1000; 
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #00a8e8;
        }
        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
            padding: 0;
        }
        nav a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }

        /* About Us Section */
        .about-container {
            max-width: 1000px;
            margin: 60px auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
        }
        .about-text {
            flex: 1;
        }
        .about-text h2 {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 15px;
            letter-spacing: 5px;
        }
        
        .about-text h4 {
            font-size: 18px;
            color: #21B7E2;
            letter-spacing: 6px;
        }
        .about-text p {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
        }
        
        .explore-btn {
            clip-path: polygon(100% 9%, 87% 90%, 85% 96%, 82% 100%, 0 100%, 0 0, 95% 0, 90% 11%);
            background: #00a8e8;
            color: white;
            padding: 18px 19px;
            border-radius: 7px;
            text-decoration: none;
            font-size: 13px;
            margin-top: 15px;
            display: inline-block;
            transition: all 0.3s ease-in-out;
            box-shadow: 20px 18px 42px -9px rgba(185, 205, 218, 0.8)
            
        }

        .explore-btn:hover {
            background: #007bb5;
            transform: scale(1.1);
            clip-path: polygon(100% 11%, 87% 90%, 85% 96%, 82% 100%, 0 100%, 0 0, 93% 0, 88% 18%);

        }

        .about-images {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: -5px;
        }
        .about-images img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            
        }
        .highlight-badge {
            background: #00a8e8;
            color: white;
            padding: 10px;
            border-radius: 10px;
            font-weight: bold;
            position: absolute;
            bottom: -10px;
            left: 10px;
            font-size: 18px;
            
        }
        .highlight-container {
            position: relative;
            display: inline-block;
            clip-path: polygon(0 0, 100% 0%, 100% 100%, 0 70%);
            width: 100%;
            height: 400px;
            background-size: contain;
        }

        .footer {
            background: #b3e0ff;
            padding: 23px 0px;
            text-align: center;
            position: relative;
            width: 100%;
            left: 0;
            bottom: 0;
        }
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
            font-size: 14px;
        }
        .footer-column {
            text-align: left;
        }
        .footer-column h4 {
            font-size: 16px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .footer-column a {
            text-decoration: none;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }
        .footer-column a:hover {
            color: #007bb5;
        }
        .social-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 15px;
        }
        .social-icons a {
            text-decoration: none;
            font-size: 20px;
            color: #007bb5;
        }
        .social-icons a:hover {
            color: #005f8d;
        }
    </style>
</head>
<body>

<!-- Header -->
<header>
    <img src="/BNBLaundrySite/js/images/bnb_logo(1).png" alt="BnB" width="300">
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="order-tracking.php">Order Tracking</a></li>
            <li><a href="services.php">Services</a></li>
            <li><a href="about-us.php"><b>About Us</b></a></li>
            <li><a href="staff-login.php">Staff Login</a></li>
        </ul>
    </nav>
</header>

<!-- About Us Section -->
<div class="about-container">
    <div class="about-text">
        <h4>A BIT</h4>
        <h2>ABOUT US</h2>
        <p>
            BnB Laundry was founded with the mission to provide high-quality, convenient, and affordable laundry services. 
            We believe in efficiency, sustainability, and ensuring that every customer’s laundry experience is hassle-free. 
            Our team is dedicated to keeping your clothes fresh and clean with state-of-the-art equipment and a commitment to excellence.
        </p>
        <a href="#" class="explore-btn">EXPLORE MORE</a>
    </div>

    <div class="about-images">
        <div class="highlight-container">
        <img src="/BNBLaundrySite/js/images/bnb_outside.png" alt="bnb_outside" >
        
        </div>
    </div>
</div>
<!-- Footer -->
<footer>
<div class="footer">
    <div class="footer-links">
        <div class="footer-column">
            <h4>About Us</h4>
            <a href="#">About us</a>
            <a href="#">Creators</a>
            <a href="#">Philosophy</a>
            <a href="#">Contact us</a>
        </div>
        <div class="footer-column">
            <h4>Company</h4>
            <a href="#">Our Team</a>
            <a href="#">Terms</a>
            <a href="#">How it Works</a>
            <a href="#">Blog</a>
        </div>
        <div class="footer-column">
            <h4>Services</h4>
            <a href="http://bnblaundryproto.kesug.com/BNBLaundrySite/js/services.php">Pickup</a>
            <a href="http://bnblaundryproto.kesug.com/BNBLaundrySite/js/services.php">Drop Off</a>
            <a href="http://bnblaundryproto.kesug.com/BNBLaundrySite/js/services.php">Laundry</a>
        </div>
    </div>
    
<center>
    <p>&copy; 2025 BnB Laundry. All Rights Reserved.</p>
</center>
</div>
</footer>

</body>
</html>
