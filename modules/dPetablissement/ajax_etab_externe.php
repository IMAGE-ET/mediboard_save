<?php /* $Id: ajax_etab_externe.php $ */

/**
 * @package Mediboard
 * @subpackage dPetablissement
 * @version $Revision: 7208 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;

$can->needsRead();

// Recuperation de l'id de l'etablissement externe
$etab_id = CValue::getOrSession("etab_id");

// R�cup�ration des etablissements externes
$etabExterne = new CEtabExterne();
if($etab_id){
  $etabExterne->load($etab_id);
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("etabExterne"     , $etabExterne      );

$smarty->display("inc_etab_externe.tpl");

?>