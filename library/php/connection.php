<?php
$conn_hostname = "localhost";
$conn_username = "root";
$conn_password = "";
$conn_databasename = "library";

$conn = mysqli_connect($conn_hostname, $conn_username, $conn_password, $conn_databasename);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}