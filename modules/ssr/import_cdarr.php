<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$sourcePath = "modules/ssr/base/nomenclature.CdARR.tar.gz";
$targetDir = "tmp/cdarr";

$targetTables    = "tmp/cdarr/tables.sql";

// Extract the SQL dump
if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  CAppUI::stepAjax("extraction-error", UI_MSG_ERROR, $sourcePath);
} 

CAppUI::stepAjax("extraction-success", UI_MSG_OK, $sourcePath, $nbFiles);

$ds = CSQLDataSource::get("cdarr");

// Cr�ation des tables
if (null == $count = $ds->queryDump($targetTables, true)) {
  $msg = $ds->error();
  CAppUI::stepAjax("ssr-import-tables-error", UI_MSG_ERROR, $msg);
}
CAppUI::stepAjax("ssr-import-tables-success", UI_MSG_OK, $count);

// Ajout des fichiers NX dans les tables
$listTables = array(
  "intervenant"   => "LIBINTERCD.TXT",
  "type_activite" => "TYPACTSSR.TXT",
  "activite"      => "CATSSR.TXT"
);

function addFileIntoDB($file, $table) {
  $reussi = 0;
  $echoue = 0;
  $ds = CSQLDataSource::get("cdarr");
  $handle = fopen($file, "r");
  
  // Ne pas utiliser fgetcsv, qui refuse de prendre en compte les caract�res en majusucules accentu�s (et d'autres caract�res sp�ciaux)
  while($line = fgets($handle)) {
    $line = str_replace("'", "\'", $line);
    $datas = explode("|", $line);
    $query = "INSERT INTO $table VALUES('".implode("','", $datas)."')";
    
    $ds->exec($query);
    if($msg = $ds->error()) {
      $echoue++;
    } else {
      $reussi++;
    }
  }

  fclose($handle);
  CAppUI::stepAjax("ssr-import-cdarr-report", UI_MSG_OK, $file, $table, $reussi, $echoue);
}

foreach($listTables as $table => $file) {
  addFileIntoDB("$targetDir/$file", $table);
}

?>
