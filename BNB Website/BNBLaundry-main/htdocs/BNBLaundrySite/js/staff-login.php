<?php
include 'db.php';
session_start(); // Ensure session is started

// If user is already logged in, redirect to dashboard
if (isset($_SESSION["staff_id"])) {
    header("Location: dashboard.php");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = ""; // Stores error messages

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Query to fetch staff details
    $stmt = $conn->prepare("SELECT * FROM Staff WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $staff = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $staff["Password"])) {
            // Successful login
            $_SESSION["staff_id"] = $staff["Staff_ID"];
            $_SESSION["staff_name"] = $staff["Name"];
            $_SESSION["staff_username"] = $staff["Username"];

            header("Location: dashboard.php"); // Redirect to dashboard
            exit();
        } else {
            $message = "Invalid username or password.";
        }
    } else {
        $message = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Login | BnB Laundry</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4fbff; text-align: center; }
        .container { max-width: 400px; margin: 50px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); }
        input { width: 100%; padding: 8px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ccc; }
        .btn { background: #00a8e8; color: white; padding: 10px; border: none; width: 100%; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #007bb5; }
        .message { color: red; font-weight: bold; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Staff Login</h2>

    <?php if (!empty($message)): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit" class="btn">Login</button>
    </form>
</div>

</body>
</html>
