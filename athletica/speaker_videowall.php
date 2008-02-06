<?php

/**********
 *
 *	speaker_videowall.php
 *	-----------
 *	
 *	configure the videowall, multiple screens
 */

require('./lib/cl_gui_button.lib.php');
require('./lib/cl_gui_page.lib.php');
require('./lib/cl_gui_menulist.lib.php');
require('./lib/cl_gui_searchfield.lib.php');

require('./lib/common.lib.php');
require('./lib/cl_videowall.lib.php');

if(AA_connectToDB() == FALSE) {	// invalid DB connection
	return;
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}


$page = new GUI_Page('speaker_videowall');
$page->startPage();
$page->printPageTitle($strVideowall);

/*$menu = new GUI_Menulist();
$menu->addButton('speaker_videowall.php?act=add', $strVideowallAdd, "_self");
$menu->addButton('speaker_videowall_show.php', $strVideowallPreview, "_blank', 'width=1024,height=768,left=50,top=50");
$menu->addButton($cfgURLDocumentation . 'help/speaker/index.html', $strHelp, '_blank');
$menu->printMenu();*/
?>

... in Arbeit ...

<?php

$page->endPage();
?>
