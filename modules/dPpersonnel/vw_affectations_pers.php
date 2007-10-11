<?php

/**
* @package Mediboard
* @subpackage dPressources
* @version $Revision: 331 $
* @author Alexis Granger
*/


global $can;



$object_id = mbGetValueFromGetOrSession("object_id","");
$object_class = mbGetValueFromGetOrSession("object_class","");
$list = mbGetValueFromGetOrSession("list","");


// Recuperation du user selectionn�
$user_id = mbGetValueFromGetOrSession("user_id");
$can->needsRead();
$ds = CSQLDataSource::get("std");
// R�cup�ration de la liste des classes disponibles
$classes = getInstalledClasses();

$affect_id = mbGetValueFromGetOrSession("affect_id", 0);
// Chargement de l'affectation s�lectionn�e
$affectation = new CAffectationPersonnel();

if($affect_id){
  $affectation->load($affect_id);
  $affectation->loadRefObject();
  $affectation->_ref_object->loadRefsFwd();
}

if($affect_id == 0){
  $user_id = "";
}

// Liste des classes disponibles
$classes = getInstalledClasses();

// Liste des utilisateur faisant parti du personnel
$personnel = new CPersonnel();
$groupby = "user_id";
$ljoin["users"] = "users.user_id = personnel.user_id";
$order = "users.user_last_name";
$personnels = $personnel->loadList(null, $order, null, $groupby, $ljoin);
foreach($personnels as &$personnel){
  $mediuser = new CMediusers();
  $listUsers[$personnel->user_id] = $mediuser->load($personnel->user_id);
}

// Calcul des personnel_ids pour chaque user
foreach($listUsers as $key=>$user){
  $personnel = new CPersonnel();
  $where["user_id"] = " = '$key'"; 
  $personnels = $personnel->loadList($where);
  foreach($personnels as $keyPers => $_personnel){
  	$listPers[$key][$_personnel->emplacement] = $_personnel->_id;
  }
}

$listAffectations = array();

// Chargement de la liste des affectations pour le filtre
$filter = new CAffectationPersonnel();
$filter->object_id = $object_id;
$filter->object_class = $object_class;
$where = array();

if($object_id){
  $where["object_id"] = " = '$object_id'";
}
if($object_class){
  $where["object_class"] = " = '$object_class'";
}
if($list){
  $where["personnel_id"] = $ds->prepareIn($list);
}

// Chargement des 50 dernieres affectations de personnel
$order = "affect_id DESC";
$limit = "0, 50";
$affectations = array();
if ($object_id || $object_class || $list) {
  $affectations = $filter->loadList($where, $order, $limit);
}

foreach($affectations as $key => $_affectation){
  $_affectation->loadPersonnel();
  $_affectation->_ref_personnel->loadRefUser();
  $_affectation->loadRefObject();
  $_affectation->_ref_object->loadRefsFwd();
}

foreach($affectations as $key => $_affect){
  $listAffectations[$_affect->_ref_personnel->emplacement][] = $_affect;
}


// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("affect_id", $affect_id);
$smarty->assign("listUsers", $listUsers);
$smarty->assign("listPers", $listPers);
$smarty->assign("user_id", $user_id);
$smarty->assign("listAffectations", $listAffectations);
$smarty->assign("affectation", $affectation);
$smarty->assign("personnels", $personnels);
$smarty->assign("filter", $filter);
$smarty->assign("classes",$classes);
$smarty->display("vw_affectations_pers.tpl");
?>
