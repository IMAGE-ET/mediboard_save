<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author Sherpa
 */

global $can;
$can->needsAdmin();

$spClasses = array(
  "CSpMalade", 
  "CSpSejMed",
  "CSpEntCCAM",
  "CSpDetCCAM",
);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("spClasses", $spClasses);
$smarty->display("configure.tpl");

?>