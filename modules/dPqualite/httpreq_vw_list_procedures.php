<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPqualit�
* @version $Revision$
* @author S�bastien Fillonneau
*/

global $can, $g;

$can->needsRead();

$doc_ged_id   = mbGetValueFromGetOrSession("doc_ged_id");
$theme_id     = mbGetValueFromGetOrSession("theme_id");
$chapitre_id  = mbGetValueFromGetOrSession("chapitre_id");
$sort_by      = mbGetValueFromGetOrSession("sort_by", "date");
$sort_way     = mbGetValueFromGetOrSession("sort_way", "DESC");
$keywords     = mbGetValueFromGet("keywords");
$first        = intval(mbGetValueFromGet("first", 0));

$ds = CSQLDataSource::get("std");

// Proc�dure active et non annul�e
$where = array();
$where[] = "annule = '0' OR annule IS NULL";
$where[] = "group_id = '$g' OR group_id IS NULL";
$where["actif"]    = "= '1'";
if($theme_id){
  $where["doc_theme_id"] = "= '$theme_id'";
}
if($chapitre_id){
  $chapitre = new CChapitreDoc();
  $chapitre->load($chapitre_id);
  $chapitre->loadChapsDeep();
  $where["doc_chapitre_id"] = $ds->prepareIn($chapitre->_chaps_and_subchaps);
}
if($keywords){
  $where["doc_ged.titre"] = "LIKE '%$keywords%'";
}
$ljoin = array();
$ljoin["doc_ged_suivi"] = "doc_ged.doc_ged_id = doc_ged_suivi.doc_ged_id";
$ljoin["doc_categories"] = "doc_ged.doc_categorie_id = doc_categories.doc_categorie_id";

$group = "doc_ged.doc_ged_id";
if ($sort_by == 'ref') {
  if(CAppUI::conf("dPqualite CDocGed _reference_doc")) {
    $sort_by = $group = "doc_categories.code, doc_chapitre_id, doc_ged.num_ref";
  } else {
  	$sort_by = $group = "doc_ged.doc_chapitre_id, doc_categories.code, doc_ged.num_ref";
  }
}
else {
	$sort_by = "doc_ged_suivi.$sort_by";
}

$procedure = new CDocGed;
$list_procedures = $procedure->loadList($where, "$sort_by $sort_way", "$first,20", $group, $ljoin);
foreach($list_procedures as &$curr_proc){
  $curr_proc->loadRefs();
  $curr_proc->loadLastActif();
}

$count_procedures = $procedure->countList($where, null, null, null, $ljoin);

if ($count_procedures >= 20)
  $pages = range(0, $count_procedures, 20);
else 
  $pages = array();
  
array_pop($pages);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("procedures", $list_procedures);
$smarty->assign("count_procedures", $count_procedures);
$smarty->assign("pages", $pages);
$smarty->assign("first", $first);

$smarty->display("inc_list_procedures.tpl");

?>