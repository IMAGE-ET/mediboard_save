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
$count  = CValue::getOrSession('count', 30);

function computeAttente($areas, &$series, $where, $ljoin, $dates, $period, $sejour, &$total, $start_field, $end_field) {
  $only_duration = empty($areas);
  
  // never when ljoin on consult (form field)
  if (strpos($start_field, "._") === false)
    $where[$start_field] = "IS NOT NULL";
    
  if (strpos($end_field, "._") === false)
    $where[$end_field] = "IS NOT NULL";
      
  if (!$only_duration) {
    foreach($areas as $key => $value) {
      
      // never when ljoin on consult (form field)
      if (isset($value[$start_field]) && strpos($start_field, "._") === false)
        $where[$start_field] = $value[$start_field];
        
      if (isset($value[$end_field]) && strpos($end_field, "._") === false)
        $where[$end_field] = $value[$end_field];
      
      $series[$key] = array('data' => array(), "label" => utf8_encode($value[0]));
      
      foreach ($dates as $i => $_date) {
        $_date_next = mbDate("+1 $period", $_date);
        $where['sejour.entree'] = "BETWEEN '$_date' AND '$_date_next'";
        $count = $sejour->countList($where, null, null, null, $ljoin);
        $total += $count;
        $series[$key]['data'][$i] = array($i, intval($count));
      }
    }
  }
  
  // Time
  $areas = array_merge(array(null));
  foreach($areas as $key => $value) {
    $key = count($series);
    
    $series[$key] = array(
      'data' => array(), 
      'yaxis' => ($only_duration ? 1 : 2), 
      'lines' => array("show" => true),
      'points' => array("show" => true),
      'mouse' => array("track" => true, "trackFormatter" => "timeLabelFormatter"),
      'label' => ($only_duration ? "" : "Temps"),
      'color' => "red",
      "shadowSize" => 0,
    );
    
    foreach ($dates as $i => $_date) {
      $_date_next = mbDate("+1 $period", $_date);
      $where['sejour.entree'] = "BETWEEN '$_date' AND '$_date_next'";
      $_sejours = $sejour->loadList($where, null, null, null, $ljoin);
      // FIXME
      
      $times = array();
      foreach($_sejours as $_sejour) {
        list($_start_class, $_start_field) = explode(".", $start_field);
        list($_end_class,   $_end_field)   = explode(".", $end_field);
        
        // load RPU
        if ($_start_class == "rpu" || $_end_class == "rpu") {
          $_sejour->loadRefRPU();
          $_rpu = $_sejour->_ref_rpu;
        }
        
        // load consult
        if ($_start_class == "consultation" || $_end_class == "consultation") {
          $_sejour->loadRefsConsultations();
          $_consult = $_sejour->_ref_consult_atu;
          if (!$_consult || !$_consult->heure) continue;
          $_consult->loadRefPlageConsult();
          if (!$_consult->_date) continue;
        }
        
        switch($_start_class) {
          case "sejour":       $_start_object = $_sejour; break;
          case "rpu":          $_start_object = $_rpu; break;
          case "consultation": $_start_object = $_consult; break;
        }
        
        switch($_end_class) {
          case "sejour":       $_end_object = $_sejour; break;
          case "rpu":          $_end_object = $_rpu; break;
          case "consultation": $_end_object = $_consult; break;
        }
        
        $start = $_start_object->$_start_field;
        $end   = $_end_object->$_end_field;
        
        if ($start && $end)
          $times[] = mbMinutesRelative($start, $end);
      }
      $count = array_sum($times);
      $mean = count($times) ? $count / count($times) : 0;
      
      $variance = 0;
      foreach($times as $time) {
        $variance += pow($time - $mean, 2);
      }
      if (count($times)) $variance /= count($times);
      $std_dev = sqrt($variance);
      
      $series[$key]['data'][$i] = array($i, $mean);
      
      // mean - std_dev
      if (!isset($series[$key+1])) {
        $series[$key+1] = $series[$key];
        $series[$key+1]["color"] = "#666";
        $series[$key+1]["lines"]["lineWidth"] = 1;
        $series[$key+1]["points"]["show"] = false;
        $series[$key+1]["label"] = null;
      }
      $series[$key+1]['data'][$i] = array($i, $mean-$std_dev);
      
      // mean + std_dev
      if (!isset($series[$key+2])) {
        $series[$key+2] = $series[$key];
        $series[$key+2]["color"] = "#666";
        $series[$key+2]["lines"]["lineWidth"] = 1;
        $series[$key+2]["points"]["show"] = false;
        $series[$key+2]["label"] = null;
      }
      $series[$key+2]['data'][$i] = array($i, $mean+$std_dev);
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
    
    $etabs["none"] = new CEtabExterne;
    $etabs["none"]->_view = "Non renseign�";
    
    $where["sejour.mode_sortie"] = "= 'transfert'";
    
    $key = 0;
    foreach($etabs as $_id => $_etab) {
      $series[$key] = array('data' => array(), 'label' => utf8_encode($_etab->_view));
      
      $sub_total = 0;
      foreach ($dates as $i => $_date) {
        $_date_next = mbDate("+1 $period", $_date);
        $where['sejour.entree'] = "BETWEEN '$_date' AND '$_date_next'";
        $where['sejour.etablissement_transfert_id'] = ($_id === "none" ? "IS NULL" : "= '$_id'");
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
    
    $services["none"] = new CService;
    $services["none"]->_view = "Non renseign�";
    
    $where["sejour.mode_sortie"] = "= 'mutation'";
      
    $key = 0;
    foreach($services as $_id => $_service) {
      $series[$key] = array('data' => array(), 'label' => utf8_encode($_service->_view));
      
      $sub_total = 0;
      foreach ($dates as $i => $_date) {
        $_date_next = mbDate("+1 $period", $_date);
        $where['sejour.entree'] = "BETWEEN '$_date' AND '$_date_next'";
        $where['sejour.service_mutation_id'] = ($_id === "none" ? "IS NULL" : "= '$_id'");
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
        "yaxis" => array("title" => "Nombre de passages"),
        "y2axis" => array("title" => "Temps (min.)"),
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
    computeAttente($areas, $series, $where, $ljoin, $dates, $period, $sejour, $total, "rpu.radio_debut", "rpu.radio_fin");
    break;
    
  // Biolo
  case "bio":
    $data[$axe] = array(
      "options" => array(
        "title" => utf8_encode("Attente biologie"),
        "yaxis" => array("title" => "Nombre de passages"),
        "y2axis" => array("title" => "Temps (min.)"),
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
    
    computeAttente($areas, $series, $where, $ljoin, $dates, $period, $sejour, $total, "rpu.bio_depart", "rpu.bio_retour");
    break;
    
  // Sp�
  case "spe":
    $data[$axe] = array(
      "options" => array(
        "title" => utf8_encode("Attente sp�cialiste"),
        "yaxis" => array("title" => "Nombre de passages"),
        "y2axis" => array("title" => "Temps (min.)"),
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
    
    computeAttente($areas, $series, $where, $ljoin, $dates, $period, $sejour, $total, "rpu.specia_att", "rpu.specia_arr");
    break;
    
  case "duree_sejour":
    $data[$axe] = array(
      "options" => array(
        "title" => utf8_encode("Dur�e de s�jour"),
        "yaxis" => array("title" => "Nombre de passages"),
        "y2axis" => array("title" => "Temps (min.)"),
      ),
      "series" => array()
    );
    
    $series = &$data[$axe]['series'];
    computeAttente(array(array("Nombre de passages")), $series, $where, $ljoin, $dates, $period, $sejour, $total, "sejour.entree", "sejour.sortie");
    break;
    
  case "duree_pec":
    $data[$axe] = array(
      "options" => array(
        "title" => utf8_encode("Dur�e de prise en charge"),
        "yaxis" => array("title" => "Nombre de passages"),
        "y2axis" => array("title" => "Temps (min.)"),
      ),
      "series" => array()
    );
    
    $series = &$data[$axe]['series'];
    computeAttente(array(array("Nombre de passages")), $series, $where, $ljoin, $dates, $period, $sejour, $total, "consultation._datetime", "sejour.sortie");
    break;
    
  case "duree_attente":
    $data[$axe] = array(
      "options" => array(
        "title" => utf8_encode("Dur�e d'attente"),
        "yaxis" => array("title" => "Nombre de passages"),
        "y2axis" => array("title" => "Temps (min.)"),
      ),
      "series" => array()
    );
    
    $series = &$data[$axe]['series'];
    computeAttente(array(array("Nombre de passages")), $series, $where, $ljoin, $dates, $period, $sejour, $total, "sejour.entree", "consultation._datetime");
    break;
    
  case "diag_infirmier":

    $data[$axe] = array(
      'options' => array(
        'title' => utf8_encode('Par diagnostique infirmier')
      ),
      'series' => array()
    );
    $ds = CSQLDataSource::get("std");
    $rpu = new CRPU;
    $ljoin = array(
      'sejour' => 'rpu.sejour_id = sejour.sejour_id',
    );
    
    unset($where["sejour.type"]);
    
    $where["diag_infirmier"] = "IS NOT NULL";
    
    $group = CGroups::loadCurrent();
    $group_id = $group->_id;
    $where['sejour.group_id'] = " = '$group_id'";
    
    $where['sejour.entree'] = "BETWEEN '".reset($dates)."' AND '".end($dates)."'";
    $nb_rpus = $rpu->countList($where, null, null, null, $ljoin);

    $percent = CValue::get("_percent") * $nb_rpus;
    
    $percent = $percent / 100 ;
    
    $sql = "SELECT UPPER(TRIM(TRAILING '\r\n' from substring_index( `diag_infirmier` , '\\n', 1 ))) AS categorie, COUNT(*) as nb_diag
    FROM `rpu`
    LEFT JOIN `sejour` ON `rpu`.sejour_id = `sejour`.sejour_id
    WHERE `diag_infirmier` IS NOT NULL
    AND `group_id` = '$group_id'
    AND `entree` BETWEEN '".reset($dates)."' AND '".end($dates)."'
    GROUP BY categorie
    HAVING nb_diag > $percent";
 
    $result = $ds->exec($sql);
    $areas = array();
    while ($row = $ds->fetchArray($result)) {
      $areas[] = $row["categorie"];
    }
    
    $areas[] = "Autre";
    
    $data[$axe]["options"]["title"] .= utf8_encode(" (" . count($areas) . " cat�gories)");
    
    $series = &$data[$axe]['series'];
    
    $totaux = array();
    
    // On compte le nombre total de rpu par date
    foreach($dates as $i => $_date) {
      $_date_next = mbDate("+1 $period", $_date);
      $where['sejour.entree'] = "BETWEEN '$_date' AND '$_date_next'";
      $totaux[$i] = $rpu->countList($where, null, null, null, $ljoin);
    }
    
    foreach($areas as $key => $value) {
      unset($where[10]);
      $label = utf8_encode($value);
      $value = addslashes($value);
      $where[10] = "rpu.diag_infirmier LIKE '$value\n%'
        OR rpu.diag_infirmier LIKE '$value\n'
        OR rpu.diag_infirmier LIKE '$value'";

      $series[$key] = array('data' => array(), 'label' => $label);
      
      foreach ($dates as $i => $_date) {
        // Si la cat�gorie est autre, on lui affecte le total soustrait aux valeurs des autres cat�gories
        if ($value == "Autre") {
          $series[$key]['data'][$i] = array($i, $totaux[$i]);
          $total += $totaux[$i];
          continue;
        }
        $_date_next = mbDate("+1 $period", $_date);
        $where['sejour.entree'] = "BETWEEN '$_date' AND '$_date_next'";
        $count = $rpu->countList($where, null, null, null, $ljoin);
        $totaux[$i] -= $count;
        
        $total += $count;
        $series[$key]['data'][$i] = array($i, intval($count)); 
      }
    }
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
