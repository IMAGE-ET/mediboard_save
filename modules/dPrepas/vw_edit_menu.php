<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $canRead, $canAdmin, $canEdit, $m, $g;

if(!$canEdit) {
  $AppUI->redirect("m=system&a=access_denied");
}

$menu_id      = mbGetValueFromGetOrSession("menu_id", null);
$plat_id      = mbGetValueFromGetOrSession("plat_id", null);
$typerepas_id = mbGetValueFromGetOrSession("typerepas_id", null);
$typeVue      = mbGetValueFromGetOrSession("typeVue", 0);

// Liste des Type de Repas
$listTypeRepas = new CTypeRepas;
$where = array("group_id" => db_prepare("= %", $g) );
$order = "debut, fin, nom";
$listTypeRepas = $listTypeRepas->loadList($where,$order);

// Cr�ation du template
$smarty = new CSmartyDP(1);

$smarty->assign("typeVue"       , $typeVue  );
$smarty->assign("listTypeRepas" , $listTypeRepas);
  
if($typeVue == 2){
  // Chargement du type de repas demand�
  $typeRepas = new CTypeRepas;
  $typeRepas->load($typerepas_id); 
  if($typeRepas->group_id != $g){
    $typeRepas = new CTypeRepas;
    $typerepas_id = null;
    mbSetValueToSession("typerepas_id", null);
  }
  
  $listHours = array();
  for($i=0;$i<=23;$i++){
   $key = ($i<=9) ? "0".$i : $i;
   $listHours[$key] = $key; 
  }
  
  $smarty->assign("listHours"    , $listHours);
  $smarty->assign("typeRepas"    , $typeRepas);
  
}elseif($typeVue == 1){
  // Chargement du plat demand�
  $plat = new CPlat;
  $plat->load($plat_id);
  if($plat->group_id != $g){
    $plat = new CPlat;
    $plat_id = null;
    mbSetValueToSession("plat_id", null);
  }else{
    $plat->loadRefsFwd();
  }
  
  // Liste des plats
  $listPlats = new CPlat;
  $where = array("group_id" => db_prepare("= %", $g) );
  $order = "nom, type";
  $listPlats = $listPlats->loadList($where,$order);
  
  $smarty->assign("listPlats"    , $listPlats);
  $smarty->assign("plat"         , $plat);
}else{
  
  // Chargement du menu demand�
  $menu = new CMenu;
  $menu->load($menu_id);
  if($menu->group_id != $g){
    $menu = new CMenu;
    $menu_id = null;
    mbSetValueToSession("menu_id", null);
  }
  
  // Liste des menus
  $listMenus = new CMenu;
  $where = array("group_id" => db_prepare("= %", $g) );
  $order = "nom";
  $listMenus = $listMenus->loadList($where, $order);
  
  foreach($listMenus as $key=>$value){
    $listMenus[$key]->loadRefsFwd();
  }
  
  $listRepeat = mbArrayCreateRange(1,5, true);
  $typePlats = new CPlat;
  
  $smarty->assign("typePlats"    , $typePlats);
  $smarty->assign("listRepeat"   , $listRepeat);
  $smarty->assign("date_debut"   , mbDate());
  $smarty->assign("listTypeRepas", $listTypeRepas);
  $smarty->assign("listMenus"    , $listMenus);
  $smarty->assign("menu"         , $menu); 
}

$smarty->display("vw_edit_menu.tpl");
?>