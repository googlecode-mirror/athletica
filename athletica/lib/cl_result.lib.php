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
    var $remark;      
    var $xAthlete;

	function Result($round=0, $startID=0, $resultID=0)
	{
		$this->round = $round;
		$this->startID = $startID;
		$this->resultID = $resultID;
		$this->performance = '';
		$this->info = ''; 
        $this->xAthlete = '';               
	}
	
	function save($performance, $info = '', $secFlag = false, $remark='', $xAthlete)
	{   $this->remark = $remark; 
        $this->performance = $performance; 
        $this->xAthlete = $xAthlete;    
     
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

         if (!is_null($performance)) {
		    $performance = trim($performance);
         }
        
        if (is_null($this->remark) && $performance == ''){  
            $reply = $this->delete();  
        }   
        else
            {            
                if (is_null($performance) &&  $this->remark == ''){   
                       $reply = $this->update_remark();     
                }
            
                else {
         
                        if ( $this->remark != '' ){   
                            $reply = $this->update_remark();     
                        }
            
     
   
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
			$sql = "SELECT r.xWettkampf 
					  FROM runde AS r 
					 WHERE r.xRunde = ".$this->round.";";
			$res = mysql_query($sql);   

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
			$sql_sex = "SELECT Geschlecht 
						  FROM kategorie 
					 LEFT JOIN wettkampf USING(xKategorie) 
					 LEFT JOIN start USING(xWettkampf) 
					 LEFT JOIN serienstart USING(xStart) 
						 WHERE xSerienstart = ".$this->startID.";";              
			$query_sex = mysql_query($sql_sex);     
			$this->calcPoints($event, mysql_result($query_sex, 0, 'Geschlecht'),$this->startID);          
			if(!empty($GLOBALS['AA_ERROR'])) {
				return;
			}
			
			if($retValidate == RES_ACT_DELETE){
				$reply = $this->delete();
			}else{
				$reply = $this->update();
			}
		}
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
				$sql = "UPDATE resultat 
						   SET Leistung = ".$this->performance.", 
							   Info = '".$this->info."', 
							   Punkte = ".$this->points." 
						 WHERE xResultat = ".$this->resultID.";";
				mysql_query($sql);

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
			$sql = "INSERT INTO resultat 
							SET Leistung = ".$this->performance.", 
								Info = '".$this->info."', 
								Punkte = ".$this->points.", 
								xSerienstart = ".$this->startID.";";
			mysql_query($sql);

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

        if ($this->performance!=''){
           
        
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
        }
		return $reply;
	}


	function calcPoints($event, $sex,$startID)  
	{
		require('./lib/utils.lib.php');
		$this->points = AA_utils_calcPoints($event, $this->performance, 0, $sex, $startID);
	}
    
    function update_remark()
    {   
        $GLOBALS['AA_ERROR'] = '';
        $query = '';
        $reply = new ResultReturn();

        mysql_query("
            LOCK TABLES rundenset READ, runde READ, runde as r READ , serie as s READ , start as st READ, 
            wettkampf as w READ , anmeldung as a READ , athlet as at READ, verein as v READ, 
            rundentyp as rt READ, serienstart as ss READ  , serienstart WRITE
        ");

        if(!empty($this->startID))    // result provided -> change it
        {
            if(AA_utils_checkReference("serienstart", "xSerienstart"
                                        , $this->startID) == 0)
            {
                $GLOBALS['AA_ERROR'] = $GLOBALS['strErrAthleteNotInHeat'];
            }
            else
            {
                $query="SELECT 
                        w.mehrkampfcode , ss.Bemerkung
                    FROM
                        serienstart as ss
                        LEFT JOIN start as st On (ss.xStart = st.xStart)
                        LEFT JOIN wettkampf as w On (w.xWettkampf = st.xWettkampf) 
                    WHERE
                        w.mehrkampfcode = 0
                        AND ss.xSerienstart = ".$this->startID;
                
                $result=mysql_query($query); 
                
                if(mysql_errno() > 0) {
                    $GLOBALS['AA_ERROR'] = mysql_errno() . ": " . mysql_error();
                }
                else {
                    if (mysql_num_rows($result) > 0) {
                    
                         $sql = "UPDATE serienstart 
                                    SET Bemerkung = '".$this->remark."'                                     
                                         WHERE xSerienstart = ".$this->startID.";";
                            mysql_query($sql);
                           
                            if(mysql_errno() > 0) {
                                $GLOBALS['AA_ERROR'] = mysql_errno() . ": " . mysql_error();
                            }  
                    }  
                    else {
                
                      // comnined event
                
                        $query_mk="SELECT 
                                    ss.xSerienstart , ss.Bemerkung   
                               FROM 
                                    runde AS r 
                                    LEFT JOIN serie AS s ON (s.xRunde = r.xRunde) 
                                    LEFT JOIN serienstart AS ss ON (ss.xSerie = s.xSerie)
                                    LEFT JOIN START AS st ON (st.xStart = ss.xStart) 
                                    LEFT JOIN wettkampf as w ON (w.xWettkampf = st.xWettkampf)
                                    LEFT JOIN anmeldung AS a ON (a.xAnmeldung = st.xAnmeldung)
                                    LEFT JOIN athlet AS at ON (at.xAthlet = a.xAthlet)
                                    LEFT JOIN verein AS v ON (v.xVerein = at.xVerein   )
                                    LEFT JOIN rundentyp AS rt ON rt.xRundentyp = r.xRundentyp                              
                               WHERE 
                                    w.mehrkampfcode > 0
                                    AND at.xAthlet = ". $this->xAthlete;
                           
                        $result=mysql_query($query_mk); 
                
                        if(mysql_errno() > 0) {
                            $GLOBALS['AA_ERROR'] = mysql_errno() . ": " . mysql_error();
                        }
                        else {
                            while ($row=mysql_fetch_row($result)){
                                    $sql = "UPDATE serienstart 
                                            SET Bemerkung = '".$this->remark."'                                     
                                            WHERE xSerienstart = ".$row[0].";";
                                    mysql_query($sql);
                           
                                    if(mysql_errno() > 0) {
                                        $GLOBALS['AA_ERROR'] = mysql_errno() . ": " . mysql_error();
                                    } 
                            }
                        } 
                    }
                }
            }
        }
        mysql_query("UNLOCK TABLES");

        return $reply;
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
