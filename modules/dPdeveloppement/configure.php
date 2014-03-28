<?php

/**
 * Onglet de configuration
 *
 * @category Developpement
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("path", realpath(__DIR__."/../../"));
$smarty->display('configure.tpl');
