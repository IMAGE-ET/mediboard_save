<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

global $AppUI;

$praticien_id = mbGetValueFromGetOrSession("praticien_id");
$function_id = mbGetValueFromGetOrSession("function_id");
$pack_id = mbGetValueFromGet("pack_id");

$pack = new CPrescriptionProtocolePack();
$pack->load($pack_id);

// Intitialisation
$protocoles_praticien = array();
$protocoles_function = array();

if($pack->praticien_id){
  // Chargement des protocoles du praticien
  $prescription = new CPrescription();
  $where = array();
  $where["praticien_id"] = " = '$pack->praticien_id'";
  $where["object_id"] = "IS NULL";
  $where["object_class"] = " = '$pack->object_class'";
  $tabProtocoles_praticien = $prescription->loadList($where, "libelle");
  foreach($tabProtocoles_praticien as $_protocole){
  	$protocoles_praticien[$_protocole->object_class][$_protocole->_id] = $_protocole;
  }
}

if($pack->praticien_id || $pack->function_id){
  $pack->loadRefPraticien();
  // Chargement des protocoles du cabinet
  $_function_id = $pack->function_id ? $pack->function_id : $pack->_ref_praticien->function_id;
  $prescription = new CPrescription();
  $where = array();
  $where["function_id"] = " = '$_function_id'";
  $where["object_id"] = "IS NULL";
  $where["object_class"] = " = '$pack->object_class'";
  $tab_protocoles_function = $prescription->loadList($where, "libelle");
  foreach($tab_protocoles_function as $_protocole){
  	$protocoles_function[$_protocole->object_class][$_protocole->_id] = $_protocole;
  }
}

$prescription = new CPrescription();
$prescription->loadRefsLinesMedComments();
$prescription->loadRefsLinesElementsComments();
$prescription->countLinesMedsElements();
// Chargement des items (protocoles) du pack de protocoles
if($pack->_id){
  $pack->loadRefsPackItems();

  foreach($pack->_ref_protocole_pack_items as $_pack_item){
    $_pack_item->loadRefPrescription();
    $_prescription =& $_pack_item->_ref_prescription;
    
    if(!$prescription->object_class){
      $prescription->object_class = $_prescription->object_class;
    }
    
    // Merge des lignes de medicaments
    $_prescription->loadRefsLinesMedComments();
    if($_prescription->_ref_lines_med_comments){
      foreach($_prescription->_ref_lines_med_comments as $type => $lines_by_type){
	      foreach($lines_by_type as $_line){
	        $_line->countSubstitionsLines();
	        $prescription->_ref_lines_med_comments[$type][] = $_line;
	      }
	    }
    }
    // Merge des lignes d'elements
    $_prescription->loadRefsLinesElementsComments();
    if($_prescription->_ref_lines_elements_comments){
	    foreach($_prescription->_ref_lines_elements_comments as $chapitre => $lines_by_chapitre){
	      foreach($lines_by_chapitre as $categorie_id => $lines_by_categorie){
	        foreach($lines_by_categorie as $type => $lines_type){
	          foreach($lines_type as $_line_elt){
	            $prescription->_ref_lines_elements_comments[$chapitre][$categorie_id][$type][$_line_elt->_id] = $_line_elt;
	          }
	        }
	      }
	    }
    }
    // Compteur de lignes
    $_prescription->countLinesMedsElements();
    foreach($_prescription->_counts_by_chapitre as $chapitre => $_count_by_chap){
      $prescription->_counts_by_chapitre[$chapitre] += $_count_by_chap;
    }
  }
}

$categories = CCategoryPrescription::loadCategoriesByChap();

// Chargement de l'utilisateur courant
$user = new CMediusers();
$user->load($AppUI->user_id);
$is_praticien = $user->isPraticien();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("praticien_id"         , $praticien_id);
$smarty->assign("function_id"          , $function_id);
$smarty->assign("protocoles_praticien" , $protocoles_praticien);
$smarty->assign("protocoles_function"  , $protocoles_function);
$smarty->assign("pack"                 , $pack);
$smarty->assign("prescription"         , $prescription);
$smarty->assign("class_category"       , new CCategoryPrescription());
$smarty->assign("is_praticien"         , $is_praticien);
$smarty->assign("mode_pack"            , 1);
$smarty->assign("today"                , mbDate());
$smarty->assign("now"                  , mbDateTime());
$smarty->assign("refresh_pharma"       , 0);
$smarty->assign("contexteType"         , "");
$smarty->assign("categories"           , $categories);
$smarty->display("inc_vw_pack_protocole.tpl");

?>