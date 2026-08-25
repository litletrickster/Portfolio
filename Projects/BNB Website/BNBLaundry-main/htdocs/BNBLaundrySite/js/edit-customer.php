<?php
include 'db.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION["staff_id"])) {
    header("Location: staff-login.php");
    exit();
}

// Check if edit_id is provided
if (!isset($_GET["edit_id"])) {
    die("Error: Customer ID is missing.");
}

$customer_id = intval($_GET["edit_id"]);

// Fetch existing customer details
$stmt = $conn->prepare("SELECT * FROM Customers WHERE Customer_ID = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

if (!$customer) {
    die("Error: Customer not found.");
}

// Handle form submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_customer"])) {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone_number"]);

    if (empty($name) || empty($email) || empty($phone)) {
        $message = "Error: All fields are required.";
    } else {
        $update_stmt = $conn->prepare("UPDATE Customers SET Name = ?, Email = ?, Phone_Number = ? WHERE Customer_ID = ?");
        $update_stmt->bind_param("sssi", $name, $email, $phone, $customer_id);

        if ($update_stmt->execute()) {
            header("Location: manage-customers.php?success=Customer updated successfully");
            exit();
        } else {
            $message = "Error: Could not update customer.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Customer | BnB Laundry</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4fbff; text-align: center; }
        .container { max-width: 500px; margin: 50px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); }
        input { width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ccc; }
        .btn { background: #00a8e8; color: white; padding: 10px; border: none; width: 100%; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #007bb5; }
        .message { color: red; font-weight: bold; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Customer</h2>

    <?php if ($message): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Full Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($customer['Name']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($customer['Email']) ?>" required>

        <label>Phone Number</label>
        <input type="text" name="phone_number" value="<?= htmlspecialchars($customer['Phone_Number']) ?>" required>

        <button type="submit" name="update_customer" class="btn">Update Customer</button>
    </form>

    <br>
    <a href="manage-customers.php" style="text-decoration: none; color: #00a8e8;">← Back to Customer Management</a>
</div>

</body>
</html>
