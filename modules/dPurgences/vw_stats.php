<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$axe    = CValue::getOrSession('axe');
$entree = CValue::getOrSession('entree', mbDate());
$count  = CValue::getOrSession('count', 30);

$filter = new CSejour;
$filter->entree = $entree;

$axes = array(
  "age" => "Tranche d'�ge",
  "sexe" => CAppUI::tr("CPatient-sexe"),
  "ccmu" => CAppUI::tr("CRPU-ccmu"),
  "mode_entree" => CAppUI::tr("CRPU-mode_entree"),
  "mode_sortie" => CAppUI::tr("CSejour-mode_sortie"),
  "provenance" => CAppUI::tr("CRPU-provenance"),
  "destination" => CAppUI::tr("CRPU-destination"),
  "orientation" => CAppUI::tr("CRPU-orientation"),
  "transport" => CAppUI::tr("CRPU-transport"),
  "without_rpu" => "S�jours d'urgence sans RPU",
  "specialist_count" => "Nombre d'attentes sp�cialiste",
  "specialist_time" => "Temps d'attente sp�cialiste (moy.)",
  "transfers_count" => "Nombre de transferts",
);

$smarty = new CSmartyDP();

$smarty->assign('filter', $filter);
$smarty->assign('axe', $axe);
$smarty->assign('axes', $axes);
$smarty->assign('count', $count);

$smarty->display('vw_stats.tpl');
