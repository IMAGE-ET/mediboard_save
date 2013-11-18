<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

CAppUI::requireModuleFile("dPurgences", "redirect_barcode");

// Parametre de tri
$order_way = CValue::getOrSession("order_way", "ASC");
$order_col = CValue::getOrSession("order_col", "_pec_transport");

// Type d'affichage main courante
$selAffichage = CValue::postOrSession("selAffichage", CAppUI::conf("dPurgences default_view"));

// Service en session pour la main courante
$service_id = CValue::postOrSession("service_id");

// Type d'affichage UHCD
$uhcd_affichage = CValue::postOrSession("uhcd_affichage", CAppUI::conf("dPurgences default_view"));

// Selection de la date
$date = CValue::getOrSession("date", CMbDT::date());
$today = CMbDT::date();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("services"        , CService::loadServicesUrgence());
$smarty->assign("group"           , CGroups::loadCurrent());
$smarty->assign("selAffichage"    , $selAffichage);
$smarty->assign("service_id"      , $service_id);
$smarty->assign("uhcd_affichage"  , $uhcd_affichage);
$smarty->assign("date"            , $date);
$smarty->assign("isImedsInstalled", (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));

$smarty->assign("main_courante_refresh_frequency",      CAppUI::conf("dPurgences main_courante_refresh_frequency"));
$smarty->assign("uhcd_refresh_frequency",               CAppUI::conf("dPurgences uhcd_refresh_frequency"));
$smarty->assign("identito_vigilance_refresh_frequency", CAppUI::conf("dPurgences identito_vigilance_refresh_frequency"));

$smarty->display("vw_idx_rpu.tpl");
