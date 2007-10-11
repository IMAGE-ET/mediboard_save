<?php

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision:
 * @author Alexis Granger
 */

global $can;

$can->needsRead();

$emplacement = mbGetValueFromGetOrSession("emplacement");
$_user_last_name = mbGetValueFromGetOrSession("_user_last_name");
$_user_first_name = mbGetValueFromGetOrSession("_user_first_name");


// Chargement du personnel selectionn�
$personnel_id = mbGetValueFromGetOrSession("personnel_id");
$personnel = new CPersonnel();
$personnel->load($personnel_id);


// Chargement de la liste des affectations pour le filtre
$filter = new CPersonnel();
$whereFilter = array();
$ljoin["users"] = "users.user_id = personnel.user_id";
$order = "users.user_last_name";

if($emplacement){
  $whereFilter["emplacement"] = " = '$emplacement'";
  $filter->emplacement = $emplacement;
}
if($_user_last_name){
  $whereFilter["user_last_name"] = "LIKE '%$_user_last_name%'";
  $filter->_user_last_name = $_user_last_name;
}
if($_user_first_name){
  $whereFilter["user_first_name"] = "LIKE '%$_user_first_name%'";
  $filter->_user_first_name = $_user_first_name;
}

$filter->nullifyEmptyFields();
$personnels = $filter->loadList($whereFilter, $order, null, null, $ljoin);

foreach($personnels as $key => $_personnel){
  $_personnel->loadRefUser();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("personnels", $personnels );
$smarty->assign("personnel" , $personnel  );
$smarty->assign("filter", $filter);

$smarty->display("vw_edit_personnel.tpl");
?>