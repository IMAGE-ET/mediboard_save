<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function graphActivite($debut = null, $fin = null, $prat_id = 0, $salle_id = 0, $bloc_id = 0, $discipline_id = 0, $codes_ccam = "", $type_hospi = "", $hors_plage) {
  if (!$debut) $debut = CMbDT::date("-1 YEAR");
  if (!$fin) $fin = CMbDT::date();
  
  $prat = new CMediusers;
  $prat->load($prat_id);
  
  $discipline = new CDiscipline;
  $discipline->load($discipline_id);

  $group_id = CGroups::loadCurrent()->_id;
  
  $salle = new CSalle;
  $salle->load($salle_id);
  
  $ticks = array();
  $serie_total = array(
    'label'   => 'Total',
    'data'    => array(),
    'markers' => array('show' => true),
    'bars'    => array('show' => false),
  );
  for ($i = $debut; $i <= $fin; $i = CMbDT::date("+1 MONTH", $i)) {
    $ticks[] = array(count($ticks), CMbDT::transform("+0 DAY", $i, "%m/%Y"));
    $serie_total['data'][] = array(count($serie_total['data']), 0);
  }
  
  $salles = CSalle::getSallesStats($salle_id, $bloc_id);
  $ds = $salle->_spec->ds;
  
  // Gestion du hors plage
  if($hors_plage) {
    $where_hors_plage = "AND (plagesop.date BETWEEN '$debut' AND '$fin'
                              OR operations.date BETWEEN '$debut' AND '$fin')";
  }
  else {
    $where_hors_plage = "AND plagesop.date BETWEEN '$debut' AND '$fin'
                         AND operations.date IS NULL
                         AND operations.plageop_id IS NOT NULL";
  }
  
  $total = 0;
  $series = array();
  foreach ($salles as $salle) {
    $serie = array(
      'label' => utf8_encode($bloc_id ? $salle->nom : $salle->_view),
      'data' => array()
    );
    
    $query = "SELECT COUNT(operations.operation_id) AS total,
      DATE_FORMAT(COALESCE(operations.date, plagesop.date), '%m/%Y') AS mois,
      DATE_FORMAT(COALESCE(operations.date, plagesop.date), '%Y%m') AS orderitem,
      sallesbloc.nom AS nom
      FROM operations
      LEFT JOIN sejour ON operations.sejour_id = sejour.sejour_id
      LEFT JOIN sallesbloc ON operations.salle_id = sallesbloc.salle_id
      LEFT JOIN plagesop ON operations.plageop_id = plagesop.plageop_id
      LEFT JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
      WHERE operations.annulee = '0'
        $where_hors_plage
        AND sejour.group_id = '$group_id'";
        
    if ($type_hospi)
      $query .= "\nAND sejour.type = '$type_hospi'";
    if ($prat_id && !$prat->isFromType(array("Anesth�siste")))
      $query .= "\nAND operations.chir_id = '$prat_id'";
    if ($prat_id && $prat->isFromType(array("Anesth�siste")))
      $query .= "\nAND (operations.anesth_id = '$prat_id' OR 
                       (plagesop.anesth_id = '$prat_id' AND (operations.anesth_id = '0' OR operations.anesth_id IS NULL)))";
    if ($discipline_id)
      $query .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
    if ($codes_ccam)
      $query .= "\nAND operations.codes_ccam LIKE '%$codes_ccam%'";
    $query .= "\nAND sallesbloc.salle_id = '$salle->_id'";
    $query .= "\nGROUP BY mois ORDER BY orderitem";
    
    $result = $ds->loadlist($query);
    
    foreach ($ticks as $i => $tick) {
      $f = true;
      foreach ($result as $r) {
        if ($tick[1] == $r["mois"]) {
          $serie["data"][] = array($i, $r["total"]);
          $serie_total["data"][$i][1] += $r["total"];
          $total += $r["total"];
          $f = false;
          break;
        }
      }
      if ($f) {
        $serie["data"][] = array(count($serie["data"]), 0);
      }
    }
    $series[] = $serie;
  }
  
  $series[] = $serie_total;
  
  // Set up the title for the graph
  if ($prat_id && $prat->isFromType(array("Anesth�siste"))) {
    $title = "Nombre d'anesth�sie par salle";
    $subtitle = "$total anesth�sies";
  }
  else {
    $title = "Nombre d'interventions par salle";
    $subtitle = "$total interventions";
  }

  if($prat_id)       $subtitle .= " - Dr $prat->_view";
  if($discipline_id) $subtitle .= " - $discipline->_view";
  if($codes_ccam)    $subtitle .= " - CCAM : $codes_ccam";
  if($type_hospi)    $subtitle .= " - ".CAppUI::tr("CSejour.type.$type_hospi");

  $options = array(
    'title' => utf8_encode($title),
    'subtitle' => utf8_encode($subtitle),
    'xaxis' => array('labelsAngle' => 45, 'ticks' => $ticks),
    'yaxis' => array('autoscaleMargin' => 1),
    'bars' => array('show' => true, 'stacked' => true, 'barWidth' => 0.8),
    'HtmlText' => false,
    'legend' => array('show' => true, 'position' => 'nw'),
    'grid' => array('verticalLines' => false),
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
