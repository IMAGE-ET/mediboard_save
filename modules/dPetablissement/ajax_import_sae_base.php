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

CCanDo::checkAdmin();

$ds = CSQLDataSource::get("sae");

$sourcePath = "modules/dPetablissement/base/sae.tar.gz";
$targetDir = "tmp/dPetablissement";
$targetPath = "tmp/dPetablissement/sae.sql";

// Extract the SQL dump
if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  CAppUI::stepAjax("Erreur, impossible d'extraire l'archive", UI_MSG_ERROR);
} 

CAppUI::stepAjax("Extraction de $nbFiles fichier(s)", UI_MSG_OK);

if (null == $lineCount = $ds->queryDump($targetPath)) {
  $msg = $ds->error();
  CAppUI::stepAjax("Erreur de requ�te SQL: $msg", UI_MSG_ERROR);
}

CAppUI::stepAjax("Import effectu� avec succ�s de $lineCount lignes", UI_MSG_OK);
