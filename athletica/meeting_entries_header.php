<?php

/**********
 *
 *	meeting_entries_header.php
 *	--------------------------
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

$page = new GUI_Page('meeting_entries_header');
$page->startPage();
$page->printPageTitle($strEntries . ": " . $_COOKIE['meeting']);

$menu = new GUI_Menulist();
$menu->addButton("meeting_entry_add.php", $strNewEntry, "detail");
$menu->addButton("meeting_entries_startnumbers.php", "$strStartnumbers ...", "detail");
$menu->addButton("meeting_entries_setteams.php", "$strTeamsAutoAssign", "detail");
$menu->addButton("meeting_entries_setgroups.php", $strCombinedGroupsAutoAssign, "detail");
$menu->addButton('meeting_entries_print.php', "$strPrint ...", "detail");
//$menu->addButton('meeting_entries_export.php', "$strExport (Excel)", "detail");
$menu->addButton($cfgURLDocumentation . 'help/meeting/entries.html', $strHelp, '_blank');
$menu->addSearchfield('meeting_entries_search.php', 'detail');
$menu->printMenu();

$page->endPage();
