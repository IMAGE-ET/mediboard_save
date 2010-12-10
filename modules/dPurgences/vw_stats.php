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
  "age"             => "Tranche d'�ge",
  "sexe"            => CAppUI::tr("CPatient-sexe"),
  "ccmu"            => CAppUI::tr("CRPU-ccmu"),
  "mode_entree"     => CAppUI::tr("CRPU-mode_entree"),
  "mode_sortie"     => CAppUI::tr("CSejour-mode_sortie"),
  "provenance"      => CAppUI::tr("CRPU-provenance"),
  "destination"     => CAppUI::tr("CRPU-destination"),
  "orientation"     => CAppUI::tr("CRPU-orientation"),
  "transport"       => CAppUI::tr("CRPU-transport"),
  "without_rpu"     => "S�jours d'urgence sans RPU",
  "transfers_count" => "Nombre de transferts",
  "mutations_count" => "Nombre de mutations"
);

$axes_other = array(
  "radio" => "Attente radio",
  "bio"   => "Attente biologie",
  "spe"   => "Attente sp�cialiste",
  "duree_sejour"  => "Dur�e de s�jour",
  "duree_pec"     => "Dur�e de prise en charge",
  "duree_attente" => "Dur�e d'attente",
  "diag_infirmier"  => "Diagnostic infirmier",
);

$smarty = new CSmartyDP();

$smarty->assign('filter', $filter);
$smarty->assign('axe', $axe);
$smarty->assign('axes', $axes);
$smarty->assign('axes_other', $axes_other);
$smarty->assign('count', $count);

$smarty->display('vw_stats.tpl');
