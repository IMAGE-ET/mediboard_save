<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$sejour_id = CValue::get("sejour_id");
$offline = CValue::get("offline");
$in_modal = CValue::get("in_modal");

if(!$sejour_id){
  CAppUI::stepMessage(UI_MSG_WARNING, "Veuillez s�lectionner un sejour pour visualiser le dossier complet");
  return;
}

// Chargement du sejour
$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadComplete();
$sejour->canRead();

// Chargement des affectations
$sejour->loadRefCurrAffectation()->loadRefLit();
foreach ($sejour->loadRefsAffectations() as $_affectation) {
  $_affectation->loadRefLit();
}

// Chargement des t�ches
foreach ($sejour->loadRefsTasks() as $_task) {
  $_task->loadRefPrescriptionLineElement();
}

// Chargement des op�rations
$sejour->loadRefsOperations();
foreach($sejour->_ref_operations as $_interv) {
  $_interv->loadRefPraticien(true);
  $_interv->_ref_praticien->loadRefFunction();
  $_interv->loadRefsConsultAnesth();
  $_interv->_ref_consult_anesth->loadRefConsultation();
  $check_lists = $_interv->loadBackRefs("check_lists", "date");
  foreach($check_lists as $_check_list) {
    $_check_list->loadItemTypes();
    $_check_list->loadBackRefs('items');
    foreach($_check_list->_back['items'] as $_item) {
      $_item->loadRefsFwd();
    }
  }
}

// Chargement du patient

$patient = $sejour->loadRefPatient();
$patient->loadComplete();
$patient->loadIPP();

// Chargement du dossier medicale
$dossier_medical = $patient->loadRefDossierMedical();
$dossier_medical->countAntecedents();
$dossier_medical->loadRefPrescription();
$dossier_medical->loadRefsTraitements();

// Chargement du dossier de soins clotur�
$prescription = new CPrescription();
$prescription->object_class = "CSejour";
$prescription->type = "sejour";
$prescription->object_id = $sejour->_id;
$prescription->loadMatchingObject();

$dossier = array();
$list_lines = array();

// Chargement des lignes
$prescription->loadRefsLinesMedComments();
$prescription->loadRefsLinesElementsComments();
$prescription->loadRefsPrescriptionLineMixes();

if (count($prescription->_ref_prescription_line_mixes)) {
  foreach($prescription->_ref_prescription_line_mixes as $_prescription_line_mix){
    $_prescription_line_mix->loadRefsLines();
    $_prescription_line_mix->calculQuantiteTotal();
    $_prescription_line_mix->loadRefPraticien();
    foreach($_prescription_line_mix->_ref_lines as $_perf_line){
      $list_lines["prescription_line_mix"][$_perf_line->_id] = $_perf_line;
      $_perf_line->loadRefsAdministrations();
      foreach($_perf_line->_ref_administrations as $_administration_perf){
        $_administration_perf->loadRefAdministrateur();
        if(!$_administration_perf->planification){
          $dossier[mbDate($_administration_perf->dateTime)]["prescription_line_mix"][$_perf_line->_id][$_administration_perf->quantite][$_administration_perf->_id] = $_administration_perf;
        }
      }
    }
  }
}

// Parcours des lignes de medicament et stockage du dossier clotur�
if (count($prescription->_ref_prescription_lines)) {
  foreach($prescription->_ref_prescription_lines as $_line_med){
    $_line_med->loadRefsFwd();
    $_line_med->loadRefsPrises();
    $_line_med->loadRefProduitPrescription();
    $_line_med->_ref_produit->loadConditionnement();
    $list_lines["medicament"][$_line_med->_id] = $_line_med;
    $_line_med->loadRefsAdministrations();
    foreach($_line_med->_ref_administrations as $_administration_med){
      $_administration_med->loadRefAdministrateur();
      if(!$_administration_med->planification){
        $dossier[mbDate($_administration_med->dateTime)]["medicament"][$_line_med->_id][$_administration_med->quantite][$_administration_med->_id] = $_administration_med;
      }
    }
  }
}

// Parcours des lignes d'elements
if (count($prescription->_ref_lines_elements_comments)) {
  foreach($prescription->_ref_lines_elements_comments as $chap => $_lines_by_chap){
    foreach($_lines_by_chap as $_lines_by_cat){
      foreach($_lines_by_cat["comment"] as $_line_elt_comment){
        $_line_elt_comment->loadRefPraticien();
      }
      foreach($_lines_by_cat["element"] as $_line_elt){
        $_line_elt->loadRefElement();
        $_line_elt->_ref_element_prescription->loadRefCategory();
        $list_lines[$chap][$_line_elt->_id] = $_line_elt;
        $_line_elt->loadRefsAdministrations();
        foreach($_line_elt->_ref_administrations as $_administration_elt){
          $_administration_elt->loadRefAdministrateur();
          if(!$_administration_elt->planification){
            $dossier[mbDate($_administration_elt->dateTime)][$chap][$_line_elt->_id][$_administration_elt->quantite][$_administration_elt->_id] = $_administration_elt;
          }
        }
      }
    }
  }
}

ksort($dossier);

// Constantes du s�jour
$sejour->loadListConstantesMedicales();
$constantes_grid = CConstantesMedicales::buildGrid($sejour->_list_constantes_medicales, false);

$praticien = new CMediusers();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("sejour"    , $sejour);
$smarty->assign("dossier"   , $dossier);
$smarty->assign("list_lines", $list_lines);
$smarty->assign("constantes_medicales_grid", $constantes_grid);
$smarty->assign("prescription", $prescription);
$smarty->assign("praticien", $praticien);
$smarty->assign("offline", $offline);
$smarty->assign("in_modal", $in_modal);
$smarty->display("print_dossier_soins.tpl");

?>