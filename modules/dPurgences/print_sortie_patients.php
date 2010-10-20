<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

// Type d'affichage
$aff_sortie = CValue::get("aff_sortie","tous");

// Parametre de tri
$order_way = CValue::getOrSession("order_way", "ASC");
$order_col = CValue::getOrSession("order_col", "_pec_transport");

// Chargement des urgences prises en charge
$ljoin = array();
$ljoin["rpu"] = "sejour.sejour_id = rpu.sejour_id";
$ljoin["consultation"] = "consultation.sejour_id = sejour.sejour_id";

// Selection de la date
$date = CValue::get("date");
$date_tolerance = CAppUI::conf("dPurgences date_tolerance");
$date_before = mbDate("-$date_tolerance DAY", $date);
$date_after  = mbDate("+1 DAY", $date);
$where = array();
$group = CGroups::loadCurrent();
$where["group_id"] = " = '$group->_id'";
$where[] = "sejour.entree_reelle BETWEEN '$date' AND '$date_after' 
  OR (sejour.sortie_reelle IS NULL AND sejour.entree_reelle BETWEEN '$date_before' AND '$date_after')";

// RPU Existants
$where["rpu.rpu_id"] = "IS NOT NULL";

if ($aff_sortie == "sortie"){
  $where["sortie_reelle"] = "IS NULL";
}

$order = "consultation.heure $order_way";

$sejour = new CSejour;
$listSejours = $sejour->loadList($where, $order, null, null, $ljoin);
foreach ($listSejours as &$_sejour) {
  $_sejour->loadRefsFwd();
  $_sejour->loadRefRPU();
  $_sejour->loadNumDossier();
  $_sejour->loadRefsConsultations();
  $_sejour->_veille = mbDate($_sejour->entree_reelle) != $date;
  
  // D�tail du RPU
  $rpu =& $_sejour->_ref_rpu;
  $rpu->loadRefSejourMutation();
  $rpu->_ref_consult->loadRefsActes();
   
  // D�tail du patient
  $patient =& $_sejour->_ref_patient; 
  $patient->loadIPP();

}

// Chargement des services
$service = new CService();
$services = $service->loadList(null, "nom");

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("date"       , $date);
$smarty->assign("listSejours", $listSejours);
$smarty->assign("services"   , $services);
$smarty->assign("print"      , true);
$smarty->display("print_sortie_patients.tpl");
?>