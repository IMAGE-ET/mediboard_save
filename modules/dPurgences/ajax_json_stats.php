<?php /* $Id: vw_stats.php 7207 2009-11-03 12:03:30Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision: 7207 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$axe    = CValue::getOrSession('axe');
$entree = CValue::getOrSession('entree', mbDate());
$period = CValue::getOrSession('period', "MONTH");

switch ($period) {
  default: $period = "DAY";
  case "DAY":
  	$format = "%d/%m/%Y";
    break;
    
  case "WEEK";
    $format = "%W";
    $entree = mbDate("+1 day last sunday", $entree);
    break;
    
  case "MONTH";
    $format = "%m/%Y";
    $entree = mbDate("first day", $entree);
    break;
}

// Dates
$dates = array();
$date = $entree;
$n = 30;
while ($n--) {
  $dates[] = $date;
  $date = mbDate("-1 $period", $date);
}

$dates = array_reverse($dates);

$where = array(
  "sejour.entree" => null, // Doit toujours etre redefini
  "sejour.type" => "= 'urg'",
);

$ljoin = array(
  'rpu' => 'sejour.sejour_id = rpu.sejour_id',
);

$rpu = new CRPU();
$sejour = new CSejour();

$data = array();

switch ($axe) {
  default: $axe = "age";
   
  // Sur le patient
  case "age":
    $data[$axe] = array(
      'options' => array(
        'title' => utf8_encode('Par tranche d\'�ge')
      ),
      'series' => array()
    );
    
    $ljoin['patients'] = 'patients.patient_id = sejour.patient_id';
    
    $series = &$data[$axe]['series'];
    $age_areas = array(0, 1, 15, 75, 85);
    foreach($age_areas as $key => $age) {
      $limits = array($age, CValue::read($age_areas, $key+1));
      $label = $limits[1] ? ("$limits[0] - ".($limits[1]-1)) : ">= $limits[0]";
      
      $min = $limits[0]*365.25;
      $max = $limits[1]*365.25;
      
      $where[100] = "TO_DAYS(sejour.entree) - TO_DAYS(patients.naissance) >= $min";
      
      if ($limits[1] != null) {
        $where[101] = "TO_DAYS(sejour.entree) - TO_DAYS(patients.naissance) < $max";
      }
      else {
        unset($where[101]);
      }
      
      $series[$key] = array('data' => array(), 'label' => "$label ans");
      
      foreach ($dates as $i => $_date) {
        $_date_next = mbDate("+1 $period", $_date);
        $where['sejour.entree'] = "BETWEEN '$_date' AND '$_date_next'";
        $count = $sejour->countList($where, null, null, null, $ljoin);
        $series[$key]['data'][$i] = array($i, intval($count));
      }
    }
    break;
  
  // Sur le patient
  case "sexe":
    $data[$axe] = array(
      "options" => array(
        "title" => utf8_encode('Par sexe')
      ),
      "series" => array()
    );
    
    $ljoin['patients'] = 'patients.patient_id = sejour.patient_id';
    
    $series = &$data[$axe]['series'];
    $areas = array("m", "f");
    foreach($areas as $key => $value) {
      $label = utf8_encode(CAppUI::tr("CPatient.$axe.$value"));
      $where["patients.$axe"] = "= '$value'";
      $series[$key] = array('data' => array(), 'label' => $label);
      
      foreach ($dates as $i => $_date) {
        $_date_next = mbDate("+1 $period", $_date);
        $where['sejour.entree'] = "BETWEEN '$_date' AND '$_date_next'";
        $count = $sejour->countList($where, null, null, null, $ljoin);
        $series[$key]['data'][$i] = array($i, intval($count));
      }
    }
    break;
  
  // Sur le RPU
  case "ccmu":
  case "mode_entree":
  case "provenance":
  case "destination":
  case "orientation":
  case "transport":
  	
    $data[$axe] = array(
      "options" => array(
        "title" => utf8_encode(CAppUI::tr("CRPU-$axe"))
      ),
      "series" => array()
    );
    
    $series = &$data[$axe]['series'];
    $areas = array_merge(array(null), array_values($rpu->_specs[$axe]->_list));
    foreach($areas as $key => $value) {
      $label = utf8_encode(CAppUI::tr("CRPU.$axe.$value"));
      $where["rpu.$axe"] = (is_null($value) ? "IS NULL" : "= '$value'");
      $series[$key] = array('data' => array(), 'label' => $label);
      
      foreach ($dates as $i => $_date) {
        $_date_next = mbDate("+1 $period", $_date);
        $where['sejour.entree'] = "BETWEEN '$_date' AND '$_date_next'";
        $count = $sejour->countList($where, null, null, null, $ljoin);
        $series[$key]['data'][$i] = array($i, intval($count));
      }
    }
    break;
    
  // Sur le s�jour
  case "mode_sortie":
    $data[$axe] = array(
      "options" => array(
        "title" => utf8_encode(CAppUI::tr("CSejour-$axe"))
      ),
      "series" => array()
    );
    
    $series = &$data[$axe]['series'];
    $areas = array_merge(array(null), array_values($sejour->_specs[$axe]->_list));
    foreach($areas as $key => $value) {
      $label = utf8_encode(CAppUI::tr("CSejour.$axe.$value"));
      $where["sejour.$axe"] = (is_null($value) ? "IS NULL" : "= '$value'");
      $series[$key] = array('data' => array(), 'label' => $label);
      
      foreach ($dates as $i => $_date) {
        $_date_next = mbDate("+1 $period", $_date);
        $where['sejour.entree'] = "BETWEEN '$_date' AND '$_date_next'";
        $count = $sejour->countList($where, null, null, null, $ljoin);
        $series[$key]['data'][$i] = array($i, intval($count));
      }
    }
    break;
}


// Ticks
$ticks = array();
foreach ($dates as $i => $_date) {
  $ticks[$i] = array($i, mbTransformTime(null, $_date, $format));
}

foreach($data as &$_data) {
  $_data["options"] = CFlotrGraph::merge("bars", $_data["options"]);
  $_data["options"] = CFlotrGraph::merge($_data["options"], array(
    'colors' => array(
      /*"#1650A8", */"#2075F5", "#A89F16", "#F5C320", 
      "#027894", "#784DFF", "#BC772A", "#FF9B34", 
      "#00A080", "#8407E1", "#D04F3E", "#FF7348", 
      "#A89FC6", "#15C320", "#027804",
    ),
    'xaxis' => array('ticks' => $ticks, 'labelsAngle' => 45),
    'bars' => array('stacked' => true),
  ));
}

CApp::json($data, "text/javascript");
