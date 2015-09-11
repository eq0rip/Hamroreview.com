<?php

include "images/rating/connect.php";

$post_id=get_the_ID();
$logged_user=wp_get_current_user();
$user_id=$logged_user->ID;

$sql="select author_review , marks from rating where post_id=".$post_id;
$result=mysqli_query($connect,$sql);
if(mysqli_num_rows($result)>0){
			$row=mysqli_fetch_assoc($result);
			$marks2=$row['marks'];
			$author_review=$row['author_review'];
			
			
}
else $marks2=0;

$sql="select marks , review from user_rating_check where post_id=".$post_id." and user_id=".$user_id;
$result=mysqli_query($connect,$sql);
if(mysqli_num_rows($result)>0){
			$row=mysqli_fetch_assoc($result);
			$marks1=$row['marks'];
			$user_review=$row['review'];
			
			
			
}
else $marks1=0;


?>

<style>

#content {
	width:100% !important;
	color:white !important;
	
}
.content_blog {
	width:100%;
	
}

.post_single {
	width:100%;
	
}
#sidebar_main {
	display:none;
}

#review_main {
	width:850px;
	height:400px;
	background:#013C58;
	float:left;
	margin-top:10px;
	color:white;
}

#rating_wrapper {
		//background:#013C58;
	
		color:white;
		width:360px;
		height:600px;
		//display:inline-block;
		float:right;
		//margin-top:10px;
		//margin-right:10px;
		//margin-left:10px;
}
#user_area {
		//background:#07A0DA;
		background:#013C58;
		color:white;
		box-shadow: 0 0 30px rgba(81, 203, 238, 1);
		width:340px;
		height:400px;
		float:left;
		margin-top:10px;
		margin-right:10px;
		margin-left:10px;
}

#author_area {
		background:rgba(81, 203, 238, 1);
		//background:#013C58;
		color:white;
		box-shadow:0 0 30px blue;
		width:auto;
		height:auto;
		float:left;
		margin-top:10px;
		margin-right:10px;
		margin-left:10px;
		padding-top:5px;
		padding-bottom:5px;
}
#user_rating {
		height:20px;
		width:240px;
		float:left;
		//box-shadow: 0 0 5px black;
		//padding: 3px 0px 3px 3px;
		margin: 0px 10px;
	   // border: 1px solid rgba(81, 203, 238, 1);
}

#user_review {
		background-color:transparent;
		color:white;
		height:300px;
		width:320px;
		float:left;
		box-shadow: 0 0 5px black;
		padding: 3px 0px 3px 3px;
		margin: 5px 5px 3px 5px;
	   // border: 1px solid rgba(81, 203, 238, 1);
}
#button_accept {
	
	height:auto;
	width:auto;
	float:right;
	margin-right:30px;
	text-decoration:none;
	padding:3px 3px 3px 3px;
}
#overall_rating {
		height:20px;
		width:260px;
		float:left;
		//box-shadow: 0 0 5px black;
		//padding: 3px 0px 3px 3px;
		margin: 0px 10px;
	   // border: 1px solid rgba(81, 203, 238, 1);
	
}
#author_review {
	background-color:transparent;
	color:white;
	//height:220px;
	//width:320px;
	float:right;
	box-shadow: 0 0 5px black;
	padding: 3px 0px 3px 3px;
	margin: 10px 5px 3px 5px;
   // border: 1px solid rgba(81, 203, 238, 1);
}
.ratings_empty1 {
            background: url('images/rating/rating_empty.png') no-repeat;
            float:      left;
            height:     28px;
            padding:    2px;
            width:      20px;
        }
.ratings_empty2 {
            background: url('images/rating/rating_empty.png') no-repeat;
            float:      left;
            height:     28px;
            padding:    2px;
            width:      20px;
        }	
.rating_full1 {
	background: url('images/rating/rating_full.png') no-repeat;
}		

.rating_full2 {
	background: url('images/rating/rating_full.png') no-repeat;
}
</style>
<script src="images/rating/js/jquery-1.11.2.js" type="text/javascript"></script>
<script>

function testos(text){
	//document.getElementById('user_review').value="<?php echo $user_review;?>";
		var post_id="<?php echo get_the_ID();?>"
		var user_id="<?php 
		$logged_user=wp_get_current_user();
		echo $logged_user->ID;
		?>"	


var datastr="text="+text+"&user_id="+user_id+"&post_id="+post_id;
$.ajax({url:"images/rating/update_user_table.php",data:datastr,success:function(resultback){
	alert('Your review is submitted');

}
});

}
$(document).ready(function(){
	show_overall_rating('<?php echo $marks1;?>','<?php echo $marks2;?>');
	document.getElementById('user_review').value="<?php echo $user_review;?>";
	var author_review="<?php echo $author_review; ?>";
	document.getElementById('author_review').innerHTML=author_review;
	$('.ratings_empty1').hover(function(){
		$(this).prevAll().addBack().addClass('rating_full1');
		$(this).nextAll().removeClass('rating_full');
	},
	function(){
		$(this).prevAll().addBack().removeClass('rating_full1');
	}
	);
	$( ".ratings_empty1" ).bind( "click", function() {
		var num=$(this).attr("id");
		var marks1=num.replace('star1','');
		
		document.getElementById('message').innerHTML="Thanks for voting";
		
		var post_id="<?php echo get_the_ID();?>"
		var user_id="<?php 
		$logged_user=wp_get_current_user();
		echo $logged_user->ID;
		?>"
		
		var data_rating="marks="+marks1+"&post_id="+post_id+"&user_id="+user_id;
		$.ajax({url:"images/rating/change_rating.php",data:data_rating,success:function(result){//returns "already voted" if voted else returns new overall rating
			 
			if(result=="already voted")
			{
				document.getElementById('message').innerHTML="You Have Already voted";
			
				
				
			}
			else
				{
					var marks2=result;
					
					
				
					
				}
				show_overall_rating(marks1,marks2);
				
			
			
		}
			
		});
		
	});
	
});

function show_overall_rating(marks1,marks2){
		$('#star2'+marks2).prevAll().addBack().css({background:"url('images/rating/rating_full.png') no-repeat"});
		if(marks2==0){
			document.getElementById('overall').innerHTML='marks1';
			$('#star2'+marks1).prevAll().addBack().css({background:"url('images/rating/rating_full.png') no-repeat"});
			
		};
		$('#star1'+marks1).prevAll().addBack().css({background:"url('images/rating/rating_full.png') no-repeat"});
		//document.getElementById('message').innerHTML+=marks2;
		document.getElementById('overall').innerHTML=marks2;
		document.getElementById('your_rating').innerHTML=marks1;
	
}
</script>




<div id="review_main" style="color:red;">
ss
<?php
echo get_post_field('post_content', $post_id);
?>
</div>
<script>
document.getElementById('review_main').style.color="white";
</script>
	
<div id="rating_wrapper">
	<div id="user_area">
		<div id="user_rating">
				<div class="ratings_empty1" id="star11"></div>
				<div class="ratings_empty1" id="star12"></div>
				<div class="ratings_empty1" id="star13"></div>
				<div class="ratings_empty1" id="star14"></div>
				<div class="ratings_empty1" id="star15"></div>
				<div class="ratings_empty1" id="star16"></div>
				<div class="ratings_empty1" id="star17"></div>
				<div class="ratings_empty1" id="star18"></div>
				<div class="ratings_empty1" id="star19"></div>
				<div class="ratings_empty1" id="star110"></div>

		</div>&nbsp Your &nbsp&nbsp Rating:<span id="your_rating"></span>/10<br/>
		
		<textarea id="user_review" placeholder="Write your review here" rows="10" cols="38px"></textarea>
		
		<span id="message"></span><div id="button_accept"><a href="javascript:testos(document.getElementById('user_review').value);">ACCEPT</a></div>
	</div>
	<div id="author_area">
		<div id="author_review">
		</div>
		<div id="overall_rating">
				<div class="ratings_empty2" id="star21"></div>
				<div class="ratings_empty2" id="star22"></div>
				<div class="ratings_empty2" id="star23"></div>
				<div class="ratings_empty2" id="star24"></div>
				<div class="ratings_empty2" id="star25"></div>
				<div class="ratings_empty2" id="star26"></div>
				<div class="ratings_empty2" id="star27"></div>
				<div class="ratings_empty2" id="star28"></div>
				<div class="ratings_empty2" id="star29"></div>
				<div class="ratings_empty2" id="star210"></div>

			
		</div>Overall rating:<span id="overall"></span>/10<br/>		
	</div>
	
</div>
