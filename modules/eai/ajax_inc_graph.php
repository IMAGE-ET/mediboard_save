<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage eai
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

CCanDo::checkAdmin(); 

//CmBObject::$useObjectCache = false;
 
$criteres         = CValue::get("selected_criteres", array());
$exchanges        = CValue::get("selected_exchanges", array());
$date_production  = CValue::getOrSession("date_production", mbDate());
$period           = CValue::getOrSession('period', "DAY");
$count            = CValue::getOrSession('count', 30);
$group_id         = CValue::getOrSession("group_id", CGroups::loadCurrent()->_id);
$mode             = CValue::GetOrSession("mode");

/* Initialisation du graphiques */
$options = array();

/* D�finition des options */
$options["legend"] = array("show" => true, "position" => "nw");
$options["grid"] = array("verticalLines" => false, "backgroundColor" => "#FFFFFF");
$options["mouse"] = array("relative" => true, "position" => "ne");
$options["yaxis"] = array("min" => 0, "autoscaleMargin" => 1);
$options["y2axis"] = array("min" => 0, "autoscaleMargin" => 1);
$options["xaxis"] = array("labelsAngle" => 45, "ticks" => array());
$options["HtmlText"] = false;
$options["spreadsheet"] = array("show" => true, "tabGraphLabel" => "Graphique", "tabDataLabel" => "Donn&eacute;es", 
                                                    "toolbarDownload" => "T&eacute;l&eacute;charger le fichir CSV", 
                                                    "toolbarSelectAll" => "S&eacute;lectionner le tableau",
                                                    "csvFileSeparator" => ";", "decimalSeparator" => ",");
if($mode == "lines") {
  $options["lines"] = array("show" => true, "lineWidth" => 2);
} else {
  $options["bars"] = array("show" => true, "barWidth" => 1, "fill" => true, "fillOpacity" => 0.4, "stacked" => false,
                                               "centered" => true);
}
$options["title"] = "Echanges";

switch ($period) {
  default: $period = "DAY";
  
  case "DAY":
    $format = "%d/%m/%Y";
    break;
    
  case "WEEK";
    $format = "%W";
    $date_production = mbDate("+1 day last sunday", $date_production);
    break;
    
  case "MONTH";
    $format = "%m/%Y";
    $date_production = mbDate("first day", $date_production);
    break;
} 

// Dates
$dates = array();
$date = $date_production;
$n = min($count, 120);
while ($n--) {
  $dates[] = $date;
  $date = mbDate("+1 $period", $date);
}

foreach ($dates as $i => $_date) {
  $options["xaxis"]["ticks"][$i] = array($i, mbTransformTime(null, $_date, $format));
}

$where = array();

if (isset($criteres["emetteur"])) {
  $where["sender_id"] = " IS NULL";
}
if (isset($criteres["destinataire"])) {
  mbTrace("destinataire checked");
  $where["receiver_id"] = " IS NULL";
}
if (isset($criteres["message_invalide"])) {
  $where["message_valide"] = " = '0'";
}
if (isset($criteres["acquittement_invalide"])) {
  $where["acquittement_valide"] = " = '0'";
}
if (isset($criteres["no_date_echange"])) {
  $where["date_echange"] = " IS NULL";
}

$where["group_id"] = " = '$group_id'";

$series = array();
$i = 0;
foreach ($exchanges as $_sub_class => $_child_classes) {
  foreach ($_child_classes as $_child_class) {
    $exchange = new $_child_class;
    $series[$i] = array("data" => array(), "label" => utf8_encode(CAppUI::tr($_child_class)));
    foreach ($dates as $j => $_date) {
      $_date_next = mbDate("+1 $period", $_date);
      $where["date_production"] = " BETWEEN '$_date' AND '$_date_next'";
      
      $count = $exchange->countList($where, null, null);
      $series[$i]["data"][$j] = array($j, $count);
    }
    $i++;
  }
}

$smarty = new CSmartyDP();
$smarty->assign("options", $options);
$smarty->assign("series", $series);
$smarty->display("inc_graph.tpl");
?>