<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author Fabien M�nager
 */
 
global $can;
$can->needsRead();

$smarty = new CSmartyDP();
$smarty->assign("fiche", new CFicheEi);
$smarty->display("vw_legende_criticite.tpl"); 

?>