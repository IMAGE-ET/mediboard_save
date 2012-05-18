<?php

/**
 * SOAP Server EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

global $m, $a;

CCanDo::checkRead();

// premi�re �tape : d�sactiver le cache lors de la phase de test
ini_set("soap.wsdl_cache_enabled", "0");

$wsdl      = CValue::get('wsdl');
$username  = CValue::request('username');
$password  = CValue::request('password');
$classname = CValue::request('class', "CEAISoapHandler");

// G�n�ration du fichier WSDL
if (isset($wsdl)) {
  if (!$classname || !class_exists($classname, true)) {
    return;
  }
  
  $class = new $classname;

  header('Content-Type: application/xml; charset=UTF-8');
  
  $functions = $returns = array();
  if ($classname != "CSoapHandler") {
    $soap_handler= new CSoapHandler();
    $functions += $soap_handler->paramSpecs;
  }
  $functions += $class->paramSpecs;
  $returns   += $class->returnSpecs;
  
  $wsdlFile = new CWsdlDocument();
  $wsdlFile->addTypes();
  $wsdlFile->addMessage($functions, $returns);
  $wsdlFile->addPortType($functions);
  $wsdlFile->addBinding($functions);
  $wsdlFile->addService($username, $password, $m, $a, $classname);
  
  echo $wsdlFile->saveXML();
} else {
  if (!$classname || !class_exists($classname, true)) {
    throw new SoapFault("1", "Error : classname is not valid");  
  }
    
  // on indique au serveur � quel fichier de description il est li�
  try {
    $serverSOAP = new SoapServer(CApp::getBaseUrl()."/?login=$username:$password&m=$m&a=$a&class=$classname&wsdl");
  } catch (Exception $e) {
    echo $e->getMessage();
  }
    
  $serverSOAP->setClass($classname); 
  
  // Lance le serveur
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $serverSOAP->handle();
  } 
  else {
     echo '<strong>This Mediboard SOAP server can handle following functions : </strong>';    
     echo '<ul>';
     foreach ($serverSOAP->getFunctions() as $_function) {
       echo '<li>' , $_function , '</li>';
     }     
     echo '</ul>';
  }
}
?>