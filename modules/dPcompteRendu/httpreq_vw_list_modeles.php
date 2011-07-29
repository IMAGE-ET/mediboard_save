<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Alexis Granger
* @abstract Permet de choisir des mod�les pour constituer des packs
*/

CCanDo::checkRead();

$object_guid  = CValue::get("object_guid");
$pack_id      = CValue::get("pack_id");
$filter_class = CValue::get("filter_class");

$object = CMbObject::loadFromGuid($object_guid, true); 
$type = "";
$name_object_id = "";

switch($object->_class) {
	case "CMediusers" :
    $type = "prat";
    $name_object_id = "user_id";
		break;
	case "CFunctions" :
	  $type = "func";
	  $name_object_id = "function_id";
		break;
	case "CGroups" :
	  $type = "etab";
	  $name_object_id = "group_id";
}

// Chargement du pack
$pack = new CPack();
$pack->load($pack_id);
$pack->loadRefsFwd();

// Mod�les concern�s
$modeles = array("prat"=>array(), "func"=>array(), "etab"=>array());
$compte_rendu = new CCompteRendu;
$where        = array();
$order        = "nom";

$where["object_id"]     = " IS NULL";
$where["object_class"]  = " = '$filter_class'";
$where[$name_object_id] = $compte_rendu->_spec->ds->prepare("= %", $object->_id);

$modeles[$type] = $compte_rendu->loadlist($where, $order);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("modeles", $modeles);
$smarty->assign("pack"   , $pack   );
$smarty->assign("filter_class", $filter_class);

$smarty->display("inc_list_modeles.tpl");
