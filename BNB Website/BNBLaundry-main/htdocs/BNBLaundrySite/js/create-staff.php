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

$message = ""; // Stores success or error message

// Handle staff registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $phone = trim($_POST["phone"]);
    $email = trim($_POST["email"]);
    $username = trim($_POST["username"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Securely hash password

    // Check if email or username already exists
    $stmt = $conn->prepare("SELECT * FROM Staff WHERE Email = ? OR Username = ?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "Error: Email or Username already exists.";
    } else {
        // Insert new staff into database
        $stmt = $conn->prepare("INSERT INTO Staff (Name, Phone_Number, Email, Username, Password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $phone, $email, $username, $password);

        if ($stmt->execute()) {
            $message = "Staff registered successfully!";
        } else {
            $message = "Error: Could not register staff.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Staff | BnB Laundry</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4fbff; text-align: center; }
        .container { max-width: 400px; margin: 50px auto; background: white; padding: 20px; border-radius: 10px; }
        input { width: 100%; padding: 8px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ccc; }
        .btn { background: #00a8e8; color: white; padding: 10px; border: none; width: 100%; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #007bb5; }
        .message { color: red; font-weight: bold; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Create Staff Account</h2>

    <?php if ($message): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Full Name</label>
        <input type="text" name="name" required>

        <label>Phone Number</label>
        <input type="text" name="phone" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit" class="btn">Register Staff</button>
    </form>
</div>

</body>
</html>
