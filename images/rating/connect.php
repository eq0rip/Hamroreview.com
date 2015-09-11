<?php
$serverName = "localhost";
$userName = "root";
$password = "";
$dbname ="hamroreview";


$connect = mysqli_connect($serverName, $userName, $password, $dbname);

if (!$connect) {
    die("failed to connect" . mysqli_connect_error());
}

?>