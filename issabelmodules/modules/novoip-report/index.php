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
    $dsnAsteriskCDR = generarDSNSistema("asteriskuser","asterisk");
    $pDB = new paloDB($dsnAsteriskCDR);  
    $clr=new CallRequest();
    if($_POST['addCall']){
        $clr->addCall($pDB);
    }
    
    switch($action){
        default: // view_form
            $content = $clr->viewCallRequest($smarty, $module_name, $local_templates_dir, $arrConf,$pDB);
            break;
    }
    
    return $content;
}
class CallRequest
{
    private $errMsg = NULL;

    function addCall($pDB){
        $result = $pDB->genExec("
        INSERT INTO `asterisk`.`novoip_callrequests` ( `name`,`prefix`, `repeat`, `event`, `status`, `trunk`) VALUES (' $_POST[name]','$_POST[prefix]', '$_POST[repeat]', '2024-02-13 00:00:00', '$_POST[status]', '$_POST[trunk]');
        ");
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
    function viewCallRequest($smarty, $module_name, $local_templates_dir, $arrConf,$pDB)
    {
        $dsnAsteriskCDR = generarDSNSistema("asteriskuser","asterisk");
        $pDB = new paloDB($dsnAsteriskCDR);    
            
        $sql = "SELECT * FROM `trunks`";
        $recordset = $pDB->fetchTable($sql, TRUE,[]);

        

        $tunks=Array();
        foreach ($recordset as $tupla) {
            
            array_push($tunks,["id"=>$tupla['trunkid'],"name"=>$tupla['name']]);
        }
        $smarty->assign("trunks", $tunks);
        

        
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
        $this->asteriskCallto($astman);

        $oForm    = new paloForm($smarty,array());
        $content  = $oForm->fetchForm("$local_templates_dir/form.tpl",_tr("Softphones"), array());

        $oGrid = new paloSantoGrid($smarty);
        $arrVoiceData = array();
        
        $sql = "SELECT * FROM `novoip_callrequests`";
        $recordset = $pDB->fetchTable($sql, TRUE,[]);
        foreach ($recordset as $item) {

            $date_insertDate =$this->gregorian_to_jalali($item['insertDate']);
            $date_event =$this->gregorian_to_jalali($item['event']);

            $arrVoiceData[] = array($item['id'],$item['name'],$item['repeat'],$item['perfix'],$date_insertDate,$date_event,$item['status'],$item['trunk']);
        }
        $oGrid->setData($arrVoiceData);
        $oGrid->setLimit(2);
        $oGrid->setTotal(6);
        $url = array('menu' => $module_name);
        $oGrid->setURL($url);

        
        $oGrid->setColumns(array('ّid','نام','تکرار','پیشوند','تاریخ ثبت','اجرا','وضعیت','ترانک'));
        $contenidoModulo = $oGrid->fetchGrid();

        return $content.$contenidoModulo;
    }
}
?>
