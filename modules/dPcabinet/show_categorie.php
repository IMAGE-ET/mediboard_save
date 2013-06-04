<?php
/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 */

CCanDo::checkRead();

$consult_id = CValue::get("consult_id");

$consult = new CConsultation();
$consult->load($consult_id);
$categorie = $consult->loadRefCategorie();


// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("categorie", $categorie);
$smarty->display("inc_icone_categorie_consult.tpl");
