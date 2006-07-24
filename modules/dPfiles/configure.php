<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

if(!$canRead) {
	$AppUI->redirect("m=system&a=access_denied");
}
 
require_once($AppUI->getModuleClass("dPfiles", "filescategory"));

$files_category_id = mbGetValueFromGetOrSession("files_category_id");

// Chargement de la cat�gorie demand�
$category=new CFilesCategory;
$category->load($files_category_id);

// Liste des Cat�gories
$lstCategory = new CFilesCategory;
$listCategory = $lstCategory->loadList();

// LIste des Class
$listClass = getChildClasses();


// Cr�ation du template
require_once( $AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("listCategory", $listCategory);
$smarty->assign("category"    , $category    );
$smarty->assign("listClass"   , $listClass   );

$smarty->display("configure.tpl");

?>