<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$sourcePath = "modules/ssr/base/nomenclature.CsARR.zip";
$targetDir = "tmp/csarr";

$targetTables    = "tmp/csarr/tables.sql";

// Extract the SQL dump
if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  CAppUI::stepAjax("extraction-error", UI_MSG_ERROR, $sourcePath);
} 

CAppUI::stepAjax("extraction-success", UI_MSG_OK, $sourcePath, $nbFiles);

$ds = CSQLDataSource::get("csarr");

// Cr�ation des tables
if (null == $count = $ds->queryDump($targetTables, true)) {
  $msg = $ds->error();
  CAppUI::stepAjax("ssr-import-tables-error", UI_MSG_ERROR, $msg);
}
CAppUI::stepAjax("ssr-import-tables-success", UI_MSG_OK, $count);

// Ajout des fichiers NX dans les tables
$listTables = array(
  "activite"             => "code_csarr_v0.txt",
  "note_activite"        => "notes_code_csarr_v0.txt",
  "geste_complementaire" => "geste_compl_csarr_v0.txt",
  "modulateur"           => "modulateur_csarr_v0.txt",
  "hierarchie"           => "hier_csarr_v0.txt",
  "note_hierarchie"      => "note_hier_csarr_v0.txt",
);

function addFileIntoDB($file, $table) {
  $reussi = 0;
  $echoue = 0;
  $ignore = 0;
  $ds = CSQLDataSource::get("csarr");
  $handle = fopen($file, "r");
  
  // First line is hearders
  fgets($handle);
  
  // Ne pas utiliser fgetcsv, qui refuse de prendre en compte les caract�res en majusucules accentu�s (et d'autres caract�res sp�ciaux)
  while ($line = fgets($handle)) {
    $line = str_replace("'", "\'", $line);
    $data = explode("|", $line);
    $data = array_map("trim", $data);

    static $note_ignores = array(
      "� l\'exclusion de :",
      "cet acte comprend :",
      "avec ou sans :",
    );
    
    // CNoteActivite: Traitements sp�cifiques 
    if ($table == "note_activite") {
      // Nettoyage des termes � ignorer
      foreach ($note_ignores as $_ignore) {
        if (stripos($data[4], $_ignore) === 0) {
          $data[4] = trim(substr($data[4], strlen($_ignore)));
          if (empty($data[4])) {
            $ignore++;
            continue 2;
          }
        }
      }
            
      // D�tection du code � exclure
      $data[6] = "";
      if (preg_match("/\([a-z]{3}\+\d{3}\)/i", $data[4], $matches)) {
        $data[6] = $matches[0];
      }
    }

    // CNoteHierarchie: Traitements sp�cifiques 
    if ($table == "note_hierarchie") {
      // Nettoyage des termes � ignorer
      foreach ($note_ignores as $_ignore) {
        if (stripos($data[4], $_ignore) === 0) {
          $data[4] = trim(substr($data[4], strlen($_ignore)));
          if (empty($data[4])) {
            $ignore++;
            continue 2;
          }
        }
      }
      
      // D�tection de la hierarchie � exclure
      $data[6] = "";
      if (preg_match("/\((\d{2}\.)+\d{2}\)/i", $data[4], $matches)) {
        $data[6] = $matches[0];
      }

      // D�tection du code � exclure
      $data[7] = "";
      if (preg_match("/\([a-z]{3}\+\d{3}\)/i", $data[4], $matches)) {
        $data[7] = $matches[0];
      }
    }

    $query = "INSERT INTO $table VALUES('".implode("','", $data)."')";
    
    $ds->exec($query);
    if ($ds->error()) {
      $echoue++;
    } else {
      $reussi++;
    }
  }

  fclose($handle);
  CAppUI::stepAjax("ssr-import-csarr-report", UI_MSG_OK, $file, $table, $ignore, $reussi, $echoue);
}

foreach($listTables as $table => $file) {
  addFileIntoDB("$targetDir/$file", $table);
}

?>
