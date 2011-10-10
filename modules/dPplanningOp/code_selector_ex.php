<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

$user = CUser::get();

$ds = CSQLDataSource::get("std");

$type         = CValue::get("type");
$mode         = CValue::get("mode", "stats");
$order        = CValue::get("order", "taux");
$chir         = CValue::get("chir");
$anesth       = CValue::get("anesth");
$object_class = CValue::get("object_class");

$profiles = array (
  "chir"   => $chir,
  "anesth" => $anesth,
  "user"   => $user->_id,
);

if ($profiles["user"] == $profiles["anesth"] || $profiles["user"] == $profiles["chir"]) {
	unset($profiles["user"]);
}

if (!$profiles["anesth"]) {
	unset($profiles["anesth"]);
}


$listByProfile = array();
$users = array();
foreach ($profiles as $profile => $_user_id) {
  // Chargement du user du profile
  $_user = new CMediusers();
  $_user->load($_user_id);
  $users[$profile] = $_user;
  
  $list = array();
	if ($type == "ccam") {
	  /**
	   * Favoris
	   */
	  if ($mode == "favoris") {
			$condition = "favoris_user = '$_user_id'";
			if ($object_class != "") { 
			  $condition .= " AND object_class = '$object_class'";
			}
			
			$sql = "select favoris_code
					from ccamfavoris
					where $condition
					group by favoris_code
					order by favoris_code";
			$codes = $ds->loadlist($sql);
			
		  foreach ($codes as $key => $value) {
		    // Attention � bien cloner le code CCAM car on rajoute une champ � la vol�e
		    $list[$value["favoris_code"]] = CCodeCCAM::get($value["favoris_code"], CCodeCCAM::MEDIUM);
		    $list[$value["favoris_code"]]->occ = "0";
		  }
		  
		  sort($list);  
	  }
	  
	
	  /**
	   *  Statistiques
	   */
	  if ($mode == "stats") {
	  
		  // Appel de la fonction listant les codes les plus utilis�s pour un praticien 
		  $actes = new CActeCCAM();
		  $codes = $actes->getFavoris($_user_id, $object_class);
		
		  foreach ($codes as $key => $value) {
		    // Attention � bien cloner le code CCAM car on rajoute une champ � la vol�e
		    $list[$value["code_acte"]] = CCodeCCAM::get($value["code_acte"], CCodeCCAM::MEDIUM);
		    $list[$value["code_acte"]]->occ = $value["nb_acte"];
		  }
		  
		  if ($order == "alpha") {
		    sort($list);
		  }
	  }
	}
	    
	if ($type=="cim10") {
	  /**
	   * Favoris
	   */
	  if ($mode == "favoris") {
		  $sql = "select favoris_code
					from cim10favoris
					where favoris_user = '$_user_id'
					order by favoris_code";
			$codes = $ds->loadlist($sql);
			
		   foreach($codes as $key => $value) {
		     $list[$value["favoris_code"]] = new CCodeCIM10($value["favoris_code"]);
		     $list[$value["favoris_code"]]->loadLite();
		     $list[$value["favoris_code"]]->occ = "0";
		   }
	  
	  }
	
	  /**
	   *  Statistiques
	   */
	  if ($mode == "stats") {
	    // Chargement des codes cim les plus utils� par le praticien $chir
		  $code = new CCodeCIM10();
		  
		  $sql = "SELECT DP, count(DP) as nb_code
		          FROM `sejour`
		          WHERE sejour.praticien_id = '$_user_id'
		          AND DP IS NOT NULL
		          AND DP != ''
		          GROUP BY DP
		          ORDER BY count(DP) DESC
		          LIMIT 50;";
		
		  $listCodes = $ds->loadList($sql);
		
		  $list = array();
		 
		  foreach($listCodes as $key => $value) {
		    $list[$value["DP"]] = new CCodeCIM10($value["DP"]);
		    $list[$value["DP"]]->loadLite();
		    $list[$value["DP"]]->occ = $value["nb_code"];
		  }
	  }
	}
	
	$listByProfile[$profile] = $list;
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("type"        , $type);
$smarty->assign("mode"        , $mode);
$smarty->assign("order"       , $order);
$smarty->assign("object_class", $object_class);
$smarty->assign("chir"        , $chir);
$smarty->assign("anesth"      , $anesth);
$smarty->assign("users"       , $users);
$smarty->assign("listByProfile", $listByProfile);
$smarty->display("code_selector_ex.tpl");

?>