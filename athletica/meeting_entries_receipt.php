<?php

/**********
 *
 *	meeting_entries_receipt.php
 *	-----------------------------
 *	
 */
 
require('./lib/cl_gui_dropdown.lib.php');
require('./lib/cl_gui_menulist.lib.php');
require('./lib/cl_gui_page.lib.php');

require('./lib/common.lib.php');

if(AA_connectToDB() == FALSE)	{				// invalid DB connection
	return;		// abort
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}

$page = new GUI_Page('meeting_entries_receipt');
$page->startPage();
$page->printPageTitle($strReceipt);

?>
<script type="text/javascript">
<!--
	function setPrint()
	{
		document.printdialog.formaction.value = 'print'
		document.printdialog.target = '_blank';
	}

	
//-->
</script>

<form action='print_meeting_receipt.php' method='get' name='printdialog'>
<input type='hidden' name='formaction' value=''>

<table class='dialog'>


<tr>
	<th class='dialog' colspan='3'><?php echo $strSelection; ?></th>
</tr>
<tr>
	<td colspan='3'>
		<table>
			
            <tr>
                <td class='dialog'><?php echo $strClub; ?></td>
                <?php
               $dd = new GUI_ClubDropDown(0);
                
                ?>
            </tr> 
         <tr>          
         <td class="dialog"><?php echo $strAthlete ?></td> 
         <td class="forms">   
         <form action='meeting_entry_add.php' method='post' name='athleteSearch'> 
         <input name='arg' type='hidden' value='change_athlete' />  
         <?php  
        
         $dropdown = new GUI_Select('athleteSearch', 1, "document.athleteSearch.submit()");    
                                                                                                    
         $sql_athlets = "SELECT    
                                at.Vorname, 
                                at.Name, 
                                a.xAnmeldung 
                         FROM 
                                anmeldung AS a
                                LEFT JOIN athlet AS at USING (xAthlet)
                         WHERE 
                                xMeeting = ".$_COOKIE['meeting_id'] . "
                         ORDER BY at.Name, at.Vorname";  
                                
                                                             
         $result_a=mysql_query($sql_athlets);
         if(mysql_num_rows($result_a) > 0)  {   
               while( $row_athlets=mysql_fetch_row($result_a)) {
                    $name_athlete=$row_athlets[1] . " " . $row_athlets[0];
                    $dropdown->addOption($name_athlete, $row_athlets[2]); 
                }
                $dropdown->selectOption($athleteSearch);
                $dropdown->addOptionNone();
                $dropdown->printList();  
         }
         else
              {$search_occurred=true;
              $search_match;   
              } 
        ?>
        </form>      
        </td> 
		</table>
	</td>
</tr>



</table>

<p />

<table>
<tr>
	<td>
		<button name='print' type='submit' onClick='setPrint()'>
			<?php echo $strPrint; ?>
		</button>
	</td>
	
</tr>
</table>

<br>



</form>

<?php
$page->endPage();
?>
