<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage developpement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

ob_clean();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->display('iframe_test.tpl');

CApp::rip();
