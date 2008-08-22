<?php
/**
 * C O N F I G U R A T I O N
 * -------------------------
 */

/* 
 *	ATTENTION:
 *	Do not change the following options without knowing what you're doing.
 *	These options steer the program flow, therefore changes almost certainly
 *	also require changes to the affected functions.
 *	
 */
 
/**
 * Application Info
 */
$cfgApplicationName = 'Athletica';
$cfgApplicationVersion = '3.3.11';
$cfgInstallDir = '[ATHLETICA]';

/**
 * Backup Info
*/
$cfgBackupCompatibles = array(
	// SLV
	'SLV_1.4',
	'SLV_1.5',
	'SLV_1.6',
	'SLV_1.7',
	'SLV_1.7.1',
	'SLV_1.7.2',
	'SLV_1.8',
	'SLV_1.8.1',
	'SLV_1.8.2',
	'SLV_1.9',
	// Athletica
	'3.0',
	'3.0.1',
	'3.1',
	'3.1.1',
	'3.1.2',
	'3.2',
	'3.2.1',
	'3.2.2',
	'3.2.3',
	'3.3',
	'3.3.1',
	'3.3.2',
	'3.3.3',
	'3.3.4',
	'3.3.5',
	'3.3.6',
	'3.3.7', 
	'3.3.8',
	'3.3.9',
	'3.3.10',
	'3.3.11',
);


/**
 * Include language parameters
 */
include("./lang/german.inc.php"); // if an other language is set, no text will be missing (even if its in german)
if(!empty($_COOKIE['language_trans'])) {
	include ($_COOKIE['language_trans']);
}
$cfgURLDocumentation = $_COOKIE['language_doc'];


/**
 * include user parameters
 */
require ('./parameters.inc.php');


/**
 *	Discipline type
 * 		Discipline types for reports and forms.
 */
$cfgDisciplineType = array($strDiscTypeNone=>0
								, $strDiscTypeTrack=>1
								, $strDiscTypeTrackNoWind=>2
								, $strDiscTypeRelay=>3
								, $strDiscTypeJump=>4
								, $strDiscTypeJumpNoWind=>5
								, $strDiscTypeHigh=>6
								, $strDiscTypeDistance=>7
								, $strDiscTypeThrow=>8
								, $strDiscCombined=>9);
								
/**
*
*	Number of attempts to be printed for default
*
**/
$cfgCountAttempts = array(
			$cfgDisciplineType[$strDiscTypeJump]=>3
			, $cfgDisciplineType[$strDiscTypeJumpNoWind]=>3
			, $cfgDisciplineType[$strDiscTypeThrow]=>6);

/**
 * Evaluation type
 *		Result evaluation strategies.
 */
$cfgEvalType = array($strEvalTypeHeat=>0
							, $strEvalTypeAll=>1
							, $strEvalTypeDiscDefault=>2);


/**
 *	Event type
 */
$cfgEventType = array(		$strEventTypeSingle=>0
							, $strEventTypeSingleCombined=>1
							, $strEventTypeTeamSM=>30
							, $strEventTypeSVMNL=>12
							, $strEventTypeClubMA=>2
							, $strEventTypeClubMB=>3
							, $strEventTypeClubMC=>4
							, $strEventTypeClubFA=>5
							, $strEventTypeClubFB=>6
							, $strEventTypeClubBasic=>7
							, $strEventTypeClubAdvanced=>8
							, $strEventTypeClubTeam=>9
							, $strEventTypeClubCombined=>10
							, $strEventTypeClubMixedTeam=>11);


/**
 *	Combined Codes referenced with WO-combined contests
 */
$cfgCombinedDef = array(	410 => 'MAN'		// Stadion
				, 411 => 'MANU20'
				, 412 => 'MANU18'
				, 402 => 'U16M'
				, 400 => 'WOM'
				, 401 => 'U18W'
				, 399 => 'U16W'
				, 396 => 'HMAN'		// Halle
				, 397 => 'HMANU20'
				, 398 => 'HMANU18'
				, 394 => 'HWOM'		// 5Kampf Halle W
				, 3942 => 'H5MAN'	// 5Kampf Halle M
				, 395 => 'HWOMU18'
				);

/**	
 *	WO-combined contests, inclusive point table
 *		MAN => contests
 *		MAN_F => formula table
 */
$cfgCombinedWO = array(	'MAN' => array(40,330,351,310,70,271,361,320,391,110)
			, 'MAN_F' => 3
			, 'MANU20' => array(40,330,348,310,70,269,359,320,391,110)
			, 'MANU20_F' => 3
			, 'MANU18' => array(40,330,347,310,70,268,358,320,389,110)
			, 'MANU18_F' => 3
			, 'U16M' => array(261,330,349,310,357,100)
			, 'U16M_F' => 1
			, 'WOM' => array(261,310,349,50,330,388,90)
			, 'WOM_F' => 4
			, 'U18W' => array(259,330,388,50,310,352,90)
			, 'U18W_F' => 4
			, 'U16W' => array(35,330,352,310,100)
			, 'U16W_F' => 2
			, 'HMAN' => array(30,330,351,310,252,320,100)
			, 'HMAN_F' => 3
			, 'HMANU20' => array(30,330,348,310,253,320,100)
			, 'HMANU20_F' => 3
			, 'HMANU18' => array(30,330,347,310,254,320,100)
			, 'HMANU18_F' => 3
			, 'HWOM' => array(255,310,349,330,90)
			, 'HWOM_F' => 4
			, 'H5MAN' => array(252,310,351,330,90)
			, 'H5MAN_F' => 3
			, 'HWOMU18' => array(256,310,352,330,90)
			, 'HWOMU18_F' => 4
			);


/**
 * Heat status
 *		Status of result announcements per heat.
 */
$cfgHeatStatus = array("open"=>0
							, "announced"=>1
							);

/**
 *	Invalid Results
 *		Codes to be used for invalid results.
 */
$cfgInvalidResult = array("DNS"=>array ("code"=>-1
													, "short"=>$strDidNotStartShort
													, "long"=>$strDidNotStart
													)
								, "DNF"=>array ("code"=>-2
													, "short"=>$strDidNotFinishShort
													, "long"=>$strDidNotFinish
													)
								, "DSQ"=>array ("code"=>-3
													, "short"=>$strDisqualifiedShort
													, "long"=>$strDisqualified
													)
								, "NRS"=>array ("code"=>-4
													, "short"=>$strNoResultShort
													, "long"=>$strNoResult
													)
								, "WAI"=>array ("code"=>-99
													, "short"=>$strQualifyWaivedShort
													, "long"=>$strQualifyWaived
													)
								);

/**
 *	Missed Attempt
 *		Codes to be used for missed attempts in technical disciplines.
 */
$cfgMissedAttempt = array("code"=>'-'
									, "db"=>-99
								);

/**
 * Program Mode
 *		Mode may be defined per meeting. Used to define nbr of result fields
 *		that are displayed on the result form for technical disciplines.
 */
$cfgProgramMode = array(0 => array	("tech_res"=>1
												, "name"=>$strProgramModeBackoffice
												)
								,1 => array	("tech_res"=>6
												, "name"=>$strProgramModeField
												)
								);


/**
 *	Qualification type
 *		Qualification type for next round		
 */
$cfgQualificationType = array("top"=>array ("code"=>1
														, "class"=>"qual_top"
														, "token"=>"Q"
														, "text"=>$strQualifyTop
														)
								, "top_rand"=>array ("code"=>2
														, "class"=>"qual_top_rand"
														, "token"=>"Q*"
														, "text"=>"$strQualifyTop $strRandom"
														)
								, "perf"=>array ("code"=>3
														, "class"=>"qual_perf"
														, "token"=>"q"
														, "text"=>$strQualifyPerformance
														)
								, "perf_rand"=>array ("code"=>4
														, "class"=>"qual_perf_rand"
														, "token"=>"q*"
														, "text"=>"$strQualifyPerformance $strRandom"
														)
								, "waived"=>array ("code"=>9
														, "class"=>"qual_waived"
														, "token"=>"vQ"
														, "text"=>"$strQualifyWaived"
														)
								);


/**
 * Round status
 *		Round status to steer meeting workflow.
 */
$cfgRoundStatus = array("open"=>0
							, "heats_in_progress"=>1
							, "heats_done"=>2
							, "results_in_progress"=>3
							, "results_done"=>4
							, "enrolement_pending"=>5
							, "enrolement_done"=>6
							, "results_sent"=>99
						);

$cfgRoundStatusTranslation = array(0=>$strOpen
											, 1=>$strHeatsInWork
											, 2=>$strHeatsDone
											, 3=>$strResultsInWork
											, 4=>$strResultsDone
											, 5=>$strEnrolementPending
											, 6=>$strEnrolementDone
										);

/**
 * Speaker status
 *		Speaker status per round to steer speaker monitor.
 */
$cfgSpeakerStatus = array("open"=>0
							, "announcement_pend"=>1
							, "announcement_done"=>2
							, "ceremony_done"=>3
						);

/**
 *
 * option list for page header and footer
 *
**/
$cfgPageLayout = array( $strPageNumbers => 0
			, $strMeetingName => 1
			, $strOrganizer => 2
			, $strDateAndTime => 3
			, $strCreatedBy => 4
			, $strOwnText => 5
			, $strNoText => 6
			);

/**
 *
 * option list for timing type
 *
**/
$cfgTimingType = array( $strNoTiming => 'no'
			, $strTimingOmega => 'omega'
			, $strTimingAlge => 'alge'
		);

/**
 * defines content types for creating export files
 *
 */
$cfgContentTypes = array(	'txt' => array('mt' => "text" // mime type
						, 'lb' => "\r\n" // line break
						, 'td' => "" // text delimiter
						, 'fd' => ",") // field delimiter
				, 'csv' => array('mt' => "application/ms-excel"
						, 'lb' => "\r\n"
						, 'td' => "\""
						, 'fd' => ";")
				, 'xls' => array('mt' => "application/ms-excel"
						, 'lb' => "\r\n"
						, 'td' => "\""
						, 'fd' => ";")
			);

/**
 *
 * License types for athletes
 *
**/
$cfgLicenseType = array(	$strLicenseTypeNormal => 1
				,$strLicenseTypeDayLicense => 2
				,$strLicenseTypeNoLicense => 3
			);

/**
 *
 * pages that can be accessed with out login
 *
 */
$cfgOpenPages = array(	"speaker"
			, "speaker_entries"
			, "speaker_entry"
			, "speaker_rankinglists"
			, "speaker_results"
			, "meeting"
			, $_COOKIE['meeting']
			, "login");

/**
 *
 * char width table for Arial
 * used to determine line height on prints (if text is too long for cell width)
 *
**/
$cfgCharWidth = array(
	chr(0)=>278,chr(1)=>278,chr(2)=>278,chr(3)=>278,chr(4)=>278,chr(5)=>278,chr(6)=>278,chr(7)=>278,chr(8)=>278,chr(9)=>278,chr(10)=>278,chr(11)=>278,chr(12)=>278,chr(13)=>278,chr(14)=>278,chr(15)=>278,chr(16)=>278,chr(17)=>278,chr(18)=>278,chr(19)=>278,chr(20)=>278,chr(21)=>278,
	chr(22)=>278,chr(23)=>278,chr(24)=>278,chr(25)=>278,chr(26)=>278,chr(27)=>278,chr(28)=>278,chr(29)=>278,chr(30)=>278,chr(31)=>278,' '=>278,'!'=>278,'"'=>355,'#'=>556,'$'=>556,'%'=>889,'&'=>667,'\''=>191,'('=>333,')'=>333,'*'=>389,'+'=>584,
	','=>278,'-'=>333,'.'=>278,'/'=>278,'0'=>556,'1'=>556,'2'=>556,'3'=>556,'4'=>556,'5'=>556,'6'=>556,'7'=>556,'8'=>556,'9'=>556,':'=>278,';'=>278,'<'=>584,'='=>584,'>'=>584,'?'=>556,'@'=>1015,'A'=>667,
	'B'=>667,'C'=>722,'D'=>722,'E'=>667,'F'=>611,'G'=>778,'H'=>722,'I'=>278,'J'=>500,'K'=>667,'L'=>556,'M'=>833,'N'=>722,'O'=>778,'P'=>667,'Q'=>778,'R'=>722,'S'=>667,'T'=>611,'U'=>722,'V'=>667,'W'=>944,
	'X'=>667,'Y'=>667,'Z'=>611,'['=>278,'\\'=>278,']'=>278,'^'=>469,'_'=>556,'`'=>333,'a'=>556,'b'=>556,'c'=>500,'d'=>556,'e'=>556,'f'=>278,'g'=>556,'h'=>556,'i'=>222,'j'=>222,'k'=>500,'l'=>222,'m'=>833,
	'n'=>556,'o'=>556,'p'=>556,'q'=>556,'r'=>333,'s'=>500,'t'=>278,'u'=>556,'v'=>500,'w'=>722,'x'=>500,'y'=>500,'z'=>500,'{'=>334,'|'=>260,'}'=>334,'~'=>584,chr(127)=>350,chr(128)=>556,chr(129)=>350,chr(130)=>222,chr(131)=>556,
	chr(132)=>333,chr(133)=>1000,chr(134)=>556,chr(135)=>556,chr(136)=>333,chr(137)=>1000,chr(138)=>667,chr(139)=>333,chr(140)=>1000,chr(141)=>350,chr(142)=>611,chr(143)=>350,chr(144)=>350,chr(145)=>222,chr(146)=>222,chr(147)=>333,chr(148)=>333,chr(149)=>350,chr(150)=>556,chr(151)=>1000,chr(152)=>333,chr(153)=>1000,
	chr(154)=>500,chr(155)=>333,chr(156)=>944,chr(157)=>350,chr(158)=>500,chr(159)=>667,chr(160)=>278,chr(161)=>333,chr(162)=>556,chr(163)=>556,chr(164)=>556,chr(165)=>556,chr(166)=>260,chr(167)=>556,chr(168)=>333,chr(169)=>737,chr(170)=>370,chr(171)=>556,chr(172)=>584,chr(173)=>333,chr(174)=>737,chr(175)=>333,
	chr(176)=>400,chr(177)=>584,chr(178)=>333,chr(179)=>333,chr(180)=>333,chr(181)=>556,chr(182)=>537,chr(183)=>278,chr(184)=>333,chr(185)=>333,chr(186)=>365,chr(187)=>556,chr(188)=>834,chr(189)=>834,chr(190)=>834,chr(191)=>611,chr(192)=>667,chr(193)=>667,chr(194)=>667,chr(195)=>667,chr(196)=>667,chr(197)=>667,
	chr(198)=>1000,chr(199)=>722,chr(200)=>667,chr(201)=>667,chr(202)=>667,chr(203)=>667,chr(204)=>278,chr(205)=>278,chr(206)=>278,chr(207)=>278,chr(208)=>722,chr(209)=>722,chr(210)=>778,chr(211)=>778,chr(212)=>778,chr(213)=>778,chr(214)=>778,chr(215)=>584,chr(216)=>778,chr(217)=>722,chr(218)=>722,chr(219)=>722,
	chr(220)=>722,chr(221)=>667,chr(222)=>667,chr(223)=>611,chr(224)=>556,chr(225)=>556,chr(226)=>556,chr(227)=>556,chr(228)=>556,chr(229)=>556,chr(230)=>889,chr(231)=>500,chr(232)=>556,chr(233)=>556,chr(234)=>556,chr(235)=>556,chr(236)=>278,chr(237)=>278,chr(238)=>278,chr(239)=>278,chr(240)=>556,chr(241)=>556,
	chr(242)=>556,chr(243)=>556,chr(244)=>556,chr(245)=>556,chr(246)=>556,chr(247)=>584,chr(248)=>611,chr(249)=>556,chr(250)=>556,chr(251)=>556,chr(252)=>556,chr(253)=>500,chr(254)=>556,chr(255)=>500);


/**
 * Connection information for the slv webserver
 *
 *
 */
$cfgSLVhost = "www.swiss-athletics.ch";
$cfgSLVuser = "athletica";
$cfgSLVpass = "impBOSS";

?>
