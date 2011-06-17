<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

// Type d'affichage
$aff_sortie = CValue::postOrSession("aff_sortie","tous");

// Parametre de tri
$order_way = CValue::getOrSession("order_way", "ASC");
$order_col = CValue::getOrSession("order_col", "_pec_transport");

// Chargement des urgences prises en charge
$ljoin = array();
$ljoin["rpu"] = "sejour.sejour_id = rpu.sejour_id";
$ljoin["consultation"] = "consultation.sejour_id = sejour.sejour_id";

// Selection de la date
$date = CValue::getOrSession("date", mbDate());
$date_tolerance = CAppUI::conf("dPurgences date_tolerance");
$date_before = mbDate("-$date_tolerance DAY", $date);
$date_after  = mbDate("+1 DAY", $date);
$where = array();
$group = CGroups::loadCurrent();
$where["group_id"] = " = '$group->_id'";
$where["sejour.annule"] = " = '0'";
$where[] = "sejour.entree BETWEEN '$date' AND '$date_after' 
  OR (sejour.sortie_reelle IS NULL AND sejour.entree BETWEEN '$date_before' AND '$date_after')";

// RPU Existants
$where["rpu.rpu_id"] = "IS NOT NULL";

if ($aff_sortie == "sortie"){
  $where["sortie_reelle"] = "IS NULL";
  $where["rpu.mutation_sejour_id"] = "IS NULL";
}

$order_col = "_pec_transport";
$order = "consultation.heure $order_way";

$sejour = new CSejour;
$listSejours = $sejour->loadList($where, $order, null, null, $ljoin);
foreach ($listSejours as &$_sejour) {
  $_sejour->loadRefsFwd();
  $_sejour->loadRefRPU();
  $_sejour->loadNumDossier();
  $_sejour->loadRefsConsultations();
  $_sejour->_veille = mbDate($_sejour->entree) != $date;
  
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

// Contraintes sur le mode de sortie / destination
$contrainteDestination["transfert"] = array("", 1, 2, 3, 4);
$contrainteDestination["normal"] = array("", 6, 7);

// Contraintes sur le mode de sortie / orientation
$contrainteOrientation["transfert"] = array("", "HDT", "HO", "SC", "SI", "REA", "UHCD", "MED", "CHIR", "OBST");
$contrainteOrientation["normal"] = array("", "FUGUE", "SCAM", "PSA", "REO");

// Praticiens urgentistes
$group = CGroups::loadCurrent();

$listPrats = CAppUI::$user->loadPraticiens(PERM_READ, $group->service_urgences_id);

// Si acc�s au module PMSI : peut modifier le diagnostic principal
$access_pmsi = 0;
if (CModule::exists("dPpmsi")) {
  $module = new CModule;
  $module->mod_name = "dPpmsi";
  $module->loadMatchingObject();
  $access_pmsi = $module->getPerm(PERM_EDIT);
}

// Si praticien : peut modifier le CCMU, GEMSA et diagnostic principal
$is_praticien = CAppUI::$user->isPraticien();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("contrainteDestination", $contrainteDestination);
$smarty->assign("contrainteOrientation", $contrainteOrientation);
$smarty->assign("services", $services);
$smarty->assign("order_col" , $order_col);
$smarty->assign("order_way" , $order_way);
$smarty->assign("listSejours", $listSejours);
$smarty->assign("aff_sortie", $aff_sortie);
$smarty->assign("listPrats", $listPrats);
$smarty->assign("date", $date);
$smarty->assign("access_pmsi", $access_pmsi);
$smarty->assign("is_praticien", $is_praticien);
$smarty->assign("today", mbDate());

$smarty->display("vw_sortie_rpu.tpl");
?>