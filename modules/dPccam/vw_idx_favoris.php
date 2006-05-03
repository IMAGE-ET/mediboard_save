<?php /* $Id: vw_idx_favoris.php,v 1.13 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision: 1.13 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}
	
require_once( $AppUI->getModuleClass('dPccam', 'acte') );

$user = $AppUI->user_id;

//Recherche des codes favoris
$query = "SELECT favoris_id, favoris_code
		  FROM ccamfavoris
		  WHERE favoris_user = '$AppUI->user_id'
		  ORDER BY favoris_code";
$favoris = db_loadList($query);

$i = 0;
$codes = array();
foreach($favoris as $key => $value) {
  $codes[$i] = new CCodeCCAM($value["favoris_code"]);
  $codes[$i]->loadLite();
  $codes[$i]->favoris_id = $value["favoris_id"];
  $i++;
}

// Cr�ation du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('codes', $codes);

$smarty->display('vw_idx_favoris.tpl');

?>