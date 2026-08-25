<?php
include 'db.php';

session_start();

// Ensure user is logged in
if (!isset($_SESSION["staff_id"])) {
    header("Location: staff-login.php");
    exit();
}

// Set PHP timezone to Philippine Time (Asia/Manila)
date_default_timezone_set('Asia/Manila');

$message = ""; // Stores success or error messages

// Handle Add Expense
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_expense"])) {
    $staff_id = $_SESSION["staff_id"];
    $type = $_POST["expense_type"];
    $amount = $_POST["amount"];
    $note = $_POST["note"];
    $timestamp = date('Y-m-d H:i:s'); // Get the current time in PHP

    if ($amount <= 0) {
        $message = "Error: Amount must be greater than zero.";
    } else {
        $stmt = $conn->prepare("INSERT INTO Expense (Staff_ID, Type, Amount, Note, Date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isdss", $staff_id, $type, $amount, $note, $timestamp);

        if ($stmt->execute()) {
            $message = "Expense recorded successfully!";
        } else {
            $message = "Error: Could not record expense.";
        }
    }
}

// Handle Delete Expense
if (isset($_GET["delete_id"])) {
    $delete_id = $_GET["delete_id"];
    $conn->query("DELETE FROM Expense WHERE Expense_ID = $delete_id");
    header("Location: expense-tracking.php");
    exit();
}

// Fetch Expenses (Filtered by Type if Selected)
$filter = isset($_GET["type"]) ? $_GET["type"] : "";
$query = "SELECT e.Expense_ID, e.Type, e.Amount, e.Note, e.Date, s.Name AS Staff_Name 
          FROM Expense e
          JOIN Staff s ON e.Staff_ID = s.Staff_ID";
if (!empty($filter)) {
    $query .= " WHERE e.Type = '$filter'";
}
$query .= " ORDER BY e.Date DESC"; // Sort by latest
$expenses = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Expense Tracking | BnB Laundry</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4fbff; }
        .container { max-width: 1000px; margin: 40px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; }

        /* Buttons */
        .btn, .dashboard-btn { display: inline-block; padding: 10px 15px; background: #007bb5; color: white; text-decoration: none; border-radius: 5px; cursor: pointer; border: none; }
        .btn:hover, .dashboard-btn:hover { background: #005f8d; }
        .dashboard-btn { margin-bottom: 20px; }

        /* Modal Styling */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.4); backdrop-filter: blur(5px); justify-content: center; align-items: center; }
        .modal-content { background: white; padding: 25px; border-radius: 12px; width: 35%; box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.15); text-align: center; animation: fadeIn 0.3s; }
        .modal-header { font-size: 20px; font-weight: bold; margin-bottom: 15px; color: #007bb5; text-align: left; }
        .close-btn { color: #e74c3c; float: right; font-size: 22px; cursor: pointer; font-weight: bold; }
        .close-btn:hover { color: darkred; }
        .modal form input, .modal form select, .modal form textarea { width: 100%; padding: 10px; margin: 10px 0; border-radius: 8px; border: 1px solid #ccc; }
        .modal form button { width: 100%; padding: 12px; background: #007bb5; color: white; border-radius: 8px; border: none; cursor: pointer; font-size: 16px; }
        .modal form button:hover { background: #005f8d; }
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
        /* Table */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: white; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #00a8e8; color: white; }
        .delete-btn { background: #e74c3c; color: white; padding: 5px 10px; border: none; cursor: pointer; border-radius: 5px; text-decoration: none; font-size: 14px; }
        .delete-btn:hover { background: #c0392b; }

        /* Footer */
        footer { text-align: center; margin-top: 20px; padding: 15px; background: white; border-radius: 10px; box-shadow: 0px -2px 4px rgba(0, 0, 0, 0.1); }

        /* Fade-in animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
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
    <h2>Expense Tracking</h2>

    <!-- Add Expense Button -->
    <button class="btn" onclick="document.getElementById('expenseModal').style.display='flex'">+ Add Expense</button>

    <!-- Modal for Adding Expenses -->
    <div id="expenseModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="document.getElementById('expenseModal').style.display='none'">&times;</span>
            <div class="modal-header">Record New Expense</div>
            <form method="POST">
                <label>Expense Type</label>
                <select name="expense_type" required>
                    <option value="Detergents">Detergents</option>
                    <option value="Electricity">Electricity</option>
                    <option value="Water">Water</option>
                    <option value="Gas">Gas</option>
                    <option value="Internet">Internet</option>
                    <option value="Rent">Rent</option>
                    <option value="Pondo">Pondo</option>
                    <option value="Miscellaneous">Miscellaneous</option>
                </select>

                <label>Amount (₱)</label>
                <input type="number" name="amount" min="1" step="0.01" required>

                <label>Note (Optional)</label>
                <textarea name="note"></textarea>

                <button type="submit" name="add_expense">Add Expense</button>
            </form>
        </div>
    </div>

    <!-- Expense Table -->
    <table>
        <tr>
            <th>Expense ID</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Note</th>
            <th>Date</th>
            <th>Recorded By</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($expenses as $expense): ?>
            <tr>
                <td><?= $expense["Expense_ID"] ?></td>
                <td><?= $expense["Type"] ?></td>
                <td>₱<?= number_format($expense["Amount"], 2) ?></td>
                <td><?= $expense["Note"] ?: "N/A" ?></td>
                <td><?= date('Y-m-d', strtotime($expense["Date"])) ?></td>
                <td><?= $expense["Staff_Name"] ?></td>
                <td><a href="?delete_id=<?= $expense['Expense_ID'] ?>" class="delete-btn">Delete</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</div>
<footer>&copy; 2025 BnB Laundry. All Rights Reserved.</footer>

<script>
    window.onclick = function(event) {
        let modal = document.getElementById('expenseModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
<script>
function toggleSidebar() {
    let sidebar = document.querySelector('.sidebar');
    let content = document.querySelector('.main-content');

    // Toggle 'closed' class for all screen sizes
    sidebar.classList.toggle('closed');
    content.classList.toggle('full');
}
</script
</body>
</html>
