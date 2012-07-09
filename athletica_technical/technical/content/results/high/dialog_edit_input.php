<?php
if(!defined('GLOBAL_PATH')) {
    define('GLOBAL_PATH', '../../../../');
}
if(!defined('ROOT_PATH')) {
    define('ROOT_PATH', '../../../');
}
if(!defined('CURRENT_CATEGORY')) {
    define('CURRENT_CATEGORY', 'athletica_tech');
}
if(!defined('CURRENT_PAGE')) {
    define('CURRENT_PAGE', 'results');
}

require_once(ROOT_PATH.'lib/inc.init.php');
require_once(ROOT_PATH.'lib/cls.result_high.php');

$events = getEvents(CFG_CURRENT_MEETING,CFG_CURRENT_EVENT);
$type = $glb_types_results[$events['disc_type']];

$result_id = $_GET['xResultat'];
$athlete_id = $_GET['athlete'];
$height = $_GET['height'];
$result = getResult($result_id);

$athlete = getAthleteDetails($athlete_id, false, 'ath_pos', 0, true);

?>
<script type="text/javascript">
    $(document).ready(function(){
        $('button').button();
        
         $('#result_edit_result').keyup(function(){
        });
        
        $('#result_edit_result').blur(function(){
            var res = $(this).val();
            
            if (res) {
                $.ajax({
                    url: '<?=$type?>/ajax_formatResult.php',
                    type: 'POST',
                    data: 'res='+res,
                    success: function(data) {
                        if(data=='error') {
                           alert('<?=$lg['ERROR_INPUT']?>');
                        } else {
                            $('#result_edit_result').val(data); 
                        }
                    }
                });  
            }
        });
        
        $('#btn_editResult').click(function(){
            show_dialog_wait('<?=javascript_prepare($lg['PLEASE_WAIT'])?>', '<?=javascript_prepare($lg['SAVING_RESULT'])?>');
            
            var result = $('#result_edit_result').val();
            
            if(result < 0) {
                var height = result;    
                var ath_res = '<?=$cfgResultsWindDefault?>';
            } else{
                var height = $('#height').val();
                var ath_res = result;
            }
            
            var ath_id = $('#xSerienstart').val();
            var res_id = $('#xResultat').val();
            var round= $('#round').val();
            var event = $('#event').val();

            data = {
                ath_res: ath_res,
                ath_id: ath_id,            
                res_id: res_id,  
                round: round,
                event: event,          
                height: height,          
            };
            
            $.ajax({
                url: '<?=$type?>/ajax_saveResult.php',
                type: 'POST',
                data: data,
                success: function(data) {
                    if(data=='result') {
                        $('#dialog_wait').dialog('close');
                        alert('<?=$lg['ERROR_INPUT']?>');
                    } else if(data=='db') {
                        $('#dialog_wait').dialog('close');
                        alert('<?=$lg['ERROR_DB']?>')
                    } else {
                        $('#dialog_edit_input').dialog('close');
                        var url = '<?=$type?>/results.php';
                        $('#div_results').load(url, function(response, status, req){
                            if(status=='success'){
                                $('#dialog_wait').dialog('close');
                            }
                        });
                    }
                }
            });
        });
    });
</script>
<input type="hidden" name="xResultat" id="xResultat" value="<?=$result_id?>">
<input type="hidden" name="xSerienstart" id="xSerienstart" value="<?=$athlete_id?>">
<input type="hidden" name="height" id="height" value="<?=$height?>">
<table>
    <colgroup>
        <col width="50">
        <col width="250">
        <col>
    </colgroup>
    <tr>
        <td><b><?=$athlete['ath_bib']?></b></td>
        <td><b><?=$athlete['ath_name']?> <?=$athlete['ath_firstname']?></b></td>
        <td><?=formatResultOutput($height).$lg['METER_SHORT']?></td>
    </tr>
    <tr>
        <td height="20"></td>
    </tr>
</table>
<table>
    <tr>
        <td class="result"><input type="text" name="result_edit_result" id="result_edit_result" class="result" autocomplete="off" tabindex="101" value="<?=$result['info']?>">
        <td><button type="button" name="btn_editResult" id="btn_editResult" tabindex="103"><?=$lg['OK']?></button></td>
    </tr>
</table>