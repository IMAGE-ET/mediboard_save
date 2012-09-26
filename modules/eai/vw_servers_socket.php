<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage eai
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

$processes = CSocketBasedServer::getPsStatus();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("processes", $processes);
$smarty->display("vw_servers_socket.tpl");

?>