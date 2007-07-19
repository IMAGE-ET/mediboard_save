<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can;

$can->needsAdmin();
$sourcePath = "modules/dPpatients/INSEE/insee.tar.gz";
$targetDir = "tmp/insee";
$targetPath = "tmp/insee/insee.sql";

// Extract the SQL dump
if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  $AppUI->stepAjax("Erreur, impossible d'extraire l'archive", UI_MSG_ERROR);
} 

$AppUI->stepAjax("Extraction de $nbFiles fichier(s)", UI_MSG_OK);

$ds = CSQLDataSource::get("INSEE");
if (null == $lineCount = $ds->queryDump($targetPath, true)) {
  $msg = $ds->error();
  $AppUI->stepAjax("Erreur de requ�te SQL: $msg", UI_MSG_ERROR);
}

$AppUI->stepAjax("import effectu� avec succ�s de $lineCount lignes", UI_MSG_OK);

?>