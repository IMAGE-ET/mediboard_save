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

// Récupération du domaine à ajouter/editer
$domain = new CDomain();
$domain->load($domain_id);
$domain->loadRefsGroupDomains();
foreach ($domain->_ref_group_domains as $_group_domain) {
  $_group_domain->loadRefGroup();
}
$domain->loadRefActor();
$domain->loadRefIncrementer()->loadView();
$domain->isMaster();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("domain", $domain);
$smarty->display("vw_list_domain.tpl");