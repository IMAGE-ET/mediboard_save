<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

CCanDo::checkRead();
$debut   = CValue::getOrSession("date_min", CMbDT::date());
$fin     = CValue::getOrSession("date_max", CMbDT::date());
$chir_id = CValue::getOrSession("chir_id");

$prats = array();
if (!$chir_id) {
  $prats = CConsultation::loadPraticiensCompta();
}
else {
  $chir = CMediusers::get($chir_id);
  $prats[$chir_id] = $chir;
}

CMbObject::massLoadFwdRef($prats, "function_id");

$ds = CSQLDataSource::get("std");

$object_classes = array(
  "Etablissement"       => array(
    "CConsultation",
    "CSejour",
    "COperation"
  ),
  "Cabinet" => array("CConsultation")
);

$secteurs =  array(
  "sect1" => 0,
  "sect2" => 0
);

$cotation = array();
$total_by_prat = array();
$total_by_class = array();
$tab_actes = array();
$total = 0;

if (CAppUI::conf("ref_pays") == 1) {
  $tab_actes["ccam"] = $secteurs;
  $tab_actes["ngap"] = $secteurs;
}
else {
  $tab_actes["tarmed"] = $secteurs;
  $tab_actes["caisse"] = $secteurs;
}

foreach ($object_classes as $type => $class_type) {
  $total_by_class[$type] = array();
  foreach ($class_type as $_class) {
    foreach ($tab_actes as $nom => $other) {
      $total_by_class[$type][$_class][$nom] = 0;
    }
  }
}

foreach ($prats as $_chir_id => $_prat) {
  $_prat->loadRefFunction();
  $cotation[$_chir_id] = array();
  $total_by_prat[$_chir_id] = 0;

  foreach ($object_classes as $type => $class_type) {
    foreach ($class_type as $_class) {
      $cotation[$_chir_id][$type][$_class] = $tab_actes;

      foreach ($cotation[$_chir_id][$type][$_class] as $_type => $value) {
        $query = "SELECT SUM(a.montant_base) AS sect1, SUM(a.montant_depassement) AS sect2
          FROM acte_$_type a";
        if ($_class == "COperation") {
          $query .= ", operations o
          WHERE o.annulee = '0'
          AND o.operation_id = a.object_id
          AND ";
        }
        elseif ($_class == "CConsultation") {
          $query .= ", consultation c WHERE ";
          if ($type == "Cabinet") {
            $query .= "c.sejour_id IS NULL ";
          }
          else {
            $query .= "c.sejour_id IS NOT NULL ";
          }
          $query .= "AND c.consultation_id = a.object_id AND ";
        }
        else {
          $query .= " WHERE ";
        }
        $query .= "a.object_class = '$_class'
          AND a.executant_id = '$_chir_id'
          AND DATE(a.execution) BETWEEN '$debut' AND '$fin';";
        $result = $ds->loadHash($query);

        $sect1 = round($result["sect1"], 2);
        $sect2 = round($result["sect2"], 2);
        $total_sect = $sect1 + $sect2;
        $cotation[$_chir_id][$type][$_class][$_type]["sect1"] = $sect1;
        $cotation[$_chir_id][$type][$_class][$_type]["sect2"] = $sect2;

        $total_by_prat[$_chir_id] += $total_sect;

        $total_by_class[$type][$_class][$_type] += $total_sect;
        $total += $total_sect;
      }
    }
  }
}

foreach ($total_by_prat as $_chir_id => $_total) {
  if ($_total == 0) {
    unset($total_by_prat[$_chir_id]);
    unset($cotation[$_chir_id]);
  }
}

$smarty = new CSmartyDP();

$smarty->assign("tab_actes" , $tab_actes);
$smarty->assign("debut"     , $debut);
$smarty->assign("fin"       , $fin);
$smarty->assign("cotation"  , $cotation);
$smarty->assign("total"     , $total);
$smarty->assign("prats"     , $prats);
$smarty->assign("total_by_prat" , $total_by_prat);
$smarty->assign("total_by_class", $total_by_class);
$smarty->assign("object_classes", $object_classes);

$smarty->display("inc_total_cotation.tpl");