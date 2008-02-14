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
$filter = new CSpDetCIM();
$filter->idacte = mbGetValueFromGetOrSession("iddiag");
$filter->idinterv = mbGetValueFromGetOrSession("idinterv");
$filter->numdos = mbGetValueFromGetOrSession("numdos");
$filter->coddia = mbGetValueFromGetOrSession("coddia");
$filter->typdia = mbGetValueFromGetOrSession("typdia");

$where = array();
if ($filter->idinterv != '') {
  $where[] = "ASCII(`idinterv`) = '$filter->idinterv'";
}

if ($filter->numdos) {
  $where["numdos"] = "LIKE '$filter->numdos%'";
}

if ($filter->coddia) {
  $where["codpra"] = "LIKE '$filter->coddia%'";
}

if ($filter->typdia) {
  $where["typdia"] = "= '$filter->typdia'";
}

$order = "iddiag";

$detscim = $filter->loadList($where, $order, "0,30");

// Chargement du sejour s�lectionn�
$detcim = new CSpDetCIM();
$detcim->load(mbGetValueFromGetOrSession("sel_idacte"));

// D�s�lection si l'ent�te n'est pas dans la recherche
if (count($where) && !array_key_exists($detcim->_id, $detscim)) {
  $detcim = new CSpDetCIM();
}

// S�lection du premier de la liste si aucun n'est d�j� s�lectionn�
if (!$detcim->_id && count($detscim)) {
  $detcim = reset($detscim);
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("filter"  , $filter);
$smarty->assign("detcim"  , $detcim);
$smarty->assign("detscim" , $detscim);

$smarty->display("view_detcim.tpl");
?>