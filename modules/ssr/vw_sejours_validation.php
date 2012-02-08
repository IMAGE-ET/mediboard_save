<?php /* $Id: vw_idx_admission.php 12978 2011-08-29 10:11:28Z flaviencrochard $ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 12978 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Filtres d'affichage

$recuse     = CValue::getOrSession("recuse", "-1");
$order_way  = CValue::getOrSession("order_way", "ASC");
$order_col  = CValue::getOrSession("order_col", "patient_id");
$date       = CValue::getOrSession("date", mbDate());
$service_id = CValue::getOrSession("service_id");
$prat_id    = CValue::getOrSession("prat_id");

$date_actuelle = mbDateTime("00:00:00");
$date_demain   = mbDateTime("00:00:00","+ 1 day");
$hier          = mbDate("- 1 day", $date);
$demain        = mbDate("+ 1 day", $date);

// R�cup�ration de la liste des services
$where = array();
$where["externe"]  = "= '0'";
$service = new CService;
$services = $service->loadGroupList($where);

// R�cup�ration de la liste des praticiens
$prat = CMediusers::get();
$prats = $prat->loadPraticiens();

$sejour = new CSejour();
$sejour->_type_admission = "ssr";
$sejour->service_id      = $service_id;
$sejour->praticien_id    = $prat_id;

// Liste des s�jours en attente de validation
$g = CGroups::loadCurrent()->_id;
$where = array();
$where["groupe_id"] = "= '$g'";
$where["recuse"]    = "= '-1'";
$where["annule"]    = "= '0'";
$where["type"]      = "= 'ssr'";
$where["entree"]    = ">= '".mbDate()."'";
$nb_sejours_attente = $sejour->countList($where);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("sejour"            , $sejour);
$smarty->assign("date_demain"       , $date_demain);
$smarty->assign("date_actuelle"     , $date_actuelle);
$smarty->assign("date"              , $date);
$smarty->assign("recuse"            , $recuse);
$smarty->assign("order_way"         , $order_way);
$smarty->assign("order_col"         , $order_col);
$smarty->assign("services"          , $services);
$smarty->assign("prats"             , $prats);
$smarty->assign("hier"              , $hier);
$smarty->assign("demain"            , $demain);
$smarty->assign("nb_sejours_attente", $nb_sejours_attente);

$smarty->display("vw_sejours_validation.tpl");

?>