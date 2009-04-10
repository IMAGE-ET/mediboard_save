<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


global $can;
$can->needsAdmin();

$marked = mbGetValueFromGet("marked");

$type = mbGetValueFromGet("type");
$types = $type == "all" ? CMouvFactory::getTypes() : array($type);

$marked = mbGetValueFromGet("marked");
$marked = $marked == "all" ? array("0", "1") : array($marked);

foreach ($types as $_type) {
  foreach ($marked as $_marked) {
	  $mouv = CMouvFactory::create($_type);
	   
		switch (mbGetValueFromGet("action")) {
			case "count":
			$count = $mouv->count($_marked);
			CAppUI::stepAjax("%s - %s : %s disponibles ", UI_MSG_OK, 
			  CAppUI::tr("CMouvement400-type-$_type"),
			  CAppUI::tr("CMouvement400-marked-$_marked"), 
			  $count);
			break;
			
			case "purge":
			$count = $mouv->purge($_marked);
			CAppUI::stepAjax("%s - %s : %s supprim�s ", UI_MSG_OK, 
			  CAppUI::tr("CMouvement400-type-$_type"),
			  CAppUI::tr("CMouvement400-marked-$_marked"), 
			  $count);
			break;
			
			default:
		  CAppUI::stepAjax("Action '$action' non prise en charge", UI_MSG_ERROR);
			break;
		}
  }
}

?>