<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can;

$can->needsAdmin();

set_time_limit(360);
ini_set("memory_limit", "128M");

$sourcePath = "modules/dPccam/base/ccam.tar.gz";
$targetDir = "tmp/ccam";

$targetTables    = "tmp/ccam/tables.sql";
$targetBaseDatas = "tmp/ccam/basedata.sql";

// Extract the SQL dump
if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  $AppUI->stepAjax("Erreur, impossible d'extraire l'archive", UI_MSG_ERROR);
} 

$AppUI->stepAjax("Extraction de $nbFiles fichier(s)", UI_MSG_OK);

$ds = CSQLDataSource::get("ccamV2");

// Cr�ation des tables
if (null == $lineCount = $ds->queryDump($targetTables, true)) {
  $msg = $ds->error();
  $AppUI->stepAjax("Import des tables - erreur de requ�te SQL: $msg", UI_MSG_ERROR);
}
$AppUI->stepAjax("Cr�ation de $lineCount tables", UI_MSG_OK);

// Ajout des donn�es de base
if (null == $lineCount = $ds->queryDump($targetBaseDatas, true)) {
  $msg = $ds->error();
  $AppUI->stepAjax("Import des donn�es de base - erreur de requ�te SQL: $msg", UI_MSG_ERROR);
}
$AppUI->stepAjax("Import des donn�es de base effectu� avec succ�s ($lineCount lignes)", UI_MSG_OK);

// Ajout des fichiers NX dans les tables
$listTables = array(
              "actes"             => "TB101.txt",
              "activite"          => "TB060.txt",
              "activiteacte"      => "TB201.txt",
              "arborescence"      => "TB091.txt",
              "associabilite"     => "TB220.txt",
              "association"       => "TB002.txt",
              "incompatibilite"   => "TB130.txt",
              "modificateur"      => "TB083.txt",
              "modificateuracte"  => "TBCCAM06.txt",
              "notes"             => "TBCCAM11.txt",
              "notesarborescence" => "TB092.txt",
              "phase"             => "TB066.txt",
              "phaseacte"         => "TB310.txt",
              "procedures"        => "TB120.txt",
              "typenote"          => "TB050.txt"
              );

function addFileIntoDB($file, $table) {
  global $dPconfig, $AppUI;
  $reussi = 0;
  $echoue = 0;
  $ds = CSQLDataSource::get("ccamV2");
  $handle = fopen($file, "r");
  while($datas = fgetcsv($handle, 20000, "|")) {
    foreach($datas as &$value) {
      $value = str_replace("'", "\'", $value);
    }
    $query = "INSERT INTO $table VALUES('".implode($datas, "','")."')";
    $ds->exec($query);
    if($msg = $ds->error()) {
      $echoue++;
    } else {
      $reussi++;
    }
  }
  $AppUI->stepAjax("Import du fichier $file dans la table $table : $reussi lignes ajout�e(s), $echoue �chou�(s)", UI_MSG_OK);
  fclose($handle);
}

foreach($listTables as $table => $file) {
  addFileIntoDB($targetDir."/".$file, $table);
}

?>
