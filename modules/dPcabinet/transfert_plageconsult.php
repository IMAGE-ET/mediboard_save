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

CCanDo::checkAdmin();

// V�rification des droits sur les praticiens
$mediuser = new CMediusers();
if(CAppUI::pref("pratOnlyForConsult", 1)) {
  $praticiens = $mediuser->loadPraticiens(PERM_EDIT);
} else {
  $praticiens = $mediuser->loadProfessionnelDeSante(PERM_EDIT);
}

// Filtre
$filter = new CPlageconsult();

if ($filter->chir_id =  CValue::getOrSession("chir_id")) {
  $where["chir_id"] = "= '$filter->chir_id'";
}

if ($filter->_date_min = CValue::getOrSession("_date_min")) {
  $where[] = "date >= '$filter->_date_min'";
}

if ($filter->_date_max = CValue::getOrSession("_date_max")) {
  $where[] = "date <= '$filter->_date_max'";
}

// Chargement des plages
$plages = array();
if ($filter->chir_id) {
  $plages = $filter->loadList($where, "date");
  foreach($plages as $_plage) {
    $_plage->loadFillRate();
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("praticiens", $praticiens);
$smarty->assign("plages"    , $plages    );
$smarty->assign("filter"    , $filter    );

$smarty->display("transfert_plageconsult.tpl");
?>