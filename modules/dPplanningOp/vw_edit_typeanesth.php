<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $can;

$can->needsAdmin();

$type_anesth_id = mbGetValueFromGetOrSession("type_anesth_id");

// Chargement du type d'anesth�sie demand�
$type_anesth = new CTypeAnesth;
$type_anesth->load($type_anesth_id);

// Liste des Type d'anesth�sie
$types_anesth = $type_anesth->loadList(null, "name");
foreach ($types_anesth as &$_type_anesth) {
  $_type_anesth->countOperations();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("types_anesth", $types_anesth);
$smarty->assign("type_anesth" , $type_anesth );

$smarty->display("vw_edit_typeanesth.tpl");

?>