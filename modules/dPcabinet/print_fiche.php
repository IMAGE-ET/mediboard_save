<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsEdit();

$date = mbGetValueFromGetOrSession("date", mbDate());
$today = mbDate();

$consultation_id = mbGetValueFromGet("consultation_id");

// Consultation courante
$consult = new CConsultation();
if ($consultation_id) {
  $consult->load($consultation_id);
  $consult->loadRefsDocs();
  $consult->loadRefConsultAnesth();
  $consult->loadRefsFwd();
  $consult->loadExamsComp();
  $consult->loadRefsExamNyha();
  $consult->loadRefsExamPossum();

  if($consult->_ref_consult_anesth->consultation_anesth_id) {
    $consult->_ref_consult_anesth->loadRefs();
  }

  $praticien =& $consult->_ref_chir;
  $patient =& $consult->_ref_patient;
  $patient->loadRefsAntecedents();
  $patient->loadRefsTraitements();
}

// Affichage des donn�es
$listChamps = array(
                1=>array("hb","ht","ht_final","plaquettes"),
                2=>array("creatinine","_clairance","na","k"),
                3=>array("tp","tca","tsivy","ecbu")
                );
$cAnesth =& $consult->_ref_consult_anesth;
foreach($listChamps as $keyCol=>$aColonne){
	foreach($aColonne as $keyChamp=>$champ){
	  $verifchamp = true;
    if($champ=="tca"){
	    $champ2 = $cAnesth->tca_temoin;
	  }else{
	    $champ2 = false;
      if(($champ=="ecbu" && $cAnesth->ecbu=="?") || ($champ=="tsivy" && $cAnesth->tsivy=="00:00:00")){
        $verifchamp = false;
      }
	  }
    $champ_exist = $champ2 || ($verifchamp && $cAnesth->$champ);
    if(!$champ_exist){
      unset($listChamps[$keyCol][$keyChamp]);
    }
	}
}
//Tableau d'unit�s
$unites = array();
$unites["hb"]         = array("nom"=>"Hb","unit"=>"g/dl");
$unites["ht"]         = array("nom"=>"Ht","unit"=>"%");
$unites["ht_final"]   = array("nom"=>"Ht final","unit"=>"%");
$unites["plaquettes"] = array("nom"=>"Plaquettes","unit"=>"");
$unites["creatinine"] = array("nom"=>"Cr�atinine","unit"=>"mg/l");
$unites["_clairance"] = array("nom"=>"Clairance de Cr�atinine","unit"=>"ml/min");
$unites["na"]         = array("nom"=>"Na+","unit"=>"mmol/l");
$unites["k"]          = array("nom"=>"K+","unit"=>"mmol/l");
$unites["tp"]         = array("nom"=>"TP","unit"=>"%");
$unites["tca"]        = array("nom"=>"TCA","unit"=>"s");
$unites["tsivy"]      = array("nom"=>"TS Ivy","unit"=>"");
$unites["ecbu"]       = array("nom"=>"ECBU","unit"=>"");

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("unites"    , $unites);
$smarty->assign("listChamps", $listChamps);
$smarty->assign("consult"   , $consult);

$smarty->display("print_fiche.tpl");
?>