<?php
  /* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4:
  Codificación: UTF-8
  +----------------------------------------------------------------------+
  | Issabel version 2.4.0-9                                               |
  | http://www.issabel.org                                               |
  +----------------------------------------------------------------------+
  | Copyright (c) 2006 Palosanto Solutions S. A.                         |
  +----------------------------------------------------------------------+
  | The contents of this file are subject to the General Public License  |
  | (GPL) Version 2 (the "License"); you may not use this file except in |
  | compliance with the License. You may obtain a copy of the License at |
  | http://www.opensource.org/licenses/gpl-license.php                   |
  |                                                                      |
  | Software distributed under the License is distributed on an "AS IS"  |
  | basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See  |
  | the License for the specific language governing rights and           |
  | limitations under the License.                                       |
  +----------------------------------------------------------------------+
  | The Initial Developer of the Original Code is PaloSanto Solutions    |
  +----------------------------------------------------------------------+
  $Id: index.php,v 1.1 2013-08-12 04:08:50 Jose Briones jbriones@elastix.com Exp $ */
//include issabel framework
include_once "libs/paloSantoGrid.class.php";
include_once "libs/paloSantoForm.class.php";
include_once "libs/paloSantoQueue.class.php";
require_once "libs/date.php";
require_once "/var/lib/asterisk/agi-bin/phpagi-asmanager.php";
require_once '/var/lib/asterisk/agi-bin/phpagi.php';

$dbfile="/var/www/db/settings.db";

function _moduleContent(&$smarty, $module_name)
{
    //include module files
    include_once "modules/$module_name/configs/default.conf.php";
    include_once "modules/$module_name/libs/paloSantoSoftphones.class.php";

    $base_dir=dirname($_SERVER['SCRIPT_FILENAME']);

    load_language_module($module_name);

    //global variables
    global $arrConf;
    global $arrConfModule;
    $arrConf = array_merge($arrConf,$arrConfModule);

    //folder path for custom templates
    $templates_dir=(isset($arrConf['templates_dir']))?$arrConf['templates_dir']:'themes';
    $local_templates_dir="$base_dir/modules/$module_name/".$templates_dir.'/'.$arrConf['theme'];

    $content = "";
    $dsnAsteriskCDR = generarDSNSistema("asteriskuser","asteriskcdrdb");
    $pDB = new paloDB($dsnAsteriskCDR);  
    $clr=new CallRequest();
    if($_POST['addCall']){
        $clr->addCall($pDB);
    }
    if($_POST['editReq']){
        $clr->editCall($pDB);
    }
    if($_GET['clr']){
        $action="clr";
    }
    if($_GET['del']){
        $clr->delCall($pDB);
    }
    switch($action){
        case 'clr':
            $content = $clr->viewNumbers($smarty, $module_name, $local_templates_dir, $arrConf,$pDB);
            break;
        default: // view_form
            $content = $clr->viewCallRequest($smarty, $module_name, $local_templates_dir, $arrConf,$pDB);
            break;
    }
    
    return $content;
}
class CallRequest
{
    private $errMsg = NULL;

    function delCall($pDB){

        
        $result = $pDB->genExec("
        delete from `asteriskcdrdb`.`novoip_callrequests` where `id`=$_GET[del];
        ");
        $result = $pDB->genExec("
                delete from `asteriskcdrdb`.`novoip_callrequests_phones` `CID`=$_GET[del];
                ");
        

    }

    function addCall($pDB){

        $con="";
        if($_POST["des"]){
            $con=Array();
            foreach($_POST["inp"] as $key=>$val ){
                array_push($con, Array(
                    "ac"=>$val,
                    "des"=>$_POST["des"][$key]
                ));
            }
            $con=json_encode($con);
        }

        $status=$_POST['status']?1:0;
        $result = $pDB->genExec("
        INSERT INTO `asteriskcdrdb`.`novoip_callrequests` ( `name`,`prefix`, `repeat`,`soundRepeat`, `event`, `status`, `trunk`,`hook`,`destination`,`callerID`,`reqNum`) VALUES (' $_POST[name]','$_POST[prefix]', '$_POST[repeat]','$_POST[soundRepeat]', '2024-02-13 00:00:00', '$status', '$_POST[trunk]', '$_POST[hook]','$con','$_POST[callerID]','$_POST[reqNum]');
        ");

        $inID = $pDB->getLastInsertId();
        if ($_FILES["sound"] && move_uploaded_file($_FILES["sound"]["tmp_name"], "/var/lib/asterisk/agi-bin/novoipagi/sounds/$inID.wav")) {

        }
        $numbers = explode("\n", $_POST['numbers']);
        foreach($numbers as $num){
            $num = preg_replace('/\s+/', '',$num);
            if($num){
                $result = $pDB->genExec("
                INSERT INTO `asteriskcdrdb`.`novoip_callrequests_phones` (`id`, `number`, `repeat`, `status`, `callDate`, `uniqueID`, `CID`) VALUES (NULL, '$num', '0', 'wating', '', '', '$inID')
                ");
            }
        }
        

    }
    function editCall($pDB){

      
        $status=$_POST['status']?1:0;
        $con="";
        if($_POST["des"]){
            $con=Array();
            foreach($_POST["inp"] as $key=>$val ){
                array_push($con, Array(
                    "ac"=>$val,
                    "des"=>$_POST["des"][$key]
                ));
            }
            $con=json_encode($con);
        }
        $result = $pDB->genExec("
        UPDATE `novoip_callrequests` SET `name`='$_POST[name]',`prefix`='$_POST[prefix]',`repeat`='$_POST[repeat]',`soundRepeat`='$_POST[soundRepeat]',`event`='2024-02-13 00:00:00',`status`='$status',`trunk`='$_POST[trunk]',`hook`='$_POST[hook]',`destination`='$con',`callerID`='$_POST[callerID]',`reqNum`='$_POST[reqNum]' WHERE id=$_POST[editReq]
        ");
       
        $inID = $_POST['editReq'];

        if ($_FILES["sound"] && move_uploaded_file($_FILES["sound"]["tmp_name"], "/var/lib/asterisk/agi-bin/novoipagi/sounds/$inID.wav")) {

        }
        
        $numbers = explode("\n", $_POST['numbers']);
        foreach($numbers as $num){
            $num = preg_replace('/\s+/', '',$num);
            if($num){
                $result = $pDB->genExec("
                INSERT INTO `asteriskcdrdb`.`novoip_callrequests_phones` (`id`, `number`, `repeat`, `status`, `callDate`, `uniqueID`, `CID`) VALUES (NULL, '$num', '0', 'wating', '', '', '$inID')
                ");
            }
        }

    }
    function gregorian_to_jalali($dt){

        $dh = new Application_Helper_date;
        $date = explode(" ", $dt);
        $date_parts = explode("-", $date[0]);
        $jalali_date = $dh->gregorian_to_jalali($date_parts[0], $date_parts[1], $date_parts[2]);
        $date_startm = $jalali_date[0] . "-" . $jalali_date[1] . "-" . $jalali_date[2]." ".$date[1];
        return $date_startm;
    }
    function _getami()
        {
            $astman = new AGI_AsteriskManager();
            $astman->log_level = 0;
            if (!$astman->connect("127.0.0.1", "admin" , obtenerClaveAMIAdmin())) {
                $this->errMsg = _tr('Error when connecting to Asterisk Manager');
                return NULL;
            }
            return $astman;
        }
    private function asteriskCallto($asm)
    {
        
        $call = $asm->send_request('Originate',
        array('channel'=> 'SIP/mokhaberat/09122389046',
        'exten'=> "7002",
        'CallerID'=> "74924444",
        'context'=> 'from-internal',
        'priority'=> 1,
        'async'=> true,
        'Data'=> [
            'mycode'=> "09122389046",
        ],'variable'=> [
            'mycode'=> "09122389046",
        ]));
        $asm->disconnect();
    }
    private function _getAsteriskQueueWaiting($astman)
    {
        $arrQue = array();

        $r = $astman->Command('queue show');
        if (!isset($r['Response']) || $r['Response'] == 'Error') {
            $this->errMsg = _tr('(internal) failed to run ami:queue show').print_r($r, TRUE);
            return NULL;
        }
        foreach (explode("\n", $r['data']) as $line) {
            $regs = NULL;
            if (preg_match('/^(\d+)\s*has (\d+)/', $line, $regs))
                $arrQue[$regs[1]] = (int)$regs[2];
        }
        return $arrQue;
    }
    private function getTrunks(){
        $dsnAsteriskCDR = generarDSNSistema("asteriskuser","asterisk");
        $pDB = new paloDB($dsnAsteriskCDR);    
            
        $sql = "SELECT * FROM `trunks`";
        $recordset = $pDB->fetchTable($sql, TRUE,[]);

        

        $tunks=Array();
        foreach ($recordset as $tupla) {
            
            array_push($tunks,["id"=>$tupla['trunkid'],"name"=>$tupla['name']]);
        }
        return $tunks;
    }
    function viewCallRequest($smarty, $module_name, $local_templates_dir, $arrConf,$pDB)
    {
        
        $smarty->assign("trunks", $this->getTrunks());
        

        
        $queue = new paloQueue($smarty);
        // $queues=$queue->getQueue(400);
        // $smarty->assign("queues", $queues);


        $astman = $this->_getami();
        if (is_null($astman)) {
            $smarty->assign("novoip_data", "errror");
        }else{
            $smarty->assign("novoip_data", "ok");
        }
        $queues = $this->_getAsteriskQueueWaiting($astman);
        if (!is_array($queues)) {
            $smarty->assign("novoip_data", $this->errMsg);
        }
        $smarty->assign("novoip_data", json_encode($queues));
        // $this->asteriskCallto($astman);

        $oForm    = new paloForm($smarty,array());
        $content  = $oForm->fetchForm("$local_templates_dir/form.tpl",_tr("Softphones"), array());

        $oGrid = new paloSantoGrid($smarty);
        $arrVoiceData = array();
        
        $sql = "SELECT * FROM `novoip_callrequests` ORDER BY id desc";
        $recordset = $pDB->fetchTable($sql, TRUE,[]);
        foreach ($recordset as $item) {

            $date_insertDate =$this->gregorian_to_jalali($item['insertDate']);
            $date_event =$this->gregorian_to_jalali($item['event']);


            $arrVoiceData[] = array("<a href='index.php?menu=novoip-callreq&clr=$item[id]'>$item[id]</a>",$item['name'],$item['callerID'],$item['repeat'],$item['soundRepeat'],$item['perfix'],$date_insertDate,$date_event,$item['status'],$item['trunk'],"<a onclick='editModal($item[id])'>edit</a>","<a onclick='deleteItem($item[id])'>delete</a>");
        }
        $oGrid->setData($arrVoiceData);
        $oGrid->setLimit(2);
        $oGrid->setTotal(6);
        $url = array('menu' => $module_name);
        $oGrid->setURL($url);

        
        $oGrid->setColumns(array('ّid','نام','کالر آیدی','تکرار','تکرار صدا','پیشوند','تاریخ ثبت','اجرا','وضعیت','ترانک','',''));
        $contenidoModulo = $oGrid->fetchGrid();

        return $content.$contenidoModulo;
    }

function viewNumbers($smarty, $module_name, $local_templates_dir, $arrConf,$pDB)
    {
        
       
        

        
        $queue = new paloQueue($smarty);
        // $queues=$queue->getQueue(400);
        // $smarty->assign("queues", $queues);
        

        $astman = $this->_getami();
        if (is_null($astman)) {
            $smarty->assign("novoip_data", "errror");
        }else{
            $smarty->assign("novoip_data", "ok2");
        }
      

        $oForm    = new paloForm($smarty,array());
        $content  = $oForm->fetchForm("$local_templates_dir/form.tpl",_tr("Softphones"), array());

        $oGrid = new paloSantoGrid($smarty);
        $arrVoiceData = array();
        
        $sql = "SELECT * FROM `novoip_callrequests_phones` where CID='$_GET[clr]'  ORDER BY id desc";
        $recordset = $pDB->fetchTable($sql, TRUE,[]);
        foreach ($recordset as $item) {
            $callData=Array(
                "duration"=>""
            );

            if($item['uniqueID']){
                $query= "SELECT * FROM `cdr` WHERE `uniqueid`='$item[uniqueID]'";
                $smarty->assign("novoip_data", "SELECT * FROM `cdr` WHERE `uniqueid`='$item[uniqueID]'");
                $result=$pDB->getFirstRowQuery($query, true,array());
                if(!$result && $result==null && count($result) < 1){
                    
                }else{
                    $callData['duration']=$result["duration"];
                }
            }

            $date_callDate =$this->gregorian_to_jalali($item['callDate']);
            

            $arrVoiceData[] = array($item['id'],$item['number'],$item['repeat'],$date_callDate,$item['status'],$callData['duration'],$item['result'],$item['uniqueID']);
        }
        $oGrid->setData($arrVoiceData);
        $oGrid->setLimit(2);
        $oGrid->setTotal(6);
        $url = array('menu' => $module_name);
        $oGrid->setURL($url);

        
        $oGrid->setColumns(array('ّid','شماره','تکرار تماس','تاریخ انجام','وضعیت','مدت زمان','اکشن','uniqueID'));
        $contenidoModulo = $oGrid->fetchGrid();

        return $content.$contenidoModulo;
    }
}
?>
