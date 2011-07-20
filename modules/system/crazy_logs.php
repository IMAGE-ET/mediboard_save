<?php /* $Id: view_messages.php 10359 2010-10-12 16:30:43Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 10359 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$mode = CValue::get("mode", "find");
$ratio = CValue::get("ratio", 1000);
$limit = CValue::get("limit", 1000);

$log = new CAccessLog();
$ds = $log->_spec->ds;

// Purge
$purged_count = null;
if ($mode == "purge") {
  $query = "DELETE FROM `access_log` 
    WHERE duration / hits > '$ratio'
    LIMIT $limit";
  $ds->exec($query);
  $purged_count = $ds->affectedRows();
}

// D�tection
$query = "SELECT module, action, COUNT(*) AS total FROM `access_log` 
  WHERE duration / hits > '$ratio'
  GROUP BY module, action";
$logs = $ds->loadList($query);


// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("logs", $logs);
$smarty->assign("ratio", $ratio);
$smarty->assign("purged_count", $purged_count);
$smarty->display("crazy_logs.tpl");