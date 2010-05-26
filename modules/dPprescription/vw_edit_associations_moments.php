<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

if(!CModule::getActive('bcb')){
  CAppUI::stepMessage(UI_MSG_ERROR, "L'administration des moments est n�cessaire seulement si une base de donn�es de m�dicaments externe est install�e");
  return;
}

global $AppUI, $can, $m;

// Recuperation des valeurs
$code_moment_id = CValue::getOrSession("code_moment_id");

// Chargement du moment selectionn�
$moment = new CBcbMoment();
$moment->load($code_moment_id);

$moment_complexe = new CMomentComplexe();
$moment_complexe->code_moment_id = $moment->code_moment_id;
$moment_complexe->loadMatchingObject();

// Chargement des moments unitaires
$moments_unitaires = CMomentUnitaire::loadAllMoments();

// Chargement des associations du moment
$moment->loadRefsAssociations();

// Chargement des moments
$moments = CBcbMoment::loadAllMoments();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("association", new CAssociationMoment());
$smarty->assign("moments", $moments);
$smarty->assign("moments_unitaires", $moments_unitaires);
$smarty->assign("moment", $moment);
$smarty->assign("moment_complexe", $moment_complexe);
$smarty->display("vw_edit_associations_moments.tpl");

?>