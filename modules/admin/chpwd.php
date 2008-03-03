<?php /* PUBLIC $Id$ */


global $AppUI, $can, $m;
$ds = CSQLDataSource::get("std");

$user = new CUser;
$where = array();
$where["user_id"]       = $ds->prepare("= %", $AppUI->user_id);
$user->loadObject($where);

$specs = $user->getSpecs();

$showMessage = dPgetParam($_REQUEST, "showMessage");

if ($showMessage) {
$message = 'Votre mot de passe ne correspond pas aux crit�res
    de s�curit� de Mediboard. Nous vous serions gr� de bien vouloir
    changer celui-ci afin qu\'il respecte ces crit�res. <br />
    La s�curit� des informations de vos patients en d�pendent.<br />
    Pour plus de pr�cisions, veuillez vous conf�rer aux 
    <a href="http://mediboard.org/public/tiki-index.php?page=Recommandations+de+la+CNIL" target="_blank">
    recommandations de la CNIL</a>.<br />
	Merci';
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