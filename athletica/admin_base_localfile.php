<?php

/********************
 *
 *	admin_base_localfile.php
 *	---------
 *	update base from local file
 *
 *******************/

$noMeetingCheck = true;
 
require('./lib/cl_gui_menulist.lib.php');
require('./lib/cl_gui_page.lib.php');

require('./lib/common.lib.php');

require('./lib/cl_xml_data.lib.php');

if(AA_connectToDB() == FALSE)	{		// invalid DB connection
	return;
}


$page = new GUI_Page('admin_base_localfile');
$page->startPage();
$page->printPageTitle($strBaseUpdate);

$menu = new GUI_Menulist();
$menu->addButton($cfgURLDocumentation . 'help/administration/base.html', $strHelp, '_blank');
$menu->printMenu();


set_time_limit(3600); // the script will break if this is not set

$xml = new XML_data();

/*mysql_query("LOCK TABLES base_athlete WRITE, base_account WRITE
	, base_performance WRITE, base_relay WRITE
	, base_svm WRITE");*/


/*mysql_query("	SELECT * INTO OUTFILE '".$temppath."base_athlete.txt'
		FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
		LINES TERMINATED BY '\n'
		FROM base_athlete");*/
if(mysql_errno() > 0){
	AA_printErrorMsg(mysql_errno().": ".mysql_error());
	break; // important
}else{
	mysql_query("TRUNCATE TABLE base_athlete");
}
/*mysql_query("	SELECT * INTO OUTFILE '".$temppath."base_account.txt'
		FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
		LINES TERMINATED BY '\n'
		FROM base_account");*/
if(mysql_errno() > 0){
	AA_printErrorMsg(mysql_errno().": ".mysql_error());
	break; // important
}else{
	mysql_query("TRUNCATE TABLE base_account");
}
/*mysql_query("	SELECT * INTO OUTFILE '".$temppath."base_performance.txt'
		FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
		LINES TERMINATED BY '\n'
		FROM base_performance");*/
if(mysql_errno() > 0){
	AA_printErrorMsg(mysql_errno().": ".mysql_error());
	break; // important
}else{
	mysql_query("TRUNCATE TABLE base_performance");
}
/*mysql_query("	SELECT * INTO OUTFILE '".$temppath."base_relay.txt'
		FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
		LINES TERMINATED BY '\n'
		FROM base_relay");*/
if(mysql_errno() > 0){
	AA_printErrorMsg(mysql_errno().": ".mysql_error());
	break; // important
}else{
	mysql_query("TRUNCATE TABLE base_relay");
}
/*mysql_query("	SELECT * INTO OUTFILE '".$temppath."base_svm.txt'
		FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
		LINES TERMINATED BY '\n'
		FROM base_svm");*/
if(mysql_errno() > 0){
	AA_printErrorMsg(mysql_errno().": ".mysql_error());
	break; // important
}else{
	mysql_query("TRUNCATE TABLE base_svm");
}

//mysql_query("DELETE FROM verein WHERE xCode != ''");

//mysql_query("UNLOCK TABLES");

$file = $_GET['file'];
if(is_file($file)){
	
	$xml->load_xml($file, "base");
	
}

$page->endPage();
?>
