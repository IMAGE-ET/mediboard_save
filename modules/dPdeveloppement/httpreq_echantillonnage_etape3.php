<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $m;

$fct_selected = mbGetValueFromGet("fct_selected", null);
$_nb_anesth   = mbGetValueFromGet("_nb_anesth"  , 0);
$_nb_cab      = mbGetValueFromGet("_nb_cab"     , 0);

$creation_prat = intval($_nb_anesth) + intval($_nb_cab);
$listPrat = null;

if($fct_selected && is_array($fct_selected)){  
  $listPrat = new CMediusers;
  $where = array();
  $ljoin = array();
  $where["function_id"] = db_prepare_in($fct_selected);
  $ljoin["users"] = "`users`.`user_id` = `users_mediboard`.`user_id`";
  $order = "`users`.`user_last_name`, `users`.`user_first_name`";
  $listPrat = $listPrat->loadList($where, $order, null, null, $ljoin);
  
  foreach($listPrat as $keyPrat=>$prat){
   if(!$prat->isPraticien()){
     unset($listPrat[$keyPrat]);
   } 
  }
  $list_10 = mbArrayCreateRange(0,10, true);
}else{
  $list_10 = mbArrayCreateRange(1,10, true);
}

$list_50 = mbArrayCreateRange(1,50, true);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("list_10"       , $list_10);
$smarty->assign("list_50"       , $list_50);
$smarty->assign("listPrat"      , $listPrat);
$smarty->assign("creation_prat" , $creation_prat);

$smarty->display("inc_echantillonnage_etape3.tpl");
?>