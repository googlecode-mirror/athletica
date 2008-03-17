<?php

if (!defined('AA_CL_RESULT_LIB_INCLUDED'))
{
	define('AA_CL_RESULT_LIB_INCLUDED', 1);

/* Class Constants */

	define ("RES_ACT_UNK", 0);
	define ("RES_ACT_INSERT", 1);
	define ("RES_ACT_UPDATE", 2);
	define ("RES_ACT_DELETE", 3);

/********************************************
 *
 * CLASS Result
 *
 * Provides functionality to insert, update and delete results.
 * Usage: After object creation, the user may call save function,
 * which determines to required DML-action.
 * Base class for more specific result classes
 *
 * Return:	object ResultReturn
 *
 *******************************************/

class Result
{
	var $round;
	var $startID;
	var $resultID;
	var $performance;
	var $info;
	var $points;

	function Result($round=0, $startID=0, $resultID=0)
	{
		$this->round = $round;
		$this->startID = $startID;
		$this->resultID = $resultID;
		$this->performance = '';
		$this->info = '';
	}
	
	function save($performance, $info = '', $secFlag = false)
	{
		require('./lib/utils.lib.php');
		$GLOBALS['AA_ERROR'] = '';

		// check if athlet valid
		$ret = AA_utils_checkReference("serienstart"
								, "xSerienstart", $this->startID);

		if($ret == 0) {	// athlete not in heat
			$GLOBALS['AA_ERROR'] = $GLOBALS['strErrAthleteNotInHeat']."(".$this->startID.")";
		}

		if(!empty($GLOBALS['AA_ERROR'])) {
			return;
		}

		AA_utils_changeRoundStatus($this->round, $GLOBALS['cfgRoundStatus']['results_in_progress']);
		if(!empty($GLOBALS['AA_ERROR'])) {
			return;
		}

		// Delete result (no performance entered)
		if($performance == '')
		{
			$reply = $this->delete();
		}
		// Add or change result
		else	
		{
			// validate performance
			$retValidate = 0;
			if($secFlag){
				$retValidate = $this->validate($performance, $info, true);
			}else{
				$retValidate = $this->validate($performance, $info);
			}
			
			if(!empty($GLOBALS['AA_ERROR'])) {
				return;
			}

			// get eventID
			$res = mysql_query("
				SELECT
					r.xWettkampf
				FROM
					runde AS r
				WHERE r.xRunde = " . $this->round
			);

			if(mysql_errno() > 0) {		// DB error
				$GLOBALS['AA_ERROR'] = mysql_errno() . ": " . mysql_error();
				return;
			}
			else {
				$row = mysql_fetch_row($res);
				$event = $row[0];					// event ID
				mysql_free_result($res);
			}

			// calculate points for this performance
			/*$sql_sex = "SELECT Geschlecht 
						  FROM athlet 
					 LEFT JOIN anmeldung USING(xAthlet) 
					 LEFT JOIN start USING(xAnmeldung) 
					 LEFT JOIN serienstart USING(xStart) 
						 WHERE xSerienstart = ".$this->startID.";";
			$query_sex = mysql_query($sql_sex);*/
			$sql_sex = "SELECT Geschlecht 
						  FROM kategorie 
					 LEFT JOIN wettkampf USING(xKategorie) 
					 LEFT JOIN start USING(xWettkampf) 
					 LEFT JOIN serienstart USING(xStart) 
						 WHERE xSerienstart = ".$this->startID.";";
			$query_sex = mysql_query($sql_sex);
			
			$this->calcPoints($event, mysql_result($query_sex, 0, 'Geschlecht'));
			if(!empty($GLOBALS['AA_ERROR'])) {
				return;
			}
			
			if($retValidate == RES_ACT_DELETE){
				$reply = $this->delete();
			}else{
				$reply = $this->update();
			}
		}
		
		return $reply;
	}


	function update()
	{
		$GLOBALS['AA_ERROR'] = '';
		$query = '';
		$reply = new ResultReturn();

		mysql_query("
			LOCK TABLES
				resultat WRITE
		");

		if(!empty($this->resultID))	// result provided -> change it
		{
			if(AA_utils_checkReference("resultat", "xResultat"
										, $this->resultID) == 0)
			{
				$GLOBALS['AA_ERROR'] = $GLOBALS['strErrAthleteNotInHeat'];
			}
			else
			{
				mysql_query("
					UPDATE resultat SET
						Leistung = " . $this->performance . "
						, Info = '" . $this->info . "'
						, Punkte = " . $this->points . "
					WHERE xResultat = " . $this->resultID
				);

				if(mysql_errno() > 0) {
					$GLOBALS['AA_ERROR'] = mysql_errno() . ": " . mysql_error();
				}
				else {
					$reply->setKey($this->resultID);
					$reply->setAction(RES_ACT_UPDATE);
					$reply->setPerformance($this->performance);
					$reply->setInfo($this->info);
				}
			}
		}
		else // no result provided -> add result
		{
			mysql_query("
				INSERT INTO resultat SET
					Leistung = " . $this->performance . "
					, Info= '" . $this->info . "'
					, Punkte = " . $this->points . "
					, xSerienstart = " . $this->startID
			);

			if(mysql_errno() > 0) {
				$GLOBALS['AA_ERROR'] = mysql_errno() . ": " . mysql_error();
			}
			else {
				$reply->setKey(mysql_insert_id());
				$reply->setAction(RES_ACT_INSERT);
				$reply->setPerformance($this->performance);
				$reply->setInfo($this->info);
			}
		}	// ET add or change
		mysql_query("UNLOCK TABLES");

		return $reply;
	}


	function delete()
	{
		$GLOBALS['AA_ERROR'] = '';
		$query = '';
		$reply = new ResultReturn;

		AA_utils_changeRoundStatus($this->round, $GLOBALS['cfgRoundStatus']['results_in_progress']);
		if(!empty($GLOBALS['AA_ERROR'])) {
			return;
		}

		mysql_query("LOCK TABLES resultat WRITE");

		mysql_query("
			DELETE FROM resultat
			WHERE xResultat= " . $this->resultID
		);

		if(mysql_errno() > 0) {
			$GLOBALS['AA_ERROR'] = mysql_errno() . ": " . mysql_error();
		}
		else {
			$reply->setKey($this->resultID);
			$reply->setAction(RES_ACT_DELETE);
		}
		mysql_query("UNLOCK TABLES");

		return $reply;
	}


	function calcPoints($event, $sex)
	{
		require('./lib/utils.lib.php');
		$this->points = AA_utils_calcPoints($event, $this->performance, 0, $sex);
	}

} // end class Result



/********************************************
 *
 * CLASS TrackResult
 *
 * Result class to validate track results
 *
 *******************************************/

class TrackResult	extends Result
{
	function validate($performance, $info, $secFlag = false)
	{
		require('./lib/cl_performance.lib.php');
		
		if($performance == "-"){
			return RES_ACT_DELETE;
		}
		
		// validate result
		$perf = new PerformanceTime($performance, $secFlag);
		$this->performance = $perf->getPerformance();
		if(is_null($this->performance)) {
			$GLOBALS['AA_ERROR'] = $GLOBALS['strErrInvalidResult'] .  $performance;
		}
		$this->info = $GLOBALS['cfgResultsInfoFill'];
		return 0;
	}
} // end class TrackResult




/********************************************
 *
 * CLASS TechResult
 *
 * Result class to validate technical results
 *
 *******************************************/

class TechResult	extends Result
{
	function validate($performance, $info)
	{
		require('./lib/cl_performance.lib.php');
		require('./lib/cl_wind.lib.php');

		// validate result
		$perf = new PerformanceAttempt($performance);
		$this->performance = $perf->getPerformance();
		if(is_null($this->performance)) {
			$GLOBALS['AA_ERROR'] = $GLOBALS['strErrInvalidResult'] .  $performance;
		}
		$this->info = $GLOBALS['cfgResultsInfoFill'];
		if($this->performance > 0) {		// valid performance
			$wind = new Wind($info);
			$this->info = $wind->getWind();
		}
	}
} // end class TrackResult




/********************************************
 *
 * CLASS HighResult
 *
 * Result class to validate technical results
 *
 *******************************************/

class HighResult	extends Result
{
	function validate($performance, $info)
	{
		require('./lib/cl_performance.lib.php');

		// validate result
		$perf = new PerformanceAttempt($performance);
		$this->performance = $perf->getPerformance();
		if($this->performance == NULL) {
			$GLOBALS['AA_ERROR'] = $GLOBALS['strErrInvalidResult'] .  $performance;
		}

		// validate attempts
		if($this->performance > 0) {
			$info = strtoupper($info);
			$info = strtr($info, '0', 'O');
			$info = str_replace("OOO", "O", $info);
			$info = str_replace("OO", "O", $info);
			if(preg_match($GLOBALS['cfgResultsHigh'], $info) == 0) {	// invalid result
				$GLOBALS['AA_ERROR'] = $GLOBALS['strErrInvalidResult'] .  $info;
				$info = NULL;
			}
		}
		else {				// negative or zero result
			$info = 'XXX';
		}
		$this->info = $info;

	}


	function calcPoints($event, $sex)
	{
		require('./lib/utils.lib.php');

		if($this->info == 'XXX') {		// last attempt
			$this->points = 0;
		}
		else {
			$this->points = AA_utils_calcPoints($event, $this->performance, 0, $sex);
		}
	}

} // end class HighResult




/********************************************
 *
 * CLASS ResultReturn
 *
 * Object returned to user after succesful
 * completion of save-method.
 *
 *******************************************/

class ResultReturn
{
	var $key;		// DB primary key of changed item
	var $action;	// action performed
	var $performance;		// new performance
	var $info;		// new info 

	function ResultReturn($key=0, $action=RES_ACT_UNK, $perf='', $info='')
	{
		$this->setKey($key);
		$this->setAction($action);
		$this->setPerformance($perf);
		$this->setInfo($info);
	}

	function setKey($key)
	{
		$this->key = $key;
	}

	function getKey()
	{
		return $this->key;
	}

	function setAction($action)
	{
		if(($action < RES_ACT_INSERT) || ($action > RES_ACT_DELETE)) {
			$action = RES_ACT_UNK;
		}
		$this->action = $action;
	}

	function getAction()
	{
		return $this->action;
	}

	function setPerformance($perf)
	{
		$this->performance = $perf;
	}

	function getPerformance()
	{
		return $this->performance;
	}

	function setInfo($info)
	{
		$this->info = $info;
	}

	function getInfo()
	{
		return $this->info;
	}

} // end class ResultReturn

} // end AA_CL_RESULT_LIB_INCLUDED

?>
