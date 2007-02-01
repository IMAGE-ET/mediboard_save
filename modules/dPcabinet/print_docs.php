<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canEdit) {
  $AppUI->redirect("m=system&a=access_denied");
}

$consultation_id = mbGetValueFromPost("consultation_id");
$nbDoc           = mbGetValueFromPost("nbDoc");
$documents       = array();

// Consultation courante
$consult = new CConsultation();
if (!$consult->load($consultation_id) || !$consult->canEdit()) {
  $AppUI->setMsg("Vous n'avez pas les droits suffisants", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}else{
  $consult->loadRefsDocs();
  $aKeysDocs = array_keys($consult->_ref_documents);
  foreach($nbDoc as $compte_rendu_id => $nb_print){
    if($nb_print>0 && in_array($compte_rendu_id,$aKeysDocs)){
      for($i=1; $i<=$nb_print; $i++){
        $documents[] = $consult->_ref_documents[$compte_rendu_id];
      }
    }
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("documents", $documents);

$smarty->display("print_docs.tpl");
?>
