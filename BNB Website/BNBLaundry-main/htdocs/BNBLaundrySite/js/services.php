<?php
include 'db.php';

// Fetch services from the database
$services_query = "SELECT Name, Service_Type, Service_Details, Price_per_unit FROM Services ORDER BY Name ASC";
$services_result = $conn->query($services_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services | BnB Laundry</title>
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding-top: 140px;
            background-color: #f4fbff;
            text-align: center;
        }
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
        .hero {
            background-color: #E3F2FD;
            padding: 50px 20px;
        }
        .hero h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }
        .hero p {
            font-size: 18px;
        }
        .services-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 40px 20px;
            background-color: #D0F6FF;
        }
        .service-box {
            background: white;
            padding: 20px;
            width: 280px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .service-box h3 {
            color: #007bb5;
        }
        .service-box p {
            font-size: 14px;
            color: #333;
        }
        .service-price {
            font-size: 18px;
            font-weight: bold;
            color: #007bb5;
            margin-top: 10px;
        }
        .footer {
            background: #ffffff;
            padding: 10px;
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<header>
    <img src="/BNBLaundrySite/js/images/bnb_logo(1).png" alt="BnB" width="300">
    <nav>
        <ul>
            <li><a href='index.php'>Home</a></li>
            <li><a href='order-tracking.php'>Order Tracking</a></li>
            <li><a href='services.php' class='active'>Services</a></li>
            <li><a href='about.php'>About Us</a></li>
            <li><a href='staff-login.php'>Staff Login</a></li>
        </ul>
    </nav>
</header>

<div class="hero">
    <h1>Our Services</h1>
    <p>We offer a variety of laundry services tailored to your needs.</p>
</div>

<div class="services-container">
    <?php if ($services_result->num_rows > 0): ?>
        <?php while ($service = $services_result->fetch_assoc()): ?>
            <div class="service-box">
                <h3><?= htmlspecialchars($service["Name"]) ?></h3>
                <p><strong>Type:</strong> <?= htmlspecialchars($service["Service_Type"]) ?></p>
                <p><?= htmlspecialchars($service["Service_Details"]) ?></p>
                <p class="service-price">₱<?= number_format($service["Price_per_unit"], 2) ?> per unit</p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No services available at the moment.</p>
    <?php endif; ?>
</div>

<footer class='footer'>
    <p>&copy; <?= date("Y") ?> BnB Laundry. All Rights Reserved.</p>
</footer>

</body>
</html>
