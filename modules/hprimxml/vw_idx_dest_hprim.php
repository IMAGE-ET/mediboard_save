<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */
 
CCanDo::checkRead();

$destinataire_id = CValue::getOrSession("destinataire_id");

// Chargement du destinataire HPRIM demand�
$dest_hprim = new CDestinataireHprim();
$dest_hprim->load($destinataire_id);
if ($dest_hprim->_id) {
  $dest_hprim->loadRefGroup();
	$dest_hprim->loadRefsExchangesSources();
}

// R�cup�ration de la liste des destinataires HPRIM
$itemDestHprim = new CDestinataireHprim;
$where = array();
$where["group_id"] = "= '".CGroups::loadCurrent()->_id."'";
$listDestHprim = $itemDestHprim->loadList($where);
foreach($listDestHprim as &$_dest_hprim) {
  $_dest_hprim->loadRefGroup();
}

// Cr�ation du template
$smarty = new CSmartyDP("modules/webservices");
$smarty->assign("destinataire"    , $dest_hprim);
$smarty->assign("listDestinataire", $listDestHprim);
$smarty->display("vw_idx_dest_xml.tpl");

?>