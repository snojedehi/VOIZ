#!/usr/bin/php
<?PHP

require_once("/var/www/html/libs/misc.lib.php");
require_once("/var/www/html/configs/default.conf.php");
require_once("/var/www/html/libs/paloSantoSampler.class.php");
require_once("/var/www/html/libs/paloSantoDB.class.php");
// AGI 7002
$url = "https://data.sazejoo.com/irest/saveCallRequest?key=agdahdbuadbn4456&m=";

function wh_log($log_msg)
{
    $log_filename = "/var/lib/asterisk/agi-bin/novoipagi/log";
    if (!file_exists($log_filename)) 
    {
        // create directory/folder uploads.
        mkdir($log_filename, 0777, true);
    }
    $log_file_data = $log_filename.'/log_' . date('d-M-Y') . '.log';
    // if you don't add `FILE_APPEND`, the file will be erased each time you add a log
    file_put_contents($log_file_data, $log_msg . "\n", FILE_APPEND);
} 
// call to function
wh_log("this is my log message");
function execute_agi($command) {
    fwrite(STDOUT, "$command\n");
    fflush(STDOUT);
    $result = fgets(STDIN);
    $ret = array('code'=> -1, 'result'=> -1, 'timeout'=> false, 'data'=> '');
    if (preg_match("/^([0-9]{1,3}) (.*)/", $result, $matches)) {
        $ret['code'] = $matches[1];
        $ret['result'] = 0;
        if (preg_match('/^result=([0-9a-zA-Z]*)(?:\s?\((.*?)\))?$/', $matches[2], $match))  {
            $ret['result'] = $match[1];
            $ret['timeout'] = ($match[2] === 'timeout') ? true : false;
            $ret['data'] = $match[2];
        }
    }
    return $ret;

}

function log_agi($entry, $level = 1) {
    if (!is_numeric($level)) {
        $level = 1;
    }
    $result = execute_agi("VERBOSE \"$entry\" $level");
}

function curl($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}





require('/var/lib/asterisk/agi-bin/phpagi.php');

$agi = new AGI();
$agi->answer();
$G_startime = time();


$cdrID = $agi->get_variable('CDR(uniqueid)');

if ($cdrID['result'] == 1) {
    $uniqueID = $cdrID['data'];
    wh_log("CDR ID: $uniqueID\n");
} else {
    wh_log("Unable to retrieve CDR ID\n");
}

$variableValue = $agi->get_variable('reqID');
$reqID=$variableValue['data'];

$variableValue = $agi->get_variable('cid');
$CID=$variableValue['data'];
wh_log("CID".$CID);

$dsnAsteriskCDR = generarDSNSistema("asteriskuser","asteriskcdrdb","/var/www/html/");
wh_log(json_encode($dsnAsteriskCDR));
$pDB = new paloDB($dsnAsteriskCDR);  
$result = $pDB->genExec("
UPDATE `asteriskcdrdb`.`novoip_callrequests_phones` SET `status` = 'down', uniqueID='$uniqueID',callDate=now() WHERE `novoip_callrequests_phones`.`id` = $reqID;
");
wh_log("
UPDATE `asteriskcdrdb`.`novoip_callrequests_phones` SET `status` = 'down', uniqueID='$uniqueID' WHERE `novoip_callrequests_phones`.`id` = $reqID;
");
#$agi->set_music(true);
$no=preg_replace("#[^0-9]#","",$agi->request[agi_callerid]);//remove any non numeric characters
wh_log('$var->'.$no);

$dg = $agi->stream_file("/var/lib/asterisk/agi-bin/novoipagi/sounds/$CID", 5);
// $dg = $agi->stream_file("/var/lib/asterisk/agi-bin/novoipagi/sounds/survey-thankyou", 2);
// $dg = $agi->stream_file("custom/sell", 2);
if ($dg['result']) {
    $agi->exec('Goto',"ext-queues,500,3");
    curl($url.$no);
}
wh_log('$dg:' . $dg['result']);
wh_log('$dg:' . json_encode($dg));
$answeredtime = time() - $G_startime;
wh_log('time:' . json_encode($answeredtime));
$callDuration = $agi->get_variable('CDR(duration)');

if ($callDuration['result'] == 1) {
    $duration = $callDuration['data'];
    wh_log("Call duration: $duration seconds");
} else {
    wh_log( "Unable to retrieve call duration");
}
exit();