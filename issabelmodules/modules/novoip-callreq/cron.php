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
require_once("/var/www/html/libs/misc.lib.php");
require_once("/var/www/html/configs/default.conf.php");
require_once("/var/www/html/libs/paloSantoSampler.class.php");
require_once("/var/www/html/libs/paloSantoDB.class.php");
// include_once "/var/www/html/libs/paloSantoGrid.class.php";
// include_once "/var/www/html/libs/paloSantoForm.class.php";
// include_once "/var/www/html/libs/paloSantoQueue.class.php";
// require_once "/var/www/html/libs/date.php";
require_once "/var/lib/asterisk/agi-bin/phpagi-asmanager.php";
require_once '/var/lib/asterisk/agi-bin/phpagi.php';


$dbfile="/var/www/db/settings.db";
printf("sss");
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
            if (!$astman->connect("127.0.0.1", "admin" , obtenerClaveAMIAdmin("/var/www/html/"))) {
                $this->errMsg = _tr('Error when connecting to Asterisk Manager');
                return NULL;
            }
            return $astman;
        }

    private function asteriskCallto($asm,$data,$pDB)
    {
        print_r($data);
        $call = $asm->send_request('Originate',
        array('channel'=> "SIP/mokhaberat/$data[number]",
        'exten'=> "7002",
        'CallerID'=> $data['callerID'],
        'context'=> 'from-internal',
        'priority'=> 1,
        'async'=> true,
        'Data'=> [
            'mycode'=> "09122389046",
        ],'variable'=> [
            'reqID'=>$data['id'],
            'number'=> $data['number'],
            'cid'=> $data['cid'],
            'repeat'=>$data['repeat'],
            'hook'=>$data['hook'],
            'ac'=>$data['ac'],
            'des'=>$data['des'],
            // 'destination'=>$data['destination'],
        ]));
        $result = $pDB->genExec("
        UPDATE `asteriskcdrdb`.`novoip_callrequests_phones` SET `repeat` = `repeat`+1 WHERE `novoip_callrequests_phones`.`id` = $data[id];
        ");
        $asm->disconnect();
    }

    function checkCall()
    {
        print_r("hi");
        $dsnAsteriskCDR = generarDSNSistema("asteriskuser","asteriskcdrdb","/var/www/html/");
        $pDB = new paloDB($dsnAsteriskCDR);  

        $sql = "SELECT * FROM `novoip_callrequests` WHERE `status` = '1' and `event`<now() ";
        $reqs = $pDB->fetchTable($sql, TRUE,[]);
        foreach ($reqs as $req) {
        
            $sql = "SELECT * FROM `novoip_callrequests_phones` WHERE `status` = 'wating' and CID=$req[id] and `repeat`<$req[repeat] ORDER BY `repeat` ASC limit $req[reqNum]";
            $recordset = $pDB->fetchTable($sql, TRUE,[]);

            
            foreach ($recordset as $tupla) {
                $astman = $this->_getami();
                if (is_null($astman)) {
                    print_r("novoip_data", "errror");
                }else{
                    print_r("novoip_data", "ok");
                }
                try{
                print("$tupla[number]\n");
                $des=json_decode($req['destination'],true)[0];
                
                $this->asteriskCallto($astman,array(
                    "id"=>$tupla['id'],"number"=>$tupla['number'],"cid"=>$tupla['CID'],"repeat"=>$req['soundRepeat'],"hook"=>$req['hook'],'des'=>$des["des"],"ac"=>$des["ac"],'callerID'=>$req['callerID'],
                ),$pDB);
                sleep(1);
                } catch (Exception $e) {
                    break;
                }
            }
        }

        
        // return $this->asteriskCallto($astman);

        
    }
}
$cl=new CallRequest();
$cl->checkCall();

?>
