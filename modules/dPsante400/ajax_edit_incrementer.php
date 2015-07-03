<?php /* $Id: view_identifiants.php 12379 2011-06-08 10:13:32Z flaviencrochard $ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision: 12379 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$incrementer_id = CValue::getOrSession("incrementer_id");
$domain_id      = CValue::getOrSession("domain_id");

// R�cup�ration due l'incrementeur � ajouter/editer 
$incrementer = new CIncrementer;
$incrementer->load($incrementer_id);
$incrementer->loadMasterDomain($domain_id);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("incrementer" , $incrementer);
$smarty->assign("domain_id" , $domain_id);
$smarty->display("inc_edit_incrementer.tpl");