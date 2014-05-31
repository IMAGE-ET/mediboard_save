<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();
$user = CUser::get();

// Ne pas supprimer, utilis� pour mettre le praticien en session
$praticien_id    = CValue::getOrSession("praticien_id");
$hide_finished   = CValue::getOrSession("hide_finished", 0);
$salle_id        = CValue::getOrSession("salle");
$bloc_id         = CValue::getOrSession("bloc_id");
$op              = CValue::getOrSession("op");
$date            = CValue::getOrSession("date", CMbDT::date());
$load_checklist  = CValue::get("load_checklist", 0);
$modif_operation = CCanDo::edit() || $date >= CMbDT::date();

// R�cup�ration de l'utilisateur courant
$currUser = new CMediusers();
$currUser->load($user->_id);
$currUser->isAnesth();
$currUser->isPraticien();

// Chargement des praticiens
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_DENY);

$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens(PERM_DENY);

// Sauvegarde en session du bloc (pour preselectionner dans la salle de reveil)
$salle = new CSalle;
$salle->load($salle_id);
CValue::setSession("bloc_id", $salle->bloc_id);

// Op�ration selectionn�e
$selOp = new COperation();
$protocoles = array();
$anesth_id = "";

if ($op) {
  $selOp->load($op);

  $selOp->canDo();
  $selOp->loadRefs();
  $selOp->loadRefPraticien();
  $selOp->loadRefChir();
  $selOp->countExchanges();
  $selOp->isCoded();
  $selOp->loadBrancardage();
  $selOp->countAlertsNotHandled();
  $selOp->_ref_consult_anesth->loadRefsTechniques();

  $sejour =& $selOp->_ref_sejour;
 
  $sejour->loadExtDiagnostics();
  $sejour->loadRefDossierMedical();
  $sejour->_ref_dossier_medical->loadRefsBack();
  $sejour->loadRefsConsultAnesth();
  $sejour->loadRefsPrescriptions();
  $sejour->_ref_consult_anesth->loadRefsFwd();
  $sejour->loadRefCurrAffectation();

  $patient =& $sejour->_ref_patient;
  $patient->loadRefPhotoIdentite();
  $patient->loadRefDossierMedical();

  if (!$selOp->_ref_consult_anesth->_id) {
    $patient->loadRefsConsultations();

    foreach ($patient->_ref_consultations as $_consultation) {
      $_consultation->loadRefsDossiersAnesth();
      $_consultation->loadRefPlageConsult();
      foreach ($_consultation->_refs_dossiers_anesth as $_dossier_anesth) {
        $_dossier_anesth->loadRefOperation();
      }
    }
  }

  $selOp->getAssociationCodesActes();
  $selOp->loadExtCodesCCAM();
  $selOp->loadPossibleActes();
  $selOp->_ref_plageop->loadRefsFwd();

  // Affichage des donn�es
  $listChamps = array(
    1 => array("hb", "ht", "ht_final", "plaquettes"),
    2 => array("creatinine", "_clairance", "na", "k"),
    3 => array("tp", "tca", "tsivy", "ecbu")
  );

  $cAnesth =& $selOp->_ref_consult_anesth;
  foreach ($listChamps as $keyCol => $aColonne) {
    foreach ($aColonne as $keyChamp => $champ) {
      $verifchamp = true;
      if ($champ == "tca") {
        $champ2 = $cAnesth->tca_temoin;
      }
      else {
        $champ2 = false;
        if (($champ == "ecbu" && $cAnesth->ecbu == "?") || ($champ == "tsivy" && $cAnesth->tsivy == "00:00:00")) {
          $verifchamp = false;
        }
      }
      $champ_exist = $champ2 || ($verifchamp && $cAnesth->$champ);
      if (!$champ_exist) {
        unset($listChamps[$keyCol][$keyChamp]);
      }
    }
  }

  $selOp->_ref_consult_anesth->_ref_consultation->loadRefsBack();
  $selOp->_ref_consult_anesth->_ref_consultation->loadRefPraticien()->loadRefFunction();
  
  // Chargement de la prescription de sejour
  if (CModule::getActive("dPprescription")) {
    $prescription = new CPrescription();
    $prescription->object_id = $selOp->sejour_id;
    $prescription->object_class = "CSejour";
    $prescription->type = "sejour";
    $prescription->loadMatchingObject();
  }
  
  $anesth_id = ($selOp->anesth_id) ? $selOp->anesth_id : $selOp->_ref_plageop->anesth_id;
  if ($anesth_id && CModule::getActive('dPprescription')) {
    $protocoles = CPrescription::getAllProtocolesFor($anesth_id, null, null, 'CSejour', 'sejour');
  }

  if (!$selOp->prat_visite_anesth_id && $selOp->_ref_anesth->_id) {
    $selOp->prat_visite_anesth_id = $selOp->_ref_anesth->_id;
  }
}

$listAnesthType = new CTypeAnesth;
$orderanesth = "name";
$listAnesthType = $listAnesthType->loadList(null, $orderanesth);

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

// Initialisation d'un acte NGAP
$acte_ngap = CActeNGAP::createEmptyFor($selOp);
// Liste des dents CCAM
$liste_dents = reset(CDentCCAM::loadList());

// V�rification de la check list journali�re
$daily_check_lists = array();
$daily_check_list_types = array();
$require_check_list = CAppUI::conf("dPsalleOp CDailyCheckList active") && $date >= CMbDT::date() && !$currUser->_is_praticien;

if ($require_check_list) {
  list($check_list_not_validated, $daily_check_list_types, $daily_check_lists) = CDailyCheckList::getCheckLists($salle, $date);

  if ($salle->cheklist_man && !$load_checklist) {
    $check_list_not_validated = 0;
  }
  if ($check_list_not_validated == 0) {
    $require_check_list = false;
  }
}

// Chargement des check lists de l'OMS
$operation_check_lists = array();
$operation_check_item_categories = array();

$operation_check_list = new CDailyCheckList();
$cat = new CDailyCheckItemCategory();
$cat->target_class = "COperation";

// Pre-anesth, pre-op, post-op
// Don't load them if we have a daily check list to fill...
if (!$require_check_list && $selOp->_id) {
  foreach ($operation_check_list->_specs["type"]->_list as $type) {
    $list = CDailyCheckList::getList($selOp, null, $type);
    $list->loadItemTypes();
    $list->loadRefsFwd();
    $list->loadBackRefs('items');
    $list->isReadonly();
    $list->_ref_object->loadRefPraticien();
    $operation_check_lists[$type] = $list;

    $cat->type = $type;
    $operation_check_item_categories[$type] = $cat->loadMatchingList("title");
  }
}

$anesth = new CMediusers();
$anesth->load($anesth_id);

// Cr�ation du template
$smarty = new CSmartyDP();

if ($selOp->_id) {
  $smarty->assign("listChamps", $listChamps);
}

$group = CGroups::loadCurrent();
$group->loadConfigValues();

$type_personnel = array("op", "op_panseuse", "iade", "sagefemme", "manipulateur");
if (count($daily_check_list_types) && $require_check_list) {
  $type_personnel = array();
  foreach ($daily_check_list_types as $check_list_type) {
    $validateurs = explode("|", $check_list_type->type_validateur);
    foreach ($validateurs as $validateur) {
      $type_personnel[] = $validateur;
    }
  }
}
$listValidateurs = CPersonnel::loadListPers(array_unique(array_values($type_personnel)), true, true);
$operateurs_disp_vasc = implode("-", array_merge(CMbArray::pluck($listChirs, "_id"), CMbArray::pluck($listValidateurs, "user_id")));

$smarty->assign("anesth_perop"           , new CAnesthPerop());
$smarty->assign("unites"                 , $unites);
$smarty->assign("acte_ngap"              , $acte_ngap);
$smarty->assign("liste_dents"            , $liste_dents);
$smarty->assign("op"                     , $op);
$smarty->assign("salle"                  , $salle_id);
$smarty->assign("currUser"               , $currUser);
$smarty->assign("listAnesthType"         , $listAnesthType);
$smarty->assign("listAnesths"            , $listAnesths);
$smarty->assign("listChirs"              , $listChirs);
$smarty->assign("operateurs_disp_vasc"   , $operateurs_disp_vasc);
$smarty->assign("modeDAS"                , CAppUI::conf("dPsalleOp CDossierMedical DAS"));
$smarty->assign("selOp"                  , $selOp);
$smarty->assign("date"                   , $date);
$smarty->assign("modif_operation"        , $modif_operation);
$smarty->assign("listValidateurs"        , $listValidateurs);
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->assign("isbloodSalvageInstalled", CModule::getActive("bloodSalvage"));
$smarty->assign("isImedsInstalled"       , (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));
$smarty->assign("codage_prat"            , $group->_configs["codage_prat"]);
$smarty->assign("_is_dentiste"           , $selOp->_ref_chir ? $selOp->_ref_chir->isDentiste(): false);

if (CModule::getActive("dPprescription")) {
  if (!isset($prescription)) {
    $prescription = new CPrescription();
  }
  $smarty->assign("prescription"           , $prescription);
}

$smarty->assign("protocoles"             , $protocoles);
$smarty->assign("anesth_id"              , $anesth_id);
$smarty->assign("anesth"                 , $anesth);
$smarty->assign("hide_finished"          , $hide_finished);
$smarty->assign("user_id"                , $user->_id);
$smarty->assign("create_dossier_anesth"  , 1);

// Daily check lists
$smarty->assign("require_check_list"             , $require_check_list);
$smarty->assign("daily_check_lists"              , $daily_check_lists);
$smarty->assign("daily_check_list_types"         , $daily_check_list_types);

// Operation check lists
$smarty->assign("operation_check_lists"          , $operation_check_lists);
$smarty->assign("operation_check_item_categories", $operation_check_item_categories);

if (CModule::getActive("maternite") && $selOp->_id) {
  $smarty->assign("naissance"            , new CNaissance);
}

$smarty->display("vw_operations.tpl");
