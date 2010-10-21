<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

$operation_id = CValue::getOrSession("operation_id");

$operation = new COperation();
$operation->load($operation_id);
$operation->loadRefSejour();
$operation->loadRefChir();

$sejour =& $operation->_ref_sejour;
$sejour->loadRefPatient();
$sejour->_ref_patient->loadRefPhotoIdentite();
$sejour->loadRefDossierMedical();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("operation", $operation);
$smarty->assign("sejour", $sejour);
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->assign("isImedsInstalled"       , (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));
$smarty->assign("date", mbDate());
$smarty->display("vw_soins_reveil.tpl");

?>