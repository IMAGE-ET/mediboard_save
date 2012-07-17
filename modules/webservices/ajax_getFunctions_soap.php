<?php /* $Id: ajax_test_dsn.php 6069 2009-04-14 10:17:11Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

include_once('ajax_connexion_soap.php');

CAppUI::stepAjax("Liste des fonctions SOAP publi�es");

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("exchange_source", $exchange_source);
$smarty->assign("functions", $soap_client->getFunctions());
$smarty->assign("types"    , $soap_client->getTypes());

$smarty->display("inc_soap_functions.tpl");

?>