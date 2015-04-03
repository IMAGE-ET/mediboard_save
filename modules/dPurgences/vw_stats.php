<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$axe            = CValue::getOrSession('axe');
$entree         = CValue::getOrSession('entree', CMbDT::date("-1 MONTH"));
$sortie         = CValue::getOrSession('sortie', CMbDT::date());
$hide_cancelled = CValue::getOrSession("hide_cancelled", 1);

$filter = new CSejour;
$filter->entree = $entree;
$filter->sortie = $sortie;

if (!$axe) {
  $axe = "age";
}

$axes = array(
  "age"                    => "Tranche d'�ge",
  "sexe"                   => CAppUI::tr("CPatient-sexe"),
  "ccmu"                   => CAppUI::tr("CRPU-ccmu"),
  "mode_entree"            => CAppUI::tr("CSejour-mode_entree"),
  "mode_sortie"            => CAppUI::tr("CSejour-mode_sortie"),
  "provenance"             => CAppUI::tr("CSejour-provenance"),
  "destination"            => CAppUI::tr("CSejour-destination"),
  "orientation"            => CAppUI::tr("CRPU-orientation"),
  "transport"              => CAppUI::tr("CSejour-transport"),
  "without_rpu"            => "S�jours d'urgence sans RPU",
  "transfers_count"        => "Nombre de transferts",
  "mutations_count"        => "Nombre de mutations",
  "accident_travail_count" => "Nombre d'accidents de travail renseign�s",
);

$axes_other = array(
  "radio"          => "Attente radio",
  "bio"            => "Attente biologie",
  "spe"            => "Attente sp�cialiste",
  "duree_sejour"   => "Dur�e de s�jour",
  "duree_pec"      => "Dur�e de prise en charge",
  "duree_attente"  => "Dur�e d'attente",
  "diag_infirmier" => "Diagnostic infirmier",
);

$smarty = new CSmartyDP();

$smarty->assign('filter'        , $filter);
$smarty->assign('axe'           , $axe);
$smarty->assign('axes'          , $axes);
$smarty->assign('axes_other'    , $axes_other);
$smarty->assign('hide_cancelled', $hide_cancelled);

$smarty->display('vw_stats.tpl');
