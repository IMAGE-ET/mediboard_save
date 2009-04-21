<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPqualit�
* @version $Revision$
* @author S�bastien Fillonneau
*/

global $AppUI, $can;

$can->needsEdit();

$doc_ged_id = mbGetValueFromGetOrSession("doc_ged_id",0);

$docGed = new CDocGed;
if(!$docGed->load($doc_ged_id) || $docGed->etat==0){
  // Ce document n'est pas valide
  $doc_ged_id = null;
  mbSetValueToSession("doc_ged_id");
  $docGed = new CDocGed;
}else{
  $docGed->loadLastActif();
  $docGed->loadRefs();
}
$docGed->loadLastEntry();
$docGed->_lastentry->loadFile();

if($docGed->etat==CDocGed::TERMINE){
  $docGed->_lastentry = new CDocGedSuivi;
}

//Proc�dure Termin� et/ou Refus�
$procTermine = CDocGed::loadProcTermineOuRefuse($AppUI->user_id);
foreach($procTermine as $keyProc => &$currProc){
  $currProc->loadRefs();
  $currProc->getEtatRedac();
  $currProc->loadLastActif();
  $currProc->loadLastEntry();
  $currProc->loadFirstEntry();
}

// Proc�dure en Cours de demande
$procDemande = CDocGed::loadProcDemande($AppUI->user_id);
foreach($procDemande as $keyProc => &$currProc){
  $currProc->loadRefs();
  $currProc->getEtatRedac();
  $currProc->loadLastActif();
  $currProc->loadLastEntry();
}

// Proc�dure en Attente de R�daction
$procEnCours = CDocGed::loadProcRedacAndValid($AppUI->user_id);
foreach($procEnCours as $keyProc => &$currProc){
	$currProc->loadRefs();
  $currProc->getEtatRedac();
  $currProc->loadLastEntry();
}

// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("etablissements" , $etablissements);
$smarty->assign("procTermine"    , $procTermine);
$smarty->assign("procDemande"    , $procDemande);
$smarty->assign("procEnCours"    , $procEnCours);
$smarty->assign("docGed"         , $docGed);

$smarty->display("vw_procencours.tpl");

?>