<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: $
* @author S�bastien Fillonneau
*/
global $AppUI, $m;


$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
$mediuser->loadRefsFwd();

// R�cup�ration des disciplines
$disciplines = new CDiscipline;
$disciplines = $disciplines->loadList();

// R�cup�ration des sp�cialit�s CPAM
$spec_cpam = new CSpecCPAM();
$spec_cpam = $spec_cpam->loadList();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("disciplines" , $disciplines            );
$smarty->assign("spec_cpam"   , $spec_cpam              );
$smarty->assign("user"        ,$mediuser                );
$smarty->assign("fonction"    , $mediuser->_ref_function);

$smarty->display("edit_infos.tpl");
?>