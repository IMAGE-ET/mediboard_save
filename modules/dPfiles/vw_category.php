<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $canRead, $canAdmin, $canEdit, $m;

if(!$canAdmin) {
	$AppUI->redirect("m=system&a=access_denied");
}

$file_category_id = mbGetValueFromGetOrSession("file_category_id");

// Chargement de la cat�gorie demand�
$category=new CFilesCategory;
$category->load($file_category_id);

// Liste des Cat�gories
$listCategory = new CFilesCategory;
$listCategory = $listCategory->loadList();

// LIste des Class
$listClass = getChildClasses();


// Cr�ation du template
$smarty = new CSmartyDP(1);

$smarty->assign("listCategory", $listCategory);
$smarty->assign("category"    , $category    );
$smarty->assign("listClass"   , $listClass   );

$smarty->display("vw_category.tpl");

?>