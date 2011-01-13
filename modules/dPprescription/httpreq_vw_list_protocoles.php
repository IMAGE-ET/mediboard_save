<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$praticien_id = CValue::get("praticien_id");
$function_id  = CValue::get("function_id");
$group_id     = CValue::get("group_id");
$protocoleSel_id = CValue::get("protocoleSel_id");

$is_praticien = CAppUI::$user->isPraticien();

$protocoles = array();
$protocole = new CPrescription();

if(!$function_id && !$praticien_id && $protocoleSel_id){
  $protocole->load($protocoleSel_id);
  $praticien_id = $protocole->praticien_id;	
  $function_id = $protocole->function_id;
}

$protocoles = CPrescription::getAllProtocolesFor($praticien_id, $function_id, $group_id);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("protocoles"      , $protocoles);
$smarty->assign("protocoleSel_id" , $protocoleSel_id);
$smarty->assign("praticien_id"    , $praticien_id);
$smarty->assign("function_id"     , $function_id);
$smarty->assign("group_id"        , $group_id);
$smarty->assign("is_praticien"    , $is_praticien);
$smarty->display("inc_vw_list_protocoles.tpl");

?>