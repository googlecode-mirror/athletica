<?php

/**********
 *
 *	meeting_teams_header.php
 *	------------------------
 *	
 */

require('./lib/cl_gui_button.lib.php');
require('./lib/cl_gui_menulist.lib.php');
require('./lib/cl_gui_page.lib.php');

require('./lib/common.lib.php');

if(AA_connectToDB() == FALSE)	{				// invalid DB connection
	return;		// abort
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}

//
//	Display data
// ------------

$page = new GUI_Page('meeting_teams_header');
$page->startPage();
$page->printPageTitle($strTeams . ": " . $_COOKIE['meeting']);

$menu = new GUI_Menulist();
$menu->addButton("meeting_teams_print.php", "$strPrint ...", "detail");
$menu->addButton("meeting_team_add.php?cat=$category", $strNewEntry, "detail");
$menu->addButton($cfgURLDocumentation . 'help/meeting/teams.html', $strHelp, '_blank');
$menu->printMenu();
$page->endPage();
