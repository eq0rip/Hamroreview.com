<?php
//get wpdb
if (is_file("../../../wp-blog-header.php")) {
include_once("../../../wp-blog-header.php");
}

global $newdbversion;
$newdbversion = 3000;


function Voteiu_Importdbconfig() {
global $user_level, $newdbversion;

if ($user_level != 10) {
?>
<p>You must be an administrator to access this area.</p>
<?php
} else {
?>
<h3>Vote It Up Database Patcher</h3>
<p>Current database version: <?php echo Voteiu_Dbversion(); ?></p>
<?php if (Voteiu_Dbversion() > $newdbversion) {
//current db version too high
?>
<p>I'm sorry but this patcher cannot be used on this version of the plugin</p>
<?php
} else {
if (Voteiu_Dbversion() == $newdbversion) {
?>
<p>Select a task</p>
<p><input type="radio" name="voteiudb_task" value="backup" checked /> Backup votes</p>
<p><input type="radio" name="voteiudb_task" value="restore" /> Restore votes</p>
<p><input type="radio" name="voteiudb_task" value="rollback" /> Rollback to previous version (warning: This will erase votes that were gained after upgrading)</p>
<p><input type="submit" value="Next" /></p>
<?php
} else {
?>
<p>Select a task</p>
<p><input type="radio" name="voteiudb_task" value="upgrade" checked /> Upgrade database</p>
<p><input type="radio" name="voteiudb_task" value="backup" /> Backup votes</p>
<p><input type="radio" name="voteiudb_task" value="restore" /> Restore votes</p>

<p><input type="submit" value="Next &raquo;" class="button-secondary action" /></p>
<?php
}
}
}
}

function Voteiu_Dbversion() {
if (get_option('voteiu_dbversion') == '') {
return 1000;
} else {
return get_option('voteiu_dbversion');
}
}

function Voteiu_Importdb() {
global $user_level, $wpdb, $newdbversion;

if ($user_level != 10) {
?>
<p>You must be an administrator to access this area.</p>
<?php
} else {
?>
<p><strong>Database patcher</strong></p>
<p>This patcher upgrades the Vote It Up database to version <?php echo $newdbversion; ?>.</p>
<?php

/*
wp_voteiu_data

DB structure:
ID
post
votes
type
*/


//Import old table
mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die(mysql_error());
mysql_select_db(DB_NAME) or die(mysql_error());
echo "Upgrading database to version ".$newdbversion."...<br />";

	$table_name = $wpdb->prefix.'voteiu_data';
	if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) {
	//Table does not exist, so create it
	//Version 3000
	$querycreatetable = "CREATE TABLE ".$wpdb->prefix."voteiu_data (
	ID int(11) NOT NULL auto_increment, 
	post int(11) NOT NULL, 
	vote text NOT NULL, 
	type text NOT NULL, 
	PRIMARY KEY (ID) );";
	$wpdb->query($querycreatetable);
	}

//Erase contents
mysql_query("TRUNCATE TABLE ".$wpdb->prefix."voteiu_data");
echo "Importing votes...<br />";

//Read from database v1. v2 will not be released to public.
//Order by ASC so that when each entry is added the one with the highest id will be added last
$oldtablecontents = mysql_query("SELECT * FROM ".$wpdb->prefix."votes ORDER BY ID ASC");

//Reading vote info for each post
while ($row = mysql_fetch_array($oldtablecontents)) {

//Votes
$votearray = explode(",", $row['votes']);
//Add each vote to table
$i = 0;
while ($i < count($votearray)) {
if ($votearray[$i] != '') {
$queryinsert = "INSERT INTO ".$wpdb->prefix."voteiu_data (post, vote, type) VALUES ("
."'".$row['post']."', "
."'".$votearray[$i]."', "
."'vote'"
.")";
//echo $queryinsert;
mysql_query($queryinsert) or die(mysql_error());
}
$i++;
}

//Sinks
$votearray = explode(",", $row['usersinks']);
//Add each vote to table
$i = 0;
while ($i < count($votearray)) {
if ($votearray[$i] != '') {
$queryinsert = "INSERT INTO ".$wpdb->prefix."voteiu_data (post, vote, type) VALUES ("
."'".$row['post']."', "
."'".$votearray[$i]."', "
."'sink'"
.")";
mysql_query($queryinsert) or die(mysql_error());
}
$i++;
}

//Guest Votes
$votearray = explode(",", $row['guests']);
//Add each vote to table
$i = 0;
while ($i < count($votearray)) {
if ($votearray[$i] != '') {
$queryinsert = "INSERT INTO ".$wpdb->prefix."voteiu_data (post, vote, type) VALUES ("
."'".$row['post']."', "
."'".$votearray[$i]."', "
."'guestvote'"
.")";
mysql_query($queryinsert) or die(mysql_error());
}
$i++;
}

//Guest Sinks
$votearray = explode(",", $row['guestsinks']);
//Add each vote to table
$i = 0;
while ($i < count($votearray)) {
if ($votearray[$i] != '') {
$queryinsert = "INSERT INTO ".$wpdb->prefix."voteiu_data (post, vote, type) VALUES ("
."'".$row['post']."', "
."'".$votearray[$i]."', "
."'guestsink'"
.")";
mysql_query($queryinsert) or die(mysql_error());
}
$i++;
}

$postdat = get_post($row['post']);
if ($postdat->post_title != '') {
echo "Post &quot;".$postdat->post_title."&quot; has been successfully imported<br />";
} else {
//echo "Post ID &quot;".$row['post']."&quot; (deleted post) has been successfully imported<br />";
}

}
echo "Import completed";
update_option('voteiu_dbversion', $newdbversion);
?>
<form method="get" action="">
<input type="hidden" name="page" value="voteitupconfig" />
<p><input type="submit" name="" value="Return to Vote It Up config" class="button-secondary action" /></p>
</form>
<?php
}
}

function Voteiu_Dbneedupdate() {
global $newdbversion;
if (Voteiu_Dbversion() < $newdbversion) {
return true;
} else {
return false;
}
}

switch ($_GET['action']) {
case "update":
//Voteiu_Importdbconfig();
Voteiu_Importdb();
break;
default:

break;
}
?>