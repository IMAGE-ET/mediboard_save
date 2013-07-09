<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$type = CValue::get("type", 'check_entree');
$ds = CSQLDataSource::get("std");
$result = "";

switch ($type) {
  case 'check_entree' :
    $message = " entr�e(s) erron�e(s)";
    $sql = "SELECT COUNT(*) AS total 
      FROM `sejour`
      WHERE `sejour`.`entree` != IF(`sejour`.`entree_reelle`,`sejour`.`entree_reelle`,`sejour`.`entree_prevue`)";
    $result = $ds->loadResult($sql);
    break;
  case 'check_sortie' :
    $message = " sortie(s) erronn�e(s)";
    $sql = "SELECT COUNT(*) AS total 
      FROM `sejour`
      WHERE `sejour`.`sortie` != IF(`sejour`.`sortie_reelle`,`sejour`.`sortie_reelle`,`sejour`.`sortie_prevue`)";
    $result = $ds->loadResult($sql);
    break;
  case 'fix_entree' :
    $message = " entr�e(s) corrig�e(s)";
    $sql = "UPDATE `sejour` SET
      `sejour`.`entree` = IF(`sejour`.`entree_reelle`,`sejour`.`entree_reelle`,`sejour`.`entree_prevue`)
      WHERE `sejour`.`entree` != IF(`sejour`.`entree_reelle`,`sejour`.`entree_reelle`,`sejour`.`entree_prevue`)";
    $ds->query($sql);
    $result = $ds->affectedRows();
    break;
  case 'fix_sortie' :
    $message = " sortie(s) corrig�e(s)";
    $sql = "UPDATE `sejour` SET
      `sejour`.`sortie` = IF(`sejour`.`sortie_reelle`,`sejour`.`sortie_reelle`,`sejour`.`sortie_prevue`)
      WHERE `sejour`.`sortie` != IF(`sejour`.`sortie_reelle`,`sejour`.`sortie_reelle`,`sejour`.`sortie_prevue`)";
    $ds->query($sql);
    $result = $ds->affectedRows();
    break;
  default: 
    CAppUI::stepAjax("Commande non reconnue", UI_MSG_ERROR);
}

CAppUI::stepAjax(CValue::first($result, "Aucune") . $message, $result ? UI_MSG_WARNING : UI_MSG_OK);
