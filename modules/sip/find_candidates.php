<?php /*  $ */

/**
 * Find candidates
 *
 * @category sip
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("pointer", null);
$smarty->assign("sejour", new CSejour());
$smarty->assign("patient", new CPatient());

$smarty->display("find_candidates.tpl");
