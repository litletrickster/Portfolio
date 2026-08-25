<?php
include 'db.php'; // Database connection
session_start();

// Ensure user is logged in
if (!isset($_SESSION["staff_id"])) {
    header("Location: staff-login.php");
    exit();
}

// Fetch all orders and expenses
$orders = $conn->query("SELECT * FROM Orders ORDER BY Date_Created DESC")->fetch_all(MYSQLI_ASSOC);
$expenses = $conn->query("SELECT * FROM Expense ORDER BY Date DESC")->fetch_all(MYSQLI_ASSOC);

// Default filter is "this month"
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'month';

// Function to filter data based on "today," "this month," or "this year"
function filterData($data, $dateField, $filter) {
    $filteredData = [];
    $currentDate = date('Y-m-d');
    $currentMonth = date('Y-m');
    $currentYear = date('Y');

    foreach ($data as $row) {
        $rowDate = date('Y-m-d', strtotime($row[$dateField]));

        if (($filter == 'today' && $rowDate == $currentDate) ||
            ($filter == 'month' && strpos($rowDate, $currentMonth) === 0) ||
            ($filter == 'year' && strpos($rowDate, $currentYear) === 0)) {
            $filteredData[] = $row;
        }
    }
    return $filteredData;
}

// Filter orders and expenses based on selected filter
$filteredOrders = filterData($orders, 'Date_Created', $filter);
$filteredExpenses = filterData($expenses, 'Date', $filter);

// Calculate totals
$totalOrders = count($filteredOrders);
$totalRevenue = array_sum(array_column($filteredOrders, 'Total_Price'));
$totalExpenses = array_sum(array_column($filteredExpenses, 'Amount'));

// Calculate net income
$totalNetIncome = $totalRevenue - $totalExpenses;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate Reports | BnB Laundry</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4fbff; text-align: center; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); }
        .summary { display: flex; justify-content: space-between; gap: 20px; margin-bottom: 20px; }
        .summary div { background: #e3f2fd; padding: 20px; border-radius: 10px; text-align: center; width: 30%; font-size: 18px; font-weight: bold; }
        select, button { padding: 10px; font-size: 16px; margin: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #00a8e8; color: white; }
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
    .export-btn {
        display: inline-block;
        padding: 12px 20px;
        font-size: 16px;
        color: white;
        background-color: #007bb5;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }
    .export-btn:hover {
        background-color: #005f8d;
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
<div class="main-content">
<div class="container">
<!-- Add Export Form with Year Selection -->
<div style="margin-top: 20px;">
    <form action="export-sales-report.php" method="GET">
        <label for="export-year">Select Year for CSV:</label>
        <select name="year" id="export-year">
            <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                <option value="<?= $y ?>" <?= ($y == date('Y')) ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
        <button type="submit" class="export-btn">Download Sales Report (CSV)</button>
    </form>
</div>
    <h2>Generate Reports</h2>

    <!-- Filter Options -->
    <form method="GET">
        <label for="filter">Show Data For:</label>
        <select name="filter" id="filter">
            <option value="today" <?= $filter == 'today' ? 'selected' : '' ?>>Today</option>
            <option value="month" <?= $filter == 'month' ? 'selected' : '' ?>>This Month</option>
            <option value="year" <?= $filter == 'year' ? 'selected' : '' ?>>This Year</option>
        </select>
        <button type="submit">Apply</button>
    </form>

    <!-- Summary Section -->
    <div class="summary">
        <div>Total Orders<br><?= $totalOrders ?></div>
        <div>Total Revenue<br>₱<?= number_format($totalRevenue, 2) ?></div>
        <div>Total Expenses<br>₱<?= number_format($totalExpenses, 2) ?></div>
    </div>

    <!-- Table Section -->
    <h3>Transactions for <?= ucfirst($filter) ?></h3>
    <table>
        <tr>
            <th>Date</th>
            <th>Daily Gross Sale</th>
            <th>Expenses</th>
            <th>Net Daily Income</th>
            <th>Remarks</th>
        </tr>
        <?php
        $dates = [];
        foreach ($filteredOrders as $order) {
            $date = date('Y-m-d', strtotime($order['Date_Created']));
            if (!isset($dates[$date])) {
                $dates[$date] = ['sales' => 0, 'expenses' => 0, 'remarks' => []];
            }
            $dates[$date]['sales'] += $order['Total_Price'];
        }

        foreach ($filteredExpenses as $expense) {
            $date = date('Y-m-d', strtotime($expense['Date']));
            if (!isset($dates[$date])) {
                $dates[$date] = ['sales' => 0, 'expenses' => 0, 'remarks' => []];
            }
            $dates[$date]['expenses'] += $expense['Amount'];
            $dates[$date]['remarks'][] = $expense['Type'] . ": ₱" . number_format($expense['Amount'], 2);
        }

        ksort($dates);

        foreach ($dates as $date => $data) {
            $netIncome = $data['sales'] - $data['expenses'];
            echo "<tr>
                <td>$date</td>
                <td>₱" . number_format($data['sales'], 2) . "</td>
                <td>₱" . number_format($data['expenses'], 2) . "</td>
                <td>₱" . number_format($netIncome, 2) . "</td>
                <td>" . implode("; ", $data['remarks']) . "</td>
            </tr>";
        }
        ?>
    </table>

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
