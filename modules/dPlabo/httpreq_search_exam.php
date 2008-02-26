<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision: $
 *  @author Alexis Granger
 */

$listExams = array();

// R�cuperation du mot recherch�
$recherche = mbGetValueFromGet("recherche");

// Chargements des analyses correspondantes
$exam = new CExamenLabo();
$limit = "30";
$where["libelle"] = "LIKE '%$recherche%' ";
$where["obsolete"] = " = '0'";
$listExams = $exam->loadList($where, null, $limit);
$countExams = $exam->countList($where);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("search"    , 1           );
$smarty->assign("recherche" , $recherche  );
$smarty->assign("listExams" , $listExams  );
$smarty->assign("countExams", $countExams );
$smarty->assign("catalogue" , new CCatalogueLabo());

$smarty->display("inc_vw_examens_catalogues.tpl");
?>
