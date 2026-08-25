<?php
include 'db.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION["staff_id"])) {
    header("Location: staff-login.php");
    exit();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle Add Service
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_service"])) {
    $service_name = $_POST["service_name"];
    $service_details = isset($_POST["service_details"]) ? $_POST["service_details"] : ''; 
    $service_type = isset($_POST["service_type"]) ? $_POST["service_type"] : 'Self-Service'; // Default to Self-Service
    $price_per_unit = $_POST["price_per_unit"];

    $stmt = $conn->prepare("INSERT INTO Services (Name, Service_Details, Service_Type, Price_per_unit) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $service_name, $service_details, $service_type, $price_per_unit);
    $stmt->execute();
}

// Handle Delete Service
if (isset($_GET["delete_id"])) {
    $delete_id = $_GET["delete_id"];
    $conn->query("DELETE FROM Services WHERE Service_ID = $delete_id");
    header("Location: manage-services.php");
    exit();
}

// Handle Edit Service
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_service"])) {
    $service_id = $_POST["service_id"];
    $service_name = $_POST["service_name"];
    $service_details = isset($_POST["service_details"]) ? $_POST["service_details"] : '';
    $service_type = isset($_POST["service_type"]) ? $_POST["service_type"] : 'Self-Service';
    $price_per_unit = $_POST["price_per_unit"];

    $stmt = $conn->prepare("UPDATE Services SET Name=?, Service_Details=?, Service_Type=?, Price_per_unit=? WHERE Service_ID=?");
    $stmt->bind_param("sssdi", $service_name, $service_details, $service_type, $price_per_unit, $service_id);
    $stmt->execute();
}

// Fetch all services
$services = $conn->query("SELECT * FROM Services ORDER BY Name ASC")->fetch_all(MYSQLI_ASSOC);

// If editing, fetch the service data
$edit_service = null;
if (isset($_GET["edit_id"])) {
    $edit_id = $_GET["edit_id"];
    $edit_service = $conn->query("SELECT * FROM Services WHERE Service_ID = $edit_id")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services | BnB Laundry</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4fbff; }
        .container { max-width: 800px; margin: 40px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #00a8e8; color: white; }
        .btn { background: #00a8e8; color: white; padding: 8px 12px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; }
        .btn:hover { background: #007bb5; }
        .delete-btn { background: #e74c3c; }
        .delete-btn:hover { background: #c0392b; }
        input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px; }

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
    <h2>Manage Services</h2>

    <!-- Add or Edit Service -->
    <form method="POST">
        <h3><?= isset($edit_service) ? "Edit Service" : "Add New Service" ?></h3>
        <input type="hidden" name="service_id" value="<?= $edit_service['Service_ID'] ?? '' ?>">

        <label>Service Name</label>
        <input type="text" name="service_name" value="<?= $edit_service['Name'] ?? '' ?>" required>

        <label>Service Details</label>
        <textarea name="service_details" required><?= $edit_service['Service_Details'] ?? '' ?></textarea>

        <label>Service Type</label>
        <select name="service_type" required>
            <option value="Self-Service" <?= (isset($edit_service) && $edit_service['Service_Type'] == 'Self-Service') ? 'selected' : '' ?>>Self-Service</option>
            <option value="Drop-Off" <?= (isset($edit_service) && $edit_service['Service_Type'] == 'Drop-Off') ? 'selected' : '' ?>>Drop-Off</option>
        </select>

        <label>Price per Unit (₱)</label>
        <input type="number" name="price_per_unit" value="<?= $edit_service['Price_per_unit'] ?? '' ?>" min="0" step="0.01" required>

        <?php if (isset($edit_service)) { ?>
            <button type="submit" name="update_service" class="btn">Update Service</button>
            <a href="manage-services.php" class="btn" style="background: gray;">Cancel</a>
        <?php } else { ?>
            <button type="submit" name="add_service" class="btn">Add Service</button>
        <?php } ?>
    </form>

    <!-- List of Services -->
    <h3>Existing Services</h3>
    <table>
        <tr>
            <th>Service ID</th>
            <th>Service Name</th>
            <th>Details</th>
            <th>Type</th>
            <th>Price per Unit</th>
            <th>Actions</th>
        </tr>
        <?php if (!empty($services)) { ?>
            <?php foreach ($services as $service) { ?>
                <tr>
                    <td><?= $service["Service_ID"] ?></td>
                    <td><?= $service["Name"] ?></td>
                    <td><?= $service["Service_Details"] ?></td>
                    <td><?= $service["Service_Type"] ?></td>
                    <td>₱<?= number_format($service["Price_per_unit"], 2) ?></td>
                    <td>
                        <a href="manage-services.php?edit_id=<?= $service['Service_ID'] ?>" class="btn">Edit</a>
                        <a href="manage-services.php?delete_id=<?= $service['Service_ID'] ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this service?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="6" style="text-align:center;">No services available</td>
            </tr>
        <?php } ?>
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
