<?php /* $Id: httpreq_vw_list_antecedents.php 1476 2007-01-19 16:40:49Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 1476 $
* @author S�bastien Fillonneau
*/

global $AppUI, $can, $m;
  
$can->needsEdit();

$sejour_id = mbGetValueFromGetOrSession("sejour_id", 0);

// Chargement du sejour
$sejour = new CSejour();
$sejour->load($sejour_id);

// Chargement du dossier medical
$sejour->loadRefDossierMedical();
$dossier_medical =& $sejour->_ref_dossier_medical;

// Chargement des antecedents et traitements
$dossier_medical->loadRefsAntecedents(true);
$dossier_medical->countAntecedents();
foreach ($dossier_medical->_ref_antecedents as &$type) {
  foreach ($type as &$ant) {
    $ant->loadLogs();
  }
}

$dossier_medical->loadRefsTraitements();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("sejour", $sejour);

$smarty->display("inc_list_ant_anesth.tpl");

?>