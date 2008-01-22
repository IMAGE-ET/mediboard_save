<?php 

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 23 $
* @author Alexis Granger
*/

$object_id = mbGetValueFromGetOrSession("object_id");
$object_class = mbGetValueFromGetOrSession("object_class");
// Chargement de la consultation
$object = new $object_class;
$object->load($object_id);
$object->loadRefsActesNGAP();

// Initialisation d'un acte NGAP
$acte_ngap = new CActeNGAP();
$acte_ngap->quantite = 1;
$acte_ngap->coefficient = 1;


// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("acte_ngap", $acte_ngap);
$smarty->assign("object"  , $object  );

$smarty->display("inc_acte_ngap.tpl");
?>