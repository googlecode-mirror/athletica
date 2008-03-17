<?php

/**********
 *
 *	event_rankinglists.php
 *	----------------------
 *	
 */

require('./lib/cl_gui_menulist.lib.php');
require('./lib/cl_gui_page.lib.php');

require('./lib/common.lib.php');
require('./lib/results.lib.php');

$disciplines=false;  

if(AA_connectToDB() == FALSE)	{				// invalid DB connection
	return;		// abort
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}

// get presets
$round = 0;
if(!empty($_GET['round'])){
	$round = $_GET['round'];
}
else if(!empty($_POST['round'])) {
	$round = $_POST['round'];
}
  
$presets = AA_results_getPresets($round);

// check discipline type of event if selected
$dtype = "";
if(!empty($presets['event'])){
	$res = mysql_query("
		SELECT d.Typ FROM 
			wettkampf as w
			LEFT JOIN disziplin as d USING(xDisziplin) 
		WHERE w.xWettkampf = ".$presets['event']
	);
    
	if(mysql_errno() > 0){
		
	}else{
		$row = mysql_fetch_array($res);
		$dtype = $row[0];
	}
}

//
//	Display print form
//

$page = new GUI_Page('event_rankinglists');
$page->startPage();
$page->printPageTitle($strRankingLists . ": " . $_COOKIE['meeting']);

$menu = new GUI_Menulist();
$menu->addButton($cfgURLDocumentation . 'help/event/rankinglists.html', $strHelp, '_blank');
$menu->printMenu();

?>
<script type="text/javascript">
<!--
	function setPrint()
	{
		document.printdialog.formaction.value = 'print';
		document.printdialog.target = '_blank';
	}
	
	function setView()
	{
		document.printdialog.formaction.value = 'view';
		document.printdialog.target = '';
	}
	
	function setExportPress()
	{
		document.printdialog.formaction.value = 'exportpress';
		document.printdialog.target = '';
	}
	
	function setExportDiplom()
	{
		document.printdialog.formaction.value = 'exportdiplom';
		document.printdialog.target = '';
	}
    
    function checkDisc()
    {   e = document.getElementById("combined"); 
        e.checked=true; 
    }
    
   
//-->
</script>

<p/>

<table><tr>
	<td>
		<?php	AA_printCategorySelection('event_rankinglists.php'
			, $presets['category'], 'post'); ?>
	</td>
	<td>
		<?php	AA_printEventSelection('event_rankinglists.php'
			, $presets['category'], $presets['event'], 'post'); ?>
	</td>
<?php
if($presets['event'] > 0) {		// event selected
?>
	<td>
		<?php AA_printRoundSelection('event_rankinglists.php'
			, $presets['category'] , $presets['event'], $round); ?>
	</td>
<?php
}
?>
    
<form action='print_rankinglist.php' method='get' name='printdialog'>

<input type='hidden' name='category' value='<?php echo $presets['category']; ?>'>
<input type='hidden' name='event' value='<?php echo $presets['event']; ?>'>
<input type='hidden' name='round' value='<?php echo $round; ?>'>
<input type='hidden' name='formaction' value=''>

<table class='dialog'>
<tr>
	<th class='dialog'>
		<input type='radio' name='type' value='single' id='type'  checked >
			<?php echo $strSingleEvent; ?></input>
	</th>
</tr>
<?php
if(($dtype == $cfgDisciplineType[$strDiscTypeJump])
	|| ($dtype == $cfgDisciplineType[$strDiscTypeJumpNoWind])
	|| ($dtype == $cfgDisciplineType[$strDiscTypeThrow])
	|| ($dtype == $cfgDisciplineType[$strDiscTypeHigh])
	|| empty($presets['event'])) {
?>
<tr>
	<th class='dialog'>
		<input type='radio' name='type' value='single_attempts' id='type' >
			<?php echo $strSingleEventAttempts; ?></input>
	</th>
</tr>

<?php
}

// Rankginglists for club and combined-events
if(empty($presets['event']))	// no event selected
{
?>
<tr>
	<th class='dialog'>
		<input type='radio' name='type' value='combined' id='combined' >
			<?php echo $strCombinedEvent; ?> </input>
        
	</th>
</tr>

<tr> 
    <td class='dialog'>
        &nbsp;&nbsp;  
        <input type='checkbox' name='sepu23' value='yes'>
            <?php echo $strSeparateU23; ?></input>
    </td>
</tr>
<?php 
                      // show disciplines for combined events 
                      
   $selection="";
if (!empty($presets['category'])){
    $selection=" AND k.xKategorie = " . $presets['category'] . " ";
}  
   $newcat="";
                 
  $c=0;
  /*
 $result_disc="SELECT   
         Distinct (k.Name )          
        , w.Mehrkampfcode
        , d.Name
        , w.xKategorie 
        , k.Geschlecht  
    FROM
        anmeldung AS a
        , athlet AS at
        , verein AS v
        , kategorie AS k
        , kategorie AS ka
        , start as st
        , wettkampf as w
        , disziplin as d
        LEFT JOIN region as re ON at.xRegion = re.xRegion
    WHERE a.xMeeting = " . $_COOKIE['meeting_id'] ."    
    AND at.xAthlet = a.xAthlet
    AND v.xVerein = at.xVerein
    AND k.xKategorie = w.xKategorie
    " . $selection . "
    AND st.xAnmeldung = a.xAnmeldung
    AND w.xWettkampf = st.xWettkampf
    AND w.Mehrkampfcode = d.Code
    AND w.Mehrkampfcode > 0
    AND ka.xKategorie = a.xKategorie"; 
   */ 
   $result_disc="SELECT   
         Distinct (k.Name )          
        , w.Mehrkampfcode
        , d.Name
        , w.xKategorie 
        , k.Geschlecht 
        
    FROM
        anmeldung AS a
        LEFT JOIN athlet AS at USING (xAthlet)
        LEFT JOIN verein AS v USING (xVerein)
        LEFT JOIN kategorie AS k  ON (k.xKategorie = w.xKategorie )
        LEFT JOIN kategorie AS ka  ON (ka.xKategorie = a.xKategorie ) 
        LEFT JOIN start as st ON (st.xAnmeldung = a.xAnmeldung )
        LEFT JOIN wettkampf as w  USING (xWettkampf)
        LEFT JOIN disziplin as d ON (w.Mehrkampfcode = d.Code) 
        LEFT JOIN region as re ON at.xRegion = re.xRegion
    WHERE a.xMeeting =  " . $_COOKIE['meeting_id'] ."  
        " . $selection . "   
        AND w.Mehrkampfcode > 0
    ORDER BY k.Anzeige";   
  
    $res_disc=mysql_query($result_disc);  
    
    while ($row_disc=mysql_fetch_array($res_disc))
        {                                 
          $tmp = $row_disc[1];
          if($tmp==394 && ($row_disc['Geschlecht']=='m' || $row_disc['Geschlecht']=='M')){
                $tmp = 3942;
          }
               
          if(isset($cfgCombinedDef[$tmp])){  
                $tt = $cfgCombinedDef[$tmp];  
                
               foreach ($cfgCombinedWO[$tt] as $key => $wert){
                   $res_comb = mysql_query("SELECT 
                                                    xDisziplin
                                                    , Name 
                                                FROM 
                                                    disziplin 
                                                WHERE
                                                    Code = $wert");
                                                    
                   $row_comb = mysql_fetch_array($res_comb);
                  
                   // show disciplines for combined event  
                   if ($newcat!=$row_disc[3]){
                        $c=0; 
                        ?>
                   
                        <tr>
                        <td class='dialog'>
                        &nbsp;&nbsp;  <?php echo $row_disc[0] . " " . $row_disc[2];   ?>
                     
                        </td>
                        </tr>
                        <?php 
                        $newcat=$row_disc[3];   
                   } 
                   ?>
                   
                   <tr>
                    <td class='dialog'>
                     &nbsp;&nbsp;
                      <input type='checkbox' name='comb_<?php echo $row_disc[3] ?>_<?php echo $c ?>' value='<?php echo $row_comb[0]; ?>' id='comb_<?php echo $row_disc[3] ?>_<?php echo $c ?>' onchange="checkDisc()">
                      <?php echo $row_comb[1];  ?></input>
                      <input type='hidden' name='count_<?php echo $row_disc[3] ?>' value='<?php echo $c; ?>'> 
                      </td>
                    </tr>

                     <?php   
                      
                      $c++;  
                   
               }                                 
              
          }
          else {              // combined codes not defined in $cfgCombinedDef
                $result_m="SELECT 
                                 d.Code 
                         FROM 
                                wettkampf AS w
                                LEFT JOIN kategorie AS k USING ( xKategorie )
                                LEFT JOIN disziplin AS d ON ( w.xDisziplin = d.xDisziplin )
                         WHERE w.xMeeting = 1 AND w.xKategorie = 1
                         ORDER BY 
                                w.Mehrkampfcode DESC 
                                , w.Mehrkampfreihenfolge
                                , d.Anzeige";   
                      
                $res_m = mysql_query($result_m);
                  
                while ($row_m=mysql_fetch_row($res_m)){   
                        
                       $res_comb_m = mysql_query("SELECT 
                                                    xDisziplin
                                                    , Name 
                                                FROM 
                                                    disziplin 
                                                WHERE
                                                    Code = $row_m[0]");
                                                    
                       $row_comb_m = mysql_fetch_array($res_comb_m);
                        
                        // show disciplines for combined event
                        if ($newcat!=$row_disc[3]){
                            $c=0; 
                            ?>
                   
                            <tr>
                                <td class='dialog'>
                                &nbsp;&nbsp;  <?php echo $row_disc[0] . " " . $row_disc[2];   ?>
                     
                                </td>
                            </tr>
                            <?php 
                            $newcat=$row_disc[3];   
                         } 
                         ?>
                   
                        <tr>
                            <td class='dialog'>
                            &nbsp;&nbsp;
                            <input type='checkbox' name='comb_<?php echo $row_disc[3] ?>_<?php echo $c ?>' value='<?php echo $row_comb_m[0]; ?>' id='comb_<?php echo $row_disc[3] ?>_<?php echo $c ?>' onchange="checkDisc()">
                            <?php echo $row_comb_m[1]; ?></input>
                            <input type='hidden' name='count_<?php echo $row_disc[3] ?>' value='<?php echo $c; ?>'> 
                            </td>
                        </tr> 
                        <?php  
                       $c++;  
                  }  
          } 
    } 

?>   

<tr>
	<th class='dialog'>
		<input type='radio' name='type' value='team'>
			<?php echo $strClubRanking; ?></input>
	</td>
</tr>

<tr>
	<th class='dialog'>
		<input type='radio' name='type' value='sheets'>
			<?php echo $strClubSheets; ?></input>
	</td>
</tr>

<?php
}

if(empty($round)){	// team sm ranking minimum is discipline
?>
<tr>
	<th class='dialog'>
		<input type='radio' name='type' value='teamsm'>
			<?php echo $strTeamSMRanking; ?></input>
	</td>
</tr>
<?php
}

if(empty($presets['event']))	// show page break only event not selected
{										
?>
<tr>
	<th class='dialog'>
		<?php echo $strPageBreak; ?>
	</th>
</tr>


<tr>
	<td class='dialog'>
		<input type='radio' name='break' value='none' checked>
			<?php echo $strNoPageBreak; ?></input>
	</td>
</tr>
<?php
	if(empty($presets['category']))	// show page break 'category' only if no
	{											// specific category selected
?>
<tr>
	<td class='dialog'>
		<input type='radio' name='break' value='category'>
			<?php echo $strCategory; ?></input>
	</td>
</tr>
<?php
	}		// ET page break category
?>
<tr>
	<td class='dialog'>
		<input type='radio' name='break' value='discipline'>
			<?php echo $strDiscipline; ?></input>
	</td>
</tr>
<?php
}		// ET page break

$tage = 1;
$sql = "SELECT DISTINCT(Datum) AS Datum 
		  FROM runde 
	 LEFT JOIN wettkampf USING(xWettkampf) 
		 WHERE xMeeting = ".$_COOKIE['meeting_id']." 
	  ORDER BY Datum ASC;";
$query = mysql_query($sql);

$tage = mysql_num_rows($query);
if($tage>1){
	?>
	<tr>
		<th class='dialog'>
			<?php echo $strDay; ?></input>
		</th>
	</tr>
	<tr>
		<td class='dialog'>
			<select name='date'>
				<option value="%">- <?=$strAll?> -</option>
				<?php
				while($row = mysql_fetch_assoc($query)){
					?>
					<option value="<?=$row['Datum']?>"><?=date('d.m.Y', strtotime($row['Datum']))?></option>
					<?php
				}
				?>
			</select>
		</td>
	</tr>
	<?php
}
?>
<tr>
	<th class='dialog'>
		<input type='checkbox' name='cover' value='cover'>
			<?php echo $strCover; ?></input>
	</th>
</tr>
<tr>
	<td class='dialog'>
		<input type='checkbox' name='cover_timing' value='1'>
			<?php echo $strTiming; ?></input>
	</td>
</tr>

</table>

<p />

<table>
<tr>
	<td>
		<button name='view' type='submit' onClick='setView()'>
			<?php echo $strShow; ?>
		</button>
	</td>
	<td>
		<button name='print' type='submit' onClick='setPrint()'>
			<?php echo $strPrint; ?>
		</button>
	</td>
</tr>
</table>

<br>

<table class="dialog">
<tr>
	<th class="dialog"><?php echo $strExport ?></th>
</tr>
<tr>
	<td class="forms">
		<input type="radio" name="limitRank" value="yes" id="limitrank">
		<?php echo $strExportRanks ?> <input type="text" size="2" name="limitRankFrom" onfocus="o = document.getElementById('limitrank'); o.checked='checked'">
		<?php echo strtolower($strTo) ?> <input type="text" size="2" name="limitRankTo" onfocus="o = document.getElementById('limitrank'); o.checked='checked'">
	</td>
</tr>
<tr>
	<td class="forms">
		<input type="radio" name="limitRank" value="no" checked><?php echo $strExportAllRanks ?>
	</td>
</tr>
<tr>
	<td class="forms" align="right">
		<button name='print' type='submit' onClick='setExportPress()'>
			<?php echo $strExportPress; ?>
		</button>
		<button name='print' type='submit' onClick='setExportDiplom()'>
			<?php echo $strExportDiplom; ?>
		</button>
	</td>
</tr>
</table>

</form>  

<?php

$page->endPage();



?>
