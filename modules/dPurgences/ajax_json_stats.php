<?php /* $Id: vw_stats.php 7207 2009-11-03 12:03:30Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision: 7207 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

CMbObject::$useObjectCache = false;

$axe    = CValue::getOrSession('axe');
$entree = CValue::getOrSession('entree', mbDate());
$period = CValue::getOrSession('period', "MONTH");
$count = CValue::getOrSession('count', 30);

function computeAttente($areas, &$series, $where, $ljoin, $dates, $period, $sejour, &$total, $start_field, $end_field) {
  foreach($areas as $key => $value) {
    $where["rpu.$start_field"] = $value["rpu.$start_field"];
    $where["rpu.$end_field"] = $value["rpu.$end_field"];
    $series[$key] = array('data' => array(), "label" => $value[0]);
    
    foreach ($dates as $i => $_date) {
      $_date_next = mbDate("+1 $period", $_date);
      $where['sejour.entree'] = "BETWEEN '$_date' AND '$_date_next'";
      $count = $sejour->countList($where, null, null, null, $ljoin);
      $total += $count;
      $series[$key]['data'][$i] = array($i, intval($count));
    }
  }
  
  // Time
  $areas = array_merge(array(null));
  foreach($areas as $key => $value) {
    $key = count($series);
    $where["rpu.$start_field"] = "IS NOT NULL";
    $where["rpu.$end_field"] = "IS NOT NULL";
    $series[$key] = array(
      'data' => array(), 
      'yaxis' => 2, 
      'lines' => array("show" => true),
      'points' => array("show" => true),
      'mouse' => array("track" => true),
      'label' => "Temps",
      "shadowSize" => 0
    );
    
    foreach ($dates as $i => $_date) {
      $_date_next = mbDate("+1 $period", $_date);
      $where['sejour.entree'] = "BETWEEN '$_date' AND '$_date_next'";
      $_sejours = $sejour->loadList($where, null, null, null, $ljoin);
      // FIXME
      
      $times = array();
      foreach($_sejours as $_sejour) {
        $_sejour->loadRefRPU();
        $_rpu = $_sejour->_ref_rpu;
        $times[] = mbMinutesRelative($_rpu->$start_field, $_rpu->$end_field);
      }
      $count = array_sum($times);
      $mean = count($times) ? $count / count($times) : 0;
      
      $variance = 0;
      foreach($times as $time) {
        $variance += pow($time - $mean, 2);
      }
      $variance /= count($times);
      $std_dev = sqrt($variance);
      
      $series[$key]['data'][$i] = array($i, intval($count));
      
      // mean - std_dev
      if (!isset($series[$key+1])) {
        $series[$key+1] = $series[$key];
        $series[$key+1]["color"] = "#666";
        $series[$key+1]["lines"]["lineWidth"] = 1;
        $series[$key+1]["points"]["show"] = false;
        $series[$key+1]["label"] = null;
      }
      $series[$key+1]['data'][$i] = array($i, intval($count)-$std_dev);
      
      // mean + std_dev
      if (!isset($series[$key+2])) {
        $series[$key+2] = $series[$key];
        $series[$key+2]["color"] = "#666";
        $series[$key+2]["lines"]["lineWidth"] = 1;
        $series[$key+2]["points"]["show"] = false;
        $series[$key+2]["label"] = null;
      }
      $series[$key+2]['data'][$i] = array($i, intval($count)+$std_dev);
    }
  }
  
  // Echange du dernier et tu premier des lignes pour avoir celle du milieu en avant plan
  $c = count($series);
  list($series[$c-3], $series[$c-1]) = array($series[$c-1], $series[$c-3]);
}

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
$n = min($count, 120);
while ($n--) {
  $dates[] = $date;
  $date = mbDate("-1 $period", $date);
}

$dates = array_reverse($dates);

$group = CGroups::loadCurrent();

$where = array(
  "sejour.entree" => null, // Doit toujours etre redefini
  "sejour.type" => "= 'urg'",
  "sejour.group_id" => "= '$group->_id'",
  "rpu.rpu_id" => "IS NOT NULL",
);

$ljoin = array(
  'rpu' => 'sejour.sejour_id = rpu.sejour_id',
);

$rpu = new CRPU();
$sejour = new CSejour();
$total = 0;

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
        $total += $count;
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
        $total += $count;
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
        $total += $count;
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
        $total += $count;
        $series[$key]['data'][$i] = array($i, intval($count));
      }
    }
    break;
    
  // S�jour sans RPU
  case "without_rpu":
    $data[$axe] = array(
      "options" => array(
        "title" => utf8_encode("S�jours d'urgence sans RPU")
      ),
      "series" => array()
    );
    
    $series = &$data[$axe]['series'];
    $areas = array_merge(array(null));
    foreach($areas as $key => $value) {
      $where["rpu.rpu_id"] = "IS NULL";
      $series[$key] = array('data' => array());
      
      foreach ($dates as $i => $_date) {
        $_date_next = mbDate("+1 $period", $_date);
        $where['sejour.entree'] = "BETWEEN '$_date' AND '$_date_next'";
        $count = $sejour->countList($where, null, null, null, $ljoin);
        $total += $count;
        $series[$key]['data'][$i] = array($i, intval($count));
      }
    }
    break;
    
    
  // Nombre de transferts
  case "transfers_count":
    $data[$axe] = array(
      "options" => array(
        "title" => utf8_encode("Nombre de transferts")
      ),
      "series" => array()
    );
    
    $series = &$data[$axe]['series'];
    
    $etab_externe = new CEtabExterne;
    $etabs = $etab_externe->loadList(null, "nom");
    
    $key = 0;
    foreach($etabs as $_id => $_etab) {
      $where["sejour.etablissement_transfert_id"] = "IS NOT NULL";
      $series[$key] = array('data' => array(), 'label' => utf8_encode($_etab->_view));
      
      $sub_total = 0;
      foreach ($dates as $i => $_date) {
        $_date_next = mbDate("+1 $period", $_date);
        $where['sejour.entree'] = "BETWEEN '$_date' AND '$_date_next'";
        $where['sejour.etablissement_transfert_id'] = "= '$_id'";
        $count = $sejour->countList($where, null, null, null, $ljoin);
        $total += $count;
        $sub_total += $count;
        $series[$key]['data'][$i] = array($i, intval($count));
      }
      $series[$key]['subtotal'] = $sub_total;
      $key++;
    }
    
    // suppression des series vides
    foreach($series as $_key => $_serie) {
      if ($_serie['subtotal'] == 0) {
        unset($series[$_key]);
      }
    }
    $series = array_values($series);
    break;
    
  // Nombre de mutations
  case "mutations_count":
    $data[$axe] = array(
      "options" => array(
        "title" => utf8_encode("Nombre de mutations")
      ),
      "series" => array()
    );
    
    $series = &$data[$axe]['series'];
    
    $service = new CService;
    $service->group_id = CGroups::loadCurrent()->_id;
    $services = $service->loadMatchingList("nom");
    
    $key = 0;
    foreach($services as $_id => $_service) {
      $where["sejour.service_mutation_id"] = "IS NOT NULL";
      $series[$key] = array('data' => array(), 'label' => utf8_encode($_service->_view));
      
      $sub_total = 0;
      foreach ($dates as $i => $_date) {
        $_date_next = mbDate("+1 $period", $_date);
        $where['sejour.entree'] = "BETWEEN '$_date' AND '$_date_next'";
        $where['sejour.service_mutation_id'] = "= '$_id'";
        $count = $sejour->countList($where, null, null, null, $ljoin);
        $total += $count;
        $sub_total += $count;
        $series[$key]['data'][$i] = array($i, intval($count));
      }
      $series[$key]['subtotal'] = $sub_total;
      $key++;
    }
    
    // suppression des series vides
    foreach($series as $_key => $_serie) {
      if ($_serie['subtotal'] == 0) {
        unset($series[$_key]);
      }
    }
    $series = array_values($series);
    break;
    
  // Radio
  case "radio":
    $data[$axe] = array(
      "options" => array(
        "title" => utf8_encode("Attente radio"),
        "yaxis" => array("title" => "Nombre"),
        "y2axis" => array("title" => "Temps"),
      ),
      "series" => array()
    );
    
    $series = &$data[$axe]['series'];
    $areas = array(
      array(
        "Sans radio",
        "rpu.radio_debut" => "IS NULL",
        "rpu.radio_fin"   => "IS NULL",
      ),
      array(
        "Attente radio sans retour",
        "rpu.radio_debut" => "IS NOT NULL",
        "rpu.radio_fin"   => "IS NULL", // FIXME: prendre en charge les attentes de moins d'une minute
      ),
      array(
        "Attente radio avec retour",
        "rpu.radio_debut" => "IS NOT NULL",
        "rpu.radio_fin"   => "IS NOT NULL",
      ),
    );
    computeAttente($areas, $series, $where, $ljoin, $dates, $period, $sejour, $total, "radio_debut", "radio_fin");
    break;
    
  // Biolo
  case "bio":
    $data[$axe] = array(
      "options" => array(
        "title" => utf8_encode("Attente biologie")
      ),
      "series" => array()
    );
    
    $series = &$data[$axe]['series'];
    $areas = array(
      array(
        "Sans biologie",
        "rpu.bio_depart" => "IS NULL",
        "rpu.bio_retour" => "IS NULL",
      ),
      array(
        "Attente biologie sans retour",
        "rpu.bio_depart" => "IS NOT NULL",
        "rpu.bio_retour" => "IS NULL", // FIXME: prendre en charge les attentes de moins d'une minute
      ),
      array(
        "Attente biologie avec retour",
        "rpu.bio_depart" => "IS NOT NULL",
        "rpu.bio_retour" => "IS NOT NULL",
      ),
    );
    
    computeAttente($areas, $series, $where, $ljoin, $dates, $period, $sejour, $total, "bio_depart", "bio_retour");
    break;
    
  // Sp�
  case "spe":
    $data[$axe] = array(
      "options" => array(
        "title" => utf8_encode("Attente sp�cialiste")
      ),
      "series" => array()
    );
    
    $series = &$data[$axe]['series'];
    $areas = array(
      array(
        "Sans sp�cialiste",
        "rpu.specia_att" => "IS NULL",
        "rpu.specia_arr" => "IS NULL",
      ),
      array(
        "Attente sp�cialiste sans retour",
        "rpu.specia_att" => "IS NOT NULL",
        "rpu.specia_arr" => "IS NULL", // FIXME: prendre en charge les attentes de moins d'une minute
      ),
      array(
        "Attente sp�cialiste avec retour",
        "rpu.specia_att" => "IS NOT NULL",
        "rpu.specia_arr" => "IS NOT NULL",
      ),
    );
    
    computeAttente($areas, $series, $where, $ljoin, $dates, $period, $sejour, $total, "specia_att", "specia_arr");
    break;
   
}

// Ticks
$ticks = array();
foreach ($dates as $i => $_date) {
  $ticks[$i] = array($i, mbTransformTime(null, $_date, $format));
}

$group_view = utf8_encode($group->_view);

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
  $_data["options"]["subtitle"] = "$group_view - Total: $total";
  
  $totals = array();
  foreach($_data["series"] as &$_series) {
    if (isset($_series["lines"]["show"]) && $_series["lines"]["show"]) {
      $_series["bars"]["show"] = false;
      continue;
    }
    
    foreach($_series["data"] as $key => $value) {
      if (!isset($totals[$key][0])) {
        $totals[$key][0] = $key;
        $totals[$key][1] = 0;
      }
      $totals[$key][1] += $value[1];
    }
  }
  
  $_data["series"][] = array(
    "data" => $totals,
    "label" => "Total",
    "bars" => array("show" => false),
    "lines" => array("show" => false),
    "markers" => array("show" => true),
  );
}

CApp::json($data, "text/javascript");
