<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$keywords = CValue::post("_view");
$all_departements = CValue::post("all_departements", 0);

if ($keywords == "") {
  $keywords = "%%";
}

$medecin = new CMedecin();
$g = CValue::getOrSessionAbs("g", CAppUI::$instance->user_group);
$indexGroup = new CGroups;
$indexGroup->load($g);
$order = 'nom';

$where = array();
$medecin_cps_prefs = CAppUI::pref("medecin_cps_pref");

if ($medecin_cps_prefs != "") {
  $cps = preg_split("/\s*[\s\|,]\s*/", $medecin_cps_prefs);
  CMbArray::removeValue("", $cps);

  if (count($cps)) {
    $where_cp = array();
    foreach ($cps as $cp) {
      $where_cp[] = "cp LIKE '".$cp."%'";
    }
    $where[] = "(".implode(" OR ", $where_cp).")";
  }
}
else if ($indexGroup->_cp_court && !$all_departements) {
  $where['cp'] = "LIKE '".$indexGroup->_cp_court."%'";
}

$matches = $medecin->seek($keywords, $where, 50, null, null, $order);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("keywords", $keywords);
$smarty->assign("matches", $matches);
$smarty->assign("nodebug", true);

$smarty->display("httpreq_do_medecins_autocomplete.tpl");

