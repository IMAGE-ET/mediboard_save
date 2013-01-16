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

$owner_types = array(
  "CMediusers" => "prat",
  "CFunctions" => "func",
  "CGroups"    => "etab",
);

$modeles = CCompteRendu::loadAllModelesFor($object->_id, $owner_types[$object->_class], $filter_class, "body");

$link = new CModeleToPack;
$link->pack_id = $pack_id;
// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("link"   , $link);
$smarty->assign("modeles", $modeles);
$smarty->assign("pack_id", $pack_id);

$smarty->display("inc_form_modeles_links.tpl");
