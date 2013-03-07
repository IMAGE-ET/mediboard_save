<?php  
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPhospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

CCanDo::checkRead();

// R�cup�ration des param�tres
$date           = CValue::getOrSession("date", CMbDT::dateTime());
$services_ids   = CValue::getOrSession("services_ids");

$group_id           = CGroups::loadCurrent()->_id;
$pref_services_ids  = json_decode(CAppUI::pref("services_ids_hospi"));

// Si la pr�f�rence existe, alors on la charge
if (isset($pref_services_ids->{"g$group_id"})) {
  $services_ids = $pref_services_ids->{"g$group_id"};
  if ($services_ids) {
    $services_ids = explode("|", $services_ids); 
  }
}
// Sinon, chargement de la liste des services en accord avec le droit de lecture
else {
  $service = new CService();
  $where = array();
  $where["group_id"]  = "= '".CGroups::loadCurrent()->_id."'";
  $where["cancelled"] = "= '0'";
  $services_ids = array_keys($service->loadListWithPerms(PERM_READ, $where, "externe, nom"));
}

if (is_array($services_ids)) {
  CMbArray::removeValue("", $services_ids);
}

if (!$services_ids) {
  $smarty = new CSmartyDP;
  $smarty->display("inc_no_services.tpl");
  CApp::rip();
}

$service = new CService();
$services = $service->loadAll($services_ids);

$services_noms = array();
foreach ($services as $serv) {
  $services_noms[$serv->_id] = $serv->nom;
}
$chambres = array();
$grilles = array();
$ensemble_lits_charges = array();

$conf_nb_colonnes = CAppUI::conf("dPhospi nb_colonnes_vue_topologique");

foreach ($services as $serv) {  
  $grille = null;
  $grille = array_fill(0, $conf_nb_colonnes, array_fill(0, $conf_nb_colonnes, 0));
  
  $chambres = $serv->loadRefsChambres();
    
  foreach ($chambres as $ch) {
    $ch->loadRefEmplacement();
    if ($ch->_ref_emplacement->_id) {
     $ch->loadRefsLits();
     foreach ($ch->_ref_lits as $lit) {
       $ensemble_lits_charges[$lit->_id] =0;
     }
     $grille[$ch->_ref_emplacement->plan_y][$ch->_ref_emplacement->plan_x] = $ch;
     $emplacement = $ch->_ref_emplacement;
      if ($emplacement->hauteur-1) {
        for ($a = 0; $a <= $emplacement->hauteur-1; $a++) {
          if ($emplacement->largeur-1) {
           for ($b = 0; $b <= $emplacement->largeur-1; $b++) {
             if ($b!=0) {
               unset($grille[$emplacement->plan_y+$a][$emplacement->plan_x+$b]);
             }
             elseif ($a!=0) {
               unset($grille[$emplacement->plan_y+$a][$emplacement->plan_x+$b]);
             }
           }
          }
          elseif ($a < $emplacement->hauteur-1) {
            $c = $a+1;
            unset($grille[$emplacement->plan_y+$c][$emplacement->plan_x]);
          }
        }
      }
      elseif ($emplacement->largeur-1) {
        for ($b = 1; $b <= $emplacement->largeur-1; $b++) {
          unset($grille[$emplacement->plan_y][$emplacement->plan_x+$b]);
        }
      } 
    }
  }
  
  //Traitement des lignes vides
    $nb;  $total;
  foreach ($grille as $j => $value) {
    $nb=0;
    foreach ($value as $i => $valeur) {
      if ($valeur=="0") {
        if ($j==0 || $j==9) {
          $nb++;
        }
        elseif (!isset($grille[$j-1][$i]) || $grille[$j-1][$i]=="0" || !isset($grille[$j+1][$i]) || $grille[$j+1][$i]=="0" ) {
          $nb++;
        }
      }
    }
    //suppression des lignes inutiles
    if ($nb==$conf_nb_colonnes) {
      unset($grille[$j]);
    }
  }
  
  //Traitement des colonnes vides
  for ($i=0;$i<$conf_nb_colonnes;$i++) {
    $nb=0;
    $total=0;
    for ($j=0;$j<$conf_nb_colonnes;$j++) {
      $total++;
      if (!isset($grille[$j][$i]) || $grille[$j][$i]=="0") {
        if ($i == 0 || $i == 9) {
          $nb++;
        }
        elseif ((!isset($grille[$j][$i-1]) || $grille[$j][$i-1]=="0") || (!isset($grille[$j][$i+1]) || $grille[$j][$i+1]=="0")) {
          $nb++;
        }
      }
    }
    //suppression des colonnes inutiles
    if ($nb==$total) {
      for ($a=0;$a<$conf_nb_colonnes;$a++) {
       unset($grille[$a][$i]);
      }
    }
  }
  $grilles[$serv->_id] = $grille;
}

$date_min = CMbDT::dateTime($date);
$date_max = CMbDT::dateTime("+1 day", $date_min);

$listAff = array();

// Chargement des affectations ayant pour lit une chambre plac�es sur le plan
$affectation = new CAffectation();
$where = array(
  "affectation.entree"  => "<= '$date_max'",
  "affectation.sortie"  => ">= '$date_min'",
  "affectation.lit_id"  => CSQLDataSource::prepareIn(array_keys($ensemble_lits_charges), null)
);

$listAff = $affectation->loadList($where);

$sejours = CMbObject::massLoadFwdRef($listAff, "sejour_id");
CMbObject::massLoadFwdRef($sejours, "patient_id");

foreach ($listAff as &$_aff) {
  $_aff->loadView();
  $_aff->loadRefSejour();
  $_aff->_ref_sejour->checkDaysRelative($date);
  $_aff->_ref_sejour->loadRefPatient()->loadRefDossierMedical(false);
}

$dossiers = CMbArray::pluck($listAff, "_ref_sejour", "_ref_patient", "_ref_dossier_medical");
CDossierMedical::massCountAntecedentsByType($dossiers, "deficience");

$listNotAff = array(
  "Non plac�s" => array(),
  "Couloir" => array()
);

$group = CGroups::loadCurrent();
// Chargement des sejours n'ayant pas d'affectation pour cette p�riode
$sejour = new CSejour();
$where = array();
$where["entree_prevue"] = "<= '$date_max'";
$where["sortie_prevue"] = ">= '$date_min'";
$where["annule"] = " = '0' ";
$where["group_id"] = "= '$group->_id'";

$listNotAff["Non plac�s"] = $sejour->loadList($where);

foreach ($listNotAff["Non plac�s"] as $key => $_sejour) {
  $_sejour->loadRefsAffectations();
  if (!empty($_sejour->_ref_affectations)) {
    unset($listNotAff["Non plac�s"][$key]);
  }
  else {
    $_sejour->loadRefPatient();
  }
  $_sejour->checkDaysRelative($date);
}

// Chargement des affectations dans les couloirs (sans lit_id)
$where = array();
$ljoin = array();
$where["lit_id"] = "IS NULL";
$where["service_id"] = CSQLDataSource::prepareIn($services_ids);
$where["entree"] = "<= '$date_max'";
$where["sortie"] = ">= '$date_min'";

$affectation = new CAffectation();
$listNotAff["Couloir"] = $affectation->loadList($where, "entree ASC", null, null, $ljoin);

foreach ($listNotAff["Couloir"] as $_aff) {
  $_aff->loadView();
  $_aff->loadRefSejour();
  $_aff->_ref_sejour->checkDaysRelative($date);
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("chambres"              , $chambres);
$smarty->assign("date"                  , $date);
$smarty->assign("chambres_affectees"    , $listAff);
$smarty->assign("list_patients_notaff"  , $listNotAff);
$smarty->assign("services"              , $services_noms);
$smarty->assign("grilles"               , $grilles);

$smarty->display("vw_placement_patients.tpl");
?>