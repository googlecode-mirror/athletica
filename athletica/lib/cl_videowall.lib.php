<?php

if (!defined('AA_CL_VIDEOWALL_LIB_INCLUDED'))
{
	define('AA_CL_VIDEOWALL_LIB_INCLUDED', 1);


/********************************************
 *
 * CLASS Videowall
 *
 * Providing functionality for editing and showing the video walls
 *
 *******************************************/

class Videowall{
	
	
	var $settings;
	
	
	function getSettings($meeting){
		
		$res = mysql_query("SELECT * FROM videowand WHERE xMeeting = $meeting ORDER BY Bildnr");
		
		if(mysql_errno() > 0){
			AA_printErrorMsg(mysql_errno().": ".mysql_error());
		}else{
			
			if(mysql_num_rows($res) > 0){
				$this->settings = array();
				
				while($row = mysql_fetch_assoc($res)){
					$this->settings[] = $row;
				}
				
				return $this->settings;
			}else{
				$this->settings = 0;
				return 0;
			}
		}
		
		return false;
	}
	
	
	function addScreen($meeting){
		
		if($this->settings == 0){
			// add first screen without any settings
			mysql_query("INSERT INTO videowand SET xMeeting = $meeting, Bildnr = 1");
		}else{
			// add screen with same global settings as last screen
			mysql_query("INSERT INTO videowand SET
						xMeeting = $meeting
						, X = '".$this->settings[0]['X']."'
						, Y = '".$this->settings[0]['Y']."'
						, Aktualisierung = '".$this->settings[0]['Aktualisierung']."'
						, Status = '".$this->settings[0]['Status']."'
						, Bildnr = '".($this->settings[0]['Bildnr']+1)."'
						");
			
		}
	}
}


}
?>
