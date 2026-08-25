<?php
include 'db.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION["staff_id"])) {
    header("Location: staff-login.php");
    exit();
}

// Handle Logout
if (isset($_POST["logout"])) {
    session_destroy();
    header("Location: staff-login.php");
    exit();
}

// Handle Order Deletion
if (isset($_GET["delete_id"])) {
    $delete_id = intval($_GET["delete_id"]);
    $stmt = $conn->prepare("DELETE FROM Orders WHERE Order_ID = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit();
}

// Fetch Summary Data
$total_orders = $conn->query("SELECT COUNT(*) AS total_orders FROM Orders")->fetch_assoc()["total_orders"];
$total_revenue = $conn->query("SELECT SUM(Total_Price) AS total_revenue FROM Orders")->fetch_assoc()["total_revenue"] ?: 0;
$total_expenses = $conn->query("SELECT SUM(Amount) AS total_expenses FROM Expense")->fetch_assoc()["total_expenses"] ?: 0;

// Filter Logic
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$price_min = isset($_GET['price_min']) ? floatval($_GET['price_min']) : 0;
$price_max = isset($_GET['price_max']) ? floatval($_GET['price_max']) : 1000000; // High default

$date_filter = "";
if ($filter === 'today') {
    $date_filter = "AND DATE(Orders.Date_Created) = CURDATE()";
} elseif ($filter === 'week') {
    $date_filter = "AND YEARWEEK(Orders.Date_Created, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($filter === 'month') {
    $date_filter = "AND MONTH(Orders.Date_Created) = MONTH(CURDATE()) AND YEAR(Orders.Date_Created) = YEAR(CURDATE())";
}

// Search Logic
$search_filter = "";
if (!empty($search)) {
    $search_filter = "AND (Orders.Order_ID LIKE ? OR Customers.Name LIKE ? OR Orders.Date_Created LIKE ?)";
}

// Fetch Transactions Based on Filter
$transactions_query = "
    SELECT Orders.Order_ID, Customers.Name AS Customer_Name, Orders.Total_Price, Orders.Status, Orders.Date_Created, Orders.Paid_Status
    FROM Orders 
    JOIN Customers ON Orders.Customer_ID = Customers.Customer_ID
    WHERE Orders.Total_Price BETWEEN ? AND ? 
    $date_filter 
    $search_filter 
    ORDER BY Orders.Date_Created DESC";

$stmt = $conn->prepare($transactions_query);

// Bind parameters dynamically
if (!empty($search)) {
    $search_param = "%" . $search . "%";
    $stmt->bind_param("dds", $price_min, $price_max, $search_param, $search_param, $search_param);
} else {
    $stmt->bind_param("dd", $price_min, $price_max);
}

$stmt->execute();
$transactions = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | BnB Laundry</title>
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4fbff;
            display: flex;
        }

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

        /* Main Content */
        .main-content {
            margin-left: 260px;
            width: 100%;
            padding: 20px;
        }

        /* Top Bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .logout-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .logout-btn:hover {
            background: #c0392b;
        }

        /* Content Section */
        .content-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #00a8e8;
            color: white;
        }

        /* Button */
        .action-btn {
            background: #007bb5;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-right: 5px;
        }
        .delete-btn {
            background: #e74c3c;
        }
        .action-btn:hover {
            background: #005f8d;
        }
        .delete-btn:hover {
            background: #c0392b;
        }
    </style>
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

<!-- Main Content -->
<div class="main-content">
    
    <div class="top-bar">
        <h1>Admin Dashboard</h1>
        <form method="POST">
            <button type="submit" name="logout" class="logout-btn">Logout</button>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="content-box">
        <h2>Transactions</h2>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Date</th>
                <th>Paid/Unpaid</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $transactions->fetch_assoc()) { ?>
                <tr>
                    <td>#<?= $row["Order_ID"] ?></td>
                    <td><?= htmlspecialchars($row["Customer_Name"]) ?></td>
                    <td>₱<?= number_format($row["Total_Price"], 2) ?></td>
                    <td><?= $row["Status"] ?></td>
                    <td><?= date('Y-m-d', strtotime($row["Date_Created"])) ?></td>
                    <td><?= $row["Paid_Status"] === "Paid" ? "Paid" : "Unpaid" ?></td>
                    <td>
                        <a href="view-order.php?order_id=<?= $row["Order_ID"] ?>" class="action-btn">View</a>
                        <a href="dashboard.php?delete_id=<?= $row["Order_ID"] ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
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
