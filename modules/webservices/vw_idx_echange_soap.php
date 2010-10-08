 <?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6287 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can;

$can->needsRead();

$echange_soap_id = CValue::get("echange_soap_id");
$page            = CValue::get('page', 0);
$now             = mbDate();
$_date_min       = CValue::getOrSession('_date_min', mbDateTime("-7 day"));
$_date_max       = CValue::getOrSession('_date_max', mbDateTime("+1 hour"));
$service         = CValue::getOrSession("service");
$web_service     = CValue::getOrSession("web_service"); 
$fonction        = CValue::getOrSession("fonction"); 

CValue::setSession("web_service", $web_service);
CValue::setSession("service"    , $service);
CValue::setSession("_date_min"  , $_date_min);
CValue::setSession("_date_max"  , $_date_max);
CValue::setSession("fonction"   , $fonction);

$doc_errors_msg = $doc_errors_ack = "";

// Chargement de l'�change SOAP demand�
$echange_soap = new CEchangeSOAP();

$echange_soap->load($echange_soap_id);
if ($echange_soap->_id) {
  $echange_soap->loadRefs(); 
    
  $echange_soap->input  = unserialize($echange_soap->input);
  if ($echange_soap->soapfault != 1) {
  	$echange_soap->output = unserialize($echange_soap->output);
  }
}

// R�cup�ration de la liste des echanges SOAP
$itemEchangeSoap = new CEchangeSOAP;

$where = array();
if ($_date_min && $_date_max) {
  $echange_soap->_date_min = $_date_min;
  $echange_soap->_date_max = $_date_max;
	$where['date_echange'] = " BETWEEN '".$_date_min."' AND '".$_date_max."' "; 
}
if ($service) {
  $where['type'] = " = '".$service."'";
}
if ($fonction) {
	$where['function_name'] = " = '".$fonction."'";
}
if ($web_service) {
  $where["web_service_name"] = " = '".$web_service."'";
}

$total_echange_soap = 0;
$echangesSoap = array();
if (($service && $web_service) || ($service && $_date_min && $_date_max)) {
  $total_echange_soap = $itemEchangeSoap->countList($where);
  $order = "date_echange DESC";
  $forceindex[] = "date_echange";
  $echangesSoap = $itemEchangeSoap->loadList($where, $order, "$page, 20", null, null, $forceindex);
}
  
  
foreach ($echangesSoap as &$_echange_soap) {  
  $url = parse_url($_echange_soap->destinataire);
  $_echange_soap->destinataire = $url['host'];
}

//$services = array();

if (!$echange_soap->_id) {
  $ds = CSQLDataSource::get("std");
  $services = $ds->loadList("SELECT type FROM echange_soap GROUP BY type");
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("echange_soap"       , $echange_soap);
$smarty->assign("echangesSoap"       , $echangesSoap);
$smarty->assign("total_echange_soap" , $total_echange_soap);
$smarty->assign("page"               , $page);

$smarty->assign("service"            , $service);
$smarty->assign("web_service"        , $web_service);
$smarty->assign("fonction"           , $fonction);
$smarty->assign("services"           , $services[0]);

$smarty->display("vw_idx_echange_soap.tpl");
?>
