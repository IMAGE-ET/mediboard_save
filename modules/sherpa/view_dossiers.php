<?php

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2165 $
* @author Sherpa
*/

global $can, $m;

$can->needsRead();

// Chargement du dossier s�lectionn�
$dossier = new CSpDossier();
$dossier->load(mbGetValueFromGetOrSession("sel_numdos"));

// R�cuperation des identifiants pour les filtres
$filter = new CSpDossier();
$filter->numdos = mbGetValueFromGetOrSession("numdos");
$filter->malnum = mbGetValueFromGetOrSession("malnum");


// Clauses where du filtre
$where = array();
if ($filter->numdos) {
  $where["numdos"] = "LIKE '$filter->numdos%'";
}
if ($filter->malnum) {
  $where["malnum"] = "LIKE '$filter->malnum'";
}

$order = "numdos";

// Chargement des objets filtr�s
$dossiers = array();
if (count($where)) {
  $dossiers = $dossier->loadList($where, $order, "0, 30");
}

// D�s�lection si le dossier n'est pas dans la recherche
if (count($where) && !array_key_exists($dossier->_id, $dossiers)) {
  $dossier = new CSpDossier();
}

// S�lection du premier de la liste si aucun n'est d�j� s�lectionn�
if (!$dossier->_id && count($dossiers)) {
  $dossier = reset($dossiers);
}

// Chargement de l'id400 associ�
$dossier->loadId400();
if ($dossier->_ref_id400->_id) {
  $dossier->_ref_id400->loadRefsFwd();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("filter"   , $filter);
$smarty->assign("dossiers"  , $dossiers);
$smarty->assign("dossier"   , $dossier );

$smarty->display("view_dossiers.tpl");
?>