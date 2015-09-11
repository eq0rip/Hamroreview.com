<?php
include "connect.php";
$post_id=$_REQUEST['post_id'];
$user_id=$_REQUEST['user_id'];
$marks=$_REQUEST['marks'];

function insert($marks,$user_id,$post_id) {
	global $connect;
	global $rating_overall;
//insert users rating to his own table user_rating_check
$sql="INSERT into user_rating_check (id,marks,user_id,post_id) VALUES ('',".$marks.",".$user_id.",".$post_id.")";
mysqli_query($connect,$sql);
//echo "Thanks for your vote";

//new rating affects overall rating so update table "rating"
$sql="select marks from rating where post_id=".$post_id;
$result=mysqli_query($connect,$sql);
if(mysqli_num_rows($result)==0){
	$sql="INSERT into rating (id,post_id,marks) VALUES ('',".$post_id.",".$marks.")";
	mysqli_query($connect,$sql);
	$new_overall=$marks;
	echo $new_overall;
}
else
{
$row=mysqli_fetch_assoc($result);
$result=$row['marks'];
$new_overall=($result+$marks)/2;
$new_overall=intval($new_overall);
$sql="UPDATE rating SET marks=".$new_overall." where post_id=".$post_id;

mysqli_query($connect,$sql);
echo $new_overall;
}
}



//dont allow revote
$sql="SELECT * from user_rating_check where user_id=".$user_id." and post_id=".$post_id;
$result=mysqli_query($connect,$sql);
if(mysqli_num_rows($result)>0)
{
	echo "already voted";
}
else insert($marks,$user_id,$post_id);

?>