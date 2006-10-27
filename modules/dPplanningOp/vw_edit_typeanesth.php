<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

if(!$canAdmin) {
	$AppUI->redirect("m=system&a=access_denied");
}

$type_anesth_id = mbGetValueFromGetOrSession("type_anesth_id");

// Chargement du type d'anesth�sie demand�
$type_anesth=new CTypeAnesth;
$type_anesth->load($type_anesth_id);

// Liste des Type d'anesth�sie
$listTypeAnesth = new CTypeAnesth;
$listTypeAnesth = $listTypeAnesth->loadList(null, "name");


// Cr�ation du template
$smarty = new CSmartyDP(1);

$smarty->assign("listTypeAnesth", $listTypeAnesth);
$smarty->assign("type_anesth"   , $type_anesth   );

$smarty->display("vw_edit_typeanesth.tpl");

?>