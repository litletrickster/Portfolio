<?php
session_start(); // Starts a new session or resumes an existing session to track user data across pages.

$servername = "sql201.infinityfree.com"; // The hostname of the database server.
$username = "if0_38341256"; // The username used to authenticate with the database.
$password = "ITS120L5300"; // The password for the database user.
$dbname = "if0_38341256_bnb_protosite"; // The name of the database to be used.

// Establishes a connection to the MySQL database using the provided credentials.
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Checks if the connection was successful. If it fails, it terminates the script and displays an error message.
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error()); // Outputs the connection error message and stops script execution.
}
?>
