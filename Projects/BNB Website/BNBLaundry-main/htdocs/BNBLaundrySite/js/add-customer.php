<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];

    $stmt = $conn->prepare("INSERT INTO Customers (Name, Phone_Number, Email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $phone, $email);
    $stmt->execute();

    echo "<script>alert('Customer added successfully!'); window.location.href = 'manage-customers.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Customer | BnB Laundry</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4fbff; text-align: center; }
        .container { max-width: 400px; margin: 50px auto; background: white; padding: 20px; border-radius: 10px; }
        input { width: 100%; padding: 8px; margin-bottom: 10px; }
        .btn { background: #00a8e8; color: white; padding: 10px; border: none; width: 100%; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #007bb5; }
    </style>
</head>
<body>

<div class="container">
    <h2>Add New Customer</h2>
    <form method="POST">
        <label>Full Name</label>
        <input type="text" name="name" required>

        <label>Phone Number</label>
        <input type="text" name="phone" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <button type="submit" class="btn">Add Customer</button>
    </form>
</div>

</body>
</html>
