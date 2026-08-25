<?php
include 'db.php';

session_start();

// Ensure user is logged in
if (!isset($_SESSION["staff_id"])) {
    header("Location: staff-login.php");
    exit();
}

// Set PHP timezone to Philippines (Asia/Manila)
date_default_timezone_set('Asia/Manila');

// Fetch customers
$customers = $conn->query("SELECT * FROM Customers ORDER BY Name ASC")->fetch_all(MYSQLI_ASSOC);

// Fetch available services
$services = $conn->query("SELECT * FROM Services ORDER BY Name ASC")->fetch_all(MYSQLI_ASSOC);

$message = "";

// Handle Order Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create_order"])) {
    $customer_id = $_POST["customer_id"];
    $payment_method = $_POST["payment_method"];
    $amount_paid = 0; // Default since payment is not made yet
    $total_price = 0;
    $status = "Pending"; // Automatically set to Pending

    // Generate PHP timestamp (Philippine Time)
    $timestamp = date('Y-m-d H:i:s', time());
    // Ensure at least one service is added
    if (!isset($_POST["services"]) || empty($_POST["services"])) {
        $message = "Error: Please add at least one service.";
    } else {
        $conn->begin_transaction(); // Start transaction

        // Insert into Orders table (Force correct PHP timestamp)
        $stmt = $conn->prepare("INSERT INTO Orders (Customer_ID, Total_Price, Status, Payment_Method, Amount_Paid, Date_Created) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Error preparing order insert: " . $conn->error);
        }
        $stmt->bind_param("idssds", $customer_id, $total_price, $status, $payment_method, $amount_paid, $timestamp);
        $stmt->execute();
        $order_id = $stmt->insert_id;

        // Ensure Order_ID is generated before inserting services
        if (!$order_id) {
            die("Error: Order ID not generated.");
        }

        // Process each service added
        foreach ($_POST["services"] as $index => $service) {
            if (!isset($service["service_id"]) || empty($service["service_id"])) {
                die("Error: Missing Service ID.");
            }

            $service_id = (int) $service["service_id"];
            $weight = isset($service["weight"]) ? (float) $service["weight"] : 0;
            $quantity = isset($service["quantity"]) ? (int) $service["quantity"] : 1;

            // Ensure service exists
            $service_data = $conn->query("SELECT Price_per_unit, Service_Type FROM Services WHERE Service_ID = $service_id")->fetch_assoc();
            if (!$service_data) {
                die("Error: Service not found for ID: $service_id");
            }
            $price_per_unit = $service_data["Price_per_unit"];
            $service_type = $service_data["Service_Type"];

            // Calculate price based on service type
            if (strtolower($service_type) == "self-service") {
                $price = $price_per_unit * $quantity;
            } else { // Drop-Off
                $price = $price_per_unit * $weight;
            }

            // ** Accumulate total price correctly **
            $total_price += $price;

            // Insert into Order_Service table
            $stmt = $conn->prepare("INSERT INTO Order_Service (Order_ID, Service_ID, Weight, Quantity, Price) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) {
                die("Error preparing Order_Service insert: " . $conn->error);
            }
            $stmt->bind_param("iiddd", $order_id, $service_id, $weight, $quantity, $price);
            if (!$stmt->execute()) {
                die("Error inserting into Order_Service: " . $stmt->error);
            }
        }

        // ** NOW UPDATE Orders table with correct total_price **
        $stmt = $conn->prepare("UPDATE Orders SET Total_Price = ?, Date_Created = ? WHERE Order_ID = ?");
        if (!$stmt) {
            die("Error preparing order update: " . $conn->error);
        }
        $stmt->bind_param("dsi", $total_price, $timestamp, $order_id);
        if (!$stmt->execute()) {
            die("Error updating total price in Orders: " . $stmt->error);
        }

        $conn->commit(); // Commit transaction
        $message = "Order successfully created!";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Record Order | BnB Laundry</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4fbff; text-align: center; }
        .container { max-width: 600px; margin: 50px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); }
        input, select { width: 100%; padding: 8px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ccc; }
        .btn { background: #00a8e8; color: white; padding: 10px; border: none; width: 100%; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #007bb5; }
        .message { color: red; font-weight: bold; margin-bottom: 10px; }
        .service-box { background: #e3f2fd; padding: 10px; border-radius: 5px; margin-bottom: 10px; }
/* Default Sidebar */
.sidebar {
    width: 250px;
    height: 100vh;
    background: #00a8e8;
    color: white;
    padding-top: 20px;
    position: fixed;
    left: 0;
    top: 0;
    transition: width 0.3s ease-in-out;
    z-index: 1000;
    overflow: hidden;
}

.sidebar h2 {
    text-align: center;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap; /* Prevents text from breaking into multiple lines */
}

/* Ensure menu items remain visible */
.sidebar ul {
    padding: 0;
    list-style: none;
    transition: opacity 0.3s ease-in-out;
}

/* Sidebar buttons (links) */
.sidebar ul li {
    padding: 15px;
    text-align: center;
}

.sidebar ul li a {
    color: white;
    text-decoration: none;
    display: block;
    font-size: 18px;
    transition: background 0.3s, color 0.3s;
}

.sidebar ul li a:hover {
    background: #007bb5;
    border-radius: 5px;
}

/* Closed Sidebar */
.sidebar.closed {
    width: 0;
    padding: 0;
}

/* Hide sidebar content when collapsed */
.sidebar.closed ul {
    opacity: 0;
    pointer-events: none; /* Prevent interactions */
}

/* Main content adjusts */
.main-content {
    margin-left: 260px;
    transition: margin-left 0.3s ease-in-out;
}

/* When sidebar is closed, push content */
.main-content.full {
    margin-left: 0;
}

/* Toggle Button */
.toggle-sidebar {
    position: fixed;
    top: 10px;
    left: 10px;
    background: #00a8e8;
    color: white;
    border: none;
    padding: 10px;
    cursor: pointer;
    z-index: 1001;
}

.toggle-sidebar:hover {
    background: #007bb5;
}
    </style>
    <script>
        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.service-box').forEach(box => {
                let serviceDropdown = box.querySelector('.service-type');
                let selectedOption = serviceDropdown.options[serviceDropdown.selectedIndex];
                let pricePerUnit = parseFloat(selectedOption.getAttribute("data-price")) || 0;
                let serviceType = selectedOption.getAttribute("data-type");
                let weight = parseFloat(box.querySelector('.weight').value) || 0;
                let quantity = parseInt(box.querySelector('.quantity').value) || 0;

                if (serviceType.toLowerCase() === "self-service") {
                    total += pricePerUnit * quantity;
                } else {
                    total += pricePerUnit * weight * quantity;
                }
            });
            document.getElementById("total_price").innerText = "Total: ₱" + total.toFixed(2);
            document.getElementById("total_price_input").value = total.toFixed(2);
        }

        function addServiceField() {
    let container = document.getElementById("service-container");
    let serviceIndex = document.querySelectorAll('.service-box').length; // Unique index for each service
    let serviceBox = document.createElement("div");
    serviceBox.classList.add("service-box");
    serviceBox.innerHTML = `
        <label>Service</label>
        <select class="service-type" name="services[${serviceIndex}][service_id]" required onchange="calculateTotal()">
            <?php foreach ($services as $service) { ?>
                <option value="<?= $service['Service_ID'] ?>" data-price="<?= $service['Price_per_unit'] ?>" data-type="<?= $service['Service_Type'] ?>">
                    <?= $service['Name'] ?> (₱<?= $service['Price_per_unit'] ?> per unit)
                </option>
            <?php } ?>
        </select>
        <label>Weight (kg)</label>
        <input type="number" class="weight" name="services[${serviceIndex}][weight]" min="0.1" step="0.1" oninput="calculateTotal()" required>
        <label>Quantity</label>
        <input type="number" class="quantity" name="services[${serviceIndex}][quantity]" min="1" step="1" oninput="calculateTotal()" required>
        <button type="button" onclick="this.parentElement.remove(); calculateTotal()">Remove</button>
    `;
    container.appendChild(serviceBox);
}

    </script>
</head>
<body>
<button class="toggle-sidebar" onclick="toggleSidebar()">☰ Menu</button>
<!-- Sidebar -->
<div class="sidebar">
    <h2>BnB Laundry</h2>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="record-transactions.php">Record Orders</a></li>
        <li><a href="manage-customers.php">Manage Customers</a></li>
        <li><a href="manage-services.php">Manage Services</a></li>
        <li><a href="expense-tracking.php">Track Expenses</a></li>
        <li><a href="generate-reports.php">Generate Reports</a></li>
        <li><a href="create-announcement.php">Create Announcements</a></li>
    </ul>
</div>
<div class="main-content">
<div class="container">
    <h2>Record New Order</h2>

    <?php if ($message): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Select Customer</label>
        <select name="customer_id" required>
            <?php foreach ($customers as $customer) { ?>
                <option value="<?= $customer['Customer_ID'] ?>"><?= $customer['Name'] ?> (<?= $customer['Phone_Number'] ?>)</option>
            <?php } ?>
        </select>

        <h3>Services</h3>
        <div id="service-container"></div>
        <button type="button" onclick="addServiceField()">+ Add Service</button>

        <label>Payment Method</label>
        <select name="payment_method" required>
            <option value="Cash">Cash</option>
            <option value="Card">Card</option>
            <option value="E-Wallet">E-Wallet</option>
        </select>

        <h3 id="total_price">Total: ₱0.00</h3>
        <input type="hidden" name="total_price" id="total_price_input" value="0">
        <button type="submit" name="create_order" class="btn">Submit Order</button>
    </form>
</div>
</div>
<script>
function toggleSidebar() {
    let sidebar = document.querySelector('.sidebar');
    let content = document.querySelector('.main-content');

    // Toggle 'closed' class for all screen sizes
    sidebar.classList.toggle('closed');
    content.classList.toggle('full');
}
</script>
</body>
</html>
