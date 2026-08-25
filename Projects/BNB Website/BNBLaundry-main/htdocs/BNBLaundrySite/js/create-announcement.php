<?php
include 'db.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION["staff_id"])) {
    header("Location: create-announcement.php");
    exit();
}

$message = "";

// Handle DELETE Announcement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_announcement"])) {
    $delete_id = intval($_POST["delete_id"]);
    $stmt = $conn->prepare("DELETE FROM Announcements WHERE Announcement_ID = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        header("Location: create-announcement.php?msg=deleted");
        exit();
    } else {
        $message = "Error: Could not delete announcement.";
    }
}

// Handle CREATE Announcement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create_announcement"])) {
    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $staff_id = $_SESSION["staff_id"];

    if (empty($title) || empty($content)) {
        $message = "Error: Title and Content cannot be empty.";
    } else {
        $stmt = $conn->prepare("INSERT INTO Announcements (Staff_ID, Title, Content, Date) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $staff_id, $title, $content);

        if ($stmt->execute()) {
            header("Location: create-announcement.php?msg=created");
            exit();
        } else {
            $message = "Error: Could not create announcement.";
        }
    }
}

// Handle EDIT Announcement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_announcement"])) {
    $edit_id = intval($_POST["edit_id"]);
    $edit_title = trim($_POST["edit_title"]);
    $edit_content = trim($_POST["edit_content"]);

    if (!empty($edit_title) && !empty($edit_content)) {
        $stmt = $conn->prepare("UPDATE Announcements SET Title = ?, Content = ?, Date = NOW() WHERE Announcement_ID = ?");
        $stmt->bind_param("ssi", $edit_title, $edit_content, $edit_id);

        if ($stmt->execute()) {
            header("Location: create-announcement.php?msg=updated");
            exit();
        } else {
            $message = "Error updating announcement.";
        }
    } else {
        $message = "Error: Title and Content cannot be empty.";
    }
}

// Fetch Announcements
$announcements = $conn->query("
    SELECT a.Announcement_ID, a.Title, a.Content, a.Date, s.Name AS Staff_Name 
    FROM Announcements a
    JOIN Staff s ON a.Staff_ID = s.Staff_ID
    ORDER BY a.Date DESC
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Announcements | BnB Laundry</title>
    <style>
        
        body { font-family: Arial, sans-serif; background-color: #f4fbff; text-align: center; }
        .container { max-width: 600px; margin: 50px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); }
        input, textarea { width: 100%; padding: 8px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ccc; }
        .btn { background: #00a8e8; color: white; padding: 10px; border: none; width: 100%; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #007bb5; }
        .message { color: red; font-weight: bold; margin-bottom: 10px; }
        .announcement-box { background: #e3f2fd; padding: 10px; border-radius: 5px; margin-bottom: 10px; text-align: left; position: relative; }
        .edit-btn, .delete-btn { padding: 6px 10px; border: none; border-radius: 5px; cursor: pointer; margin-right: 5px; }
        .edit-btn { background: #f4b400; color: white; }
        .delete-btn { background: #e74c3c; color: white; }
        .edit-btn:hover { background: #c79100; }
        .delete-btn:hover { background: #c0392b; }
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
        /* Modal (Popup) Styling */
        .modal {
            display: none; 
            position: fixed; 
            left: 0; top: 0; width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.5); 
            justify-content: center; 
            align-items: center;
        }
        .modal-content {
            background: white; padding: 20px; border-radius: 10px; width: 40%;
            box-shadow: 0px 4px 8px rgba(0,0,0,0.2); text-align: center;
        }
        .modal-header {
            font-size: 20px; font-weight: bold; margin-bottom: 15px; text-align: left;
        }
        .close-btn {
            color: #e74c3c; float: right; font-size: 22px; cursor: pointer;
        }
        .close-btn:hover { color: darkred; }
    </style>
</head>
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
<body>
<div class="main-content">
<div class="container">
    <h2>Create Announcement</h2>
    <?php if ($message): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Title</label>
        <input type="text" name="title" required>

        <label>Content</label>
        <textarea name="content" rows="4" required></textarea>

        <button type="submit" name="create_announcement" class="btn">Create Announcement</button>
    </form>
</div>

<div class="container">
    <h2>Existing Announcements</h2>
    <?php foreach ($announcements as $announcement): ?>
        <div class="announcement-box">
            <h4><?= htmlspecialchars($announcement["Title"]) ?></h4>
            <p><?= nl2br(htmlspecialchars($announcement["Content"])) ?></p>
            <small>By <?= htmlspecialchars($announcement["Staff_Name"]) ?> on <?= date("F j, Y, g:i a", strtotime($announcement["Date"])) ?></small>
            <br>
            <button class="edit-btn" onclick="openEditModal(<?= $announcement['Announcement_ID'] ?>, '<?= htmlspecialchars(addslashes($announcement["Title"])) ?>', '<?= htmlspecialchars(addslashes($announcement["Content"])) ?>')">Edit</button>
            <form method="POST" style="display:inline;">
                <input type="hidden" name="delete_id" value="<?= $announcement['Announcement_ID'] ?>">
                <button type="submit" name="delete_announcement" class="delete-btn" onclick="return confirm('Are you sure you want to delete this?');">Delete</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>
</div>
<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeEditModal()">&times;</span>
        <h3>Edit Announcement</h3>
        <form method="POST">
            <input type="hidden" name="edit_id" id="edit_id">
            <label>Title</label>
            <input type="text" name="edit_title" id="edit_title" required>
            <label>Content</label>
            <textarea name="edit_content" id="edit_content" rows="4" required></textarea>
            <button type="submit" name="edit_announcement" class="btn">Update</button>
        </form>
    </div>
</div>

<script>
function openEditModal(id, title, content) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_content').value = content;
    document.getElementById('editModal').style.display = "flex";
}
function closeEditModal() {
    document.getElementById('editModal').style.display = "none";
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
</script>
</body>
</html>
