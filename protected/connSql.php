<?php
$servername = "localhost";
$dbname = "backendDatabase";
$dbuser = "admin";
$dbpass = "password"; // i know this looks bad, and looks unsecure and stuff. i dont care right now. sql cant be accessed from the internet anyways.

$conn = new mysqli($servername, $dbuser, $dbpass, $dbname, 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
 
?>