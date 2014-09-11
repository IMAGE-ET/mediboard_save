<?php /* $Id: ajax_test_dsn.php 6069 2009-04-14 10:17:11Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Check params
if (null == $exchange_source_name = CValue::get("exchange_source_name")) {
  CAppUI::stepAjax("Aucun nom de source d'�change sp�cifi�", UI_MSG_ERROR);
}

$exchange_source = CExchangeSource::get($exchange_source_name, "ftp", true, null, false);

$ftp = new CFTP();
$ftp->init($exchange_source);

try {
  $ftp->connect();
  CAppUI::stepAjax("Connect� au serveur $ftp->hostname et authentifi� en tant que $ftp->username");
} catch (CMbException $e) {
  $e->stepAjax();
  return;
}

if ($ftp->passif_mode) {
  CAppUI::stepAjax("Activation du mode passif");
}

try {
  $files = $ftp->getListFiles($ftp->fileprefix, true);
} catch (CMbException $e) {
  $e->stepAjax();
  return;
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("exchange_source", $exchange_source);
$smarty->assign("files", $files);

$smarty->display("inc_ftp_files.tpl");
