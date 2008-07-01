<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualit�
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$doc_ged_id   = mbGetValueFromGetOrSession("doc_ged_id" , 0);
$selTheme     = mbGetValueFromGetOrSession("selTheme"   , 0);
$selChapitre  = mbGetValueFromGetOrSession("selChapitre", 0);
$fileSel = new CFile;

$docGed = new CDocGed;
if(!$docGed->load($doc_ged_id)){
  // Ce document n'est pas valide
  $doc_ged_id = null;
  mbSetValueToSession("doc_ged_id");
  $docGed = new CDocGed;
}else{
  $docGed->loadLastActif();
  if(!$docGed->_lastactif->doc_ged_suivi_id || $docGed->annule){
    // Ce document n'est pas Termin� ou est suspendu
    $doc_ged_id = null;
    mbSetValueToSession("doc_ged_id");
    $docGed = new CDocGed;	
  }else{
    $docGed->_lastactif->loadFile();
    $docGed->loadRefs();
  }
}

// Liste des Th�mes
$listThemes = new CThemeDoc;
$where = array();
$where[] = "group_id = '$g' OR group_id IS NULL";
$listThemes = $listThemes->loadlist($where,"nom");

// Liste des chapitres
$listChapitres = new CChapitreDoc;
$order = "group_id, nom";
$where = array();
$where["pere_id"] = "IS NULL";
$where[] = "group_id = '$g' OR group_id IS NULL";
$listChapitres = $listChapitres->loadlist($where,$order);
foreach($listChapitres as &$_chapitre) {
  $_chapitre->loadChapsDeep(); 
}

// Proc�dure active et non annule
$procedures = new CDocGed;

$where = array();
$where["annule"]   = "= '0'";
$where[] = "group_id = '$g' OR group_id IS NULL";
$where["actif"]    = "= '1'";
if($selTheme){
  $where["doc_theme_id"] = "= '$selTheme'";
}
if($selChapitre){
  $where["doc_chapitre_id"] = "= '$selChapitre'";
}
$ljoin = array();
$ljoin["doc_ged_suivi"] = "doc_ged.doc_ged_id = doc_ged_suivi.doc_ged_id";
    
$procedures = $procedures->loadList($where, null, null, null, $ljoin);
foreach($procedures as $keyProc=>$currProc){
  $procedures[$keyProc]->loadRefs();
  $procedures[$keyProc]->loadLastActif();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("selTheme"       , $selTheme);
$smarty->assign("selChapitre"    , $selChapitre);
$smarty->assign("listThemes"     , $listThemes);
$smarty->assign("listChapitres"  , $listChapitres);
$smarty->assign("procedures"     , $procedures);
$smarty->assign("docGed"         , $docGed);
$smarty->assign("fileSel"        , $fileSel);

$smarty->display("vw_procedures.tpl");
?>
