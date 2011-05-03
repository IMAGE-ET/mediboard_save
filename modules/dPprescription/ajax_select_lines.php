<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_id = CValue::get("prescription_id");
$protocole_id    = CValue::get("protocole_id");
$pratSel_id      = CValue::get("pratSel_id");
$praticien_id    = CValue::get("praticien_id");

$prescription = new CPrescription;
$prescription->load($prescription_id);

$prescription->loadRefsLinesMedComments("1", "", $protocole_id);
$prescription->loadRefsLinesElementsComments("1", "", "", $protocole_id);
$prescription->loadRefsPrescriptionLineMixes("", 1, 1, $protocole_id);
$prescription->countLinesMedsElements(null, null, $protocole_id);

// On instancie le protocole utilis� pour r�cup�rer
// le champ checked_lines (lignes coch�es dans la modale)
$protocole = new CPrescription;
$protocole->load($protocole_id);

// Suppression des lignes sign�es
foreach ($prescription->_ref_lines_med_comments["med"] as $key=>$_line) {
  if ($_line->signee) {
    unset($prescription->_ref_lines_med_comments["med"][$key]);
    $prescription->_counts_by_chapitre["med"] --;
  }
}

foreach ($prescription->_ref_lines_med_comments["comment"] as $key=>$_line) {
  if ($_line->signee) {
    unset($prescription->_ref_lines_med_comments["comment"][$key]);
  }
}

if ($prescription->_ref_lines_elements_comments) {
  foreach ($prescription->_ref_lines_elements_comments as $chap_key=>&$chapitre) {
    foreach ($chapitre as &$cat) {
      foreach ($cat as &$_elements) {
        foreach ($_elements as $element_key=>$_line) {
          if ($_line->signee) {
            unset($_elements[$element_key]);
            $prescription->_counts_by_chapitre[$chap_key] --;
          }
        }
      }
    }
  }
}
foreach ($prescription->_ref_prescription_line_mixes as $key=>$_line_mix) {
  if ($_line_mix->signature_prat) {
    $prescription->_counts_by_chapitre["med"] -= $_line_mix->countBackRefs("lines_mix");
    unset($prescription->_ref_prescription_line_mixes[$key]);
  }
  else {
    $_line_mix->loadRefsLines();
  }
}

$smarty = new CSmartyDP;
$smarty->assign("prescription"   , $prescription);
$smarty->assign("pratSel_id"     , $pratSel_id);
$smarty->assign("praticien_id"   , $praticien_id);
$smarty->assign("prescription_id", $prescription_id);
$smarty->assign("checked_lines"  , $protocole->checked_lines);
$smarty->assign("now", mbDate());
$smarty->display("inc_select_lines.tpl");

?>