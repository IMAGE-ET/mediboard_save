<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Labo
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$pratId    = CValue::getOrSession("object_id");
$pratId400 = CValue::getOrSession("id400");
$date      = CMbDT::dateTime();

//Cr�ation d'un nouvel id400 pour le laboratoire
$new_idex = new CIdSante400();

$prat = new CMediusers();
$listPrat = $prat->loadPraticiens();

$remote_name = CAppUI::conf("dPlabo CCatalogueLabo remote_name");

$idex = new CIdSante400();
$idex->object_class = "CMediusers";
$idex->tag = $remote_name;

$idexs = $idex->loadMatchingList();

foreach ($idexs as $_idex) {
  $_idex->loadRefs();
}

$prescriptionlabo_source = CExchangeSource::get("prescriptionlabo", "ftp", true, null, false);
$get_id_prescriptionlabo_source = CExchangeSource::get("get_id_prescriptionlabo", "soap", true, null, false);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("prescriptionlabo_source" , $prescriptionlabo_source);
$smarty->assign("get_id_prescriptionlabo_source" , $get_id_prescriptionlabo_source);
$smarty->assign("listPrat", $listPrat);
$smarty->assign("date", $date);
$smarty->assign("remote_name", $remote_name);
$smarty->assign("newId400", $new_idex);
$smarty->assign("list_idSante400", $idexs);

$smarty->display("configure.tpl");