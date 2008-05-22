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
$addiction = new CAddiction();
$addiction->loadAides($user->user_id);

$antecedent = new CAntecedent();
$antecedent->loadAides($user->user_id);

$traitement = new CTraitement();
$traitement->loadAides($user->user_id);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("addiction" , $addiction);
$smarty->assign("antecedent", $antecedent);
$smarty->assign("traitement", $traitement);

$smarty->display("vw_ant_easymode.tpl");
?>