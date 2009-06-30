<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$user = new CUser;
$user->load($AppUI->user_id);
$user->updateSpecs();

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
$smarty->assign("user",    $user);
$smarty->assign("message", $message);
$smarty->display("change_password.tpl");
?>