<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */
 
global $can;

$can->needsRead();

$dest_hprim_id = CValue::get("dest_hprim_id");

$listEtab = CGroups::loadGroups();

// Chargement du destinataire HPRIM demand�
$dest_hprim = new CDestinataireHprim();
$dest_hprim->load($dest_hprim_id);
if($dest_hprim->load($dest_hprim_id)) {
  $dest_hprim->loadRefsFwd();
}


// R�cup�ration de la liste des destinataires HPRIM
$itemDestHprim = new CDestinataireHprim;

$where = array();
$where["group_id"] = "= '".CGroups::loadCurrent()->_id."'";
$listDestHprim = $itemDestHprim->loadList($where);
foreach($listDestHprim as &$_dest_hprim) {
  $_dest_hprim->loadRefsFwd();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("dest_hprim"      , $dest_hprim);
$smarty->assign("exchange_objects", CExchangeSource::getObjects());
$smarty->assign("listEtab"        , $listEtab);
$smarty->assign("listDestHprim"   , $listDestHprim);
$smarty->display("vw_idx_dest_hprim.tpl");
?>
