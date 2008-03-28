<?php /* PUBLIC $Id$ */


global $AppUI, $can, $m;
$ds = CSQLDataSource::get("std");

$user = new CUser;
$where = array();
$where["user_id"]       = $ds->prepare("= %", $AppUI->user_id);
$user->loadObject($where);

$specs = $user->getSpecs();

$forceChange = dPgetParam($_REQUEST, "forceChange");

if ($forceChange) {
$message = '<strong>Votre mot de passe ne correspond pas aux crit�res de s�curit� de Mediboard</strong>. Vous ne pourrez
            pas acc�der � Mediboard tant que vous ne l\'aurez pas chang� afin qu\'il respecte ces crit�res.
            La s�curit� des informations de vos patients en d�pend.<br />
            Pour plus de pr�cisions, veuillez vous r�f�rer aux 
            <a href="http://mediboard.org/public/tiki-index.php?page=Recommandations+de+la+CNIL" target="_blank">
            recommandations de la CNIL</a>.<br />';
} else {
  $message = null;
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("user"    , $user    );
$smarty->assign("specs"   , $specs   );
$smarty->assign("message" , $message );
$smarty->display("change_password.tpl");
?>