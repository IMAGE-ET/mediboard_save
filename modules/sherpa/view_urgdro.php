<?php

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Sherpa
*/

global $can, $m;

$can->needsRead();

// Chargement du droit s�lectionn�
$droit = new CSpUrgDro();
$droit->load(mbGetValueFromGetOrSession("sel_numdos"));

// R�cuperation des identifiants pour les filtres
$filter = new CSpUrgDro();
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
$droits = array();
if (count($where)) {
  $droits = $droit->loadList($where, $order, "0, 30");
}

// D�s�lection si le droit n'est pas dans la recherche
if (count($where) && !array_key_exists($droit->_id, $droits)) {
  $droit = new CSpOuvDro();
}

// S�lection du premier de la liste si aucun n'est d�j� s�lectionn�
if (!$droit->_id && count($droits)) {
  $droit = reset($droits);
}

// Chargement de l'id400 associ�
$droit->loadId400();
if ($droit->_ref_id400->_id) {
  $droit->_ref_id400->loadRefsFwd();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("filter"   , $filter);
$smarty->assign("droits"  , $droits);
$smarty->assign("droit"   , $droit );

$smarty->display("view_urgdro.tpl");
?>