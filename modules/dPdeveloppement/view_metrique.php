<?php /* $Id:  $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $can;
$can->needsRead();

$ds = CSQLDataSource::get("std");

// Nom des classes � r�cup�rer
$listesClasses = array(
  'CUser', 'CSalle', 'CPatient', 'CChambre', 'CLit', 
  'CConsultation', 'CCompteRendu', 'CSejour', 'COperation', 
  'CFile', 'CPrescription', 'CPrescriptionLineMedicament', 
  'CNaissance', 'CRPU'
);

$result = array();
foreach ($listesClasses as $class){
	$object = new $class;
  $sql = "SHOW TABLE STATUS LIKE '{$object->_spec->table}'";
  $statusTable = $ds->loadList($sql);
  if ($statusTable)
    $result[$class] = $statusTable[0];
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("result" , $result);
$smarty->display("view_metrique.tpl");

?>