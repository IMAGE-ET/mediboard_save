<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

$codeacte     = mbGetValueFromGetOrSession("codeacte");
$object_class = mbGetValueFromGet("object_class");
$hideSelect   = mbGetValueFromGet("hideSelect", 0);

$code = new CCodeCCAM($codeacte);
$code->Load();

// Variable permettant de savoir si l'affichage du code complet est necessaire
$codeComplet = false;
$codeacte = $code->code;

if($code->_activite != ""){
  $codeComplet = true;
  $codeacte .= "-$code->_activite";  
  if($code->_phase != ""){
    $codeacte .= "-$code->_phase";
  }
}

$codeacte = strtoupper($codeacte);

$favoris = new CFavoriCCAM();

// Cr�ation du template
$smarty = new CSmartyDP();

// @todo : ne passer que $code. Adapter le template en cons�quence
$smarty->assign("code", $code);
$smarty->assign("codeComplet"  , $codeComplet);
$smarty->assign("tarif"        , $code->_default);
$smarty->assign("favoris"      , $favoris);
$smarty->assign("codeacte"     , $codeacte);
$smarty->assign("libelle"      , $code->libelleLong);
$smarty->assign("rq"           , $code->remarques);
$smarty->assign("act"          , $code->activites);
$smarty->assign("codeproc"     , $code->procedure["code"]);
$smarty->assign("textproc"     , $code->procedure["texte"]);
$smarty->assign("remboursement", $code->remboursement);
$smarty->assign("place"        , $code->place);
$smarty->assign("chap"         , $code->chapitres);
$smarty->assign("asso"         , $code->assos);
$smarty->assign("incomp"       , $code->incomps);
$smarty->assign("object_class" , $object_class);
$smarty->assign("hideSelect"   , $hideSelect);
$smarty->display("vw_full_code.tpl");

?>