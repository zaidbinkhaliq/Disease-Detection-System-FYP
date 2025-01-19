<?php
// Database credentials
$servername = "localhost";   // Server name or IP address
$username = "root"; // Database username
$password = "123456789@Pakistan"; // Database password
$dbname = "credentials"; // Name of the database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// No connection closure here; it will be done in login.php
?>


