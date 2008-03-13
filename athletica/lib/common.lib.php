<?php
error_reporting(0);
ini_set('max_execution_time', 360);

/**
 * common functions
 * ----------------
 * This library contains common functions.
 * Attention: use utils.lib.php for functions that return errors as a global
 * field.
 */

if(!session_id())
{
	session_start('athletica');
}

if (!defined('AA_COMMON_LIB_INCLUDED'))
{
	define('AA_COMMON_LIB_INCLUDED', 1);


require('./lib/utils.lib.php');
require('./config.inc.php');

   
   
/**
 *	Languages
 *		Define list of available languages.
 *
 *    cfgLanguage:
 *		- file: include translation table
 *		- doc: URL to application documentation
 */

	$cfgLanguage = array("Deutsch"
								=> array	("file" => "./lang/german.inc.php"
											, "doc" => "doc/de/"
											, "short" => "de"
											)
							, "English"
								=> array	("file" => "./lang/english.inc.php"
											, "doc" => "doc/de/"
											, "short" => "en"
											)
							, "Français"
								=> array 	("file" => "./lang/french.inc.php"
											, "doc" => "doc/de/"
											, "short" => "fr"
											)
							, "Italiano"
								=> array 	("file" => "./lang/italian.inc.php"
											, "doc" => "doc/de/"
											, "short" => "it"
											)
					);

	if(empty($_COOKIE['language_trans'])) {
		setcookie("language_trans", $cfgLanguage['Deutsch']['file']
			, time()+$cfgCookieExpires);
		$_COOKIE['language_trans'] = $cfgLanguage['Deutsch']['file'];
	}
	if(empty($_COOKIE['language_doc'])) {
		setcookie("language_doc", $cfgLanguage['Deutsch']['doc']
			, time()+$cfgCookieExpires);
		$_COOKIE['language_doc'] = $cfgLanguage['Deutsch']['doc'];
	}
	if(empty($_COOKIE['language'])) {
		setcookie("language", $cfgLanguage['Deutsch']['short']
			, time()+$cfgCookieExpires);
		$_COOKIE['language'] = $cfgLanguage['Deutsch']['short'];
	}

/*
 * ------------------------------------------------------
 *
 *	Processing Functions
 *	--------------------
 *	various processing functions
 *
 * ------------------------------------------------------
 */


	/**
	 * Establish DB connection
	 *
	 * @return		DB link
	 */
	function AA_connectToDB()
	{
		$db = AA_utils_connectToDB();
		if(!empty($GLOBALS['AA_ERROR'])) {
			AA_printErrorPage($GLOBALS['AA_ERROR']);
		}
		return $db;
	}


	/**
	 * Check if meeting selected
	 *
	 * @return TRUE if OK, FALSE if not OK
	 *
	 */
	function AA_checkMeetingID()
	{
		global $noMeetingCheck;
		
		if(!$noMeetingCheck){
			$ret = TRUE;
			if(empty($_COOKIE['meeting_id']))
			{
				AA_printErrorPage($GLOBALS['strNoMeetingSelected']);
				$ret = FALSE;
			}
			return $ret;
		}
		
		if(isset($_SESSION['meeting_infos'])){
			unset($_SESSION['meeting_infos']);
		}
		
		$sql = "SELECT * 
				  FROM meeting 
				 WHERE xMeeting = ".$_COOKIE['meeting_id'].";";
		$query = mysql_query($sql);
		
		if($query && mysql_num_rows($query)==1){
			$row = mysql_fetch_assoc($query);
			$_SESSION['meeting_infos'] = $row;
		}
	}
	
	/**
	
	check if xControl of the meeting is set
	
	@return 1 if xControl is given and result upload is wished
		2 if no result upload will be made
		0 if xControl is 0 but the result upload is activated
	
	*/
	function AA_checkControl(){
		
		if(AA_checkMeetingID()){
			
			$res = mysql_query("SELECT xControl, Online FROM meeting WHERE xMeeting = ".$_COOKIE['meeting_id']);
			if(mysql_errno() > 0){
				AA_printErrorPage(mysql_errno().": ".mysql_error());
			}else{
				$row = mysql_fetch_array($res);
				if($row[0] > 0 && $row[1] == 'y'){
					return 1;
				}elseif($row[0] == 0 && $row[1] == 'y'){
					return 0;
				}elseif($row[1] == 'n'){
					return 2;
				}
			}
			
		}
		
	}

/*
 * ------------------------------------------------------
 *
 *	Data functions
 *	--------------------
 *	various functions to retrieve or check data from DB
 *
 * ------------------------------------------------------
 */


	/**
	 * Get first category ID from DB
	 *
	 * @return	Category ID (primary key)
	 */
	function AA_getFirstCategoryID()
	{
		$result = mysql_query("
			SELECT
				DISTINCT w.xKategorie
			FROM
				wettkampf AS w
				, kategorie AS k
			WHERE
				w.xMeeting=" . $_COOKIE['meeting_id'] . "
				AND w.xKategorie = k.xKategorie
			ORDER BY
				k.Anzeige
		");

		if(mysql_errno() > 0) {
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			$category = 0;		// set category to zero
		}
		else {
			$row = mysql_fetch_row($result);
			$category = $row[0];			// preselect default category
		}
		mysql_free_result($result);

		return $category;
	}
	
	
	/**
	 * Get width of a string in the current font (Arial/Helvetica)
	 *
	 * @param	string		any text
	 * @param	int		font size
	 * @return	int		width of given text
	 *
	**/
	function AA_getStringWidth($s, $size)
	{
		global $cfgCharWidth;
		$s=(string)$s;
		$cw=&$cfgCharWidth;
		$w=0;
		$l=strlen($s);
		for($i=0;$i<$l;$i++)
			$w+=$cw[$s{$i}];
		return $w*$size/1000;
	}
	
	
	/**
	 * Check if event or round belongs to a combined contest
	 *
	 * @param	int		event
	 * @param	int		round
	 * @return	TRUE/FALSE
	**/
	function AA_checkCombined($event=0, $round=0){
		global $cfgEventType, $strEventTypeSingleCombined;
		
		if($event > 0){
			
			$res = mysql_query("SELECT Typ FROM
						wettkampf
					WHERE	xWettkampf = $event
					AND	xMeeting = ".$_COOKIE['meeting_id']);
			if(mysql_errno() > 0) {
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}else{
				
				$row = mysql_fetch_array($res);
				if($row[0] == $cfgEventType[$strEventTypeSingleCombined]){
					return true;
				}else{
					return false;
				}
				
			}
			
		}elseif($round > 0){
			
			$res = mysql_query("SELECT Typ FROM
						wettkampf as w
						, runde as r
					WHERE	r.xRunde = $round
					AND	r.xWettkampf = w.xWettkampf
					AND	w.xMeeting = ".$_COOKIE['meeting_id']);
			if(mysql_errno() > 0) {
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}else{
				
				$row = mysql_fetch_array($res);
				if($row[0] == $cfgEventType[$strEventTypeSingleCombined]){
					return true;
				}else{
					return false;
				}
				
			}
			
		}
		
	}
	
	
	/**
	 * Check if event or round belongs to a svm contest
	 *
	 * @param	int		event
	 * @param	int		round
	 * @return	TRUE/FALSE
	**/
	function AA_checkSVM($event=0, $round=0){
		global $cfgEventType, $strEventTypeSingleCombined, $strEventTypeTeamSM;
		
		if($event > 0){
			
			$res = mysql_query("SELECT Typ FROM
						wettkampf
					WHERE	xWettkampf = $event
					AND	xMeeting = ".$_COOKIE['meeting_id']);
			if(mysql_errno() > 0) {
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}else{
				
				$row = mysql_fetch_array($res);
				if($row[0] > $cfgEventType[$strEventTypeSingleCombined]
					&& $row[0] != $cfgEventType[$strEventTypeTeamSM]){
					return true;
				}else{
					return false;
				}
				
			}
			
		}elseif($round > 0){
			
			$res = mysql_query("SELECT Typ FROM
						wettkampf as w
						, runde as r
					WHERE	r.xRunde = $round
					AND	r.xWettkampf = w.xWettkampf
					AND	w.xMeeting = ".$_COOKIE['meeting_id']);
			if(mysql_errno() > 0) {
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}else{
				
				$row = mysql_fetch_array($res);
				if($row[0] > $cfgEventType[$strEventTypeSingleCombined]
					&& $row[0] != $cfgEventType[$strEventTypeTeamSM]){
					return true;
				}else{
					return false;
				}
				
			}
			
		}
		
	}
	
	/**
	 * Check if event or round belongs to a team sm contest
	 *
	 * @param	int		event
	 * @param	int		round
	 * @return	TRUE/FALSE
	**/
	function AA_checkTeamSM($event=0, $round=0){
		global $cfgEventType, $strEventTypeTeamSM;
		
		if($event > 0){
			
			$res = mysql_query("SELECT Typ FROM
						wettkampf
					WHERE	xWettkampf = $event
					AND	xMeeting = ".$_COOKIE['meeting_id']);
			if(mysql_errno() > 0) {
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}else{
				
				$row = mysql_fetch_array($res);
				if($row[0] == $cfgEventType[$strEventTypeTeamSM]){
					return true;
				}else{
					return false;
				}
				
			}
			
		}elseif($round > 0){
			
			$res = mysql_query("SELECT Typ FROM
						wettkampf as w
						, runde as r
					WHERE	r.xRunde = $round
					AND	r.xWettkampf = w.xWettkampf
					AND	w.xMeeting = ".$_COOKIE['meeting_id']);
			if(mysql_errno() > 0) {
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}else{
				
				$row = mysql_fetch_array($res);
				if($row[0] == $cfgEventType[$strEventTypeTeamSM]){
					return true;
				}else{
					return false;
				}
				
			}
			
		}
		
	}
	
	
	/**
	 * Check athlete's age
	 *
	 * @param	int			category			uniqe key of category
	 * @param	int			year				birth year
	 * @return	TRUE/FALSE
	 */
	function AA_checkAge($category, $year)
	{
		$limit = FALSE;
		if($category > 0)
		{
			$res = mysql_query("SELECT kategorie.Alterslimite"
								. " FROM kategorie"
								. " WHERE kategorie.xKategorie = " . $category);

			if(mysql_errno() > 0) {		// DB error
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else {
				$row = mysql_fetch_row($res);
				$agelimit = $row[0];
				mysql_free_result($res);

				$res = mysql_query("SELECT DATE_FORMAT(meeting.DatumVon, '%Y')"
							. " FROM meeting"
							. " WHERE meeting.xMeeting = " . $_COOKIE['meeting_id']);

				if(mysql_errno() > 0) {		// DB error
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
				else {
					$row = mysql_fetch_row($res);

					if(($row[0] - $year) <= $agelimit) {
						$limit = TRUE;
					}
					mysql_free_result($res);
				}	// ET DB error meeting date
			}	// ET DB error category
		}	// ET categore provided
		return $limit;
	}


	/**
	 * Check data reference
	 *
	 * @param		string	table		name of table to be checked
	 * @param		string	unique	name of unique key column
	 * @param		int			id			uniqe key of item to be checked (xTablename)
	 * @return	int			rows		nbr of rows found
	 */
	function AA_checkReference($table, $unique, $id)
	{
		$rows = AA_utils_checkReference($table, $unique, $id);
		if(!empty($GLOBALS['AA_ERROR'])) {
			AA_printErrorMsg($GLOBALS['AA_ERROR']);
		}
		return $rows;
	}


	/**
	 * Check if relay event
	 *
	 * @param	int			id			uniqe key of event
	 * @return	TRUE/FALSE
	 */
	function AA_checkRelay($event=0, $round=0)
	{
		$relay = FALSE;
		if($event > 0)
		{
			$result = mysql_query("SELECT disziplin.Staffellaeufer"
										. " FROM disziplin"
										. ", wettkampf"
										. " WHERE wettkampf.xWettkampf = " . $event
										. " AND wettkampf.xDisziplin = disziplin.xDisziplin");
			if(mysql_errno() > 0) {		// DB error
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else {
				$row = mysql_fetch_row($result);
				if($row[0] > 0) {
					$relay = TRUE;
				}
				mysql_free_result($result);
			}
		}
		elseif($round > 0)
		{
			$result = mysql_query("SELECT disziplin.Staffellaeufer"
										. " FROM disziplin"
										. ", wettkampf"
										. ", runde"
										. " WHERE wettkampf.xWettkampf = runde.xWettkampf"
										. " AND runde.xRunde = $round"
										. " AND wettkampf.xDisziplin = disziplin.xDisziplin");
			if(mysql_errno() > 0) {		// DB error
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else {
				$row = mysql_fetch_row($result);
				if($row[0] > 0) {
					$relay = TRUE;
				}
				mysql_free_result($result);
			}
		}
		return $relay;
	}


	/**
	 * get discipline type
	 * -------------------
	*/
	function AA_getDisciplineType($round)
	{
		require('./config.inc.php');

		$status = 0;
		$result = mysql_query("
			SELECT
				disziplin.Typ
				, wettkampf.Windmessung
			FROM
				runde
				, wettkampf
				, disziplin
			WHERE runde.xRunde = $round
			AND wettkampf.xWettkampf = runde.xWettkampf
			AND disziplin.xDisziplin = wettkampf.xDisziplin
		");

		if(mysql_errno() > 0)		// DB error
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			$type = '';
		}
		else
		{
			$row = mysql_fetch_row($result);

			// check whether standard discipline type has been overruled by
			// meeting definitions (wind or no wind)
			if(($row[0] == $cfgDisciplineType[$strDiscTypeTrack])
				&& ($row[1] == 0))	// overruled: no wind
			{
				$type = $cfgDisciplineType[$strDiscTypeTrackNoWind];
			}
			else if(($row[0] == $cfgDisciplineType[$strDiscTypeJump])
				&& ($row[1] == 0))	// overruled: no wind
			{
				$type = $cfgDisciplineType[$strDiscTypeJumpNoWind];
			}
			else
			{
				$type = $row[0];
			}
			mysql_free_result($result);
		}		// ET DB error
		return $type;
	}



	/**
	 * Get last used startnumber
	 *
	 * @return	int startnumber
	 */
	function AA_getLastStartnbr()
	{
		$n = 0;		// default
		$result = mysql_query("
			SELECT
				MAX(Startnummer)
			FROM
				anmeldung
			WHERE xMeeting = " . $_COOKIE['meeting_id']
		);
									
		if(mysql_errno() > 0) {		// DB error
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			if(mysql_num_rows($result) > 0)
			{
				$row = mysql_fetch_row($result);
				if($row[0] != null) {
					$n = $row[0];
				}
			}
			mysql_free_result($result);
		}
		return $n;
	}
	
	
	/**
	 * Get last used startnumber for a relay
	 *
	 * @return	int startnumber
	 */
	function AA_getLastStartnbrRelay()
	{
		$nbr = 0;
		
		$result = mysql_query("
				SELECT
					MAX(Startnummer)
				FROM
					staffel
				WHERE
					xMeeting = ". $_COOKIE['meeting_id']);
		if(mysql_errno() > 0) {		// DB error
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}else{
			
			$row = mysql_fetch_array($result);
			$nbr = $row[0];
			
		}
		
		return $nbr;
	}
	
	
	/**
	 * Get next free start number from pool
	 *
	 * @param	int	entered start number
	 * @return	int	free startnumber
	 */
	function AA_getNextStartnbr($n){
		
		$pool = array();
		
		$res = mysql_query("SELECT Startnummer FROM
					staffel
				WHERE	xMeeting = ".$_COOKIE['meeting_id']."
				AND	Startnummer >= $n
				");
		if(mysql_errno() > 0) {		// DB error
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}else{
			
			while($row = mysql_fetch_array($res)){
				$pool[] = $row[0];
			}
			
		}
		
		$res = mysql_query("SELECT Startnummer FROM
					anmeldung
				WHERE	xMeeting = ".$_COOKIE['meeting_id']."
				AND	Startnummer >= $n
				");
		if(mysql_errno() > 0) {		// DB error
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}else{
			
			while($row = mysql_fetch_array($res)){
				$pool[] = $row[0];
			}
			
		}
		
		sort($pool);
		
		if(count($pool) > 0){
			
			// search next available number
			$lp = $pool[0];
			foreach($pool as $p){
				if(($p-$lp) > 1){
					return ($lp+1);
				}
				$lp = $p;
			}
			
			return ($lp+1);
		}
		
		return 0;
	}
	
	
	/**
	 * get round status
	 * ----------------
	*/
	function AA_getRoundStatus($round)
	{
		$status = AA_utils_getRoundStatus($round);
		if(!empty($GLOBALS['AA_ERROR'])) {
			AA_printErrorMsg($GLOBALS['AA_ERROR']);
		}
		return $status;
	}


	/**
	 * get next round
	 * --------------
	 *
	 * - event: event to be processed
	 * - round: current round
	 *
	 * returns: next round's primary key, or zero
	*/
	function AA_getNextRound($event, $round)
	{
		$nextRound = 0;		// initialize return value

		if((!empty($event)) && (!empty($round)))	// check parameters
		{
			// check if another round follows
			$result = mysql_query("	SELECT
												xRunde
											FROM
												runde
											WHERE xWettkampf = $event
											ORDER BY
												Datum
												, Startzeit
											");

			if(mysql_errno() > 0) {		// DB error
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else
			{
				$found = FALSE;

				while($row = mysql_fetch_row($result))
				{
					$r = $row[0];			// keep ID of round currently processed
					if($found == TRUE) {	// break if this is the next round
						break;
					}
					if($row[0] == $round) {	// current round found
						$found = TRUE;
					}
				}
				mysql_free_result($result);
				
				if(($found == TRUE) && ($r != $round))		// next round found
				{
					$nextRound = $r;
				}
			}
		}	// ET input parameters
		return $nextRound;
	}


/*
 * ------------------------------------------------------
 *
 *	String Format Functions
 *	-----------------------
 *	different functions to format strings to support
 * MySQL formats.
 *
 * ------------------------------------------------------
 */

	/**
	 * format year of birth
	 *
	 */
	function AA_formatYearOfBirth($year)
	{
		$y=substr($year, -2);
		return $y;
	}

	/**
	 * set two-digit year to correct four-digit year
	 *
	 */
	function AA_setYearOfBirth($year)
	{
		if($year <= 30) {
			$year = $year+2000;
		}
		else if ($year <= 99) {
			$year = $year+1900;
		}
		return $year;
	}



	/**
	 * format meter result
	 *
	 */
	function AA_formatResultMeter($meter)
	{
		if($meter > 0)		// leave negative or zero results unchanged
		{
			$m = (int) ($meter / 100);		// calculate meters
			$cm = $meter % 100;	// keep remainder

			$meter = $m . $GLOBALS['cfgResultsMeterSeparator'];
			if($cm < 10) {
				$meter = $meter . "0";
			}
			$meter = $meter . $cm;
		}	// ET negative value
		else  if($meter == $GLOBALS['cfgMissedAttempt']['db']) {
			$meter = $GLOBALS['cfgMissedAttempt']['code'];
		}

		return $meter;
	}



	/**
	 * format time result
	 *  - rankingList: on ranking lists show only hundrets of seconds
	 *  - secFlag: format time in seconds e.g. 62.12
	 */
	function AA_formatResultTime($time, $rankingList = false, $secFlag = false)
	{
		if($time >= 0)
		{
			if($secFlag){
				$sec = (int) ($time / 1000);			// calculate only in seconds
				$frac = $time % 1000;
			}else{
				$hrs = (int) ($time / 3600000);		// calculate hours
				$time = $time % 3600000;	// keep remainder
				$min = (int) ($time / 60000);		// calculate minutes
				$time = $time % 60000;		// keep remainder
				$sec = (int) ($time / 1000);			// calculate seconds
				$frac = $time % 1000;		// keep remainder
			}

			$time = '';

			// Hours
			if($hrs > 0) {
				$time = $hrs . $GLOBALS['cfgResultsHourSeparator'];
			}

			// Minutes
			if(($min > 0) && ($time == '')) {	// no time set yet
				$time = $min . $GLOBALS['cfgResultsMinSeparator'];
			}
			else if($min >= 10) {		// hrs already set
				$time = $time . $min . $GLOBALS['cfgResultsMinSeparator'];
			}
			else if(($min < 10) && ($time != '')) { // hrs already set
				$time = $time . "0" . $min . $GLOBALS['cfgResultsMinSeparator'];
			}
		
			// Seconds
			if(($sec >= 0) && ($time == '')) {	// no time set yet
				$time = $sec . $GLOBALS['cfgResultsSecSeparator'];
			}
			else if($sec >= 10) {		// min already set
				$time = $time . $sec . $GLOBALS['cfgResultsSecSeparator'];
			}
			else if(($sec < 10) && ($time != '')) {	// min already set
				$time = $time . "0" . $sec . $GLOBALS['cfgResultsSecSeparator'];
			}

			// Fractions
			
			if($rankingList){
				$frac = ceil($frac/10);
				if($frac < 10) {
					$time = $time . "0";
				}
				if($frac >= 100){
					$tmp = mktime($hour, $min, $sec);
					$tmp++;
					$frac = "00";
					
					if(date("H",$tmp) > 0){
						$time = date("H".$GLOBALS['cfgResultsHourSeparator']."i".$GLOBALS['cfgResultsMinSeparator']."s".$GLOBALS['cfgResultsSecSeparator'],$tmp);
					}elseif(date("i",$tmp) > 0){
						$time = date("i".$GLOBALS['cfgResultsMinSeparator']."s".$GLOBALS['cfgResultsSecSeparator'],$tmp);
					}elseif(date("s",$tmp) > 0){
						$time = date("s".$GLOBALS['cfgResultsSecSeparator'],$tmp);
					}
					if($secFlag){
						$time = ((date("i",$tmp)*60)+date("s",$tmp)).$GLOBALS['cfgResultsSecSeparator'];
					}
				}
				$time = $time . $frac; // on rankinglist, show only 100of secs, rounded up in each case
			}else{
				if($frac < 100) { // add a zero if fraction is only 2 digits
					$time = $time . "0";
				}
				if($frac < 10) { // add again a zero if fraction is 1 digit
					$time = $time . "0";
				}
				if($frac % 10 == 0) {	// show hundredths of seconds
					$time = $time . round($frac/10);
				}
				else {			// show thousandths of seconds
					$time = $time . $frac;
				}
			}
		}	// ET negative value

		return $time;
	}
	
	
	/**
	 * format day times entered in one field
	 * 	return array (hour, minutes)
	 *
	**/
	
	function AA_formatEnteredTime($st){
		$ret = false;
		
		if(preg_match("/[\.,;:]/",$st) == 0){
			$ret = array();
			$ret[] = substr($st,0,-2);
			if(strlen($st) == 3){
				$ret[] = substr($st,1);
			}elseif(strlen($st) == 4){
				$ret[] = substr($st,2);
			}
		}else{
			$ret = array();
			$ret = preg_split("/[\.,;:]/", $st);
		}
		
		return $ret;
	}



/*
 * ------------------------------------------------------
 *
 *	HTML Print Functions
 *	--------------------
 *	different functions generating HTML output
 *
 * ------------------------------------------------------
 */

	/**
	 * Error Message
	 * - show javascript alert
	 * - show error message in status bar
	 */
	function AA_printErrorMsg($msg)
	{
		// provide plain text message for certain DB errors
		//echo $msg;
		if(strncmp($msg, '1062', 4) == 0) {
			$msg = $GLOBALS['strDBErrorDoubleEntry'];
		}
		else if(strncmp($msg, '1064', 4) == 0) {
			$msg = $GLOBALS['strDBErrorSQLError'];
		}
?>
		<script type="text/javascript">
		<!--
			alert("<?php echo $msg; ?>");
			top.frames[2].location.href= "status.php?msg=<?php echo $GLOBALS['strError']
				. ": " . $msg; ?>";
		//-->
		</script>
<?php
	}


	/**
	 * Error Message
	 * - show javascript alert
	 * - show error message in status bar
	 */
	function AA_printErrorPage($msg)
	{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <title>error_page</title>
</head>
<body>
<?php	AA_printErrorMsg($msg); ?>
</body>
<?php
	}



	/**
	 * Warning Message
	 */
	function AA_printWarningMsg($msg)
	{
?>
	<table>
		<tr><td class='warning'><?php echo $msg; ?></td><tr>
	</table>
<?php
	}


	/**
	 * Drop-down list Category, with auto-update
	 *		arg. 1 = category ID
	 *		arg. 2 = teams only
	 *
	 */
	function AA_printCategorySelectionDropDown($category)
	{
		require('./lib/cl_gui_dropdown.lib.php');
?>
	<table>
		<tr>
			<th class='dialog'><?php echo $GLOBALS['strCategory']; ?></th>
<?php
		$dd = new GUI_CategoryDropDown($category, 'document.cat_selection.submit()');
?>
		</tr>
	</table>
<?php
	}


	/**
	 * Category selection
	 *	query arg. 1 = form_action
	 *	query arg. 2 = category ID
	 *
	 */
	function AA_printCategorySelection($form_action, $category, $method='post')
	{
?>
<form action='<?php echo $form_action; ?>' method='<?php echo $method; ?>' name='cat_selection'>
	<input name='arg' type='hidden' value='select_cat' />
<?php
		AA_printCategorySelectionDropDown($category);
?>
</form>
<?php
	}


	/**
	 * Category selection on entry form
	 *		arg. 1 = form_action
	 *		arg. 2 = category ID
	 *		arg. 3 = club ID
	 *		arg. 2 = team events only
	 */
	function AA_printCategoryEntries($form_action, $category, $club, $teams=FALSE)
	{
?>
<form action='<?php echo $form_action; ?>' method='post' name='cat_selection'>
	<input name='club' type='hidden' value='<?php echo $club; ?>' />
<?php
		AA_printCategorySelectionDropDown($category, $teams);
?>
</form>
<?php
	}



	/**
	 * Club selection drop down
	 *	query arg. 1 = form_action
	 *	query arg. 2 = club ID
	 *	query arg. 3 = category ID
	 *	query arg. 4 = event ID
	 *	query arg. 5 = all:	show all clubs
	 */
	function AA_printClubSelection($form_action, $club, $category, $event, $all=false)
	{
		require('./lib/cl_gui_dropdown.lib.php');
?>
<script type="text/javascript">
<!--
	function check()
	{
		if(document.club_selection.club.value == 'new')
		{
			window.open("admin_clubs.php", "_self");
		}
		else {
			document.club_selection.submit()
		}
	}
//-->
</script>

<form action='<?php echo $form_action; ?>' method='post' name='club_selection'>
	<input name='arg' type='hidden' value='select_club' />
	<input name='category' type='hidden' value='<?php echo $category; ?>' />
	<input name='event' type='hidden' value='<?php echo $event; ?>' />
		<table>
			<tr>
				<th class='dialog'><?php echo $GLOBALS['strClub']; ?></th>
<?php
		$dd = new GUI_ClubDropDown($club, $all, "check()");
?>
		</tr>
	</table>
</form>
<?php
	}


	/**
	 * Event selection drop down
	 *	query arg. 1 = form_action
	 *	query arg. 2 = category ID
	 *	query arg. 3 = event ID
	 *	query arg. 4 = HTTP method (default GET)
	 */
	function AA_printEventSelection($action, $category, $event, $method='get')
	{
?>
<form action='<?php echo $action; ?>' method='<?php echo $method; ?>'
		name='event_selection'>
	<input name='category' type='hidden' value='<?php echo $category; ?>' />
	<table>
		<tr>
			<th class='dialog'><?php echo $GLOBALS['strEvent']; ?></th>
<?php

		$dd = new GUI_EventDropDown($category, $event, 'document.event_selection.submit()');
?>
		</tr>
	</table>
</form>
<?php
	}
	
	
	/**
	 * Combined event selection drop down
	 *	query arg. 1 = form_action
	 *	query arg. 2 = category ID
	 *	query arg. 3 = combined Code
	 *	query arg. 4 = HTTP method (default GET)
	 */
	function AA_printEventCombinedSelection($action, $category, $comb, $method='get')
	{
?>
<form action='<?php echo $action; ?>' method='<?php echo $method; ?>'
		name='event_combined_selection'>
	<input name='category' type='hidden' value='<?php echo $category; ?>' />
	<table>
		<tr>
			<th class='dialog'><?php echo $GLOBALS['strCombinedEvent']; ?></th>
<?php

		$dd = new GUI_EventCombinedDropDown($category, $comb, 'document.event_combined_selection.submit()');
?>
		</tr>
	</table>
</form>
<?php
	}


	/**
	 * Heat Selection:
	 *		requires anchor tags (#heat_X) to be set in HTML page
	 *		- arg. 1 = round ID
	 *		- arg. 1 = speaker page
	 */
	function AA_printHeatSelection($round, $speaker=false)
	{
		require('./lib/cl_gui_button.lib.php');

		$status = "";
		if($speaker == true) {
			$status = ", Status";	// select heat status
		}
		
		// read all heats
		// (Remark: LPAD(s.Bezeichnung,5,'0') is used to order heats by their
		// name. This trick is necessary as 'Bezeichnung' may be alpha-numeric.)
		$result = mysql_query("
			SELECT
				xSerie
				, Bezeichnung
				, LPAD(Bezeichnung,5,'0') as heatid
				$status
			FROM
				serie
			WHERE xRunde = $round
			ORDER BY
				heatid
		");

		if(mysql_errno() > 0)		// DB error
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			// show heat selection if more than one heat
			if(mysql_num_rows($result) > 1)
			{
?>
<form>
<table>
	<tr>
		<th class='dialog'><?php echo $GLOBALS['strHeats']; ?></th>
		<td />
<?php
				$class='nav';
				while($row = mysql_fetch_row($result))
				{
					if(($speaker == true)
						&& ($row[3] == $GLOBALS['cfgHeatStatus']['announced']))
					{
						echo "<td id='heat_$row[0]' class='nav_announced'>\n";
					}
					else {
						echo "<td id='heat_$row[0]' class='nav'>\n";
					}
					?>
					<a href='#heat_<?php echo $row[1]; ?>'><?php echo $row[1]; ?></a>
					</td>
					<?php
				}
?>
		</td>
	</tr>
</table>
</form>
<?php
			}
			mysql_free_result($result);
		}
	}


	/**
	 * Relay selection drop down
	 *	query arg. 1 = action
	 *	query arg. 2 = category ID
	 *	query arg. 3 = event ID
	 *
	 */
	function AA_printRelaySelection($action, $category, $event, $club)
	{
?>
<form action='<?php echo $action; ?>' method='post' name='rel_selection'>
	<input name='arg' type='hidden' value='select_rel' />
	<input name='category' type='hidden' value='<?php echo $category; ?>' />
	<input name='club' type='hidden' value='<?php echo $club; ?>' />
	<table>
		<tr>
			<th class='dialog'><?php echo $GLOBALS['strDiscipline']; ?></th>
<?php
		$dd = new GUI_EventDropDown($category, $event, 'document.rel_selection.submit()', true);
?>
		</tr>
	</table>
</form>
<?php
	}


	/**
	 * Round selection drop down
	 * -------------------------
	 */

	function AA_printRoundSelection($action, $category, $event, $round)
	{
?>
	<form action='<?php echo $action; ?>' method='post' name='round_selection'>
		<input name='arg' type='hidden' value='select_round' />
		<input name='category' type='hidden' value='<?php echo $category; ?>' />
		<input name='event' type='hidden' value='<?php echo $event; ?>' />
		<table>
			<tr>
				<th class='dialog'><?php echo $GLOBALS['strRound']; ?></th>
<?php
		$dd = new GUI_RoundDropDown($event, $round);
?>
			</tr>
		</table>
	</form>
<?php
	}

	/**
	 * get the best result from previous day for disciplines technique
	 * ---------------------------------------------------------------
	 */
		 
function AA_getBestPreviousTech($event,$disciplin, $enrolement)
{    
	 $best = 0;
	 $max_array= array();
	 $i=0;  
	 $mysql="SELECT
					s.xStart,
					s.xWettkampf
			  FROM
					start as s                                  
			  WHERE
					s.xAnmeldung=" . $enrolement;    
			 
	 $result = mysql_query($mysql);     
	 if(mysql_errno() > 0){
			AA_printErrorPage(mysql_errno().": ".mysql_error());
	 }else{    
			while ($row=mysql_fetch_row($result)) {      // all events belong to this enrolement                
				 if ($row[1]!=$event) {
					   $mysql_1="SELECT                                   
										w.xWettkampf
								 FROM
										wettkampf as w                                 
								 WHERE
										w.xWettkampf=" . $row[1] . "
										AND xDisziplin=" . $disciplin; 
					  
					   $result1 = mysql_query($mysql_1); 
					   if (mysql_num_rows($result1) > 0) {        // event for checked discipline                            
												
							 $mysql_2="SELECT
											r.xResultat,
											r.Leistung
									   FROM
											serie as se
											INNER JOIN serienstart as st USING (xSerie)
											INNER JOIN resultat as r USING (xSerienstart)  
									   WHERE
											st.xStart=" . $row[0]; 
										  
							 $result2 = mysql_query($mysql_2);     
							 if(mysql_errno() > 0){
								AA_printErrorPage(mysql_errno().": ".mysql_error());
							 }else{ 
								  if (mysql_num_rows($result2) > 0) {  
									  
										while ($row2=mysql_fetch_row($result2)){ 
											 $max_array[$i]=$row2[1];
											 $i++;
										} 
								  }  
						   }
					   } 
				}           
		   } 
		  $best=max($max_array);   
		}     
   return $best;
}
						 

 /**
	 * get the best result from previous day for disciplines track 
	 * -----------------------------------------------------------
	 */


function AA_getBestPreviousTrack($event,$disciplin, $enrolement)
{   
	 $best = 0; 
	 $i=0;                     
	 $min_array= array();   
  
	 $mysql="SELECT 
					a.xAthlet                
			   FROM
					anmeldung as a                                                 
			   WHERE
					a.xAnmeldung=" . $enrolement;           
	 
	 $result = mysql_query($mysql);         
			 
	 $row = mysql_fetch_row($result);  
	 $athlet=$row[0];   
   
	 $mysql_1="SELECT 
					s.xStart,
					s.xWettkampf,
					s.xAnmeldung,
					xDisziplin
			   FROM
					wettkampf as w 
					INNER JOIN start as s USING (xWettkampf)                                  
			   WHERE
					s.xAnmeldung=" . $enrolement. "
					AND xDisziplin=" . $disciplin. "
					ORDER BY s.xWettkampf ASC";  
						 
	 $result1 = mysql_query($mysql_1);  
	  
	 if(mysql_errno() > 0){
			AA_printErrorPage(mysql_errno().": ".mysql_error());
	 }else{  
			while ($row1 = mysql_fetch_row($result1)) {  // all events from same enrolement and same discipline                 
					  
					if ($row1[1]!=$event) {                        
						$event_previous=$row1[1];      
					   
						$mysql_2="SELECT                   
										r.xRunde                                        
								  FROM
										runde as r 
										INNER JOIN serie as s USING (xRunde) 
										INNER JOIN serienstart as st USING (xSerie) 
										INNER JOIN resultat as res USING (xSerienstart)                                                             
								  WHERE 
										r.xWettkampf=" . $event_previous; 
										   
						$result2 = mysql_query($mysql_2);                    
			 
						$round=0;  
						while ($row2 = mysql_fetch_row($result2)) {
																	// all rounds from every event from same enrolement and same discipline  
							  if ($round!=$row2[0]){  
								   $mysql_3="SELECT                    
													ss.xSerienstart 
													, a.xAthlet 
											 FROM runde AS r
													, serie AS s
													, serienstart AS ss
													, start AS st
													, anmeldung AS a
													, athlet AS at
													, verein AS v
													LEFT JOIN rundentyp AS rt
													ON rt.xRundentyp = r.xRundentyp                                       
											 WHERE r.xRunde =" .$row2[0] . "
													AND s.xRunde = r.xRunde
													AND ss.xSerie = s.xSerie
													AND st.xStart = ss.xStart
													AND a.xAnmeldung = st.xAnmeldung
													AND at.xAthlet = a.xAthlet 
													AND v.xVerein = at.xVerein"; 
											   
								   $result3 = mysql_query($mysql_3);  
													  
								   while ($row3 = mysql_fetch_row($result3)) {  
								   
										if ($row3[1]==$athlet) {   // check if result belong to the athlet
											 
											 $mysql_4="SELECT rs.xResultat
															, rs.Leistung
															, rs.Info
													   FROM 
															resultat AS rs                                                         
													   WHERE rs.xSerienstart =" .$row3[0] ."
															ORDER BY rs.Leistung ASC";  
												 
											$result4 = mysql_query($mysql_4);
											$row4= mysql_fetch_row($result4);  
											$min_array[$i]=$row4[1];
											$i++; 
									}  
							}  
					}
				$round=$row2[0];
			   } 
			 }
		   }
		   $best=min($min_array);   
	  } 
   return $best;
}

	
} // end AA_COMMON_LIB_INCLUDED
?>
