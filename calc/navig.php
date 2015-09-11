<html>
<style>
#nav
{
	background:RED;
	height:auto;
	padding-top:10px;
	padding-bottom:10px;
	width:100%;
}
#main
{
	background:BROWN;
	height:500;
	padding-top:10px;
	padding-bottom:10px;
	width:100%;
}
#form
{
	background:RED;
	height:500;
	padding-top:10px;
	padding-bottom:10px;
	width:100%;
}
#contact
{
	background:BLUE;
	height:500;
	padding-top:10px;
	padding-bottom:10px;
	width:100%;
}
.unchecked
{
display:none;	
}
.checked
{
	display:block;
}

</style>

<div id="nav">
<a href="javascript:change('main','form','contact');" name="main">HOME</a>
<a href="javascript:change('form','main','contact');" name="form">Form</a>
<a href="javascript:change('contact','main','form');" name="contact">contact</a>

</div>
<div id="main" class="unchecked">
this is home
</div>
<div id="form" class="unchecked">
this is form
</div>
<div id="contact" class="unchecked">
this is contact
</div>

<script>


function change(bhado1,bhado2,bhado3){
	
	document.getElementById(bhado1).className="checked";
	document.getElementById(bhado2).className="unchecked";
	document.getElementById(bhado3).className="unchecked";
}
</script>
</html>