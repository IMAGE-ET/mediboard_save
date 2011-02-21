<?php
/**
 * Configuration view for forms
 *  
 * @category forms
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$smarty = new CSmartyDP();

//$smarty->assign('var', $states);

$smarty->display("configure.tpl");
