<?php /* $Id $ */

/**
 * Merge two domains EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$domains_id = CValue::get("domains_id");
if (!is_array($domains_id)) {
  $domains_id = explode("-", $domains_id);
}

CMbArray::removeValue("", $domains_id);

$domains    = array();
$checkMerge = array();
if (count($domains_id) != 2) {
  $checkMerge[] = CAppUI::tr("mergeTooFewObjects");
}

foreach ($domains_id as $domain_id) {
  $domain = new CDomain();
  
  // the CMbObject is loaded
  if (!$domain->load($domain_id)){
    CAppUI::setMsg("Chargement impossible de l'objet [$domain_id]", UI_MSG_ERROR);
    continue;
  }
  
  $domain->loadRefIncrementer();
  $domain->loadRefActor();
  
  $domains[] = $domain;
}

$domain1 = $domains[0];
$domain2 = $domains[1];

if (($domain1->incrementer_id && $domain2->actor_id) || ($domain2->incrementer_id && $domain1->actor_id)) {
  $checkMerge[] = CAppUI::tr("CDomain-merge_incompatible-incrementer_actor");
}

/*if (($domain1->derived_from_idex && !$domain2->derived_from_idex) || ($domain2->derived_from_idex && !$domain1->derived_from_idex)) {
  $checkMerge[] = CAppUI::tr("CDomain-merge_incompatible-derived_from_idex");
}*/

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("domains", $domains);
$smarty->assign("checkMerge", $checkMerge);
$smarty->display("inc_domains_merge.tpl");