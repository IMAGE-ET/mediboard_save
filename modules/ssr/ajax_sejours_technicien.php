<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Plateaux disponibles
$technicien_id = CValue::get("technicien_id");
$date = CValue::getOrSession("date", mbDate());

$technicien = new CTechnicien();
$technicien->load($technicien_id);
$technicien->loadRefKine();
$kine_id = $technicien->_ref_kine->_id;

$sejours = CBilanSSR::loadSejoursSSRfor($technicien_id, $date);
foreach ($sejours as $_sejour) {
  $_sejour->checkDaysRelative($date);
  $_sejour->loadRefPatient();
}

// Remplacements
$replacement = new CReplacement;
$replacements = $replacement->loadListFor($kine_id, $date);

$sejours_remplaces = array();
foreach ($replacements as $_replacement) {
  // D�tail sur le cong�
  $_replacement->loadRefConge();
  $_replacement->_ref_conge->loadRefUser();
  $_replacement->_ref_conge->_ref_user->loadRefFunction();
	
  // D�tails des s�jours remplac�s
  $_replacement->loadRefSejour();
	$sejour =& $_replacement->_ref_sejour;
  $sejour->checkDaysRelative($date);
  $sejour->loadRefPatient();
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("sejours", $sejours);
$smarty->assign("replacements", $replacements);
$smarty->display("inc_sejours_technicien.tpl");
?>