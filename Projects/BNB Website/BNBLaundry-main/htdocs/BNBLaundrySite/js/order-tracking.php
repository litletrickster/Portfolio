<?php
include 'db.php';

// Get order ID from user input
$order_id = isset($_GET['order_id']) ? trim($_GET['order_id']) : null;
$order = null;

if ($order_id) {
    // Fetch order details from the database
    $order_query = "
        SELECT Orders.Order_ID, Customers.Name AS Customer_Name, Orders.Status
        FROM Orders
        JOIN Customers ON Orders.Customer_ID = Customers.Customer_ID
        WHERE Orders.Order_ID = ?";
    
    $stmt = $conn->prepare($order_query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order_result = $stmt->get_result();
    
    if ($order_result->num_rows > 0) {
        $order = $order_result->fetch_assoc();
        
        // Fetch associated services
        $services_query = "
            SELECT Services.Name AS Service_Name, Order_Service.Quantity, Order_Service.Weight
            FROM Order_Service
            JOIN Services ON Order_Service.Service_ID = Services.Service_ID
            WHERE Order_Service.Order_ID = ?";
        
        $stmt = $conn->prepare($services_query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $services_result = $stmt->get_result();
        
        $order["services"] = [];
        while ($service = $services_result->fetch_assoc()) {
            $order["services"][] = $service;
        }
    }
}

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Order Tracking - BnB Laundry</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding-top: 200px;
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
        .container {
            margin-top: 50px;
        }
        .input-section {
            background: #D0F6FF;
            padding: 30px;
            border-radius: 15px;
            display: inline-block;
        }
        .input-box {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 24px;
            margin: 5px;
            border: 2px solid #00a8e8;
            border-radius: 5px;
        }
        .order-details {
            background: white;
            padding: 30px;
            border-radius: 15px;
            display: inline-block;
            text-align: left;
            margin-top: 30px;
            width: 50%;
        }
        .status {
            background: orange;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
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
    <img src='/BNBLaundrySite/js/images/bnb_logo(1).png' alt='BnB' width='300'>
    <nav>
        <ul>
            <li><a href='index.php'>Home</a></li>
            <li><a href='order-tracking.php' class='active'>Order Tracking</a></li>
            <li><a href='services.php'>Services</a></li>
            <li><a href='about.php'>About Us</a></li>
            <li><a href='staff-login.php'>Staff Login</a></li>
        </ul>
    </nav>
</header>

<div class='container'>";

if (!$order_id) {
    // Order ID Input Section
    echo "
    <h1>Order ID Number</h1>
    <form method='GET' action='order-tracking.php'>
        <div class='input-section'>
            <input type='text' name='order_id' maxlength='6' style='font-size: 24px; text-align: center; width: 200px; padding: 10px; border: 2px solid #00a8e8; border-radius: 10px;' required>
            <br><br>
            <button type='submit' style='background: #00a8e8; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;'>Track Order</button>
        </div>
    </form>";
} else if ($order) {
    // Order Details Display
    echo "
    <h1>Order ID #$order_id</h1>
    <div class='order-details'>
        <h3>Customer Name</h3>
        <p style='background: #b3e0ff; padding: 10px; border-radius: 5px;'>{$order['Customer_Name']}</p>

        <h3>Services</h3>
        <table style='width: 100%; border-collapse: collapse; margin-top: 10px;'>
            <tr>
                <th style='background: #00a8e8; color: white; padding: 10px; border-radius: 5px;'>Service</th>
                <th style='background: #00a8e8; color: white; padding: 10px; border-radius: 5px;'>Quantity</th>
                <th style='background: #00a8e8; color: white; padding: 10px; border-radius: 5px;'>Weight (kg)</th>
            </tr>";
    
    foreach ($order['services'] as $service) {
        echo "
            <tr>
                <td style='background: #b3e0ff; padding: 10px; border-radius: 5px; text-align: center;'>{$service['Service_Name']}</td>
                <td style='background: #b3e0ff; padding: 10px; border-radius: 5px; text-align: center;'>{$service['Quantity']}</td>
                <td style='background: #b3e0ff; padding: 10px; border-radius: 5px; text-align: center;'>{$service['Weight']} kg</td>
            </tr>";
    }

    echo "
        </table>
        <h3>Status:</h3>
        <p class='status'>{$order['Status']}</p>
    </div>";
} else {
    // If Order ID is invalid
    echo "
    <h1>Order Not Found</h1>
    <p>The order ID you entered does not exist. Please check again.</p>
    <a href='order-tracking.php' style='color: #00a8e8;'>Try Again</a>";
}

echo "</div>

<footer class='footer'>
    <p>&copy; " . date("Y") . " BnB Laundry. All Rights Reserved.</p>
</footer>

</body>
</html>";
?>
