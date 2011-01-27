<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$name = CValue::get("name");
$action = CValue::get("action", "check");
$etat = "ko";
switch($name){
  case 'conf-service':
    $config = new CConfigService();
    break;    
  case 'conf-moment':
    $config = new CConfigMomentUnitaire();
    break;
}

if($action == "check"){
  if(null == $config->getSHM($name)){
    $etat = "vide";
  } else {
    if($config->getSHM($name) == $config->getAllConfigs()){
      $etat = "ok";
    }
  }
}

if($action == "create"){
  $config->setConfigInSHM();
  if($config->getSHM($name) == $config->getAllConfigs()){
    $etat = "ok";
  }  
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("etat", $etat);
$smarty->assign("name", $name);
$smarty->display("inc_check_conf.tpl");

?>