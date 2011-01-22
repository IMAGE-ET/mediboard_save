<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPfiles
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$do = CValue::get("do", "0");

// Auto send categories
$category = new CFilesCategory();
$category->send_auto = "1";
foreach ($categories = $category->loadMatchingList() as $_category) {
  $_category->countDocItems();
  $_category->countUnsentDocItems();
}

// Unsent docItems
$max_load = CAppUI::conf("dPfiles CDocumentSender auto_max_load");
$where["file_category_id"] = CSQLDataSource::prepareIn(array_keys($categories));
$where["etat_envoi"      ] = "!= 'oui'";
$where["object_id"       ] = "IS NOT NULL";

$file = new CFile();
$items["CFile"] = $file->loadList($where, "file_id DESC", $max_load);
$count["CFile"] = $file->countList($where);

$document = new CCompteRendu();
$items["CCompteRendu"] = $document->loadList($where, "compte_rendu_id DESC", $max_load);
$count["CCompteRendu"] = $document->countList($where);

// Sending
$max_send = CAppUI::conf("dPfiles CDocumentSender auto_max_send");
$sent = 0;
foreach ($items as $_items) {
  foreach ($_items as $_item) {
	  $_item->loadTargetObject();
	  if ($do && !$_item->_send_problem) {
      // Max sent
      if (++$sent > $max_send) {
        break;
      }

	    $_item->_send = "1";
	    $_item->_send_problem = $_item->store();
	    
			// To track whether sending has been tried
	    $_item->_send = "1";
			
	  }
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("do", $do);
$smarty->assign("categories", $categories);
$smarty->assign("items", $items);
$smarty->assign("count", $count);
$smarty->assign("max_load", $max_load);
$smarty->assign("max_send", $max_send);

$smarty->display("send_documents.tpl");
?>