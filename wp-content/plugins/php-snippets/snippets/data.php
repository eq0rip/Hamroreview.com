<?php
$serverName = "localhost";
$userName = "root";
$password = "";
$dbname ="hamroreview";

$EID="2";
$salary="2000";
$room="3";
$age="13";
$sex="M";
$working="12";
$family="3";
$marital="sd";
$type="sd";
$period="22";

$connect = mysqli_connect($serverName, $userName, $password, $dbname);

if (!$connect) {
    die("failed to connect" . mysqli_connect_error());
}
for($i=0;$i<50;$i++)
{
$sql="INSERT INTO `employee`(`EmployeeID`, `Salary`, `Room no.`, `Age`, `Sex`, `Working hour`, `Family member`, `Marital status`, `Contract type`, `Contract period`) VALUES ($EID,$salary,$room,$age,$sex,$working,$family,$marital,$type,$period)";

mysqli_query($connect,$sql);


}

?>