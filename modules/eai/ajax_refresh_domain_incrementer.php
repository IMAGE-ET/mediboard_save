<?php
/**
 * View interop receiver EAI
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$domain_id = CValue::getOrSession("domain_id");
$domain    = new CDomain();
$domain->load($domain_id);
$domain->loadRefsGroupDomains();
$domain->loadRefIncrementer()->loadView();
$domain->isMaster();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("domain", $domain);
$smarty->display("inc_vw_domain_incrementer.tpl");