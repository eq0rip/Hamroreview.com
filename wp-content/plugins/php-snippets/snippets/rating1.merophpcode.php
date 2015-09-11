
<style>
.ratings_empty {
            background: url('images/rating/rating_empty.png') no-repeat;
            float:      left;
            height:     28px;
            padding:    2px;
            width:      32px;
        }
.rating_wrapper {
		background:#013C58;
		width:360px;
		height:400px;
		display:inline-block;
		float:right;
		margin-top:10px;
		margin-right:10px;
		margin-left:10px;
		
		
		
}
.rating_interact {
		background:#07A0DA;
		padding-top:15px;
		
		width:100%;
		height:100px;
		
}
.rating_current_cursor {
	background: url('images/rating/rating_full.png') no-repeat;
}
.rating_current_hitler {
	background: url('images/rating/rating_half.png') no-repeat;
}
</style>
<script src="images/rating/js/jquery-1.11.2.js" type="text/javascript"></script>
<script type="text/javascript" src="images/rating/js/rating.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $(".ratings_empty").hover(function(){
     $(this).prevAll().addBack().addClass('rating_current_cursor');
	 $(this).nextAll().removeClass('rating_current_cursor');
    },
	function() {
		$(this).prevAll().addBack().removeClass('rating_current_cursor');
	}
	);
	
	$(".ratings_empty").click(function() {
		var num=$(this).attr("id");
		var marks=num.replace('star','');
	    var post_id="<?php echo get_the_ID();?>"
		var user_id="<?php 
		$logged_user=wp_get_current_user();
		echo $logged_user->ID;
		?>"
		
		var data_rating="marks="+marks+"&post_id="+post_id+"&user_id="+user_id;
		
		
		
		$.ajax({url:"images/rating/change_rating.php",data:data_rating,success:function(result){//returns "already voted" if voted else returns new overall rating
			 
			if(result=="already voted")
			{
				document.getElementById('show').innerHTML+="already";
				
				
			}
			else
				{
					document.getElementById('show').innerHTML+=result;
					showNewrating(result);
					
					
					
				}
			
			
		}
			
		});
	});
				
});


function showrating(x){
	//$('#star10').prevAll().addBack().css({background:"url('images/rating/rating_empty.png') no-repeat"});
	$('#star'+x).prevAll().addBack().css({background:"url('images/rating/rating_full.png') no-repeat"});
}
</script>
<?php
include 'images/rating/connect.php';

?>

<div class="rating_wrapper" id="main">
	<div class="rating_interact">
		<div class="ratings_empty" id="star1"></div>
		<div class="ratings_empty" id="star2"></div>
		<div class="ratings_empty" id="star3"></div>
		<div class="ratings_empty" id="star4"></div>
		<div class="ratings_empty" id="star5"></div>
		<div class="ratings_empty" id="star6"></div>
		<div class="ratings_empty" id="star7"></div>
		<div class="ratings_empty" id="star8"></div>
		<div class="ratings_empty" id="star9"></div>
		<div class="ratings_empty" id="star10"></div>
		<div id="show">hehe</div>
	</div>
<div>
<?php
if(is_user_logged_in()){ //check if user is logged in
	$logged_user=wp_get_current_user();
	
		$sql="SELECT marks FROM user_rating_check where user_id=".$logged_user->ID." and post_id=".get_the_ID();
		$result=mysqli_query($connect,$sql);
		if(mysqli_num_rows($result)>0){
			
			$row=mysqli_fetch_assoc($result);
			$rating=$row['marks'];
			echo "your rating:".$rating."/10<br/>";
		}
		else{
			echo "Please vote";
		}
	 
}else {
	echo "Please Login to rate";
}
   
$sql="SELECT marks FROM rating where post_id=".get_the_ID();
$result=mysqli_query($connect,$sql);
if(mysqli_num_rows($result)>0){
	while($row=mysqli_fetch_assoc($result)){
		$rating_overall=$row['marks'];
	}
}
?>
<script>showrating(<?php echo $rating_overall?>);</script>

<?php

echo "overall rating:$rating_overall"."/10";
?>
</div>
</div>

<script>
function showNewrating(x) {
	$('#star10').prevAll().addBack().css({background:"url('images/rating/rating_empty.png') no-repeat"});
	$('#star'+x).prevAll().addBack().css({background:"url('images/rating/rating_full.png') no-repeat"});
}
</script>
