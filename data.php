<?php
$serverName = "localhost";
$userName = "root";
$password = "";
$dbname ="hamroreview";
$input1= array('ram','shyam');

$connect = mysqli_connect($serverName, $userName, $password, $dbname);

if (!$connect) {
    die("failed to connect" . mysqli_connect_error());
}
for($i=0;$i<50;$i++)
{
$EID="";
$salary=rand(2000,2600);
$room=rand(10,100);
$age=rand(18,31);
$sex="M";
$working=rand(8,19);
$family=rand(2,8);
$marital=array_rand($input1,2);
$type="sd";
$period=rand(1,9);


$sql="INSERT INTO `employee`(`EmployeeID`, `Salary`, `Room no.`, `Age`, `Sex`, `Working hour`, `Family member`, `Marital status`, `Contract type`, `Contract period`) VALUES('',".$salary.",".$room.",".$age.",'".$sex."',".$working.",".$family.",'".$marital."','".$type."',".$period.");";
echo $sql;
mysqli_query($connect,$sql);



}

?>