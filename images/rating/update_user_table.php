<?php
include "connect.php";
$post_id=$_REQUEST['post_id'];
$user_id=$_REQUEST['user_id'];


$sql="UPDATE  user_rating_check SET review='".$_REQUEST['text']."' where post_id=".$post_id." and user_id=".$user_id;
mysqli_query($connect,$sql);
echo $sql;

?>