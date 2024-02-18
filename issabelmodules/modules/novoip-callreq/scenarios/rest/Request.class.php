<?php
/*
  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4:
  Codificación: UTF-8
  +----------------------------------------------------------------------+
  | Issabel version 0.5                                                  |
  | http://www.issabel.org                                               |
  +----------------------------------------------------------------------+
  | Copyright (c) 2006 Palosanto Solutions S. A.                         |
  | Copyright (c) 1997-2003 Palosanto Solutions S. A.                    |
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
  | Autores: Alberto Santos Flores <asantos@palosanto.com>               |
  +----------------------------------------------------------------------+
  $Id: ContactList.class.php,v 1.1 2012/02/07 23:49:36 Alberto Santos Exp $
*/

$documentRoot = $_SERVER["DOCUMENT_ROOT"];
require_once "$documentRoot/libs/REST_Resource.class.php";
require_once "$documentRoot/libs/paloSantoJSON.class.php";
require_once "$documentRoot/modules/address_book/libs/core.class.php";
/*
 * Para esta implementación de REST, se tienen los siguientes URI
 * 
 *  /ContactList            application/json
 *      GET     lista un par de URIs para contactos internos y externos
 *  /ContactList/internal[?limit=X&offset=Y]   application/json
 *      GET     lista un reporte de todos los contactos internos, o de los 
 *              indicados por los parámetros limit y offset.
 *  /ContactList/internal/XXXX application/json
 *      GET     reporta la información del contacto interno cuyo número de 
 *              teléfono es XXXX
 *  /ContactList/external[?limit=X&offset=Y]   application/json
 *      GET     lista un reporte de todos los contactos externos, o de los 
 *              indicados por los parámetros limit y offset.
 *      POST    recibe una representación estándar application/x-www-form-urlencoded
 *              que contiene [phone first_name last_name email address company 
 *              notes status cell_phone home_phone fax1 fax2 province city 
 *              company_contact contact_rol], y crea un nuevo
 *              contacto para el usuario.
 *  /ContactList/internal/XXXX application/json
 *      GET     reporta la información del contacto externo cuyo ID de base de 
 *              datos es XXXX.
 *      PUT     actualiza la información del contacto externo XXXX con [phone 
 *              first_name last_name email address company notes status cell_phone
 *              home_phone fax1 fax2 province city company_contact contact_rol]
 *      DELETE  borra el contacto externo
 */

class Request
{
    private $resourcePath;
    function __construct($resourcePath)
    {
	$this->resourcePath = $resourcePath;
    }

    function URIObject()
    {
	$uriObject = NULL;
	if (count($this->resourcePath) <= 0) {
		$uriObject = new AddRequest();
	} elseif (in_array($this->resourcePath[0], array('add', 'update',"get"))) {
	    switch (array_shift($this->resourcePath)) {
	    case 'add':
            $uriObject = new AddRequest();
		break;
	    case 'update':
            $uriObject = new UpdateRequest();
		break;
        case 'get':
            $uriObject = new GEtRequest();
		break;
	    }
	}
	if(count($this->resourcePath) > 0)
	    return NULL;
	else
	    return $uriObject;
    }
}

class AddRequest extends REST_Resource
{
	function HTTP_GET()
    {
    	$json = new Services_JSON();
        
        return $json->encode(array(
            'name'  =>  "as",
            'hi'  =>  'ok',));
    }
    function HTTP_POST()
    {
    	$json = new Services_JSON();
        return $json->encode(array(
            'shayan'  =>  'post',
            'hi'  =>  'ok2',));
    }
}
class GEtRequest extends REST_Resource
{
	function HTTP_GET()
    {
    	$json = new Services_JSON();
        $dsnAsteriskCDR = generarDSNSistema("asteriskuser","asteriskcdrdb");
        $pDB = new paloDB($dsnAsteriskCDR);                                   
        $query= "SELECT * FROM `novoip_callrequests` WHERE `id` = $_GET[eid]";
        $result=$pDB->getFirstRowQuery($query, true,array());
        
        return $json->encode(array(
            'id'  =>  $result["id"],
            'name'  =>  $result["name"],
            'prefix'  =>  $result["prefix"],
            'repeat'  =>  $result["repeat"],
            'event'  =>  $result["event"],
            'status'  =>  $result["status"],
            'trunk'  =>  $result["trunk"],
            'hook'  =>  $result["hook"],
            'destination'=>$result['destination']
            ));
    }
    function HTTP_POST()
    {
    	$json = new Services_JSON();
        return $json->encode(array(
            'shayan'  =>  'UpdateRequest',
            'hi'  =>  'ok2',));
    }
}
class UpdateRequest extends REST_Resource
{
	function HTTP_GET()
    {
    	$json = new Services_JSON();
        return $json->encode(array(
            'shayan'  =>  'UpdateRequest',
            'hi'  =>  'ok',));
    }
    function HTTP_POST()
    {
    	$json = new Services_JSON();
        return $json->encode(array(
            'shayan'  =>  'UpdateRequest',
            'hi'  =>  'ok2',));
    }
}
?>
