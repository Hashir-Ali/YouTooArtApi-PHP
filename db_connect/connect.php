<?php
$servername = "localhost";
$username = "root";
$password = "";
$db = "youtooart";

//Creating connection
$conn = new mysqli($servername, $username, $password, $db);

//Checking connection
if ($conn->connect_error) {
	die("Connection Failed: ". $conn->connect_error);
}
