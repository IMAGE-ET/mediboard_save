<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

global $m;

// On sauvegarde le module pour que les mises en session des param�tes se fassent
// dans le module depuis lequel on acc�de � la ressource
$save_m = $m;

$current_m     = CValue::get("current_m");
$m = $current_m;

$date_planning    = CValue::getOrSession("date_planning", CMbDT::date());
$praticien_id     = CValue::getOrSession("planning_chir_id");
$scroll_top       = CValue::get("scroll_top", null);
$bloc_id          = CValue::getOrSession("bloc_id");
$show_cancelled   = CValue::getOrSession("show_cancelled", 0);
$show_operations  = CValue::getOrSession("show_operations", 1);

//alerts
$nbIntervHorsPlage  = 0;
$nbIntervNonPlacees = 0;
$nbAlertesInterv    = 0;
$debut = $fin = $date_planning;

$bloc = new CBlocOperatoire();
$where = array();
if ($bloc_id) {
  $where["bloc_operatoire_id"] = " = '$bloc_id'";
}
$blocs = $bloc->loadList($where);

if (count($blocs) == 1) {
  $current_bloc = reset($blocs);
}

foreach ($blocs as $_bloc) {
  /** @var CBlocOperatoire $_bloc */
  $_bloc->canDo();
  $_bloc->loadRefsSalles();
  $nbAlertesInterv+= count($_bloc->loadRefsAlertesIntervs());
}

$group = CGroups::loadCurrent();

// R�cup�ration des salles
$salle = new CSalle();
$where = array();
$ljoin = array();
$order = "bloc_operatoire.nom";


$blocs = $bloc->loadGroupList();
if ($bloc_id) {
  $where["bloc_id"] = "= '$bloc_id'";
}
else {
  $where["bloc_id"] = CSQLDataSource::prepareIn(array_keys($blocs));
}

$where["group_id"] = "= '$group->_id'";
$ljoin["bloc_operatoire"] = "bloc_operatoire.bloc_operatoire_id = sallesbloc.bloc_id";

$salles = $salle->loadList($where, $order, null, null, $ljoin);
$salles_ids = array_keys($salles);

// R�cup�ration des op�rations
$operation = new COperation();

$where = array();
$ljoin = array();

$where["operations.date"] = "= '$date_planning'";
if (!$show_cancelled) {
  $where["operations.annulee"] = "= '0'";
}
//$where["operations.plageop_id"] = "IS NULL";
if ($bloc_id) {
  $where["sallesbloc.bloc_id"] = "= '$bloc_id'";
}

if ($praticien_id) {
  $where["operations.chir_id"] = " = '$praticien_id'";
}

$operations = $operation->loadList($where, null, null, null, $ljoin);
$nbIntervHorsPlage = $operation->countList($where, null, $ljoin);

$prats  = CMbObject::massLoadFwdRef($operations, "chir_id");
CMbObject::massLoadFwdRef($operations, "salle_id");
CMbObject::massLoadFwdRef($operations, "anesth_id");
CMbObject::massLoadFwdRef($prats, "function_id");

// R�cup�ration des commentaires
$commentaire = new CCommentairePlanning();
$where = array();

$where[] = "'$date_planning' BETWEEN date(debut) AND date(fin)";
$where["salle_id"] = CSQLDataSource::prepareIn($salles_ids);

$commentaires = $commentaire->loadList($where);

// R�cup�ration des plages op�ratoires
$plageop = new CPlageOp();
$where = array();

$where["date"] = " = '$date_planning'";
$where["salle_id"] = CSQLDataSource::prepareIn($salles_ids);

$plages = $plageop->loadList($where);

// Cr�ation du planning
$planning = new CPlanningWeek(0, 0, count($salles), count($salles), false, "auto");
$planning->title =  "Planning du ".CMbDT::format($date_planning, "%A %d %B %Y");


//load the current bloc
if (isset($current_bloc)) {
  $planning->title .= " - $current_bloc->nom";
}

$planning->guid = "planning_interv";
$planning->hour_min  = str_pad(CAppUI::conf("reservation debut_planning"), 2, 0, STR_PAD_LEFT);
$planning->dragndrop = $planning->resizable = CCanDo::edit() ? 1 : 0; //hack for "false => 0"
$planning->hour_divider = 12;
$planning->show_half = true;
$i = 0;
$today = CMbDT::date();

foreach ($salles as $_salle) {
  $label_day = $bloc_id ? $_salle->_shortview : str_replace("-", "<br/>", $_salle->_view);
  $planning->addDayLabel($i, $label_day, null, null,null, true, array("salle_id" => $_salle->_id));

  if ($today == $date_planning) {
    $planning->addEvent(new CPlanningEvent(null, "$i ".CMbDT::time(), null, null, "red", null, "now"));
  }
  $i++;
}

// Tri des op�rations par salle
$operations_by_salle = array();
foreach ($operations as $key => $_operation) {
  /** @var COperation $_operation */
  if (!$_operation->salle_id) {
    unset($operations[$key]);
    continue;
  }
  
  if (!isset($operations_by_salle[$_operation->salle_id])) {
    $operations_by_salle[$_operation->salle_id] = array();
  }
  $operations_by_salle[$_operation->salle_id][] = $_operation;
}

// Tri des commentaires par salle
$commentaires_by_salle = array();
foreach ($commentaires as $key => $_commentaire) {
  /** @var CCommentairePlanning $_commentaire */
  $salle_id = $_commentaire->salle_id;
  if (!isset($commentaires_by_salle[$salle_id])) {
    $commentaires_by_salle[$salle_id] = array();
  }
  $commentaires_by_salle[$salle_id][] = $_commentaire;
}

// Tri des plages par salle
$plages_by_salle = array();
CMbObject::massLoadFwdRef($plages, "chir_id");
CMbObject::massLoadFwdRef($plages, "spec_id");

foreach ($plages as $_plage) {
  /** @var CPlageOp $_plage */
  $_plage->loadRefChir();
  $_plage->loadRefSpec();
  $_plage->loadRefsOperations();
  $salle_id = $_plage->salle_id;
  if (!isset($plages_by_salle[$salle_id])) {
    $plages_by_salle[$salle_id] = array();
  }
  $plages_by_salle[$salle_id][] = $_plage;

  //load operation in salle
  foreach ($_plage->_ref_operations as $_op) {
    if ($praticien_id != $_op->chir_id && $praticien_id != "") {
      continue;
    }
    if (!$show_cancelled) {
      if (!$_op->annulee) {
        $operations_by_salle[$salle_id][] = $_op;
      }
    }
    else {
        $operations_by_salle[$salle_id][] = $_op;
    }
  }
}

// Ajout des �v�nements (op�rations)
$can_edit = CCanDo::edit();

$diff_hour_urgence = CAppUI::conf("reservation diff_hour_urgence");

//prestations
$prestations_journalieres = CPrestationJournaliere::loadCurrentList();
$prestation_id   = CAppUI::pref("prestation_id_hospi");

if ($show_operations) {
  /** @var $_operation COperation */
  foreach ($operations_by_salle as $salle_id => $_operations) {
    $i = array_search($salle_id, $salles_ids);
    foreach ($_operations as $_operation) {
      $_operation->_ref_salle = $_operation->loadFwdRef("salle_id");

      $first_log = $_operation->loadFirstLog();

      $_operation->loadRefAffectation();
      $lit = $_operation->_ref_affectation->_ref_lit;
      $chir    = $_operation->loadRefChir();
      $chir->loadRefFunction();
      $chir->getBasicInfo();
      $chir_2  = $_operation->loadRefChir2();
      $chir_2->loadRefFunction();
      $chir_3  = $_operation->loadRefChir3();
      $chir_3->loadRefFunction();
      $chir_4  = $_operation->loadRefChir4();
      $chir_4->loadRefFunction();

      $anesth  = $_operation->_ref_anesth = $_operation->loadFwdRef("anesth_id");
      $sejour  = $_operation->loadRefSejour();
      $charge = $sejour->loadRefChargePriceIndicator();
      $sejour->loadLiaisonsForPrestation("all");
      $patient = $sejour->loadRefPatient();
      $patient->loadRefDossierMedical();
      $patient->_ref_dossier_medical->countAllergies();
      $patient->_ref_dossier_medical->loadRefsAntecedents();
      $besoins = $_operation->loadRefsBesoins();


      //liaisons
      $liaison_sejour = "";
      foreach ($sejour->_liaisons_for_prestation as $_liaison) {
        if ($date_planning >= $_liaison->date && ($_liaison->_ref_item->_id)) {
          $liaison_sejour .= $_liaison->_ref_item->nom;
          $liaison_sejour .= " | ";
        }
      }

      //en plage & non valid�, skip
      if ($_operation->plageop_id && !$_operation->rank) {
        continue;
      }

      $offset_bottom = 0;
      $offset_top    = 0;

      if (!$anesth->_id) {
        $anesth = $_operation->loadFwdRef("anesth_id", true);
      }

      //best time (horaire voulu / time_operation
      $horaire = CMbDT::time($_operation->_datetime_best);
      $debut = "$i {$horaire}";
      $debut_op = $horaire;
      $fin_op = CMbDT::addTime($_operation->temp_operation, $horaire);
      $duree = CMbDT::minutesRelative($horaire, $fin_op);


      // pr� op
      if ($_operation->presence_preop) {
        $hour_debut_preop = CMbDT::subTime($_operation->presence_preop, $_operation->time_operation);
        $offset_top = CMbDT::minutesRelative($hour_debut_preop, $_operation->time_operation);
        $duree = $duree + $offset_top;
        $debut = "$i $hour_debut_preop";
      }

      //post op
      if ($_operation->presence_postop) {
        $hour_fin_postop = CMbDT::addTime($_operation->presence_postop, $fin_op);
        $offset_bottom = CMbDT::minutesRelative($fin_op, $hour_fin_postop);
        $duree = $duree + $offset_bottom;
      }

      //factures
      $sejour->loadRefsFactureEtablissement();
      $facture = $sejour->_ref_last_facture;

      //antecedants
      $count_atcd = 0;
      foreach ($patient->_ref_dossier_medical->_ref_antecedents_by_type as $_type => $_atcd) {
        if ($_type != "alle") {
          $count_atcd += count($_atcd);
        }
      }

      //besoins
      if (count($besoins)) {
        CMbObject::massLoadFwdRef($besoins, "type_ressource_id");
        foreach ($besoins as $_besoin) {
          $_besoin->loadRefTypeRessource();
        }
      }

      //template de contenu
      $smarty = new CSmartyDP("modules/reservation");
      $smarty->assign("operation"           , $_operation);
      $smarty->assign("patient"             , $patient);
      $smarty->assign("sejour"              , $sejour);
      $smarty->assign("liaison_sejour"      , $liaison_sejour);
      $smarty->assign("charge"              , $charge);
      $smarty->assign("facture"             , $facture);
      $smarty->assign("debut_op"            , $debut_op);
      $smarty->assign("fin_op"              , $fin_op);
      $smarty->assign("lit"                 , $lit);
      $smarty->assign("chir"                , $chir);
      $smarty->assign("chir_2"              , $chir_2);
      $smarty->assign("chir_3"              , $chir_3);
      $smarty->assign("chir_4"              , $chir_4);
      $smarty->assign("anesth"              , $anesth);
      $smarty->assign("count_atcd"          , $count_atcd);
      $smarty->assign("besoins"             , $besoins);
      $smarty->assign("interv_en_urgence"   , abs(CMbDT::hoursRelative("$_operation->date $debut_op", $first_log->date)) <= $diff_hour_urgence);
      $smartyL = $smarty->fetch("inc_planning/libelle_plage.tpl");
      //@todo : UGLY, find a global better way !
      $smartyL = htmlspecialchars_decode(CMbString::htmlEntities($smartyL, ENT_NOQUOTES), ENT_NOQUOTES);

      // couleurs
      $color = CAppUI::conf("hospi colors default");
      $important = true;
      $css = null;
      if ($sejour->annule) {
        $css = "hatching";
        $important = false;
      }
      else {
        switch ($sejour->recuse) {
          case "0":
              $color = CAppUI::conf("hospi colors $sejour->type");
            break;
          case "-1" :
            $color = CAppUI::conf("hospi colors recuse");
            $css = "recuse";
            break;
        }
      }

      $event = new CPlanningEvent($_operation->_guid, $debut, $duree, $smartyL, "#$color", $important, $css, $_operation->_guid, false);

      if ($can_edit) {
        $event->addMenuItem("edit" , "Modifier cette intervention");
        $event->addMenuItem("cut"  , "Couper cette intervention");
        $event->addMenuItem("copy" , "Copier cette intervention");
        $event->addMenuItem("clock", utf8_encode("Modifier les dates d'entr�e et sortie du s�jour"));
      }

      if ($offset_bottom) {
        $event->offset_bottom = $offset_bottom;
        $event->offset_bottom_text = "Post op";
      }

      if ($offset_top) {
        $event->offset_top = $offset_top;
        $event->offset_top_text = "Pre op";
      }

      $event->plage["id"] = $_operation->_id;
      $event->type = "operation_horsplage";
      $event->draggable = $event->resizable = CCanDo::edit();
      if ($_operation->rank) {
        $event->type = "operation_enplage";
        $event->draggable = false;
      }

      $planning->addEvent($event);
    }
  }
}

// Ajout des �v�nements (commentaires)
foreach ($commentaires_by_salle as $salle_id => $_commentaires) {
  $i = array_search($salle_id, $salles_ids);
  
  foreach ($_commentaires as $_commentaire) {
    $debut = "$i ".CMbDT::time($_commentaire->debut);
    
    $duree = CMbDT::minutesRelative(CMbDT::time($_commentaire->debut), CMbDT::time($_commentaire->fin));
    
    $libelle = "<span
    style='display: none;'
    data-entree_prevue='$_commentaire->debut'
    data-sortie_prevue='$_commentaire->fin'
    data-libelle='$_commentaire->libelle'
    data-commentaire='$_commentaire->commentaire'
    data-duree='$duree'
    data-color='$_commentaire->color'></span>".
    "<span style='font-size: 11px; font-weight: bold;'>".CMbString::htmlEntities($_commentaire->libelle)."</span>".
    "\n<span class='compact'>".CMbString::htmlEntities($_commentaire->commentaire)."</span>";

    $event = new CPlanningEvent($_commentaire->_guid, $debut, $duree, $libelle, "#$_commentaire->color", true, null, $_commentaire->_guid, false);
    
    $event->type = "commentaire_planning";
    $event->draggable = $event->resizable = CCanDo::edit();
    $event->plage["id"] = $_commentaire->_id;
    
    if ($can_edit) {
      $event->addMenuItem("edit"    , "Modifier ce commentaire");
      $event->addMenuItem("copy"    , "Copier ce commentaire");
      $event->addMenuItem("cancel"  , "Supprimer ce commentaire");
    }
    
    $planning->addEvent($event);
  }
}

// Ajout des plages
foreach ($plages_by_salle as $salle_id => $_plages) {
  $i = array_search($salle_id, $salles_ids);
  
  foreach ($_plages as $_plage) {

    $validated = count($_plage->loadRefsOperations(false, null, true, true));
    $total = count($_plage->loadRefsOperations(false));
    $_plage->loadRefAnesth();

    $debut = "$i ".CMbDT::time($_plage->debut);
    
    $duree = CMbDT::minutesRelative(CMbDT::time($_plage->debut), CMbDT::time($_plage->fin));
    
    $libelle = CMbString::htmlEntities($_plage->chir_id ? $_plage->_ref_chir->_view : $_plage->_ref_spec->_view);
    $libelle.= "\n ( $validated / $total)";
    if ($_plage->_ref_anesth->_id) {
      $libelle.= "<hr/> <img src='images/icons/anesth.png'/> $_plage->_ref_anesth";
    }

    $event = new CPlanningEvent($_plage->_guid, $debut, $duree, $libelle, "#efbf99", true, null, $_plage->_guid, false);

    $event->below = true;
    $event->type = "plage_planning";
    $event->plage["id"] = $_plage->_id;

    if ($can_edit) {
      $event->addMenuItem("edit" , utf8_encode("Modifier cette plage"));
      $event->addMenuItem("list" , utf8_encode("Gestion des interventions"));
    }

    $planning->addEvent($event);
  }
}

$m = $save_m;

$planning->allow_superposition = true;
$planning->rearrange(); //ReArrange the planning

$smarty = new CSmartyDP();
$smarty->assign("planning",             $planning);
$smarty->assign("salles"  ,             $salles);
$smarty->assign("salles_ids",           $salles_ids);
$smarty->assign("date_planning",        $date_planning);
$smarty->assign("scroll_top",           $scroll_top);
$smarty->assign("show_cancelled",       $show_cancelled);
$smarty->assign("show_operations",      $show_operations);
$smarty->assign("bloc_id",              $bloc_id );
$smarty->assign("prestations",          $prestations_journalieres);
$smarty->assign("height_planning_resa", CAppUI::pref("planning_resa_height", 1500));

$smarty->assign("nbIntervNonPlacees",   $nbIntervNonPlacees);
$smarty->assign("nbIntervHorsPlage" ,   $nbIntervHorsPlage );
$smarty->assign("nbAlertesInterv",      $nbAlertesInterv);
$smarty->display("inc_vw_planning.tpl");
