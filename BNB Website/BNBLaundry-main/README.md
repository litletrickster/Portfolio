BnB Laundry Management System 🧺
A web-based laundry management system for efficient order tracking, customer management, and financial reporting.

📌 Features
User Management: Staff can log in and manage customers, orders, and services.
Order Tracking: Customers can track their orders via order ID.
Service Management: Add, edit, and delete services offered by the laundry shop.
Expense Tracking: Log daily expenses to monitor financials.
Sales & Expense Reports: Generate and export financial reports in CSV format.
Announcements: Staff can create and manage announcements.
🛠️ Tech Stack
Frontend: HTML, CSS, JavaScript
Backend: PHP
Database: MySQL
Hosting: InfinityFree (for testing)
Version Control: Git & GitHub
📂 Project Setup (Local Development)
Follow these steps to set up the project on your local machine:

1️⃣ Clone the Repository
sh
Copy
Edit
git clone https://github.com/yourusername/bnb-laundry-management.git
cd bnb-laundry-management
2️⃣ Set Up Local Server
You need a local server environment like XAMPP or MAMP.

Install XAMPP (Windows) or MAMP (Mac).
Start Apache and MySQL from the control panel.
Place the project folder inside htdocs (XAMPP) or MAMP/htdocs.
3️⃣ Configure Database
Open phpMyAdmin (http://localhost/phpmyadmin).
Create a new database:
sql
Copy
Edit
CREATE DATABASE bnb_laundry;
Import the database schema from the bnb_laundry.sql file in the project.
4️⃣ Configure Database Connection
Edit db.php to match your local database settings:

php
Copy
Edit
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bnb_laundry";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
5️⃣ Start the Application
Open your browser and navigate to:
arduino
Copy
Edit
http://localhost/bnb-laundry-management/
Login as a staff member using:
vbnet
Copy
Edit
Username: admin
Password: admin123 (default)
📤 Deployment
If deploying to a free hosting service like InfinityFree, follow these steps:

Upload all project files via FTP.
Create a MySQL database in the hosting panel.
Update the database credentials in db.php.
Make sure mod_rewrite is enabled for friendly URLs.
