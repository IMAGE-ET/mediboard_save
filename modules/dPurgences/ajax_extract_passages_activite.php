<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

CApp::setMemoryLimit("512M");

$now = CValue::get("debut_selection", CMbDT::dateTime());

$date_tolerance = CAppUI::conf("dPurgences date_tolerance");
$date_before = CMbDT::date("-$date_tolerance DAY", $now);
$date_after  = CMbDT::date("+1 DAY", $now);

$group_id = CGroups::loadCurrent()->_id;

$extractPassages = new CExtractPassages();
$extractPassages->debut_selection = $now;
$extractPassages->fin_selection   = $now;
$extractPassages->type            = "activite";
$extractPassages->group_id        = CGroups::loadCurrent()->_id;
$extractPassages->date_extract    = $now;

$datas = array(
  "PRESENTS"    => 0,
  "ATTENTE"     => 0,
  "AVAL"        => 0,
  "BOX"         => 0,
  "DECHOC"      => 0,
  "PORTE"       => 0,
  "RADIO"       => 0,
  "MAXPATIENTS" => CAppUI::conf("cerveau max_patient"),
  "TOTBOX"      => 0,
  "TOTDECHOC"   => 0,
  "TOTPORTE"    => 0
);

// Chargement des rpu de la main courante
$sejour = new CSejour;
$where = array();
$ljoin["rpu"] = "sejour.sejour_id = rpu.sejour_id";

$where[] = "sejour.entree BETWEEN '$now' AND '$date_after'
  OR (sejour.sortie_reelle IS NULL AND sejour.entree BETWEEN '$date_before' AND '$date_after' AND sejour.annule = '0')";

// RPUs
$where[]                  = "rpu.rpu_id IS NOT NULL";
$where["sejour.group_id"] = "= '$group_id'";
//$where["sejour.type"]     = "= 'urg'";
$order                    = "sejour.entree ASC";

/** @var CSejour[] $sejours */
$sejours = $sejour->loadList($where, $order, null, "sejour.sejour_id", $ljoin);

$count_sejour = 0;
//work
foreach ($sejours as $_sejour) {
  if ($_sejour->sortie_reelle) {
    continue;
  }

  $count_sejour++;

  $affectation = $_sejour->getCurrAffectation($now);

  //total
  $datas['PRESENTS']++;

  $rpu = $_sejour->loadRefRPU();

  $comptabilise = false;
  //plac�
  if ($affectation->_id) {
    $lit = $affectation->loadRefLit();
    $chambre = $lit->loadRefChambre();
    if ($chambre->_id) {
      // salle d'attente
      if ($chambre->is_waiting_room) {
        $datas["ATTENTE"]++;
        $comptabilise = true;
      }

      //salle d'examen
      if ($chambre->is_examination_room) {
        $datas["BOX"]++;
        $comptabilise = true;
      }

      if ($chambre->is_sas_dechoc) {
        $datas["DECHOC"]++;
        $comptabilise = true;
      }
    }
  }

  if (!$comptabilise) {
    $consult = $rpu->loadRefConsult();

    if (!$consult || !$consult->_id) {
      $datas["ATTENTE"]++;
    }
  }

  //mutation
  if ($rpu->mutation_sejour_id) {
    $datas["AVAL"]++;
  }

  //porte
  if ($_sejour->UHCD) {
    $datas["PORTE"]++;
  }

  //radio
  if ($rpu->radio_debut && !$rpu->radio_fin) {
    $datas['RADIO']++;
  }
}

//totaux

$lit = new CLit();
$where = array();
$ljoin = array();
$ljoin["chambre"] = "lit.chambre_id = chambre.chambre_id";
$ljoin["service"] = "service.service_id = chambre.service_id";
$where["service.externe"] = "= '0'";
$where["service.uhcd"] = "= '1'";
$where["service.group_id"] = " = '$group_id'";
$where["lit.annule"] = " = '0'";

//uhcd
$datas["TOTPORTE"] = $lit->countList($where, null, $ljoin);

//urgences
$where["service.uhcd"] = " IS NOT NULL";
$where["service.urgence"] = "= '1'";

$where["chambre.is_examination_room"] = " = '1'";
$datas["TOTBOX"] = $lit->countList($where, null, $ljoin);

$where["chambre.is_examination_room"] = " IS NOT NULL";
$where["chambre.is_sas_dechoc"] = " = '1'";
$datas["TOTDECHOC"] = $lit->countList($where, null, $ljoin);

// Appel de la fonction d'extraction du RPUSender
$rpuSender = $extractPassages->getRPUSender();
if (!$rpuSender) {
  CAppUI::stepAjax("Aucun sender d�finit dans le module dPurgences.", UI_MSG_ERROR);
}
$extractPassages = $rpuSender->extractActivite($extractPassages, $datas);

CAppUI::stepAjax("Extraction de $count_sejour s�jours du $now termin�e.", UI_MSG_OK);
if (!$extractPassages->message_valide) {
  CAppUI::stepAjax("Le document produit n'est pas valide.", UI_MSG_WARNING);
}
else {
  CAppUI::stepAjax("Le document produit est valide.", UI_MSG_OK);
}

echo "<script>extract_passages_id = $extractPassages->_id;</script>";

