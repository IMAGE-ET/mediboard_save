<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPbloc
 *	@version $Revision$
 *  @author Romain Ollivier
 */
 
global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass($m, 'salle'));

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

// R�cup�ration des salles
$order[] = "nom";
$salles = new CSalle;
$salles = $salles->loadList(null, $order); 

// R�cup�ration de la salle � ajouter/editer
$salleSel = new CSalle;
$salleSel->load(mbGetValueFromGetOrSession('salle_id'));

// Cr�ation du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('salles', $salles);
$smarty->assign('salleSel', $salleSel);

$smarty->display('vw_idx_salles.tpl');

?>