<?php /* $Id:  $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Fabien M�nager
*/


global $can;
// @todo � transf�rer dans  dPpatient
// En l'�tat on ne peut pas v�rifier les droits sur dPcabinet
//$can->needsRead();

$user_id = mbGetValueFromGetOrSession("user_id");

// On charge le praticien
$user = new CMediusers;
$user->load($user_id);
$user->loadRefs();
$canUser = $user->canDo();

// V�rification des droits sur les praticiens
//$canUser->needsEdit(array("chirSel"=>0));

// Chargement des aides � la saisie
$antecedent = new CAntecedent();
$aides_antecedent = array();

// Pr�paration du chargement des aides
$where = array();
$where[] = "user_id = '$user_id' OR function_id = '$user->function_id'"; // We do not need it to be classified
$where["class"] = "= '$antecedent->_class_name'";
$order = "name";

$aide = new CAideSaisie();

// Initialisation des aides
foreach ($antecedent->_helped_fields as $field => $props) {
 $prop = $props["depend_value_1"];
  if ($prop) {
    // Chargement des Aides de l'utilisateur
    foreach ($antecedent->_specs[$prop]->_list as $type) {
    	$where["depend_value_1"] = "= '$type'";
      $aides = $aide->loadList($where, $order);
		  $_aides_antecedent[$type] = $aides;
    }
    
    $where["depend_value_1"] = 'IS NULL';
    $aides = $aide->loadList($where, $order);
    
    if (count($aides)) {
      $_aides_antecedent[] = $aides;
    }
  }
}

// Initialisation du tableau d'antecedents
foreach($antecedent->_specs["type"]->_list as $_type){
  $aides_antecedent[$_type] = array();
}

foreach($_aides_antecedent as $type => $_aides){
  foreach($_aides as $_aide_antecedent){
    $aides_antecedent[$type][$_aide_antecedent->depend_value_2][] = $_aide_antecedent;
  }
}

ksort($aides_antecedent);

$traitement = new CTraitement();
$traitement->loadAides($user->user_id);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("aides_antecedent", $aides_antecedent);
$smarty->assign("traitement", $traitement);

$smarty->display("vw_ant_easymode.tpl");
?>