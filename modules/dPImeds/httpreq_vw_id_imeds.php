<?php /* $Id:  $*/

/**
* @package Mediboard
* @subpackage dPImeds
* @version $Revision:  $
* @author Alexis Granger
*/

$user_id = mbGetValueFromGet("user_id");
$tag = mbGetValueFromGet("tag");
$type = mbGetValueFromGet("type");

$today = mbDateTime();

// Chargement du mediuser
$mediuser = new CMediusers();
$mediuser->load($user_id);

// Chargement de l'id externe
$mediuser->loadLastId400($tag);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("id_externe" , $mediuser->_ref_last_id400);
$smarty->assign("mediuser"   , $mediuser);
$smarty->assign("tag"        , $tag);
$smarty->assign("today"      , $today);

$smarty->display("inc_vw_id_imeds.tpl");

?>