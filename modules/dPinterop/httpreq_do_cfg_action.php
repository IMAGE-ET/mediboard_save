<?php /* $Id: httpreq_do_ghs_action.php 28 2006-05-05 08:34:11Z Rhum1 $ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision: 28 $
* @author Thomas Despoix
*/

global $AppUI, $can, $m;

$action = mbGetValueFromGet("action");

switch ($action) {
  case "extractFiles":
    extractFiles("serveurActes" , "HprimSchemaServeurActe_v101.zip");
    //extractFiles("evenementPmsi", "hprimSchemaPMSI_v101.zip" );
    extractFiles("evenementPmsi", "hprimSchemaPMSI_v101_modif.zip" );
    break;

  default:
    echo "<div class'error'>Action '$action' inconnue</div>";
}

function extractFiles($schemaDir, $schemaFile) {
  $baseDir = "modules/dPinterop/hprim";
  $destinationDir = "$baseDir/$schemaDir";
  $archivePath = "$baseDir/$schemaFile";
  if (false != $nbFiles = CMbPath::extract($archivePath, $destinationDir)) {
    echo "<div class='message'>Extraction de $nbFiles fichiers pour $schemaDir</div>";
  } else {
    echo "<div class='error'>Impossible d'extraire l'archive $schemaFile</div>";
  }
}

?>