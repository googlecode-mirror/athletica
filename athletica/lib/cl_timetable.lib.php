<?php

if (!defined('AA_CL_TIMETABLE_LIB_INCLUDED'))
{
	define('AA_CL_TIMETABLE_LIB_INCLUDED', 1);

/********************************************
 *
 * CLASS Timetable
 *
 *		Timetable maintenance
 *		
 *******************************************/

class Timetable
{
	var $date;
	var $event;
	var $hour;
	var $round;
	var $min;
	var $type;
	var $group;
	var $etime; // enrolement time
	var $mtime; // manipulation time (stellzeit)

	/*		Timetable()
	 * 	----------- 
	 *		Gets session variables
	 */
	function Timetable()
	{
		$this->date = $_POST['date'];
		$this->event = $_POST['item'];
		$this->round = $_POST['round'];
		$this->type = $_POST['roundtype'];
		$this->hour = $_POST['hr'];
		$this->min = $_POST['min'];
		$this->group = $_POST['g'];
		$this->etime = $_POST['etime'];
		$this->mtime = $_POST['mtime'];
	}

	/*		add()
	 *  	-----
	 *		add a new event round
	 */
	function add()
	{
		require('./lib/utils.lib.php');
		$GLOBALS['AA_ERROR'] = '';

		// Error: Empty fields
		if(empty($this->date) || empty($this->hour) || empty($this->min)
			|| empty($this->event))
		{
			$GLOBALS['AA_ERROR'] = $GLOBALS['strErrEmptyFields'];
		}
		else
		{
			// Error: Invalid time
			if(($this->hour<0) || ($this->hour>23)
				|| ($this->min<0) || ($this->min>59))
			{
				$GLOBALS['AA_ERROR'] = $GLOBALS['strErrInvalidTime'];
			}
			else
			{
				mysql_query("LOCK TABLES wettkampf, rundentyp READ, runde WRITE");
				// check if event is valid
				if(AA_utils_checkReference("wettkampf", "xWettkampf",
					$this->event)==0)
				{
					$GLOBALS['AA_ERROR'] = $GLOBALS['strEvent'] . $GLOBALS['strErrNotValid'];
				}
				else
				{
					// check if roundtype is valid
					if((!empty($this->type))
						&& (AA_utils_checkReference("rundentyp", "xRundentyp",
							$this->type) == 0))
					{
						$GLOBALS['AA_ERROR'] = $GLOBALS['strType'] . $GLOBALS['strErrNotValid'];
					}elseif(empty($this->type)){ // round type has to be given!!
						$GLOBALS['AA_ERROR'] = $GLOBALS['strType'] . $GLOBALS['strErrNotValid'];
					}
					// OK: try to add round
					else
					{
						
						if(!empty($this->etime)){
							$et = AA_formatEnteredTime($this->etime);
							$sqlEtime = ", Appellzeit = '$et[0]:$et[1]:00'";
						}
						if(!empty($this->mtime)){
							$mt = AA_formatEnteredTime($this->mtime);
							$sqlMtime = ", Stellzeit = '$mt[0]:$mt[1]:00'";
						}
						
						mysql_query("
							INSERT runde SET
								Datum='" . $this->date . "'
								, Startzeit='" . $this->hour .":". $this->min .":00" . "'
								, xRundentyp=" . $this->type . "
								, xWettkampf=" . $this->event."
								$sqlEtime
								$sqlMtime
								, Gruppe = '".$this->group."'"
						);
					}
				}
			}
			if(mysql_errno() > 0)
			{
				$GLOBALS['AA_ERROR'] = mysql_errno() . ": " . mysql_error();
			}
			mysql_query("UNLOCK TABLES");
		}
	}

	/*		delete()
	 *  	--------
	 *		delete an event round
	 */
	function delete()
	{
		require('./lib/utils.lib.php');
		$GLOBALS['AA_ERROR'] = '';

		// Error: Empty fields
		if(empty($_POST['round']))
		{
			$GLOBALS['AA_ERROR'] = $GLOBALS['strErrEmptyFields'];
		}
		// OK: try to delete round
		else
		{
			mysql_query("LOCK TABLES serie READ, runde WRITE");
			// Still in use?
			if(AA_utils_checkReference("serie", "xRunde", $this->round) != 0)
			{
				$GLOBALS['AA_ERROR'] = $GLOBALS['strRound'] . $GLOBALS['strErrStillUsed'];
			}
			else	// OK: Not used anymore
			{
				mysql_query("
					DELETE FROM
						runde
					WHERE xRunde = " . $this->round
				);
			}
			// Check if any error returned from DB
			if(mysql_errno() > 0)
			{
				$GLOBALS['AA_ERROR'] = mysql_errno() . ": " . mysql_error();
			}

			mysql_query("UNLOCK TABLES");
		}
	}

	/*		change()
	 *  	--------
	 *		change an event round
	 */
	function change()
	{
		// Error: Empty fields
		if(empty($this->round))
		{
			$GLOBALS['AA_ERROR'] = $GLOBALS['strErrEmptyFields'];
		}
		// OK: try to change round
		else
		{
			mysql_query("LOCK TABLES serie READ, runde WRITE");

			$status = AA_utils_getRoundStatus($this->round);

			if($status == $GLOBALS['cfgRoundStatus']['results_done'])
			{
				$GLOBALS['AA_ERROR'] = $GLOBALS['strErrResultsEntered'];
			}
			else
			{
				
				if(empty($this->type)){ // round type is not optional!
					$GLOBALS['AA_ERROR'] = $GLOBALS['strType'] . $GLOBALS['strErrNotValid'];
				}else{
					
					if(!empty($this->etime)){
						$et = AA_formatEnteredTime($this->etime);
						$sqlEtime = ", Appellzeit = '$et[0]:$et[1]:00'";
					}
					if(!empty($this->mtime)){
						$mt = AA_formatEnteredTime($this->mtime);
						$sqlMtime = ", Stellzeit = '$mt[0]:$mt[1]:00'";
					}
					
					mysql_query("
						UPDATE runde SET
							Datum = '" . $this->date . "'
							, Startzeit = '".$this->hour.":".$this->min.":00"."'
							, xRundentyp = " . $this->type . "
							$sqlEtime
							$sqlMtime
						WHERE xRunde = " . $this->round
					);
	
					if(mysql_errno() > 0)
					{
						$GLOBALS['AA_ERROR'] = mysql_errno() . ": " . mysql_error();
					}
				}
			}
			mysql_query("UNLOCK TABLES");

			if($status > 0)
			{
				$txt = $GLOBALS['strTimetableChanged'] . ": "
						 . $this->date . ", "
						 . $this->hr . ":" . $this->min;
				AA_utils_logRoundEvent($this->round, $txt);
			}
		}	// ET round status
	}

} // end Timetable


/********************************************
 *
 * CLASS TimetableNew
 *
 *		Timetable class for new events
 *		
 *******************************************/

class TimetableNew extends Timetable
{
	/*		TimetableNew()
	 * 	-------------- 
	 *		Variables provided
	 */
	function TimetableNew($date, $item, $round, $roundtype, $hr, $min, $et='', $mt='')
	{
		$this->date = $date;
		$this->event = $item;
		$this->hour = $hr;
		$this->round = $round;
		$this->min = $min;
		$this->type = $roundtype;
		$this->etime = $et;
		$this->mtime = $mt;
	}

} // end TimetableNew

}	// ET AA_CL_TIMETABLE_LIB_INCLUDED
?>
