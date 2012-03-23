<?php /* $Id: ajax_etab_externe.php $ */

/**
 * @package Mediboard
 * @subpackage dPetablissement
 * @version $Revision: 7208 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Recuperation de l'id de l'etablissement externe
$etab_id = CValue::getOrSession("etab_id");

// R�cup�ration des etablissements externes
$etab_externe = new CEtabExterne();
if($etab_id){
  $etab_externe->load($etab_id);
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("etab_externe", $etab_externe);

$smarty->display("inc_etab_externe.tpl");
