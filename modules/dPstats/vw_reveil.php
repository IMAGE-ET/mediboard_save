<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPstats
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$filter = new COperation();

global $debutact, $finact, $prat_id, $salle_id, $bloc_id;
global $discipline_id, $codes_ccam;

$debutact      = $filter->_date_min = CValue::getOrSession("_date_min", CMbDT::date("-1 YEAR"));
$rectif        = CMbDT::transform("+0 DAY", $filter->_date_min, "%d")-1;
$debutact      = $filter->_date_min = CMbDT::date("-$rectif DAYS", $filter->_date_min);

$finact        = $filter->_date_max = CValue::getOrSession("_date_max",  CMbDT::date());
$rectif        = CMbDT::transform("+0 DAY", $filter->_date_max, "%d")-1;
$finact        = $filter->_date_max = CMbDT::date("-$rectif DAYS", $filter->_date_max);
$finact        = $filter->_date_max = CMbDT::date("+ 1 MONTH", $filter->_date_max);
$finact        = $filter->_date_max = CMbDT::date("-1 DAY", $filter->_date_max);

$prat_id       = $filter->_prat_id = CValue::getOrSession("prat_id", 0);
$bloc_id       = CValue::getOrSession("bloc_id");
$discipline_id = $filter->_specialite = CValue::getOrSession("discipline_id", 0);
$codes_ccam    = $filter->codes_ccam = strtoupper(CValue::getOrSession("codes_ccam", ""));

// map Graph Interventions
CAppUI::requireModuleFile("dPstats", "graph_patparheure_reveil");
CAppUI::requireModuleFile("dPstats", "graph_patrepartjour");

$user = new CMediusers;
$listPrats = $user->loadPraticiens(PERM_READ);

$bloc = new CBlocOperatoire();
$listBlocs = CGroups::loadCurrent()->loadBlocs();
$listBlocsForSalles = $listBlocs;

$bloc->load($bloc_id);
if ($bloc->_id) {
	foreach ($listBlocsForSalles as $key => &$curr_bloc) {
		if ($curr_bloc->_id != $bloc->_id) {
			unset ($listBlocsForSalles[$key]);
		}
	}
}

$discipline = new CDiscipline();
$listDisciplines = $discipline->loadUsedDisciplines();

$graphs = array(
  graphPatRepartJour    ($debutact, $finact, $prat_id, $bloc_id, $discipline_id, $codes_ccam),
  graphPatParHeureReveil($debutact, $finact, $prat_id, $bloc_id, $discipline_id, $codes_ccam),
);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("filter"       			  		, $filter            );
$smarty->assign("listPrats"      		  		, $listPrats         );
$smarty->assign("listBlocs"               , $listBlocs         );
$smarty->assign("listBlocsForSalles"      , $listBlocsForSalles);
$smarty->assign("bloc"                    , $bloc              );
$smarty->assign("listDisciplines"		  		, $listDisciplines   );
$smarty->assign("graphs"		  	        	, $graphs            );

$smarty->display("vw_reveil.tpl");
