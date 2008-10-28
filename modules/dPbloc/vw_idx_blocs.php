<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPbloc
 *	@version $Revision$
 *  @author Fabien M�nager
 */
 
global $can;
$can->needsEdit();

$bloc_id = mbGetValueFromGetOrSession("bloc_id", 0);

// R�cup�ration des blocs de l'etablissement
$blocs_list = CGroups::loadCurrent()->loadBlocs(PERM_EDIT);

// R�cup�ration du bloc � modifier
$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("blocs_list", $blocs_list);
$smarty->assign("bloc",       $bloc);

$smarty->display("vw_idx_blocs.tpl");

?>