<?php

/********************
 *
 *	admin_registration.php
 *	---------
 *	get registrations from the online slv system
 *
 *******************/

require('./lib/cl_gui_menulist.lib.php');
require('./lib/cl_gui_page.lib.php');

require('./lib/common.lib.php');

require('./lib/cl_xml_data.lib.php');
require('./lib/cl_http_data.lib.php');

if(AA_connectToDB() == FALSE)	{		// invalid DB connection
	return;
}

if(AA_checkMeetingID() == FALSE){		// no meeting selected
	return;		// abort
}

//
//	Display enrolement list
//

$page = new GUI_Page('admin_registration');
$page->startPage();
/*$page->printPageTitle($strBaseUpdate);*/
$page->printPageTitle($strMeetingSync);

$menu = new GUI_Menulist();
$menu->addButton($cfgURLDocumentation . 'help/administration/base.html', $strHelp, '_blank');
$menu->printMenu();
?>
<p/>

<?php

$http = new HTTP_data();
$webserverDomain = "slv.exigo.ch"; // domain of swiss-athletics webserver

// handle arguments
$login = false;
$mcontrol = "";
$mname = "";
$mdate = "";
$slvsid = "";
$list = false;
$reg = false;

if($_POST['arg'] == "login"){
	
	// sending login information and global last change date of the base_data
	$result = mysql_query("SELECT global_last_change FROM base_log where type like 'base_%' ORDER BY id_log DESC LIMIT 1");
	if(mysql_errno > 0){
		AA_printErrorMsg(mysql_errno().": ".mysql_error());
	}else{
		// the following information is used to determinate wich base file we need to download
		if(mysql_num_rows($result) == 0){
			$glc = "";
			$type = "complete"; // for a complete base data download
		}else{
			$row = mysql_fetch_array($result);
			$glc = $row[0];
			$type = "update"; // if there is already a log entry, a complete download was made once
		}
		
		mysql_free_result($result);
		
		//$result = $http->send_get('slv.exigo.ch', '/downloads/verbandstagung_201104.ppt' , 'file', 'test.ppt', true);
		$post = "clubnr=".urlencode($_POST['clubnr'])."&pass=".urlencode($_POST['pass'])
			."&glc=".urlencode($glc)."&type=".urlencode($type);
		$result = $http->send_post($webserverDomain, '/meetings/athletica/login.php', $post, 'ini');
		if(!$result){
			AA_printErrorMsg($strErrLogin);
		}else{
			switch($result['login']){
				case "error":
				AA_printErrorMsg($result['error']);
				break;
				
				case "ok":
				$login = true;
				echo "<p>$strLoginTrue</p>";
				$slvsid = $result['sid']; // remember session id from slv server
				$basefiles = explode(":",$result['files']); // get files to download
				$filetype = $result['filetype']; // return complete or update, 
								//maybe the glc-date is too old, so the login script returned a complete set
				$newglc = substr($result["newglc"],0,4)."-".substr($result["newglc"],4,2)."-".substr($result["newglc"],6,2);
				
				$_POST['arg'] = 'list';
				$_POST['slvsid'] = $slvsid;
				
				break;
				
				case "denied":
				$login = false;
				echo "<p>$strLoginFalse</p>";
				break;
			}
		}
	}
}

if(!empty($_POST['slvsid'])){
	
	if($_POST['arg'] == "list"){
		// get meetinglist
		
		//$result = $http->send_get('slv.exigo.ch', '/downloads/verbandstagung_201104.ppt' , 'file', 'test.ppt', true);
		$post = "sid=".$_POST['slvsid'];
		$result = $http->send_post($webserverDomain, '/meetings/athletica/export_meeting_list.php', $post, 'ini');
		if(!$result){
			AA_printErrorMsg($strErrLogin);
		}else{
			switch($result['login']){
				
				case "ok":
				$login = true;
				
				$slvsid = $_POST['slvsid']; // remember session id from slv server
				$mcontrol = explode(":",$result['Control']);
				$mname = explode(":",$result['Name']);
				$mdate = explode(":",$result['Startdate']);
				
				$list = true;
				
				break;
				
				case "denied":
				$login = false;
				echo "<p>$strLoginFalse</p>";
				break;
			}
		}
		
	}elseif($_POST['arg'] == "reg"){
		// get xml for registrations
		
		$post = "sid=".$_POST['slvsid']."&meetingid=".$_POST['control'];
		$result = $http->send_post($webserverDomain, '/meetings/athletica/export_meeting.php', $post, 'file', 'reg.xml');
		if(!$result){
			AA_printErrorMsg($strErrLogin);
		}else{
			$login = true;
			$reg = true;
			$xml = new XML_data();
			$xml->load_xml($result, 'reg');
			
			// save eventnr
			mysql_query("update meeting set xControl = ".$_POST['control']." where xMeeting = ".$_COOKIE['meeting_id']);
			if(mysql_errno() > 0){
				AA_printErrorMsg(mysql_errno().": ".mysql_error());
			}
		}
	}
}

//
// show meeting list
//
if($list){
	
?>

<table class='dialog'>
<form method="post" action="admin_registration.php" target="_self">
<input type="hidden" value="reg" name="arg">
<input type="hidden" value="<?php echo $slvsid; ?>" name="slvsid">
<tr>
	<th><?php echo $strBaseMeeting; ?></th>
</tr>
<tr>
	<td>
	<select name="control" size="10">
	
	<?php
	$i = 0;
	foreach($mcontrol as $control){
		echo "<option value='$control'>".$mname[$i].", ".$mdate[$i]."</option>";
		$i++;
	}
	?>
	
	</select>
	</td>
</tr>
<tr>
	<td>
	<br/>
	<?=$strBaseMeetingAct?><br/>
	<b><?=$_SESSION['meeting_infos']['Name']?></b><br/><br/>
	
	<input type="submit" value="<?php echo $strNext ?>">
	</td>
</tr>
</form>
</table>

<?php
	
}

//
// show succes on reg xml
//
if($reg){
	
	echo "<p>$strBaseRegOk</p>";
	
}

if(!$login){
	
	// show login form

?>
<table class='dialog'>
<tr>
	<th><?php echo $strLoginSlv; ?></th>
</tr>
<tr>
	<td>
		<table class='admin'>
		<form action='admin_registration.php' name='base' method='post' target='_self'>
		<input type="hidden" name="arg" value="login">
		<tr>
			<td>
				<?php echo $strClubNr ?>
			</td>
			<td>
				<input type="text" name="clubnr" value="">
			</td>
		</tr>
		<tr>
			<td>
				<?php echo $strPassword ?>
			</td>
			<td>
				<input type="password" name="pass" value="">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" name="submit" value="<?php echo $strLogin ?>">
			</td>
		</tr>
		</form>	
		</table>
	</td>
</tr>
</table>

<?php
} // end if login

$page->endPage();
?>
