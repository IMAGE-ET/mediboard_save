<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

// R�cup�ration du compte-rendu
$compte_rendu_id = mbGetValueFromGet("compte_rendu_id", 0);

$compteRendu = new CCompteRendu;
$compteRendu->load($compte_rendu_id);

$cr = $compteRendu->source;

// Initialisation de FCKEditor
$templateManager = new CTemplateManager;
$templateManager->printMode = true;
$templateManager->initHTMLArea();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("cr", $cr);

$smarty->display("print_cr.tpl");

?>