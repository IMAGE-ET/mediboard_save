<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m, $g;

if(!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$menu_id  = mbGetValueFromGetOrSession("menu_id" , null);
$repas_id = mbGetValueFromGet("repas_id" , null);

$menu = new CMenu;
$menu->load($menu_id);

$repas = new CRepas;
$repas->load($repas_id);

// Chargement des plat compl�mentaires
$plats     = new CPlat;
$listPlats = array();
$where              = array();
$where["typerepas"] = db_prepare("= %",$menu->typerepas);
$order              = "nom";
foreach($plats->_enums["type"] as $key=>$value){
  $listPlats[$value] = array();
  
  if($menu->modif){
    $where["type"] = db_prepare("= %",$value);
    $listPlats[$value] = $plats->loadList($where, $order);
  }
}


// Cr�ation du template
$smarty = new CSmartyDP(1);

$smarty->assign("menu"      , $menu);
$smarty->assign("listPlats" , $listPlats);
$smarty->assign("plats"     , $plats);
$smarty->assign("repas"     , $repas);

$smarty->display("inc_repas.tpl");
?>