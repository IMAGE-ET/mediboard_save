<?php

CCanDo::checkEdit();

// R�cup�ration des param�tres
$object_guid = CValue::get("object_guid");

$object = CMbObject::loadFromGuid($object_guid);

$affectations_uf = $object->loadBackRefs("ufs");

$uf  = new CUniteFonctionnelle();
$uf->group_id = CGroups::loadCurrent()->_id;
$ufs = $uf->loadMatchingList('libelle');

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("object", $object);
$smarty->assign("ufs", $ufs);
$smarty->assign("affectations_uf", $affectations_uf);

$smarty->display("inc_affectation_uf.tpl");
?>