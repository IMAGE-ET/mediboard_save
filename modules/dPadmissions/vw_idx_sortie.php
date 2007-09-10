<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

// Type d'affichage
$vue = mbGetValueFromGetOrSession("vue", 0);

// R�cup�ration des dates
$date = mbGetValueFromGetOrSession("date", mbDate());

$date_actuelle = mbDateTime("00:00:00");
$date_demain = mbDateTime("00:00:00","+ 1 day");


$now  = mbDate();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("date_actuelle", $date_actuelle);
$smarty->assign("date_demain", $date_demain);
$smarty->assign("date" , $date );
$smarty->assign("now" , $now );
$smarty->assign("vue" , $vue );

$smarty->display("vw_idx_sortie.tpl");

?>