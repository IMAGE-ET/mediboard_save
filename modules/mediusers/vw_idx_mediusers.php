<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

// Droit de lecture dPsante400
$moduleSante400 = CModule::getInstalled("dPsante400");
$canReadSante400 = $moduleSante400 ? $moduleSante400->canRead() : false;

// R�cup�ration du user � ajouter/editer
$mediuserSel = new CMediusers;
$mediuserSel->load(mbGetValueFromGetOrSession("user_id"));

// R�cup�ration des fonctions
$order = array("group_id", "text");
$functions = new CFunctions;
$functions = $functions->loadList(null, $order);

// R�cup�ration des disciplines
$disciplines = new CDiscipline;
$disciplines = $disciplines->loadList();

// R�cup�ration des sp�cialit�s CPAM
$spec_cpam = new CSpecCPAM();
$spec_cpam = $spec_cpam->loadList();

// R�cuperation des utilisateurs
foreach ($functions as $key => $function) {
  $functions[$key]->loadRefs();
}
  
// R�cup�ration des profils
$where = array (
  "user_username" => "LIKE '>> %'"
);
$profiles = new CUser();
$profiles = $profiles->loadList($where);

// Cr�ation du template
$smarty = new CSmartyDP(1);

$smarty->assign("canReadSante400", $canReadSante400);
$smarty->assign("mediuserSel"    , $mediuserSel    );
$smarty->assign("profiles"       , $profiles       );
$smarty->assign("functions"      , $functions      );
$smarty->assign("disciplines"    , $disciplines    );
$smarty->assign("spec_cpam"      , $spec_cpam      );

$smarty->display("vw_idx_mediusers.tpl");

?>