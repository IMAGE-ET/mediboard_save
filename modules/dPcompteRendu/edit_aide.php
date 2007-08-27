<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: $
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$can->needsRead();
$ds = CSQLDataSource::get("std");
$field = mbGetValueFromGet("field");
$text  = utf8_decode(mbGetValueFromGet("text" ));
$class = mbGetValueFromGet("class");

// Liste des users accessibles
$listPrat = new CMediusers();
$listFct = $listPrat->loadFonctions(PERM_EDIT);
$listPrat = $listPrat->loadUsers(PERM_EDIT);

$listFunc = new CFunctions();
$listFunc = $listFunc->loadSpecialites(PERM_EDIT);

// Objet cibl�
$object = new $class;
$dependValues = null;
if ($depend_field = $object->_helped_fields[$field]) {
  $dependValues = @$object->_enumsTrans[$depend_field];
}

// Nouvelle Aide � la saisie
$aide = new CAideSaisie();
$aide->class   = $class;
$aide->field   = $field;
$aide->text    = stripslashes($text);
$aide->user_id = $AppUI->user_id;

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("aide"     , $aide);
$smarty->assign("dependValues", $dependValues);
$smarty->assign("listFunc" , $listFunc);
$smarty->assign("listPrat" , $listPrat);

$smarty->display("vw_edit_aides.tpl");
?>