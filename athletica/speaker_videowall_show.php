<?php

/**********
 *
 *	speaker_videowall_show.php
 *	-----------
 *	
 */

require('./lib/cl_gui_videowall.lib.php');

require('./lib/common.lib.php');
require('./lib/cl_videowall.lib.php');

if(AA_connectToDB() == FALSE) {	// invalid DB connection
	return;
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}


$page = new GUI_Videowall('speaker_videowall');
$page->startPage();


?>



<?php

$page->endPage();
?>
