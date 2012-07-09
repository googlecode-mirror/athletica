<?php

/********************
 *
 *	index.php
 *	---------
 *	
 *******************/
                     

require('./lib/cl_gui_page.lib.php');
include('./lib/cl_gui_select.lib.php'); 

require('./lib/common.lib.php');
require('./lib/timetable.lib.php');   

require_once('./lib/timing.lib.php');  

require("./lib/cl_ftp_data.lib.php");  
require_once('./lib/cl_http_data.lib.php'); //include class         
      
 $xMeeting = '';
 $host = '';  
 $dir = ''; 
       
     
 // set DB path to the server  
 if (!empty($_GET['path'])){       
     $GLOBALS['cfgDBhost'] =  $_GET['path']; 
     $dbhost = $_GET['path'];
 }
 elseif (!empty($_POST['path'])){
         $GLOBALS['cfgDBhost'] =  $_POST['path']; 
          $dbhost = $_POST['path']; 
 }
 elseif(!empty($_POST['dbhost'])){
    $dbhost = $_POST['dbhost'];
    $GLOBALS['cfgDBhost'] =  $dbhost;
 }  
 
 if (!empty($_GET['arg'])){       
     $arg =  $_GET['arg'];    
 }
 
      
  // set directory to the server  
 if (!empty($_GET['dir'])){       
     $GLOBALS['cfgDir'] =  $_GET['dir']; 
     $dir = $_GET['dir'];
 }
 elseif (!empty($_POST['dir'])){
         $GLOBALS['cfgDir'] =  $_POST['dir']; 
         $dir = $_POST['dir']; 
 }
 elseif(!empty($_POST['dir'])){
    $dir = $_POST['dir'];
    $GLOBALS['cfgDir'] =  $dir;
 }   
 
  
 // set ftp data 
     // host 
 if (!empty($_GET['host'])){    
     $host = $_GET['host'];
 }
 elseif (!empty($_POST['host'])){      
         $host = $_POST['host']; 
 }
 
    // user
 if (!empty($_GET['user'])){    
     $user = $_GET['user'];
 }
 elseif (!empty($_POST['user'])){  
         $user = $_POST['user']; 
 }

   // pwd
 if (!empty($_GET['pwd'])){      
     $pwd = $_GET['pwd'];
 }
 elseif (!empty($_POST['pwd'])){              
         $pwd = $_POST['pwd']; 
 }
 
   // URL
 if (!empty($_GET['url'])){    
     $url = $_GET['url'];
 }
 elseif (!empty($_POST['url'])){          
         $url = $_POST['url']; 
 }
        
    
 if(!empty($_GET['xMeeting'])){
    $xMeeting = $_GET['xMeeting'];  
 }
            
 
 
 $dbconnect = false; 
if (!empty($GLOBALS['cfgDBhost'])) {
    
    if(AA_connectToDB() == FALSE)	{		// invalid DB connection   	
        $dbconnect = false;
    }
    else {
          $dbconnect = true;   
    }
}

$dbconnect_live = false;       
if(AA_connectToDB_live() == FALSE)    {        // invalid DB connection       
        $dbconnect_live = false;
}
else {
        $dbconnect_live = true;  
        
        
       // if (!empty($_POST['host']) && !empty($_POST['user'])  && !empty($_POST['pwd'])) {
       if (isset($_POST['host']) || isset($_GET['host']) ) { 
         $sql = "UPDATE  
                        athletica_liveResultate.config 
                    SET 
                        ftpHost = '". $host ."',
                        ftpUser = '". $user ."',    
                        ftpPwd = '". $pwd ."',  
                        url = '". $url . "'";     
                
                $result = mysql_query($sql);  
                if(mysql_errno() > 0) {
                    AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                   
                }  
        } 
        
         
        $result = mysql_query("
            SELECT
                *
            FROM
                athletica_liveResultate.config");
        if(mysql_errno() > 0) {
            AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
            
        }
        else {
            $row = mysql_fetch_row($result);
            $host = $row[1];
            $user = $row[2];    
            $pwd = $row[3];    
            $url = $row[4];               
            mysql_free_result($result);              
            
            $GLOBALS['cfgUrl'] =  $url; 
            
            if (empty($host) || empty($user) || empty($pwd) || empty($url)){
                 $error_msg = $strUrlMsg;
            }
            else {
                  $error_msg = '';
            }
             
        } 
}


 $noMeetingCheck = true;                          

//
//	display time
//

$now = getdate();
$zero = '';
if($now['minutes'] < 10) {
    $zero = '0';
}

$timestamp = $now['mday']
                . "." . $now['mon']
                . "." . $now['year']
                . ", " . $now['hours']
                . "." . $zero. $now['minutes'];

$page = new GUI_Page('Live Resultate Upload');
$page->startPage();
$page->printPageTitle("Live Resultate Upload  (". $timestamp. " Uhr)");

?>

<table width="300" border="0" cellpadding="0" cellspacing="0" >
	<tr>
		<td  style="vertical-align: top;">
			<table class='dialog' width="100%">             
				<tr>
					<th><?php echo $strTitleLiveRes; ?></th>
				</tr>
				<tr>
					<td>
						<form action='index.php' name='lang' method='post' target='_top' >
							
							<table class='admin'>
                            

                                <tr class='dialog'>
                                    <th colspan='2'><?php echo $strFtp; ?></th>
                                </tr>                                  
                                <br />
                                <tr>
                                    <td><br/><?php echo $strHost; ?></td>
                               
                                    <td><br/><input name="host" value='<?php echo $host; ?>' size="30" type="text" id="host" >                                  
                                     
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo $strUser; ?></td>
                               
                                    <td><input name="user" value='<?php echo $user; ?>' size="30" type="text" id="user" >                                  
                                     
                                    </td>
                                </tr>
                            
                                <tr>
                                    <td><?php echo $strPwd; ?></td>
                               
                                    <td><input name="pwd" value='<?php echo $pwd; ?>' size="30" type="password" id="pwd" >                                  
                                     
                                    </td>
                                </tr>
                                   <tr><td>&nbsp;</td></tr>  
                            
                                <tr>
                                    <td><?php echo $strUrl; ?></td>
                               
                                    <td><input name="url" value='<?php echo $url; ?>' size="30" type="text" id="url">                                  
                                     
                                    </td>
                                </tr>  
                                 <tr><td></td><td class="error"><?php echo $error_msg; ?></td></tr>     
                            
                                <tr><td>&nbsp;</td></tr>  
                            
                            
                                <tr class='dialog'>
                                    <th colspan='2'><?php echo $strAthleticaServer; ?></th>  
                                </tr>                                  
                               
                                <tr>
                                    <td><br/><?php echo $strServerIP; ?></td>
                               
                                    <td><br/><input name="path" value='<?php echo $GLOBALS['cfgDBhost'] ?>' size="30" type="text" id="path" >                                  
                                     
                                    </td>
                                </tr>
                                <tr><td>&nbsp;</td></tr>  
                                
                                <tr>
                                    <td><?php echo $strDir; ?></td>
                                </tr>                                  
                                <tr>
                                    <td>
                                      
                                 <input name="dir" value='<?php echo $GLOBALS['cfgDir'] ?>' size="30" type="text" id="dir" >                                 
                                     
                                    </td>
                                    <td class="forms">
                              <input type="button" value="Dateien auf Server löschen" id="clear" onclick="document.location.href = 'index.php?xMeeting='+document.getElementById('xMeetingselectbox').value+'&amp;path='+document.getElementById('path').value+'&amp;dir='+document.getElementById('dir').value+'&amp;host='+document.getElementById('host').value+'&amp;user='+document.getElementById('user').value+'&amp;pwd='+document.getElementById('pwd').value+'&amp;url='+document.getElementById('url').value+'&amp;url='+document.getElementById('url').value+'&amp;arg=clear'"/></td> 
                              
                                </tr> 
                                <tr><td> <br />  
                                <?php
                               //  if (!$dbconnect || ($dbconnect && empty($GLOBALS['cfgDir']))) { 
                                 if (!$dbconnect) { 
                                 ?>  
                                
                                <button type='submit'>
                                    <?php echo $strNext; ?>
                                    </button>   </td></tr>    
                                    
                                <?php
                                 }
                                //if ((!empty($_GET['path']) || !empty($_POST['path'])) && ($dbconnect && !empty($GLOBALS['cfgDir']))) {    
                                if ((!empty($_GET['path']) || !empty($_POST['path'])) && ($dbconnect)) {
                                    ?>
                                    
                                 <tr>
                                
                                </tr>                                      
                                    
                                    
                                    
                                <tr>
                                <td><?=$strMeetingTitle?> 
                                                <?php
                                                $dropdown = new GUI_Select('xMeeting', 1, '');
                                                $dropdown->addOptionsFromDB("select xMeeting, Name from athletica.meeting order by DatumVon, DatumBis");
                                                $dropdown->selectOption($xMeeting);
                                                $dropdown->printList();
                                                 ?>
                                </td>
                                </tr>                                  
                                <tr><td>&nbsp;</td></tr>
								<tr class='odd'> 	
                                  <input name='dbhost' type='hidden' value='<?php echo $GLOBALS['cfgDBhost'] ?>' /> 
                                  <input name='dir' type='hidden' value='<?php echo $GLOBALS['cfgDir'] ?>' />                                     								
									<td class="forms"><input type="button" value="Start" id="start" onclick="document.location.href = 'index.php?xMeeting='+document.getElementById('xMeetingselectbox').value+'&amp;path='+document.getElementById('path').value+'&amp;dir='+document.getElementById('dir').value+'&amp;host='+document.getElementById('host').value+'&amp;user='+document.getElementById('user').value+'&amp;pwd='+document.getElementById('pwd').value+'&amp;url='+document.getElementById('url').value+'&amp;url='+document.getElementById('url').value+'&amp;arg=start'"/></td> 
                                    <td class="forms"><input type="button" value="Stop" id="stop" onclick="timeout_stop()" /></td>
                                   
								</tr>
                               
                                 
                                
                                <?php
                                    }
                                ?>
							</table>
						</form>
					</td>
				</tr>
			</table><br/> 
		</td>
	</tr>
</table>
        
<?php

if(empty($xMeeting))
    {
        // delete cookies
        setcookie("meeting_id", "", time()-3600);
        setcookie("meeting", "", time()-3600);
        
        
        
    }
    // OK: try to add cookie
    elseif ($dbconnect)
    {  
        // get stadium name
        $result = mysql_query("
            SELECT
                Name
            FROM
                athletica.meeting
            WHERE xMeeting = " . $_GET['xMeeting']
        );
        if(mysql_errno() > 0) {
            AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
            $meeting_name = "";
        }
        else {
            $row = mysql_fetch_row($result);
            $meeting_name = $row[0];
            mysql_free_result($result);
        }

        // store cookies on browser
        setcookie("meeting_id", $_GET['xMeeting'], time()+$cfgCookieExpires);
        setcookie("meeting", $meeting_name, time()+$cfgCookieExpires);
        // update current cookies
        $_COOKIE['meeting_id'] = $_GET['xMeeting'];
        $_COOKIE['meeting'] = $meeting_name;   
       
        $noMeetingCheck = true; 
      
        if(AA_checkMeetingID() == FALSE) {        // no meeting selected
            return;        // abort
        }   
      
      
           
              
       

    if ($arg == 'clear'){
        
          $http = new HTTP_data(); 
          $post = '';
          $result = $http->send_post($host, 'http://' . $url . '/' .$GLOBALS['cfgDir']  .'/live_delete.php', $post, 'file', '');         
          if(!$result){
            AA_printErrorMsg($strErrFtpNoDel);
          }
         
       
    }
    else { 
        if ($arg == 'start' && !empty($host) && !empty($user) && !empty($pwd) && !empty($url)){ 
             // set meeting StatusChanged to yes
              // get stadium name
                $result = mysql_query("
                    UPDATE  
                        athletica.meeting 
                    SET 
                        StatusChanged = 'y'   
                    WHERE xMeeting = " . $_GET['xMeeting']
                );
                if(mysql_errno() > 0) {
                    AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                   
                } 
                
                require('config.inc.end.php');    
          }
        if (!empty($host) && !empty($user) && !empty($pwd) && !empty($url)){ 
            AA_timetable_display(); 
       }
       
    }
?>

<script type="text/javascript">
<!--
     
     activ = window.setTimeout("updatePage()", <?php echo $cfgMonitorReload * 1000; ?>);
                
    function updatePage()
    {   
        window.open("index.php?xMeeting=<?php echo $xMeeting; ?>&path=<?php echo  $GLOBALS['cfgDBhost']; ?>&dir=<?php echo  $GLOBALS['cfgDir']; ?>", "_self");
    }
    
    function timeout_stop()
    {  
        clearTimeout(activ);
       
    }
    
   
    
 //-->
</script>          
    
<?php     
        
    }    


$page->endPage();
