<?php /* $Id: httpreq_do_add_ccam.php 2818 2007-10-16 09:16:17Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can;

$can->needsAdmin();

set_time_limit(360);
ini_set("memory_limit", "128M");

$sourcePath = "modules/dPccam/base/ngap.tar.gz";
$targetDir = "tmp/ngap";
$targetTables = "tmp/ngap/ngap.sql";

// Extract the SQL dump
if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  $AppUI->stepAjax("Erreur, impossible d'extraire l'archive", UI_MSG_ERROR);
} 

$AppUI->stepAjax("Extraction de $nbFiles fichier(s)", UI_MSG_OK);

$ds = CSQLDataSource::get("ccamV2");

// Cr�ation de la table
if (null == $lineCount = $ds->queryDump($targetTables, true)) {
  $msg = $ds->error();
  $AppUI->stepAjax("Import des tables - erreur de requ�te SQL: $msg", UI_MSG_ERROR);
}
$AppUI->stepAjax("Table import�e", UI_MSG_OK);

?>
