<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Fabien M�nager
*/

global $m;

$user = CMediusers::get();

if(!$user->isMedical() &&
   !CModule::getCanDo('soins')->read && 
   !CModule::getCanDo('dPurgences')->read && 
   !CModule::getCanDo('dPcabinet')->edit){
     CModule::getCanDo($m)->redirect();
}

$context_guid          = CValue::get('context_guid');
$selected_context_guid = CValue::get('selected_context_guid', $context_guid);
$patient_id            = CValue::get('patient_id');
$readonly              = CValue::get('readonly');
$selection             = CValue::get('selection');
$date_min              = CValue::get('date_min');
$date_max              = CValue::get('date_max');
$print                 = CValue::get('print');
$limit                 = CValue::get('limit', 50);

if (!$selection || $selected_context_guid === 'all') {
  //$selection = CConstantesMedicales::$list_constantes;
  $conf_constantes = explode("|", CConstantesMedicales::getConfig("important_constantes"));
  $selection = array_intersect_key(CConstantesMedicales::$list_constantes, array_flip($conf_constantes));
}
else {
  $selection_flip = array_flip($selection);
  $selection = array_intersect_key(CConstantesMedicales::$list_constantes, $selection_flip);
}

$constants_to_draw = ($print == 1 ? $selection : CConstantesMedicales::$list_constantes);

if ($selected_context_guid !== 'all')
  $context = CMbObject::loadFromGuid($selected_context_guid);
else
  $context = CMbObject::loadFromGuid($context_guid);
  
$context->loadRefs();

if ($context) {
  $patient = $context->_ref_patient;
}

if ($patient_id) {
  $patient = new CPatient;
  $patient->load($patient_id);
}

$latest_constantes = $patient->loadRefConstantesMedicales();
$patient->loadRefPhotoIdentite();

$where = array(
  "patient_id" => " = '$patient->_id'"
);

// Construction d'une constante m�dicale
$constantes = new CConstantesMedicales();
$constantes->patient_id = $patient->_id;
$constantes->loadRefPatient();

// Les constantes qui correspondent (dans le contexte ou non)
$where_context = $where;
$where_context["context_class"] = "IS NOT NULL";
$where_context["context_id"] = "IS NOT NULL";

$query = new CRequest;
$query->addTable($constantes->_spec->table);
$query->addColumn("context_class");
$query->addColumn("context_id");
$query->addWhere($where_context);
$query->addGroup(array("context_class", "context_id"));

$query = $query->getRequest();
$list = $constantes->_spec->ds->loadList($query);
$list_contexts = array();

foreach($list as $_context) {
  $c = new $_context["context_class"];
  $c = $c->getCached($_context["context_id"]);
  if ($c instanceof CConsultation && $c->sejour_id) continue; // Cas d'un RPU
  $c->loadRefsFwd();
  $list_contexts[$c->_guid] = $c;
}

$current_context = CMbObject::loadFromGuid($context_guid);
$current_context->loadComplete();

// Cas d'un RPU
if ($current_context instanceof CConsultation && $current_context->sejour_id) {
  $current_context->loadRefSejour();
  $current_context = $current_context->_ref_sejour;
  $current_context->loadComplete();
  $context = $current_context;
  $context_guid = $current_context->_guid;
}
if (!isset($list_contexts[$current_context->_guid])){
  $list_contexts[$current_context->_guid] = $current_context;
}

if (!count($list_contexts)) {
  $list_contexts[] = $current_context;
}

if ($context && $selected_context_guid !== 'all') {
  $where["context_class"] = " = '$context->_class'";
  $where["context_id"] = " = '$context->_id'";
  
  // Needed to know if we are in the right context
  $constantes->context_class = $context->_class;
  $constantes->context_id = $context->_id;
  $constantes->loadRefContext();
}

$whereOr = array();
foreach($constants_to_draw as $name => $params) {
  if ($name[0] === "_") continue;
  $whereOr[] = "$name IS NOT NULL ";
}
$where[] = implode(" OR ", $whereOr);

if ($date_min) {
  $where[] = "datetime >= '$date_min'";
}

if ($date_max) {
  $where[] = "datetime <= '$date_max'";
}

// Les constantes qui correspondent (dans le contexte cette fois)
$list_constantes = $constantes->loadList($where, "datetime DESC", $limit);
$list_constantes = array_reverse($list_constantes, true);

$constantes_medicales_grid = CConstantesMedicales::buildGrid($list_constantes, false);

$standard_struct = array(
  "series" => array(
    array(
      "data" => array(),
      //"options" => array()
    )
  )
);

// Petite fonction utilitaire de r�cup�ration des valeurs
function getValue($v) {
  return ($v === null) ? null : floatval($v);
}

function getMax($n, $array) {
  $orig_n = $n;
  
  if (substr($n, 0, 1) == "@") {
    $n = -10e6;
  }
  
  $max = $n;
  foreach ($array as $a) 
    if (isset($a[1])) $max = max($n, $a[1], $max);
    
  if ($orig_n != $n) {
    $max += floatval(substr($orig_n, 1));
  }
  
  return $max;
}

function getMin($n, $array) {
  $orig_n = $n;
  
  if (substr($n, 0, 1) == "@") {
    $n = +10e6;
  }
  
  $min = $n;
  foreach ($array as $a)
    if (isset($a[1])) $min = min($n, $a[1], $min);
    
  if ($orig_n != $n) {
    $min += floatval(substr($orig_n, 1));
  }
  
  return $min;
}

$dates     = array();
$hours     = array();
$comments  = array();
$const_ids = array();
$data      = array();
$graphs    = array();

foreach ($constants_to_draw as $name => $params) {
  if ($name[0] === "_" && empty($params["plot"])) continue;
  
  $data[$name] = $standard_struct;
  
  if (isset($params["formfields"]) && empty($params["candles"])) {
    $serie = &$data[$name]["series"];
    
    $serie = array();
    foreach($params["formfields"] as $_field) {
      $serie[] = array(
        "data" => array(),
        "label" => CAppUI::tr("CConstantesMedicales-$_field-court"),
      );
    }
  }
}

$cumuls_day = array();

// Si le s�jour a des constantes m�dicales
if ($list_constantes) {
  foreach ($list_constantes as $cst) {
    $comment = utf8_encode($cst->comment);
    $dates[] = mbTransformTime($cst->datetime, null, '%d/%m/%y');
    $hours[] = mbTransformTime($cst->datetime, null, '%Hh%M');
    $comments[] = $comment;
    $const_ids[] = $cst->_id;
    $cst->loadLogs();
    
    foreach ($constants_to_draw as $name => $params) {
      if ($name[0] === "_" && empty($params["plot"])) continue;
      
      $candles = isset($params["candles"]);
      
      $d = &$data[$name];

      $user_view = "";
      
      if ($name[0] !== "_") {
        $log = $cst->loadLastLogForField($name);
        if (!$log->_id && $cst->_ref_last_log) {
          $log = $cst->_ref_last_log;
        }
        $log->loadRefsFwd();
        
        if ($log->_ref_user) {
          $user_view = utf8_encode($log->_ref_user->_view);
        }
      }
      
      // normal plots
      if (empty($params["candles"])) {
        if (isset($params["formfields"])) {
          $fields = $params["formfields"];
        }
        else {
          $fields = array($name);
        }
        
        $i = count($d["series"][0]["data"]);
        foreach($fields as $n => $_field) {
          $ya = $yb = $yc = $yd = null;
          
          $ya = getValue($cst->$_field);
            
          $d["series"][$n]["data"][] = array(
            $i, 
            $ya, $yb, $yc, $yd,
            $user_view, 
            $comment,
            utf8_encode($params['unit']),
          );
          
          if (isset($params["cumul_reset_config"])) {
            $reset_hour = CConstantesMedicales::getResetHour($name);
            $day_24h = mbTransformTime("-$reset_hour hours", $cst->datetime, '%d/%m/%y');
    
            if (!isset($cumuls_day[$name][$day_24h])) {
              $cumuls_day[$name][$day_24h] = array("n" => 0, "value" => 0);
            }
            
            $cumuls_day[$name][$day_24h]["value"] += $ya;
            $cumuls_day[$name][$day_24h]["n"]++;
          }
        }
      }
        
      // composite plots (TA)
      else {
        $fields = $params["formfields"];
        $first = true;
        
        $i = count($d["series"][0]["data"]);
        foreach($fields as $n => $_field) {
          $ya = $yb = $yc = $yd = null;
          
          // first series : all values
          if ($first) {
            $ya = $yb = getValue($cst->{$fields[1]});
            $yc = $yd = getValue($cst->{$fields[0]});
            
            $d["series"][$n]["candles"]["show"] = true;
            $d["series"][$n]["markers"]["position"] = "cb";
          }
          
          // second series : only second value
          else {
            $ya = getValue($cst->$fields[0]);
            $d["series"][$n]["markers"]["position"] = "ct";
          }
          
          $d["series"][$n]["markers"]["show"] = true;
          $d["series"][$n]["lines"]["show"] = false;
          $d["series"][$n]["points"]["show"] = false;
            
          $d["series"][$n]["data"][] = array(
            $i, 
            $ya, $yb, $yc, $yd,
            $user_view, 
            $comment,
            utf8_encode($params['unit']),
          );
          
          $first = false;
        }
      }
     
      $graphs[] = "constantes-medicales-$name";
    }
  }
}

// Pour les tensions art�rielles, changer les unit�s suivant la config
$unite_ta = CAppUI::conf("dPpatients CConstantesMedicales unite_ta");

foreach($cumuls_day as $name => $days) {
  $_data = &$data[$name];
  
  $offset = 0;
  foreach($days as $day => $values) {
    $_data["series"][] = array(
      "data" => array(array(
        $offset-0.5, 
        $values["value"], 
        utf8_encode(CAppUI::tr("CConstantesMedicales-$name-desc")), 
        null,
        utf8_encode(CValue::read(CConstantesMedicales::$list_constantes[$name], "unit")),
      )),
      "cumul" => $day,
      "lines" => array("show" => false),
      "points" => array("show" => false),
      "markers" => array(
        "show" => true,
        "position" => "rm",
      ),
      "bars" => array(
        "show" => true,
        "barWidth" => $values["n"],
        "centered" => false,
        "lineWidth" => 1,
      ),
      "color" => "#4DA74D",
      "mouse" => array(
        "relative" => false,
        "position" => "nw",
      ),
    );
    
    $offset += $values["n"];
  }
  
  $first = array_shift($_data["series"]);
  array_push($_data["series"], $first);
}

foreach($data as $name => &$_data) {
  $params = CConstantesMedicales::$list_constantes[$name];
  
  // And the options
  if (isset($params["standard"])) {
    $_data["standard"] = $params["standard"];
  }
  
  $margin_ratio = 0.1;
  
  if (in_array($name, array("ta", "ta_gauche", "ta_droit"))) {
    if (isset($params["conversion"][$unite_ta]) && ($unite_ta != $params["conversion"][$unite_ta])) {
      $_data["standard"] *= $params["conversion"][$unite_ta];
    }
    
    $margin_ratio = 0.3;
    $params['unit'] = $unite_ta;
  }
  
  $all_y_values = CMbArray::pluck($_data["series"], "data");
  $y_values = array();
  
  foreach($all_y_values as $_values) {
    $y_values = array_merge($y_values, $_values);
  }
  
  $margin = abs($params["min"] - $params["max"]) * $margin_ratio;
  
  $_data["options"] = array(
    "title" => utf8_encode(CAppUI::tr("CConstantesMedicales-$name-desc").($params['unit'] ? " ({$params['unit']})" : "")),
    "yaxis" => array(
      "min" => getMin($params["min"], $y_values) - $margin, // min
      "max" => getMax($params["max"], $y_values) + $margin, // max
    )
  );
  
  if (isset($params["colors"])) {
    $_data["options"]["colors"] = $params["colors"];
  }
}

//mbTrace($data);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign('readonly',      $readonly);
$smarty->assign('constantes',    $constantes);
$smarty->assign('context',       $context);
$smarty->assign('context_guid',  $context_guid);
$smarty->assign('list_contexts', $list_contexts);
$smarty->assign('all_contexts',  $selected_context_guid == 'all');
$smarty->assign('patient',       $patient);
$smarty->assign('data',          $data);
$smarty->assign('dates',         $dates);
$smarty->assign('hours',         $hours);
$smarty->assign('comments',      $comments);
$smarty->assign('const_ids',     $const_ids);
$smarty->assign('latest_constantes', $latest_constantes);
$smarty->assign('selection',     $selection);
$smarty->assign('print',         $print);
$smarty->assign('graphs',        $graphs);
$smarty->assign('limit',         $limit);
$smarty->assign('constantes_medicales_grid', $constantes_medicales_grid);
$smarty->display('inc_vw_constantes_medicales.tpl');

?>