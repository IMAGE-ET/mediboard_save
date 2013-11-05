<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// L'utilisateur est-il praticien ?
$chir = null;
$mediuser = CMediusers::get();
if ($mediuser->isPraticien()) {
  $chir = $mediuser->createUser();
}
// Praticien selectionn�
$chirSel = CValue::getOrSession("chirSel", $chir ? $chir->user_id : null);

// Type de vue
$show_payees   = CValue::getOrSession("show_payees"  , 1);
$show_annulees = CValue::getOrSession("show_annulees", 0);

// Plage de consultation selectionn�e
$plageconsult_id = CValue::getOrSession("plageconsult_id", null);
$plageSel = new CPlageconsult();
if (($plageconsult_id === null) && $chirSel && $is_in_period) {
  $nowTime = CMbDT::time();
  $where = array(
    "chir_id = '$chirSel' OR remplacant_id = '$chirSel' OR pour_compte_id = '$chirSel'",
    "date"    => "= '$today'",
    "debut"   => "<= '$nowTime'",
    "fin"     => ">= '$nowTime'"
  );
  $plageSel->loadObject($where);
}
if (!$plageSel->plageconsult_id) {
  $plageSel->load($plageconsult_id);
}
else {
  $plageconsult_id = $plageSel->plageconsult_id;
}
$plageSel->loadRefsFwd(1);
$plageSel->loadRefsNotes();
$plageSel->loadRefsBack($show_annulees, true, $show_payees);

if ($plageSel->_affected && count($plageSel->_ref_consultations)) {
  $firstconsult = reset($plageSel->_ref_consultations);
  $_firstconsult_time = substr($firstconsult->heure, 0, 5);
  $lastconsult = end($plageSel->_ref_consultations);
  $_lastconsult_time  = substr($lastconsult->heure, 0, 5);
}

$consults = $plageSel->_ref_consultations;
CMbObject::massLoadFwdRef($consults, "sejour_id");
CMbObject::massLoadFwdRef($consults, "patient_id");
CMbObject::massLoadFwdRef($consults, "categorie_id");

// D�tails sur les consultation affich�es
foreach ($plageSel->_ref_consultations as $keyConsult => &$consultation) {
  $consultation->loadRefSejour(1);
  $consultation->loadRefPatient(1);
  $consultation->loadRefCategorie(1);
  $consultation->countDocItems();
  //check 3333tel
  if (CModule::getActive("3333tel")) {
    C3333TelTools::checkConsults($consultation, $plageSel->_ref_chir->function_id);
  }
}

if ($plageSel->chir_id != $chirSel && $plageSel->remplacant_id != $chirSel &&  $plageSel->pour_compte_id != $chirSel) {
  $plageconsult_id = null;
  $plageSel = new CPlageconsult();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("plageSel"          , $plageSel);
$smarty->assign("chirSel"           , $chirSel);
$smarty->assign("show_payees"       , $show_payees);
$smarty->assign("show_annulees"     , $show_annulees);
$smarty->assign("mediuser"          , $mediuser);

$smarty->display("inc_consultations.tpl");