<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "itp_database";

// Create connection
$db_obj = new mysqli($servername, $username, $password, $database);

// Check connection
if (!$db_obj) {
  die("Connection failed: " . mysqli_connect_error());
}
?>