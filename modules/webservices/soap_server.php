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

CAppUI::requireModuleClass("eai", "CEAISoapHandler");

$wsdl = CValue::get('wsdl');

// premi�re �tape : d�sactiver le cache lors de la phase de test
ini_set("soap.wsdl_cache_enabled", "0");

$username = CValue::get('username');
$password = CValue::get('password');
  
// G�n�ration du fichier WSDL
if (isset($wsdl)) {
  header('Content-Type: application/xml; charset=UTF-8');
  
  $functions = CEAISoapHandler::$paramSpecs;
  
  $wsdlFile = new CWsdlDocument();
  $wsdlFile->addTypes();
  $wsdlFile->addMessage($functions);
  $wsdlFile->addPortType($functions);
  $wsdlFile->addBinding($functions);
  $wsdlFile->addService($username, $password, $m, $a);
  
  echo $wsdlFile->saveXML();
} else {
  // on indique au serveur � quel fichier de description il est li�
  try {
    $serverSOAP = new SoapServer(CApp::getBaseUrl()."/index.php?login=1&username=$username&password=$password&m=$m&a=$a&wsdl");
  } catch (Exception $e) {
    echo $e;
  }
    
  $serverSOAP->setClass("CEAISoapHandler"); 
  
  // lancer le serveur
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $serverSOAP->handle();
  } else {
     echo '<strong>This SOAP server can handle following functions : </strong>';    
     echo '<ul>';
     foreach($serverSOAP->getFunctions() as $_function)        
          echo '<li>' , $_function , '</li>';
     echo '</ul>';
  }
}
?>