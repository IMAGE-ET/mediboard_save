<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage search
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

//cr�ation du template
$smarty = new CSmartyDP();
$smarty->display("configure.tpl");
