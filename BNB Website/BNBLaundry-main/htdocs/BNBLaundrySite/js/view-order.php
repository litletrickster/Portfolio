<?php
include 'db.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION["staff_id"])) {
    header("Location: staff-login.php");
    exit();
}

// Get Order ID from URL
if (!isset($_GET["order_id"])) {
    die("Error: Order ID is missing.");
}

$order_id = intval($_GET["order_id"]);

// Handle Order Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_order"])) {
    $new_status = $_POST["status"];
    $new_paid_status = $_POST["paid_status"];

    $stmt = $conn->prepare("UPDATE Orders SET Status = ?, Paid_Status = ? WHERE Order_ID = ?");
    $stmt->bind_param("ssi", $new_status, $new_paid_status, $order_id);
    
    if ($stmt->execute()) {
        $message = "Order updated successfully!";
    } else {
        $message = "Error updating order: " . $conn->error;
    }
}

// Fetch Order Details
$order_query = "
    SELECT Orders.Order_ID, Customers.Name AS Customer_Name, Customers.Phone_Number, Customers.Email, 
           Orders.Total_Price, Orders.Amount_Paid, Orders.Status, Orders.Paid_Status, Orders.Date_Created 
    FROM Orders 
    JOIN Customers ON Orders.Customer_ID = Customers.Customer_ID
    WHERE Orders.Order_ID = $order_id";
$order_result = $conn->query($order_query);

if ($order_result->num_rows === 0) {
    die("Error: Order not found.");
}

$order = $order_result->fetch_assoc();

// Fetch Services in the Order
$services_query = "
    SELECT Services.Name AS Service_Name, Order_Service.Weight, Order_Service.Quantity, Order_Service.Price
    FROM Order_Service
    JOIN Services ON Order_Service.Service_ID = Services.Service_ID
    WHERE Order_Service.Order_ID = $order_id";
$services_result = $conn->query($services_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Order | BnB Laundry</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4fbff;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        .order-details, .service-details {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #00a8e8;
            color: white;
        }
        .btn {
            background: #007bb5;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            text-align: center;
            width: 100%;
            margin-top: 10px;
            font-size: 16px;
        }
        .btn:hover {
            background: #005f8d;
        }
        .alert {
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }
        label {
            font-weight: bold;
        }
        select, input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Order Details</h2>

    <?php if (isset($message)): ?>
        <div class="alert <?= strpos($message, 'successfully') !== false ? 'alert-success' : 'alert-error' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <div class="order-details">
        <p><strong>Order ID:</strong> <?= $order["Order_ID"] ?></p>
        <p><strong>Customer:</strong> <?= htmlspecialchars($order["Customer_Name"]) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($order["Phone_Number"]) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($order["Email"]) ?></p>
        <p><strong>Date Created:</strong> <?= date('Y-m-d H:i:s', strtotime($order["Date_Created"])) ?></p>
        <p><strong>Total Price:</strong> ₱<?= number_format($order["Total_Price"], 2) ?></p>
        <p><strong>Amount Paid:</strong> ₱<?= number_format($order["Amount_Paid"], 2) ?></p>
    </div>

    <h3>Services in Order</h3>
    <div class="service-details">
        <table>
            <tr>
                <th>Service Name</th>
                <th>Weight (kg)</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
            <?php while ($service = $services_result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($service["Service_Name"]) ?></td>
                    <td><?= number_format($service["Weight"], 2) ?> kg</td>
                    <td><?= $service["Quantity"] ?></td>
                    <td>₱<?= number_format($service["Price"], 2) ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <h3>Edit Order</h3>
    <form method="POST">
        <label for="status">Order Status:</label>
        <select name="status" id="status">
            <option value="Pending" <?= ($order["Status"] == "Pending") ? "selected" : "" ?>>Pending</option>
            <option value="Finished" <?= ($order["Status"] == "Finished") ? "selected" : "" ?>>Finished</option>
        </select>

        <label for="paid_status">Payment Status:</label>
        <select name="paid_status" id="paid_status">
            <option value="Not Paid" <?= ($order["Paid_Status"] == "Not Paid") ? "selected" : "" ?>>Not Paid</option>
            <option value="Paid" <?= ($order["Paid_Status"] == "Paid") ? "selected" : "" ?>>Paid</option>
        </select>

        <button type="submit" name="update_order" class="btn">Save Changes</button>
    </form>
</div>

</body>
</html>
