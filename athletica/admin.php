<?php

/********************
 *
 *	admin.php
 *	---------
 *	
 *******************/

$noMeetingCheck = true;
 
require('./lib/cl_gui_menulist.lib.php');
require('./lib/cl_gui_page.lib.php');
include('./lib/cl_gui_select.lib.php');
include('./lib/cl_protect.lib.php');
require('./lib/cl_http_data.lib.php');

require('./lib/common.lib.php');

if(AA_connectToDB() == FALSE)	{		// invalid DB connection
	return;
}

if($_POST['arg'] == "set_password"){
	
	$p = new Protect();
	
	if(!empty($_POST['password'])){
		$p->secureMeeting($_COOKIE['meeting_id'], $_POST['password']);
	}
	
}

if($_POST['arg'] == "del_password"){
	
	$p = new Protect();
	
	$p->unsecureMeeting($_COOKIE['meeting_id']);
	
}

//
//	Display administration
//

$page = new GUI_Page('admin');
$page->startPage();
$page->printPageTitle($strAdministration);

$menu = new GUI_Menulist();
$menu->addButton($cfgURLDocumentation . 'help/administration/index.html', $strHelp, '_blank');
$menu->printMenu();
?>
<p/>

<script type="text/javascript">

function checkForm(){
	var obj = document.meetingpw;
	if(obj.password.value==''){
		alert('<?php echo $strProtectPasswordEmpty ?>');
		obj.password.focus();
		return false;
	}
}

function removePassword(){
	
	document.meetingpw.arg.value = "del_password";
	document.meetingpw.submit();
	
}

</script>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="475" style="vertical-align: top;">
			<table class='dialog' width="475">
				<tr>
					<th><?php echo $strConfiguration; ?></th>
				</tr>
				<tr>
					<td>
						<form action='index.php' name='lang' method='get' target='_top'>
							<input name='language' type='hidden' value='change'>
							<table class='admin'>
								<tr class='odd'>
									<td width="80"><?=$strLanguage?></td>
									<td class='forms'>
										<?php
										$dropdown = new GUI_Select('lang', 1, 'document.lang.submit()');

										foreach($cfgLanguage as $key=>$value)
										{
											$dropdown->addOption($key, $key);
											if($_COOKIE['language_trans'] == $cfgLanguage[$key]['file']) {
												$dropdown->selectOption($key);
											}
										}
										$dropdown->printList();
										?>
								</tr>
							</table>
						</form>
					</td>
				</tr>
			</table><br/>
			
			<?php
			if(isset($_SESSION['meeting_infos']) && count($_SESSION['meeting_infos'])>0 && ($_SESSION['meeting_infos']!='meetingNotChosen' &&  $_SESSION['meeting_infos']!='noMeeting')){
			                                               	
                ?>
				<table class='dialog' width="475">
					<tr>
						<th class='insecure'><?=$strProtectMeeting?></th>
					</tr>
					<tr>
						<td>
							<table class='admin'>
								<tr class="odd">
									<td width="70" rowspan="2" style="text-align: center;"><img src="img/password.gif" border="0" alt="<?=$strProtectMeeting?>" title="<?=$strProtectMeeting?>"/></td>
									<td><?php echo $strProtectMeetingInfo ?></td>
								</tr>
								<tr class='even'>
									<form name="meetingpw" method="post" action="admin.php" onsubmit="return checkForm();">
									<input type="hidden" name="arg" value="set_password">
									<td>
									<input type="password" name="password" size="30"/>&nbsp;&nbsp;
									<input type="submit" value="<?php echo $strSetPassword; ?>"/>
									<?php
									$p = new Protect();
									if($p->isRestricted($_COOKIE['meeting_id']))
									{
										?>
										<br/><br/><input type="button" value="<?php echo $strDeletePassword; ?>" onclick="removePassword()">
										<?php
									}
									?>
									</td>
									</form>
								</tr>
							</table>
						</td>
					</tr>
				</table><br/>
                <?php  
                          
                }
                
                if(isset($_SESSION['meeting_infos']) && count($_SESSION['meeting_infos'])>0 &&  $_SESSION['meeting_infos']!='noMeeting'){                                                                            
                                                                                            
                ?>
				<table class='dialog' width="475">			
					<tr>
						<th class='dialog'><?=$strDatabase?> - <?=$strBackup?></th>
					</tr>
					<tr>
						<td>
							<table class='admin'>
								<form action='admin_backup.php' method='get' name='db'
									enctype='multipart/form-data'>
									<input type='hidden' name='arg' value='backup'/>
								<tr>
									<td width="70" rowspan="2" style="text-align: center;">
										<img src="img/db_save.gif" border="0" alt="<?=$strRestore?>" title="<?=$strRestore?>"/>
									</td>
									<td><?=$strBackupInfo?></tr>
								</tr>
								<tr class="even">
									<td>
										<button name='backup' type='submit'><?=$strBackupOK?>
										</button>
									</td>
								</tr>
							</form>	
							</table>
						</td>
					</tr>
				</table><br/>
				<?php
                }  
			
			?>
			
			<table class='dialog' width="475">			
				<tr>
					<th class='dialog'><?=$strDatabase?> - <?=$strRestore?></th>
				</tr>
				<tr>
					<td>
						<table class='admin'>
							<form action='admin_backup.php' method='post' name='db2' enctype='multipart/form-data'>
							<tr class='odd'>
								<td width="70" rowspan="3" style="padding-left: 0px; text-align: center;"><img src="img/db_restore.gif" border="0" alt="<?=$strRestore?>" title="<?=$strRestore?>"/></td>
								<td><?=$strBackupRestoreInfo?></td>
							</tr>
							<tr class="even">
								<td>
									<input type='hidden' name='arg' value='restore'/>
										<?=$strBackupFile?>:&nbsp;
									</input>
									<input type="hidden" name="MAX_FILE_SIZE" value="6194304" />
									<input name='bkupfile' type='file' accept='text/sql' maxlength="6194304">
								</td>
							</tr>
							<tr class="even">
								<td>
									<button name='backup' type='submit'><?=$strRestore?>
									</button>
								</td>
							</tr>
						</form>	
						</table>
					</td>
				</tr>
			</table><br/>
			
			<table class='dialog' width="475">
				<tr>
					<th class='faq'><?php echo $strFaq; ?></th>
				</tr>
				<tr>
					<td>
						<table class='admin'>
							<tr class='even'>
								<td>
								<input type="button" value="<?php echo $strEdit; ?>" onclick="javascript:document.location.href='admin_faq.php'">
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table><br/>
			
			<table class='dialog' width="475">
				<tr>
					<th class='dialog'><?php echo $strLinks; ?></th>
				</tr>
				<tr>
					<td>
						<table class='admin'>
							<tr class='odd'>
								<td>&bull;
									<a href='<?php echo $cfgURLDocumentation; ?>index.html' target='_blank'>
									<?php echo $strDocumentation; ?> (HTML)</a>
								</td>
							</tr>
							<tr class='even'>
								<td>&bull;
									<a href='<?php echo $cfgURLDocumentation; ?>athletica.pdf' target='_blank'>
									<?php echo $strDocumentation; ?> (PDF)</a>
								</td>
							</tr>
							<tr class='odd'>
								<td>&bull;
									<a href='LICENSE.txt' target='_blank'><?php echo $strLicense; ?></a>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
		<td width="10"></td>
		<td style="vertical-align: top;">
			<table class='dialog' width="475">
				<tr>
					<th class='bestlistupdate'><?=$strEmptyCache?></th>
				</tr>
				<tr>
					<td>
						<table class='admin'>
							<colgroup>
								<col width="50%"/>
								<col width="50%"/>
							</colgroup>
							<tr class='odd'>
								<td colspan="2"><?=$strEmptyCacheInfo?></td>
							</tr>
							<tr class='even'>
								<td>
									<input type="button" value="<?=$strEmptyCache?>" class="uploadbutton" onclick="javascript:document.location.href='admin_base.php?arg=empty'">
								</td>
								<td style="text-align: right;">&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
			</table><br/><br/>
			
			<table class='dialog' width="475">
				<tr>
					<th class='baseupdate'><?=$strBaseUpdate?></th>
				</tr>
				<tr>
					<td>
						<table class='admin'>
							<colgroup>
								<col width="50%"/>
								<col width="50%"/>
							</colgroup>
							<tr class='odd'>
								<td colspan="2"><?=$strBaseRemark?></td>
							</tr>
							<tr class='even'>
								<td>
									<input type="button" value="<?=$strNext?>" class="baseupdatebutton" onclick="javascript:document.location.href='admin_base.php'">
								</td>
								<td style="text-align: right;">
									<input type="button" value="<?=$strReset?>" class="baseresetbutton" onclick="javascript:document.location.href='admin_base.php?arg=reset'">
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<?php
			if(isset($_SESSION['meeting_infos']) && count($_SESSION['meeting_infos'])>0 && ($_SESSION['meeting_infos']!='meetingNotChosen' &&  $_SESSION['meeting_infos']!='noMeeting')){
			           	
                ?>
				<br/><br/>
				<table class='dialog' width="475">
					<tr>
						<th class='sync'><?=$strMeetingSync?></th>
					</tr>
					<tr>
						<td>
							<table class='admin'>
								<tr class='odd'>
									<td><?=$strMeetingSyncRemark?></td>
								</tr>
								<tr class='even'>
									<td>
										<input type="button" value="<?=$strNext?>" class="syncbutton" onclick="javascript:document.location.href='admin_registration.php'">
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table><br/><br/>
				<table class='dialog' width="475">
					<tr>
						<th class='bestlistupdate'><?=$strBestlistUpdate?></th>
					</tr>
					<tr>
						<td>
							<table class='admin'>
								<tr class='odd'>
									<td><?=$strBestlistRemark?></td>
								</tr>
								<tr class='even'>
									<td><input type="button" value="<?=$strNext?>" class="uploadbutton" onclick="javascript:document.location.href='admin_results.php'"></td>
								</tr>
							</table>
						</td>
					</tr>
				</table><br/><br/>
				<?php
			}
			
			$newer = false;
			
			$http = new HTTP_data();
			$webserverDomain = "slv.exigo.ch"; // domain of swiss-athletics webserver
			
			$result = $http->send_post($webserverDomain, '/meetings/athletica/version.php', '', 'ini');
			$version = $result['version'];
			$datum = $result['datum'];
			
			$act_version = $cfgApplicationVersion;
			
			$version_short = substr($version, 0, 3);
			$act_version_short = substr($cfgApplicationVersion, 0, 3);
			$version_sub = (strlen($version)>=5) ? substr($version, 4) : 0;
			$act_version_sub = (strlen($act_version)>=5) ? substr($act_version, 4) : 0;
			
			if($version_short>$act_version_short){
				$newer = true;
			} elseif($version_short==$act_version_short){
				if($version_sub>$act_version_sub){
					$newer = true;
				}
			}
			
			if($newer){
				$athletica_de = 'http://www.swiss-athletics.ch/index.php?option=com_content&task=view&id=140&Itemid=358&lang=de';
				$athletica_fr = 'http://www.swiss-athletics.ch/index.php?option=com_content&task=view&id=140&Itemid=358&lang=fr';
				$link = ($lang=='fr') ? $athletica_fr : $athletica_de;
				?>			
				<table class='dialog' width="475" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<th width="26" class='updatebox'><img src="img/update.gif" width="22" height="22"/></th>
						<th class='updatebox'><?=$strOldVersion?></th>
					</tr>
					<tr>
						<td colspan="2">
							<table class='admin'>
								<tr class='odd'>
									<td>
										<?=$strNewVersionDownload?><br/><br/>
										
										<?=$strYourVersion?>: <?=$act_version?><br/>
										<b><?=$strNewestVersion?>: <?=$version?> (<?=$datum?>)</b><br/><br/>
									</td>
								</tr>
								<tr class='even'>
									<td><input type="button" value="<?=$strUpdateDownload?> &raquo;" class="uploadbutton" onclick="window.open('<?=$link?>');"></td>
								</tr>
							</table>
						</td>
					</tr>
				</table><br/>
				<?php
			} else {
				?>
				<table class='dialog' width="475" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<th width="26" class='updateboxok'><img src="img/update_ok.gif" width="22" height="22"/></th>
						<th class='updateboxok'><?=$strVersionOK?></th>
					</tr>
					<tr>
						<td colspan="2">
							<table class='admin'>
								<tr class='odd'>
									<td>
										<?=$strVersionOK?><br/><br/>
										
										<?=$strYourVersion?>: <?=$act_version?><br/>
										<b><?=$strNewestVersion?>: <?=$version?> (<?=$datum?>)</b><br/><br/>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table><br/>
				<?php
			}
			?>
		</td>
	</tr>
</table>

<?php
$page->endPage();
?>
