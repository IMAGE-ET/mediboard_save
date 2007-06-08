<?php

global $AppUI, $can, $m, $g;

$object_class    = mbGetValueFromGetOrSession("object_class");
$object_id       = mbGetValueFromGetOrSession("object_id");
$module          = mbGetValueFromGetOrSession("module");
$do_subject_aed  = mbGetValueFromGetOrSession("do_subject_aed");
$chir_id         = mbGetValueFromGetOrSession("chir_id");


//Chargement de la liste des praticiens
$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens();

// Chargement de la liste des anesthesistes
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes();


$object = new $object_class;
$object->load($object_id);

$object->updateFormFields();
$object->loadRefsActesCCAM();
$object->loadRefsCodesCCAM();
$object->loadPossibleActes();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("listAnesths"      , $listAnesths);
$smarty->assign("listChirs"        , $listChirs);
$smarty->assign("subject"          , $object);
$smarty->assign("module"           , $module);
$smarty->assign("do_subject_aed"   , $do_subject_aed);
$smarty->assign("chir_id"          , $chir_id); 

$smarty->display("inc_gestion_ccam.tpl");

?>