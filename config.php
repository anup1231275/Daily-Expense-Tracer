<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = ""; // XAMPP default is empty
$database = "dailyexpense";

// Create connection
$conn = mysqli_connect("localhost", "root", "root123", "dailyexpense");


// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
