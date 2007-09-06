<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

// R�cup�ration du user � ajouter/editer
$mediuserSel = new CMediusers;
$mediuserSel->load(mbGetValueFromGetOrSession("user_id"));

// Chargement des banques
$banque = new CBanque();
$banques = $banque->loadList();

// R�cup�ration des fonctions
$groups = new CGroups;
$order = "text";
$groups = $groups->loadList(null, $order);
foreach ($groups as $key => $group) {
  $groups[$key]->loadRefsBack();
  foreach($groups[$key]->_ref_functions as $keyFct => $function){
    // R�cuperation des utilisateurs
    $groups[$key]->_ref_functions[$keyFct]->loadRefs();
  }
}

// R�cup�ration des disciplines
$disciplines = new CDiscipline;
$disciplines = $disciplines->loadList();

// R�cup�ration des sp�cialit�s CPAM
$spec_cpam = new CSpecCPAM();
$spec_cpam = $spec_cpam->loadList();
  
// R�cup�ration des profils
$where = array (
  //"user_username" => "LIKE '>> %'"
    "template" => "= '1'"
);
$profiles = new CUser();
$profiles = $profiles->loadList($where);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("banques"      , $banques      );
$smarty->assign("mediuserSel"  , $mediuserSel  );
$smarty->assign("profiles"     , $profiles     );
$smarty->assign("groups"       , $groups       );
$smarty->assign("disciplines"  , $disciplines  );
$smarty->assign("spec_cpam"    , $spec_cpam    );

$smarty->display("vw_idx_mediusers.tpl");

?>