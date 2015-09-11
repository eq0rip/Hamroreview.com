<?php
//=====================================
//==  DEBUG utilities
//=====================================

define('DEBUG_FILE_NAME', 'debug.log');

if (!function_exists('ds')) { function ds(&$var) { 	if (is_user_logged_in()) dumpScreen($var); } }
if (!function_exists('df')) { function df(&$var) { 	if (is_user_logged_in()) dumpFile($var); } }
if (!function_exists('dm')) { function dm($var) {	if (is_user_logged_in()) dieMsg($var); } }
if (!function_exists('tm')) { function tm($var) {	if (is_user_logged_in()) traceMsg($var); } }

function dieMsg($msg) {
	traceMsg($msg);
	die($msg);
}

function traceMsg($msg) {
	//file_put_contents(DEBUG_FILE_NAME, date('d.m.Y H:i:s')." $msg\n", FILE_APPEND);
}

function dumpVar(&$var) {
	return textDump($var);
}

function dumpScreen(&$var) {
	if ((is_array($var) || is_object($var)) && count($var))
		echo "<pre>\n".nl2br(htmlspecialchars(textDump($var)))."</pre>\n";
	else
		echo "<tt>".nl2br(htmlspecialchars(textDump($var)))."</tt>\n";
}

function dumpFile(&$var) {
	traceMsg("\n\n".textDump($var));
}

function textDump(&$var, $level=0)  {
	if (is_array($var)) $type="Array[".count($var)."]";
	else if (is_object($var)) $type="Object";
	else $type="";

	if ($type) {
		$rez = "$type\n";
		for (Reset($var), $level++; list($k, $v)=each($var); ) {
			if (is_array($v) && $k==="GLOBALS") continue;
			for ($i=0; $i<$level*3; $i++) $rez .= " ";
			$rez .= $k.' => '. textDump($v, $level);
		}
	} else
		$rez = '"'.$var.'"'."\n";
	return $rez;
}

function dumpWP_is($query=null) {
global $wp_query;
if (!$query) $query = $wp_query;
echo "<tt>"
	."<br>admin=".is_admin()
	."<br>main_query=".is_main_query()."  query=".$query->is_main_query()
	."<br>query->is_posts_page=".$query->is_posts_page
	."<br>home=".is_home()."  query=".$query->is_home()
	."<br>fp=".is_front_page()."  query=".$query->is_front_page()
	."<br>search=".is_search()."  query=".$query->is_search()
	."<br>category=".is_category()."  query=".$query->is_category()
	."<br>tag=".is_tag()."  query=".$query->is_tag()
	."<br>archive=".is_archive()."  query=".$query->is_archive()
	."<br>day=".is_day()."  query=".$query->is_day()
	."<br>month=".is_month()."  query=".$query->is_month()
	."<br>year=".is_year()."  query=".$query->is_year()
	."<br>author=".is_author()."  query=".$query->is_author()
	."<br>page=".is_page()."  query=".$query->is_page()
	."<br>single=".is_single()."  query=".$query->is_single()
	."<br>singular=".is_singular()."  query=".$query->is_singular()
	."<br>attachment=".is_attachment()."  query=".$query->is_attachment()
	."<br>WooCommerce=".is_woocommerce_page()
	."<br><br />"
	."</tt>";
}

?>