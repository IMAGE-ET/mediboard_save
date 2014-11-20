<?php 

/**
 * $Id$
 *  
 * @category Messagerie
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$class      = CValue::get("class");
$mode       = CValue::get("mode", "unlinked");
$page       = CValue::get("start", 0);
$account_id = CValue::get("account_id");
$iteration = 30;

/** @var CDocumentExterne $doc */
$doc = new $class();
$doc->account_id = $account_id;
$account = $doc->loadRefAccount();


$nb_total_unlinked = $doc->count_document_total("unlinked");
$nb_documents = $doc->count_document_total($mode) ? $doc->count_document_total($mode) : 0;
/** @var CDocumentExterne[] $documents */
$documents = $doc->get_document_list($mode, $page, $iteration);

foreach ($documents as $_doc) {
  $_doc->loadRefFile(true);
}

$smarty = new CSmartyDP("modules/messagerie");
$smarty->assign("nb_total_documents", $nb_documents);
$smarty->assign("nb_unlinked", $nb_total_unlinked);
$smarty->assign("account_id", $account_id);
$smarty->assign("mode", $mode);
$smarty->assign("_status", CDocumentExterne::$_status_available);
$smarty->assign("documents", $documents);
$smarty->assign("doc", $doc);
$smarty->assign("page", $page);
$smarty->assign("iteration", $iteration);
$smarty->display("inc_list_external_documents.tpl");