<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author S�bastien Fillonneau
*/
global $AppUI, $m;


$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
$mediuser->loadRefsFwd();

// R�cup�ration des disciplines
$disciplines = new CDiscipline;
$disciplines = $disciplines->loadList();

// Chargement des banques
$order = "nom ASC";
$banque = new CBanque();
$banques = $banque->loadList(null, $order);


// R�cup�ration des sp�cialit�s CPAM
$spec_cpam = new CSpecCPAM();
$spec_cpam = $spec_cpam->loadList();

$affiche_nom = CValue::get("affiche_nom",0);

// Source SMTP
$smtp_source = CExchangeSource::get("mediuser-".$mediuser->_id, "smtp", true);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("banques"     , $banques                );
$smarty->assign("disciplines" , $disciplines            );
$smarty->assign("spec_cpam"   , $spec_cpam              );
$smarty->assign("user"        , $mediuser               );
$smarty->assign("fonction"    , $mediuser->_ref_function);
$smarty->assign("affiche_nom" , $affiche_nom            );
$smarty->assign("smtp_source" , $smtp_source            );
$smarty->display("edit_infos.tpl");

?>