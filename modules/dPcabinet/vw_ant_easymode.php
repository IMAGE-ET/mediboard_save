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

// Chargement des aides � la saisie
$antecedent = new CAntecedent();
$antecedent->loadAides($user->_id);
$aides_antecedent = $antecedent->_aides_all_depends["rques"];

$traitement = new CTraitement();
$traitement->loadAides($user->_id);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("aides_antecedent", $aides_antecedent);
$smarty->assign("antecedent", $antecedent);
$smarty->assign("traitement", $traitement);

$smarty->display("vw_ant_easymode.tpl");
?>