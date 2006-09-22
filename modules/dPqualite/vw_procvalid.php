<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualit�
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $canAdmin, $m, $g;

if (!$canAdmin) {
  $AppUI->redirect( "m=system&a=access_denied" );
}


$doc_ged_id = mbGetValueFromGetOrSession("doc_ged_id",0);

$docGed = new CDocGed;
if(!$docGed->load($doc_ged_id) || $docGed->etat == CDOC_TERMINE){
  // Ce document n'est pas valide
  $doc_ged_id = null;
  mbSetValueToSession("doc_ged_id");
  $docGed = new CDocGed;
}else{
  $docGed->loadLastActif();
  $docGed->loadRefsBack();
}

$docGed->loadLastEntry();


// Proc�dure en Cours de demande
$procDemande = new CDocGed;
$procDemande = $procDemande->loadProcDemande($AppUI->user_id);
foreach($procDemande as $keyProc => $currProc){
  $procDemande[$keyProc]->loadRefsBack();
  $procDemande[$keyProc]->getEtatRedac();
  $procDemande[$keyProc]->loadLastActif();
  $procDemande[$keyProc]->loadLastEntry();
}

// Proc�dure non termin� Hors demande
$procEnCours = new CDocGed;
$procEnCours = $procEnCours->loadProcRedacAndValid($AppUI->user_id);
foreach($procEnCours as $keyProc => $currProc){
    $procEnCours[$keyProc]->loadRefsBack();
  $procEnCours[$keyProc]->getEtatValid();
  $procEnCours[$keyProc]->loadLastEntry();
}

$order = "code";
// Liste des Cat�gories
$listCategories = new CCategorieDoc;
$listCategories = $listCategories->loadlist(null,$order);

// Liste des Th�mes
$listThemes = new CThemeDoc;
$listThemes = $listThemes->loadlist(null,"nom");

// Liste des Chapitres
$listChapitres = new CChapitreDoc;
$listChapitres = $listChapitres->loadlist(null,$order);

$versionDoc = array();
if($docGed->version){
  $versionDoc[] = ($docGed->version)+ 0.1;
  $versionDoc[] = intval($docGed->version)+1;
}else{
  $versionDoc[] = "1";
}
// Cr�ation du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("procDemande"    , $procDemande);
$smarty->assign("procEnCours"    , $procEnCours);
$smarty->assign("listCategories" , $listCategories);
$smarty->assign("listThemes"     , $listThemes);
$smarty->assign("listChapitres"  , $listChapitres);
$smarty->assign("docGed"         , $docGed);
$smarty->assign("g"              , $g);
$smarty->assign("versionDoc"     , $versionDoc);
$smarty->assign("user_id"        , $AppUI->user_id);

$smarty->display("vw_procvalid.tpl");
?>
