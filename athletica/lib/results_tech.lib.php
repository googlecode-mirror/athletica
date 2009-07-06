<?php

/**********
 *
 *    tech results
 *    
 */

if (!defined('AA_RESULTS_TECH_LIB_INCLUDED'))
{
    define('AA_RESULTS_TECH_LIB_INCLUDED', 1);

function AA_results_Tech($round, $layout)
{                          
require('./lib/cl_gui_button.lib.php');

require('./config.inc.php');
require('./lib/common.lib.php');
require('./lib/heats.lib.php');
require('./lib/results.lib.php');
require('./lib/utils.lib.php');
require('./lib/cl_wind.lib.php');

$presets = AA_results_getPresets($round);    // read GET/POST variables

$nextRound = AA_getNextRound($presets['event'], $round);

$svm = AA_checkSVM(0, $round); // decide whether to show club or team name  

//
// terminate result processing
//
if($_GET['arg'] == 'results_done')
{
    $eval = AA_results_getEvaluationType($round);
    $combined = AA_checkCombined(0, $round);
    
    mysql_query("LOCK TABLES r READ, s READ, ss READ, runde READ");
    
    // if this is a combined event, rank all rounds togheter
    $roundSQL = "";
    $roundSQL2 = "";
    if($combined){
        $roundSQL = "AND serie.xRunde IN (";
        $roundSQL2 = "AND s.xRunde IN (";
        $res_c = mysql_query("SELECT xRunde FROM runde WHERE xWettkampf = ".$presets['event']);
        while($row_c = mysql_fetch_array($res_c)){
            $roundSQL .= $row_c[0].",";
            $roundSQL2 .= $row_c[0].",";
        }
        $roundSQL = substr($roundSQL,0,-1).")";
        $roundSQL2 = substr($roundSQL2,0,-1).")";
    }else{
        $roundSQL = "AND serie.xRunde = $round";
        $roundSQL2 = "AND s.xRunde = $round";
    }
    
    // evaluate max. nbr of results entered
    $r = 0;
    $result = mysql_query("SELECT COUNT(*)"
                                . " FROM resultat AS r"
                                . ", serienstart AS ss"
                                . ", serie AS s"
                                . " WHERE r.xSerienstart = ss.xSerienstart"
                                . " AND ss.xSerie = s.xSerie"
                                //. " AND s.xRunde = " . $round
                                . " $roundSQL2 "
                                . " GROUP BY r.xSerienstart"
                                . " ORDER BY 1 DESC");

    if(mysql_errno() > 0) {        // DB error
        AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
    }
    else {
        $row = mysql_fetch_row($result);        // get first row
        $r = $row[0];                                // max. nbr of results
        mysql_free_result($result);
    }

    if($r > 0)        // any results found
    {
        mysql_query("DROP TABLE IF EXISTS tempresult");    // temporary table

        if(mysql_errno() > 0) {        // DB error
            AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
        }
        else
        {
            mysql_query("
                LOCK TABLES
                    resultat READ
                    , serie READ
                    , wettkampf READ
                    , serienstart WRITE
                    , tempresult WRITE
            ");

            // Set up a temporary table to hold all results for ranking.
            // The number of result columns varies according to the maximum
            // number of results per athlete.
            $qry = "
                CREATE TABLE tempresult (
                    xSerienstart int(11)
                    , xSerie int(11)";

            for($i=1; $i <= $r; $i++) {
                $qry = $qry . ", Res" . $i . " int(9) default '0'";
                $qry = $qry . ", Wind" . $i . " char(5) default '0'";
            }
            $qry = $qry . ") TYPE=HEAP";

            mysql_query($qry);    // create temporary table

            if(mysql_errno() > 0) {        // DB error
                AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
            }
            else
            {
                $result = mysql_query("
                    SELECT
                        resultat.Leistung
                        , resultat.Info
                        , serienstart.xSerienstart
                        , serienstart.xSerie
                    FROM
                        resultat
                        , serienstart
                        , serie
                    WHERE resultat.xSerienstart = serienstart.xSerienstart
                    AND serienstart.xSerie = serie.xSerie
                    $roundSQL
                    AND resultat.Leistung >= 0
                    ORDER BY
                        serienstart.xSerienstart
                        ,resultat.Leistung DESC
                ");
               
                if(mysql_errno() > 0) {        // DB error
                    AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                }
                else
                {
                    // initialize variables
                    $ss = 0;
                    $i = 0;
                    // process every result
                    while($row = mysql_fetch_row($result))
                    {
                        if($ss != $row[2])     // next athlete
                        {
                            // add one row per athlete to temp table
                            if($ss != 0) {
                                for(;$i < $r; $i++) { // fill remaining result cols.
                                    $qry = $qry . ",0,''";
                                }
                                mysql_query($qry . ")");
                                if(mysql_errno() > 0) {        // DB error
                                    AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                                }
                            }
                            // (re)set SQL statement
                            $qry = "INSERT INTO tempresult VALUES($row[2],$row[3]";
                            $i = 0;
                        }
                        $qry = $qry . ",$row[0],'$row[1]'";    // add current result to query
                        $ss = $row[2];                // keep athlete's ID
                        $i++;                                // count nbr of results
                    }
                    mysql_free_result($result);

                    // insert last pending data in temp table
                    if($ss != 0) {
                        for(;$i < $r; $i++) {    // fill remaining result cols.
                            $qry = $qry . ",0,''";
                        }
                        mysql_query($qry . ")");
                        if(mysql_errno() > 0) {        // DB error
                            AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                        }
                    }
                }

                if($eval == $cfgEvalType[$strEvalTypeHeat]) {    // eval per heat
                    $qry = "
                        SELECT
                            *
                        FROM
                            tempresult
                        ORDER BY
                            xSerie";

                    for($i=1; $i <= $r; $i++) {
                        $qry = $qry . ", Res" . $i . " DESC";
                    }                                                                                                            
                }
                else {    // default: rank results from all heats together
                    $qry = "
                        SELECT
                            *
                        FROM
                            tempresult
                        ORDER BY ";
                    $comma = "";
                    // order by available result columns
                    for($i=1; $i <= $r; $i++) {
                        $qry = $qry . $comma . "Res" . $i . " DESC";
                        $comma = ", ";
                    }

                }

                $result = mysql_query($qry);

                if(mysql_errno() > 0) {        // DB error
                    AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                }
                else {
                    // initialize variables
                    $heat = 0;
                    $perf_old[] = '';
                    $j = 0;
                    $rank = 0;
                    // set rank for every athlete
                    while($row = mysql_fetch_row($result))
                    {
                        for($i=0; $i <= $r; $i++) {
                            $perf[$i] = $row[(2*$i)+2];
                            $wind[$i] = $row[(2*$i)+3];
                        }

                        if(($eval == $cfgEvalType[$strEvalTypeHeat])    // new heat
                            &&($heat != $row[1]))
                        {
                            $j = 0;        // restart ranking
                            $perf_old[] = '';
                        }

                        $j++;                                // increment ranking
                        if($perf_old != $perf) {    // compare performances
                            $rank = $j;    // next rank (only if not same performance)
                        }

                        mysql_query("
                            UPDATE serienstart SET
                                Rang = $rank
                            WHERE xSerienstart = $row[0]
                        ");

                        if(mysql_errno() > 0) {
                            AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                        }
                        $heat = $row[1];        // keep current heat ID
                        $perf_old = $perf;
                    }
                    mysql_free_result($result);
                }

                mysql_query("DROP TABLE IF EXISTS tempresult");

                if(mysql_errno() > 0) {        // DB error
                    AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                }
            }    // ET DB error (create temp table)

            mysql_query("UNLOCK TABLES");
        }    // ET DB error (drop temp table)
    }    // ET any results found
    
    AA_results_setNotStarted($round);    // update athletes with no result

    AA_utils_changeRoundStatus($round, $cfgRoundStatus['results_done']);
    if(!empty($GLOBALS['AA_ERROR'])) {
        AA_printErrorMsg($GLOBALS['AA_ERROR']);
    }
    AA_results_resetQualification($round);
}

//
// calculate ranking points if needed
//
if(($_GET['arg'] == 'results_done')
|| ($_POST['arg'] == 'save_rank')){
    
    AA_utils_calcRankingPoints($round);
    
}

//
// Qualify athletes after ranks are set
//
if(($_GET['arg'] == 'results_done')
 || ($_POST['arg'] == 'save_rank')
 || ($_POST['arg'] == 'set_qual'))
{
    // read qualification criteria
    $qual_top = 0;
    $qual_perf = 0;
    $result = mysql_query("SELECT QualifikationSieger"
                                            . ", QualifikationLeistung"
                                            . " FROM runde"
                                            . " WHERE xRunde = " . $round);

    if(mysql_errno() > 0) {        // DB error
            AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
    }
    else
    {
        if(($row = mysql_fetch_row($result)) == TRUE);
        {
            $qual_top = $row[0];
            $qual_perf = $row[1];
        }
        mysql_free_result($result);
    }    // ET DB error

    // qualify top athletes for next round
    if($qual_top > 0)
    {
        mysql_query("LOCK TABLES serie READ, serienstart WRITE");

        // get athletes by qualifying rank (random order if same rank)
        $result =    mysql_query("SELECT serienstart.xSerienstart"
                            . ", serienstart.xSerie"
                            . ", serienstart.Rang"
                            . " FROM serienstart"
                            . ", serie"
                            . " WHERE serienstart.Rang > 0"
                            //. " AND serienstart.Rang <= " . $qual_top // don't limit rank
                            . " AND serienstart.xSerie = serie.xSerie"
                            . " AND serie.xRunde = " . $round
                            . " AND serienstart.Qualifikation = 0" // don't update athletes who got 'waived' flag
                            . " ORDER BY serienstart.xSerie"
                            . ", serienstart.Rang ASC"
                            . ", RAND()");

        if(mysql_errno() > 0) {
            AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
        }
        else
        {
            $h = 0;
            unset($heats);        // clear array containing heats

            while($row = mysql_fetch_row($result))
            {
                if($h != $row[1]) {    // new heat
                    if(count($starts) > 0) {    // count athletes
                        $heats[] = $starts;        // keep athletes per heat
                    }
                    unset($starts);
                    $c = 0;
                }
                $starts[$row[0]] = $row[2];    // keep athlete's rank
                $h = $row[1];                        // keep heat
            }
            $heats[] = $starts;                    // keep remaining athletes
            mysql_free_result($result);

            foreach($heats as $starts)        // process every heat
            {
                $rankcount = array_count_values($starts);    // count athletes/rank

                $q = 0;
                foreach($starts as $id=>$rank)    // process every athlete
                {
                    // check if more athletes per rank than qualifying spots
                    if($rankcount[$rank] > ($qual_top - $rank + 1)) {
                        $qual = $cfgQualificationType['top_rand']['code'];
                    }
                    else {
                        $qual = $cfgQualificationType['top']['code'];
                    }

                    if($q < $qual_top)        // not more than qualifying spots
                    {
                        mysql_query("UPDATE serienstart SET"
                                        . " Qualifikation = " . $qual
                                        . " WHERE xSerienstart = " . $id);

                        if(mysql_errno() > 0) {
                            AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                        }
                        $q++;        // count nbr of qualified athletes
                    }
                }

            }    // END loop every heat
        }    // ET DB error
        mysql_query("UNLOCK TABLES");
    }    // ET top athletes

    // qualify top performing athletes for next round
    if($qual_perf > 0)
    {
        mysql_query("LOCK TABLES resultat READ, serie READ, serienstart WRITE");

        // get remaining athletes by performance (random order if equal performance)

        /* other possible criteria to order equal performances:
         * - ranking within heat (not implemented)
         * - wind (not implemented)
         */

        $result = mysql_query("SELECT serienstart.xSerienstart"
                                    . ", resultat.Leistung"
                                    . ", serienstart.Qualifikation"
                                    . " FROM resultat"
                                    . ", serienstart"
                                    . ", serie"
                                    . " WHERE resultat.xSerienstart = serienstart.xSerienstart"
                                    . " AND resultat.Leistung > 0"
                                    . " AND (serienstart.Qualifikation = 0 "
                                        ." OR serienstart.Qualifikation = ".$cfgQualificationType['waived']['code'].")"
                                    . " AND serienstart.xSerie = serie.xSerie"
                                    . " AND serie.xRunde = " . $round
                                    . " ORDER BY resultat.Leistung DESC"
                                    . ", RAND()");

        if(mysql_errno() > 0) {
            AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
        }
        else
        {
            $i=1;
            $perf=0;
            $cWaived = 0;
            while($row = mysql_fetch_row($result))
            {
                // count waived qualifyings
                if($row[2] == $cfgQualificationType['waived']['code']){
                    $cWaived++;
                    continue;
                }
                
                if($i > $qual_perf) {    // terminate if enough top performers found
                    if($perf != $row[1]) {    // last perf. worse than last qualified
                        $perf=0;
                    }
                    break;
                }
                
                // if athletes waived on qualifying, set random code for next best athletes
                $code = $cfgQualificationType['perf']['code'];
                if($i+$cWaived > $qual_perf){
                    $code = $cfgQualificationType['perf_rand']['code'];
                }
                
                mysql_query("UPDATE serienstart SET"
                            . " Qualifikation = " . $code
                            . " WHERE xSerienstart = " . $row[0]);

                if(mysql_errno() > 0) {
                    AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                }
                $i++;
                $perf = $row[1];    // keep performance
            }

            // reset performance if enough qualifing spots
            if(mysql_num_rows($result) <= $qual_perf) {
                $perf=0;
            }

            mysql_free_result($result);

            // Change qualification type to "perf_rand" for athletes with same
            // performance as the 1st unqualified athlete
            if($perf != 0)
            {
                $result = mysql_query("SELECT serienstart.xSerienstart"
                                            . " FROM resultat"
                                            . ", serienstart"
                                            . ", serie"
                                            . " WHERE resultat.xSerienstart = serienstart.xSerienstart"
                                            . " AND resultat.Leistung = " . $perf
                                            . " AND serienstart.Qualifikation > 0"
                                            . " AND serienstart.xSerie = serie.xSerie"
                                            . " AND serie.xRunde = " . $round);

                if(mysql_errno() > 0) {
                    AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                }
                else
                {
                    while($row = mysql_fetch_row($result))
                    {
                        mysql_query("UPDATE serienstart SET"
                                            . " Qualifikation = " . $cfgQualificationType['perf_rand']['code']
                                            . " WHERE xSerienstart = " . $row[0]);

                        if(mysql_errno() > 0) {
                            AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                        }
                    }
                    mysql_free_result($result);
                }
            } // ET unqualified athlete

        }    // ET DB error qualified by performance

        mysql_query("UNLOCK TABLES");
    }    // ET top performances
}


//
// print HTML page header
//
AA_results_printHeader($presets['category'], $presets['event'], $round);

$mergedMain=AA_checkMainRound($round);
if ($mergedMain != 1) {

// read round data
if($round > 0)
{
    $status = AA_getRoundStatus($round);

    // No action yet
    if(($status == $cfgRoundStatus['open'])
        || ($status == $cfgRoundStatus['enrolement_done'])
        || ($status == $cfgRoundStatus['heats_in_progress']))
    {
        AA_printWarningMsg($strHeatsNotDone);
    }
    // Enrolement pending
    else if($status == $cfgRoundStatus['enrolement_pending'])
    {
        AA_printWarningMsg($strEnrolementNotDone);
    }
    // Heat seeding completed, ready to enter results
    else if($status >= $cfgRoundStatus['heats_done'])
    {
        // get program mode
        $prog_mode = AA_results_getProgramMode();
        
        AA_heats_printNewStart($presets['event'], $round, "event_results.php");

        // display all athletes
        $result = mysql_query("SELECT rt.Name"
                                . ", rt.Typ"
                                . ", s.xSerie"
                                . ", s.Bezeichnung"
                                . ", s.Wind"
                                . ", an.Bezeichnung"
                                . ", ss.xSerienstart"
                                . ", ss.Position"
                                . ", ss.Rang"
                                . ", a.Startnummer"
                                . ", at.Name"
                                . ", at.Vorname"
                                . ", at.Jahrgang"
                                 . ", if('".$svm."', t.Name, IF(a.Vereinsinfo = '', v.Name, a.Vereinsinfo))"   
                                . ", LPAD(s.Bezeichnung,5,'0') as heatid"
                                . ", r.Versuche"
                                . ", ss.Qualifikation"
                                . ", at.Land"
                                . ", r.nurBestesResultat"
                                . ", ss.Bemerkung"
                                . ", at.xAthlet"
                                . " FROM runde AS r"
                                . ", serie AS s"
                                . ", serienstart AS ss"
                                . ", start AS st"
                                . ", anmeldung AS a"
                                . ", athlet AS at"
                                . ", verein AS v"                                   
                                . " LEFT JOIN team AS t ON(a.xTeam = t.xTeam)" 
                                . " LEFT JOIN rundentyp AS rt"
                                . " ON rt.xRundentyp = r.xRundentyp"
                                . " LEFT JOIN anlage AS an"
                                . " ON an.xAnlage = s.xAnlage"
                                . " WHERE r.xRunde = " . $round
                                . " AND s.xRunde = r.xRunde"
                                . " AND ss.xSerie = s.xSerie"
                                . " AND st.xStart = ss.xStart"
                                . " AND a.xAnmeldung = st.xAnmeldung"
                                . " AND at.xAthlet = a.xAthlet"
                                . " AND v.xVerein = at.xVerein"
                                . " ORDER BY heatid, ss.Position");
               
        if(mysql_errno() > 0) {        // DB error
            AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
        }
        else
        {
            AA_results_printMenu($round, $status);

            // initialize variables
            $h = 0;
            $i = 0;
            $r = 0;
            $rowclass = 'odd';
            $r_rem = 0;
            
            $nextRound = AA_getNextRound($presets['event'], $round);
            
            // show qualification form if another round follows
            if($nextRound > 0){    // next round
                $sql = "SELECT QualifikationSieger, 
                               QualifikationLeistung 
                          FROM runde 
                         WHERE xRunde = ".$round.";";
                $result2 = mysql_query($sql);
                
                if(mysql_errno()>0){    // DB error
                    AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                } else {
                    $row2 = mysql_fetch_row($result2);
                    if($row2==true){    // round found
                        ?>
                        <p/>
                            <form name="qualification" action="event_results.php" method="post">
                                <input type="hidden" name="arg" value="set_qual"/>
                                <input type="hidden" name="round" value="<?=$round?>"/>
                                <table class="dialog">
                                    <tr>
                                        <td class="dialog"><?=$strQualification?> <?=$strQualifyTop?></td>
                                        <td class="dialog"><input type="text" name="qual_top" class="nbr" maxlength="4" value="<?=$row2[0]?>"/></td>
                                        <td class='dialog'><?=$strQualification?> <?=$strQualifyPerformance?></td>
                                        <td class='dialog'><input type="text" name="qual_perf" class="nbr" maxlength="4" value="<?=$row2[1]?>"/></td>
                                        <td><button type="submit"><?=$strChange?></button></td>
                                    </tr>
                                </table>
                            </form>
                            
                            <form name="frmQual" action="event_results.php" method="post">
                                <input type="hidden" name="arg" value="change_qual"/>
                                <input type="hidden" name="round" value="<?=$round?>"/>
                                <input type="hidden" name="focus" value="qual_0"/>
                                <input type="hidden" name="item" value="0"/>
                                <input type="hidden" name="oldqual" value="0"/>
                                <input type="hidden" name="heat" value="0"/>
                                <input type="hidden" name="qual" value="0"/>
                            </form>
                            <script type="text/javascript">
                                function changequal(valFocus, valItem, valOldQual, valHeat, valQual){
                                    var obj = document.frmQual;
                                    
                                    obj.focus.value = valFocus;
                                    obj.item.value = valItem;
                                    obj.oldqual.value = valOldQual;
                                    obj.heat.value = valHeat;
                                    obj.qual.value = valQual;
                                    obj.submit();
                                }
                            </script>
                        <p/>
                        <?php
                    } // ET round found
                }    // ET DB error
            }    // ET next round
            
?>
<p/>
<table class='dialog'>
<?php
            $btn = new GUI_Button('', '');    // create button object

            while($row = mysql_fetch_row($result))
            {
                
                //
                // get entered number of attempts
                //
                $maxatt = $row[15];
                if($maxatt != 0){
                    $cfgProgramMode[$prog_mode]['tech_res'] = $maxatt;
                }

/*
 *  Heat headerline
 */
                if($h != $row[2])        // new heat
                {
                    $h = $row[2];                // keep heat ID

                    if(is_null($row[0])) {        // only one round
                        $title = "$strFinalround";
                    }
                    else {        // more than one round
                        $title = "$row[0]";
                    }

                    $c = 0;
                    if($status == $cfgRoundStatus['results_done']) {
                        $c++;        // increment colspan to include ranking
                    }
?>
    <tr>
        <form action='event_results.php#heat_<?php echo $row[3]; ?>' method='post'
            name='heat_id_<?php echo $h; ?>'>

        <th class='dialog' colspan='
<?php echo 5 + $cfgProgramMode[$prog_mode]['tech_res'] + $c; ?>'>
            <?php echo $title; ?>
            <input type='hidden' name='arg' value='change_heat_name' />
            <input type='hidden' name='round' value='<?php echo $round; ?>' />
            <input type='hidden' name='item' value='<?php echo $row[2]; ?>' />
            <input class='nbr' type='text' name='id' maxlength='2'
                value='<?php echo $row[3]; ?>'
                onChange='document.heat_id_<?php echo $h;?>.submit()' />
                <a name='heat_<?php echo $row[3]; ?>' />
        </th>
        </form>
    </tr>

    <tr>
        <th class='dialog'><?php echo $strPositionShort; ?></th>
        <th class='dialog' colspan='2'><?php echo $strAthlete; ?></th>
        <th class='dialog'><?php echo $strYearShort; ?></th>
        <th class='dialog'><?php echo $strCountry; ?></th>
        <th class='dialog'><?php if($svm){ echo $strTeam; }else{ echo $strClub;} ?></th>
<?php
                    if($status == $cfgRoundStatus['results_done']) {
?>
        <th class='dialog'><?php echo $strRank; ?></th>
<?php

                        if($nextRound > 0) {
?>
        <th class='dialog'><?php echo $strQualification; ?></th>
<?php
                        }
                    }
                    
                    if($cfgProgramMode[$prog_mode]['tech_res'] <= 1)
                    {
?>
        <th class='dialog'><?php echo $strPerformance; ?></th>
<?php
                    }
                    else    // different header if more than one result to be entered
                    {
                        for($c=1; $c<=$cfgProgramMode[$prog_mode]['tech_res']; $c++)
                        {
?>
        <th class='dialog'><?php echo $c . "."; ?></th>
<?php
                        }
?> 
                    <th class='dialog'><?php echo $strResultRemark; ?></th> 
<?php     
                    }
?>
    </tr>
<?php
                }        // ET new heat

/*
 * Athlete data lines
 */
                $i++;
                if($row[7] % 2 == 0) {        // even row numer
                    $rowclass='odd';
                }
                else {                            // odd row number
                    $rowclass='even';
                }
?>
    <tr class='<?php echo $rowclass; ?>'>
        <td class='forms_right'><?php echo $row[7]; /* position */ ?></td>
        <td class='forms_right'><?php echo $row[9]; /* start nbr */ ?></td>
        <td nowrap><?php echo $row[10] . " " . $row[11];  /* name */ ?></td>
        <td class='forms_ctr'><?php echo AA_formatYearOfBirth($row[12]); ?></td>
        <td><?=(($row[17]!='' && $row[17]!='-') ? $row[17] : '&nbsp;')?></td>
        <td nowrap><?php echo $row[13]; /* club */ ?></td>
<?php
                $res = mysql_query("SELECT rs.xResultat"
                    . ", rs.Leistung"
                    . ", rs.Info"
                    . " FROM resultat AS rs"
                    . " WHERE rs.xSerienstart = " . $row[6]. "
                    ORDER BY rs.xResultat");
               
                if(mysql_errno() > 0) {        // DB error
                    AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                }
                else
                {
                    // Show rank
                    if($status == $cfgRoundStatus['results_done'])
                    {
?>
        <form action='event_results.php' method='post'
            name='rank_<?php echo $r; ?>'>
        <td>
            <input type='hidden' name='arg' value='save_rank' />
            <input type='hidden' name='round' value='<?php echo $round; ?>' />
            <input type='hidden' name='item' value='<?php echo $row[6]; ?>' />
            <input type='hidden' name='focus' value='rank_<?php echo $r; ?>' />
            <input class='nbr' type='text' name='rank' maxlength='3'
                value='<?php echo $row[8]; ?>' onChange='document.rank_<?php echo $r; ?>.submit()' />
        </td>
        <?php
        if($status == $cfgRoundStatus['results_done'])
                    {
                        if($nextRound>0){
                            ?>
                            <form name="qual_<?=$i?>" action="event_results.php" method="post">
                                <td>
                                    <input type="hidden" name="arg" value="change_qual"/>
                                    <input type="hidden" name="round" value="<?=$round?>"/>
                                    <input type="hidden" name="focus" value="qual_<?=$i?>"/>
                                    <input type="hidden" name="item" value="<?=$row[6]?>"/>
                                    <input type="hidden" name="oldqual" value="<?=$row[16]?>"/>
                                    <input type="hidden" name="heat" value="<?=$row[2]?>"/>
                                    <?php
                                    $dropdown = new GUI_Select('qual', 1, 'changequal("qual_'.$i.'", '.$row[6].', '.$row[16].', '.$row[2].', this.value)');
                                    $dropdown->addOptionNone();
                                    foreach($cfgQualificationType as $type){
                                        $dropdown->addOption($type['text'], $type['code']);
                                        if($type['code']==$row[16]){
                                            $dropdown->selectOption($type['code']);
                                        }
                                    }
                                    $dropdown->printList();
                                    ?>
                                </td>
                            </form>
                            <?php
                            $i++;        // next element
                            }    // qualification info
                        }
                        else
                        {    // no rank
?>
        <td />
<?php
                            if($nextRound > 0) {
?>
        <td />
<?php
                            }
                        }    // ET valid rank
                        ?>
        </form>
<?php
                    }
                    
                     for($c=1; $c<=$cfgProgramMode[$prog_mode]['tech_res']; $c++)
                    {        
                        
                       // Result focus:
                        // - Backoffice mode: same athlete, next result
                        // - Field mode: next athlete, same result
                        
                        $r++;        // increment result form counter    
                      
                        if($cfgProgramMode[$prog_mode]['name'] == $strProgramModeBackoffice)    // backoffice mode
                        { 
                            
                            if ($row[18] == 'y'){                                              // only best result --> focus next line
                                $focus = "perf_" . ($r + $cfgProgramMode[$prog_mode]['tech_res'] + 1); 
                               
                                if(mysql_num_rows($result) == $i) { // no more athletes
                                    if($c == $cfgProgramMode[$prog_mode]['tech_res']) {    // last result
                                        $focus = "perf_" . ($r+1);    // keep focus on this athlete
                                    }
                                    else {
                                        $focus = "perf_" . ($c+1);    // focus to next result of first athlete
                                    }
                                }   
                            }
                            else {
                                if($c == $cfgProgramMode[$prog_mode]['tech_res']) {    // last result of this line
                                    if(mysql_num_rows($result) == $i) { // no more athletes 
                                        $focus = "perf_" . $r;        // keep focus on last athlete  
                                    }
                                    else {
                                        $focus = "perf_" . ($r+2);        // focus to next line result  
                                    }
                                }
                                else {
                                    $focus = "perf_" . ($r+1);        // focus to next result
                                }  
                            } 
                        }
                        else {    // field mode                          
                           
                                $focus = "perf_" . ($r + $cfgProgramMode[$prog_mode]['tech_res'] + 1);
                                if(mysql_num_rows($result) == $i) { // no more athletes
                                    if($c == $cfgProgramMode[$prog_mode]['tech_res']) {    // last result
                                        $focus = "perf_" . $r;    // keep focus on this athlete
                                    }
                                    else {
                                        $focus = "perf_" . ($c+1);    // focus to next result of first athlete
                                    }
                                }
                            
                        }    // ET program mode
                       
                        $item = '';
                        $perf = '';
                        $info = '';
                        if($resrow = mysql_fetch_row($res)) {
                            $item = $resrow[0];
                            $perf = AA_formatResultMeter($resrow[1]);
                            $info = $resrow[2];
                        }

                        if($status != $cfgRoundStatus['results_done']) {  
?>
        <form action='controller.php' method='post'
            name='perf_<?php echo $r; ?>' target='controller'>
        <td nowrap>
            <input type='hidden' name='act' value='saveResult' />
            <input type='hidden' name='obj' value='perf_<?php echo $r; ?>' />
            <input type='hidden' name='type' value='<?php echo $layout; ?>' />
            <input type='hidden' name='round' value='<?php echo $round; ?>' />
            <input type='hidden' name='start' value='<?php echo $row[6]; ?>' />
            <input type='hidden' name='item' value='<?php echo $item; ?>' />
<?php
                            // technical disciplines with wind
                            if($layout == $cfgDisciplineType[$strDiscTypeJump])
                            {
?>
            <input class='perfmeter' type='text' name='perf' maxlength='6'
                value='<?php echo $perf; ?>'
                onChange='checkSubmit(document.perf_<?php echo $r; ?>, <?php echo $focus; ?>)' />
            <input class='nbr' type='text' name='wind' maxlength='5'
                value='<?php echo $info; ?>'
                onChange='submitResult(document.perf_<?php echo $r; ?>, <?php echo $focus; ?>)' />
<?php
                            }
                            // technical disciplines without wind
                            else
                            {
?>
            <input class='perfmeter' type='text' name='perf' maxlength='6'
                value='<?php echo $perf; ?>'
                onChange='submitResult(document.perf_<?php echo $r; ?>, <?php echo $focus; ?>)' />
            
<?php
                            }
?>
        </td>
        </form>    
<?php
       if ($c==$cfgProgramMode[$prog_mode]['tech_res']){
            $r++;
            if(mysql_num_rows($result) != $i) { //  more athletes  
                  $focus="perf_".($c+$r+1); 
            }
            else {
                  $focus="perf_".$r;   
            }   
     
?>                            
       <form action='controller.php' method='post'  
            name='perf_<?php echo $r; ?>' target='controller'>   
        <td>
        <input type='hidden' name='act' value='saveResult' />
            <input type='hidden' name='obj' value='perf_<?php echo $r; ?>' />
            <input type='hidden' name='type' value='<?php echo $layout; ?>' />
            <input type='hidden' name='round' value='<?php echo $round; ?>' />
            <input type='hidden' name='start' value='<?php echo $row[6]; ?>' />
            <input type='hidden' name='item' value='<?php echo $item; ?>' />
            <input type='hidden' name='xAthlete' value='<?php echo $row[20]; ?>' />  
        <input class='textshort' type='text' name='remark' maxlength='5'
                value='<?php echo $row[19]; ?>'
                onChange='submitResult(document.perf_<?php echo $r; ?>, <?php echo $focus; ?>)' />
                </td>
        </form>    
<?php
        }
        
        
?>
        
        
<?php
                        }
                        else {    // results done
?>
        <td nowrap>
<?php
                            // technical disciplines with wind
                            if($layout == $cfgDisciplineType[$strDiscTypeJump]) {
                                echo "$perf ( $info )";
                            }
                            else {
                                echo "$perf";
                            }
                           
?>
        </td>
<?php
                         if ($c==$cfgProgramMode[$prog_mode]['tech_res']){
?>  
                                <td nowrap><?php     echo $row[19]; ?></td>
<?php                                
                            }
                            
                        }    // ET results done
                    }    // end loop every tech result acc. programm mode
                   
                   
?>  
          
        <td>
<?php
                    $btn->set("event_results.php?arg=del_start&item=$row[6]&round=$round", $strDelete);
                    $btn->printButton();
?>
        </td>
<?php
                }    // ET DB error
            }
?>
</table>
<?php
            mysql_free_result($result);
        }        // ET DB error
    }
}        // ET round selected
 
if(!empty($presets['focus'])) {
?>
<script type="text/javascript">
<!--
    if(document.<?php echo $presets['focus']; ?>) {
        document.<?php echo $presets['focus']; ?>.rank.focus();
        document.<?php echo $presets['focus']; ?>.rank.select();
        window.scrollBy(0,200);
    }
//-->
</script>
<?php
}
?>

</body>
</html>

<?php
}
else
{
        AA_printErrorMsg($strErrMergedRound); 
    } 

}    // End Function AA_results_Tech

}    // AA_RESULTS_TECH_LIB_INCLUDED
?>
