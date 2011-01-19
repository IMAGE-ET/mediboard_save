<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function graphPatJourSalle($debut = null, $fin = null, $prat_id = 0, $salle_id = 0, $bloc_id = 0, $discipline_id = null, $codeCCAM = '') {
  if (!$debut) $debut = mbDate("-1 YEAR");
  if (!$fin) $fin = mbDate();
  
  $prat = new CMediusers;
  $prat->load($prat_id);
  
  $salle = new CSalle;
  $salle->load($salle_id);
  
  $discipline = new CDiscipline;
  $discipline->load($discipline_id);

  $ticks = array();
  for($i = $debut; $i <= $fin; $i = mbDate("+1 MONTH", $i)) {
    $ticks[] = array(count($ticks), mbTransformTime("+0 DAY", $i, "%m/%Y"));
  }

  $where = array();
  $where['stats'] = " = '1'";
  if($salle_id) {
    $where['salle_id'] = " = '$salle_id'";
  } elseif($bloc_id) {
    $where['bloc_id'] = "= '$bloc_id'";
  }
  
  $salles = $salle->loadList($where);
  $series = array();
  $serie = array('data' => array());

  $query = "SELECT COUNT(operations.operation_id) AS total,
    COUNT(DISTINCT(plagesop.date)) AS nb_days,
    COUNT(DISTINCT(sallesbloc.salle_id)) AS nb_salles,
    DATE_FORMAT(plagesop.date, '%m/%Y') AS mois,
    DATE_FORMAT(plagesop.date, '%Y-%m-01') AS orderitem
    FROM operations
    INNER JOIN sallesbloc ON operations.salle_id = sallesbloc.salle_id
    LEFT JOIN plagesop ON operations.plageop_id = plagesop.plageop_id
    LEFT JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
    WHERE 
      sallesbloc.stats = '1' AND 
      plagesop.date BETWEEN '$debut' AND '$fin' AND 
      operations.annulee = '0'";

  $query_hors_plage = "SELECT COUNT(operations.operation_id) AS total,
    COUNT(DISTINCT(operations.date)) AS nb_days,
    COUNT(DISTINCT(sallesbloc.salle_id)) AS nb_salles,
    DATE_FORMAT(operations.date, '%m/%Y') AS mois,
    DATE_FORMAT(operations.date, '%Y-%m-01') AS orderitem
    FROM operations
    INNER JOIN sallesbloc ON operations.salle_id = sallesbloc.salle_id
    LEFT JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
    WHERE 
      sallesbloc.stats = '1' AND
      operations.date IS NOT NULL AND
      operations.plageop_id IS NULL AND
      operations.date BETWEEN '$debut' AND '$fin' AND 
      operations.annulee = '0'";
  
  if($prat_id) {
    $query .= "\nAND operations.chir_id = '$prat_id'";
    $query_hors_plage .= "\nAND operations.chir_id = '$prat_id'"; 
  }
  
  if($discipline_id) {
    $query .= "\nAND users_mediboard.discipline_id = '$discipline_id'"; 
    $query_hors_plage .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
  }
  
  if($codeCCAM) {
    $query .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
    $query_hors_plage .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
  }

  if($salle_id) {
    $query .= "\nAND sallesbloc.salle_id = '$salle_id'";
    $query_hors_plage .= "\nAND sallesbloc.salle_id = '$salle_id'";
  } elseif($bloc_id) {
    $query .= "\nAND sallesbloc.bloc_id = '$bloc_id'";
    $query_hors_plage .= "\nAND sallesbloc.bloc_id = '$bloc_id'";
  }
  
  $query .= "\nGROUP BY mois ORDER BY orderitem";
  $query_hors_plage .= "\nGROUP BY mois ORDER BY orderitem";

  $result = $prat->_spec->ds->loadlist($query);
  $result_hors_plage = $prat->_spec->ds->loadlist($query_hors_plage);
  
  foreach($ticks as $i => $tick) {
    $f = true;
    foreach($result as $r) {
      if($tick[1] == $r["mois"]) {
        $calcul_hors_plage = 0;
        foreach($result_hors_plage as $key => $rb) {
          if ($tick[1] == $rb["mois"]) {
            $calcul_hors_plage = $rb["total"]/($rb["nb_days"]*$rb["nb_salles"]);
            unset($result_hors_plage[$key]);
            break;
          }
        }
        $serie['data'][] = array($i, $r["total"]/($r["nb_days"]*$r["nb_salles"]) + $calcul_hors_plage);
        $f = false;
      }
    }
    if($f) $serie["data"][] = array(count($serie["data"]), 0);
  }

  $series[] = $serie;
  
  // Set up the title for the graph
  $title = "Patients / jour / salle active dans le mois";
  $subtitle = "Uniquement les jours d'activit�";
  if($prat_id)       $subtitle .= " - Dr $prat->_view";
  if($discipline_id) $subtitle .= " - $discipline->_view";
  if($salle_id)      $subtitle .= " - $salle->nom";
  if($codeCCAM)      $subtitle .= " - CCAM : $codeCCAM";

  $options = array(
    'title' => utf8_encode($title),
    'subtitle' => utf8_encode($subtitle),
    'xaxis' => array('labelsAngle' => 45, 'ticks' => $ticks),
    'yaxis' => array('autoscaleMargin' => 1, 'min' => 0),
    'lines' => array('show' => true, 'filled' => true, 'fillColor' => '#999'),
    'markers' => array('show' => true),
    'points' => array('show' => true),
    'HtmlText' => false,
    'legend' => array('show' => true, 'position' => 'nw'),
    'mouse' => array('track' => true, 'relative' => true, 'position' => 'ne'),
    'spreadsheet' => array(
      'show' => true,
      'csvFileSeparator' => ';',
      'decimalSeparator' => ',',
      'tabGraphLabel' => utf8_encode('Graphique'),
      'tabDataLabel' => utf8_encode('Donn�es'),
      'toolbarDownload' => utf8_encode('Fichier CSV'),
      'toolbarSelectAll' => utf8_encode('S�lectionner tout le tableau')
    )
  );
  
  return array('series' => $series, 'options' => $options);
}