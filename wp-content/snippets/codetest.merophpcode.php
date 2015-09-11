<div style="background:gray;width:600;height:500;margin:auto auto;align:center;">
<?php
$serverName = "localhost";
$userName = "root";
$password = "";
$dbname ="hamroreview";


$connect = mysqli_connect($serverName, $userName, $password, $dbname);

if (!$connect) {
    die("failed to connect" . mysqli_connect_error());
}
$sql="";
echo $sql;
mysqli_query($connect,$sql);
mysqli_close($connect);



?>
</div>
