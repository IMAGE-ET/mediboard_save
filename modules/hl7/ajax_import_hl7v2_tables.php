<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

$table_entry = new CHL7v2TableEntry();
$where[]     =  "(user = '1') OR (code_mb IS NOT NULL)";
if ($table_entry->countList($where) > 0) {
  CAppUI::stepAjax("Des donn�es ont �t� saisies manuellement - Import impossible", UI_MSG_ERROR);
}

$sourcePath = "modules/hl7/base/hl7v2.tar.gz";
$targetDir = "tmp/hl7";
$targetPath = "tmp/hl7/hl7v2.sql";

// Extract the SQL dump
if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  CAppUI::stepAjax("Erreur, impossible d'extraire l'archive", UI_MSG_ERROR);
} 

CAppUI::stepAjax("Extraction de $nbFiles fichier(s)", UI_MSG_OK);

$ds = CSQLDataSource::get("hl7v2");
if (null == $lineCount = $ds->queryDump($targetPath)) {
  $msg = $ds->error();
  CAppUI::stepAjax("Erreur de requ�te SQL: $msg", UI_MSG_ERROR);
}

CAppUI::stepAjax("Import effectu� avec succ�s de $lineCount lignes", UI_MSG_OK);

/*
$path = "/path/to/hl7_tables";

$ds = CSQLDataSource::get("hl7v2");

$query = "CREATE TABLE IF NOT EXISTS `table_entry` (
  `number` INT(5) UNSIGNED NOT NULL,
  `code_hl7` VARCHAR(30), 
  `code_mb` VARCHAR(30), 
  `description` VARCHAR(255)
)";
$ds->exec($query);

$ds->exec("TRUNCATE TABLE `table_entry`");

$csv_files = glob($path."/table-*.csv");
natsort($csv_files);

$count = 0;

foreach($csv_files as $csv_file) {
	preg_match('/(\d*)\.csv$/', $csv_file, $matches);
	$number = $matches[1];
	
	$items = array();
	
	$fp = fopen($csv_file, "r");
  while($line = fgetcsv($fp, null, ";")) {
  	$desc = $ds->escape($line[1]);
    $hl7  = $ds->escape($line[0]);
  	$items[] = "($number, '$hl7', '$desc')";
  }
	
	$count += count($items);
	$query = "INSERT INTO `table_entry` (`number`, `code_hl7`, `description`) VALUES ".implode(", ", $items);
  $ds->exec($query);
}

CAppUI::stepAjax("$count �l�ments import�es dans ".count($csv_files)." tables");

CApp::rip();*/
