<?php

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2165 $
* @author Sherpa
*/

global $can, $m;

$can->needsRead();

// R�cuperation des identifiants pour les filtres
$filter = new CSpNGAP();
$filter->idacte = mbGetValueFromGetOrSession("idacte");
$filter->idinterv = mbGetValueFromGetOrSession("idinterv");
$filter->numdos = mbGetValueFromGetOrSession("numdos");
$filter->malnum = mbGetValueFromGetOrSession("malnum");
$day   = mbGetValue(mbGetValueFromGetOrSession("Day"  ), "__");
$month = mbGetValue(mbGetValueFromGetOrSession("Month"), "__");
$year  = mbGetValue(substr(mbGetValueFromGetOrSession("Year"), 2, 2), "__");
$filter->_date = "$day/$month/$year";
$filter->pracod = mbGetValueFromGetOrSession("pracod");

// Clauses du filtre
$where = array();
if ($filter->idacte) {
  $where[] = "ASCII(`idacte`) = '$filter->idacte'";
}

if ($filter->idinterv != '') {
  $where[] = "ASCII(`idinterv`) = '$filter->idinterv'";
}

if ($filter->numdos) {
  $where["numdos"] = "LIKE '$filter->numdos%'";
}

if ($filter->malnum) {
  $where["malnum"] = "LIKE '$filter->malnum%'";
}

//if ($filter->_date != "__/__/__") {
//  $where[] = "ASCII(`date`) LIKE '$filter->_date%'";
//}

if ($filter->pracod) {
  $where["pracod"] = "LIKE '$filter->pracod%'";
}

$order = "idacte";

$detsngap = $filter->loadList($where, $order, "0,30");
$count_detsngap = $filter->countList($where);

// Chargement du sejour s�lectionn�
$detngap = new CSpNGAP();
$detngap->load(mbGetValueFromGetOrSession("sel_idacte"));

// D�s�lection si l'ent�te n'est pas dans la recherche
if (count($where) && !array_key_exists($detngap->_id, $detsngap)) {
  $detngap = new CSpNGAP();
}

// S�lection du premier de la liste si aucun n'est d�j� s�lectionn�
if (!$detngap->_id && count($detsngap)) {
  $detngap = reset($detsngap);
}

// Chargement de l'id400 associ�
$detngap->loadId400();
if ($detngap->_ref_id400->_id) {
  $detngap->_ref_id400->loadRefsFwd();
}


// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("date"  , str_replace("_", "", "$year-$month-$day"));
$smarty->assign("filter"   , $filter);
$smarty->assign("detngap"  , $detngap);
$smarty->assign("detsngap" , $detsngap);

$smarty->display("view_detngap.tpl");
?>