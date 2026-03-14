<?php
//this is to connect to MySQL

$servername = "localhost";
$username = "root";
$password = "";



//create connection
$conn = new mysqli($servername, $username, $password, "webDProject");


//check connection
if($conn->connect_error)
{
    die("Connection failed: " . $conn->connect_error);
}
    //echo "Connected Successfully";
?>