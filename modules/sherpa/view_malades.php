<?php

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2165 $
* @author Sherpa
*/

global $can, $m;

$can->needsRead();

$max = 30;

// Chargement du patient s�lectionn�
$malade = new CSpMalade;
$malade->load(mbGetValueFromGetOrSession("sel_malnum"));

// R�cuperation des identifiants pour les filtres
$filter = new CSpMalade;
$filter->malnum = mbGetValueFromGetOrSession("malnum");
$filter->malnom = strtoupper(mbGetValueFromGetOrSession("malnom"));
$filter->malpre = strtoupper(mbGetValueFromGetOrSession("malpre"));
$malade_day   = mbGetValue(mbGetValueFromGetOrSession("Date_Day"  ), "__");
$malade_month = mbGetValue(mbGetValueFromGetOrSession("Date_Month"), "__");
$malade_year  = mbGetValue(mbGetValueFromGetOrSession("Date_Year" ), "____");
$filter->datnai = "$malade_day/$malade_month/$malade_year";


// Clauses where du filtre
$where = array();

if ($filter->malnum) {
  $where["malnum"] = "LIKE '$filter->malnum%'";
}

if ($filter->malnom) {
  $where["malnom"] = "LIKE '$filter->malnom%'";
}

if ($filter->malpre) {
  $where["malpre"] = "LIKE '$filter->malpre%'";
}

if ($filter->datnai != "__/__/____") {
  $where["datnai"] = "LIKE '$filter->datnai'";
}

$order = "malnom, malpre, datnai";

// Chargement des objets filtr�s
$maladesCount = 0;
$malades = array();
if (count($where)) {
  $malades = $malade->loadList($where, $order, "0, $max");
  $maladesCount = $malade->countList($where);
}

// D�s�lection si le malade n'est pas dans la recherche
if (count($where) && !array_key_exists($malade->_id, $malades)) {
  $malade = new CSpMalade();
}

// S�lection du premier de la liste si aucun n'est d�j� s�lectionn�
if (!$malade->_id && count($malades)) {
  $malade = reset($malades);
}

// Chargement de l'id400 associ�
$malade->loadId400();
if ($malade->_ref_id400->_id) {
  $malade->_ref_id400->loadRefsFwd();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("filter"   , $filter);
$smarty->assign("dateMal"  , str_replace("_", "", "$malade_year-$malade_month-$malade_day"));
$smarty->assign("malades"  , $malades);
$smarty->assign("malade"   , $malade );
$smarty->assign("maladesCount", $maladesCount);

$smarty->display("view_malades.tpl");
?>