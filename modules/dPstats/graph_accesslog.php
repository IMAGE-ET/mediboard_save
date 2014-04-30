<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPstats
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

function graphAccessLog($module_name, $action_name, $startx, $endx, $interval = 'one-day', $left, $right, $DBorNotDB = false, $human_bot = null) {
  switch ($interval) {
    default:
    case "one-day":
      $step          = "+10 MINUTES";
      $period_format = "%H:%M";
      $hours         = 1/6;
      $ticks_modulo  = 3;
      break;

    case "one-week":
      $step          = "+1 HOUR";
      $period_format = "%a %d %Hh";
      $hours         = 1;
      $ticks_modulo  = 6;
      break;

    case "height-weeks":
      $step          = "+1 DAY";
      $period_format = "%d/%m";
      $hours         = 24;
      $ticks_modulo  = 2;
      break;

    case "one-year":
      $step          = "+1 WEEK";
      $period_format = "%Y S%U";
      $hours         = 24 * 7;
      $ticks_modulo  = 2;
      break;

    case "four-years":
      $step          = "+1 MONTH";
      $period_format = "%m/%Y";
      $hours         = 24 * 30;
      $ticks_modulo  = 2;
      break;

    case "twenty-years":
      $step          = "+1 YEAR";
      $period_format = "%Y";
      $hours         = 24 * 30 * 12;
      $ticks_modulo  = 1;
      break;
  }
  
  $datax = array();
  $i = 0;
  for ($d = $startx; $d <= $endx; $d = CMbDT::dateTime($step, $d)) {
    $datax[] = array($i, CMbDT::format($d, $period_format));
    $i++;
  }

  $logs = CAccessLog::loadPeriodAggregation($startx, $endx, $period_format, $module_name, $action_name, $DBorNotDB, $human_bot);

  if (!$DBorNotDB) {
    $duration    = array();
    $processus   = array();
    $processor   = array();
    $request     = array();
    $nb_requests = array();
    $peak_memory = array();
    $errors      = array();
    $warnings    = array();
    $notices     = array();
    
    $hits = array();
    $size = array();

    $datetime_by_index = array();
    
    $errors_total = 0;
    foreach ($datax as $x) {
      // Needed
      $duration[$x[0]]    = array($x[0], 0);
      $processus[$x[0]]   = array($x[0], 0);
      $processor[$x[0]]   = array($x[0], 0);
      $request[$x[0]]     = array($x[0], 0);
      $nb_requests[$x[0]] = array($x[0], 0);
      $peak_memory[$x[0]] = array($x[0], 0);
      $errors[$x[0]]      = array($x[0], 0);
      $warnings[$x[0]]    = array($x[0], 0);
      $notices[$x[0]]     = array($x[0], 0);
      
      $hits[$x[0]] = array($x[0], 0);
      $size[$x[0]] = array($x[0], 0);


      foreach ($logs as $log) {
        if ($x[1] == CMbDT::format($log->period, $period_format)) {
          $duration[$x[0]]    = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '').'duration'});
          $processus[$x[0]]   = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '').'processus'});
          $processor[$x[0]]   = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '').'processor'});
          $request[$x[0]]     = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '').'request'});
          $nb_requests[$x[0]] = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '').'nb_requests'});
          $peak_memory[$x[0]] = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '').'peak_memory'});
          $errors[$x[0]]      = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '').'errors'});
          $warnings[$x[0]]    = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '').'warnings'});
          $notices[$x[0]]     = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '').'notices'});
          $errors_total += $log->_average_errors + $log->_average_warnings + $log->_average_notices;
          
          $hits[$x[0]] = array($x[0], $log->{($right[1] == 'mean' ? '_average_' : '').'hits'} / ($right[1] == 'mean' ? $hours : 1));
          $size[$x[0]] = array($x[0], $log->{($right[1] == 'mean' ? '_average_' : '').'size'} / ($right[1] == 'mean' ? $hours : 1));

          $datetime_by_index[$x[0]] = $log->period;
        }
      }
    }

    // Removing some xaxis ticks
    foreach ($datax as $i => &$x) {
      if ($i % $ticks_modulo) {
        $x[1] = '';
      }
    }

    $title = '';
    if ($module_name) {
      $title .= CAppUI::tr("module-$module_name-court");
    }
    if ($action_name) {
      $title .= " - $action_name";
    }
    
    $subtitle = CMbDT::format($endx, CAppUI::conf("longdate"));
    
    $options = array(
      'title'    => utf8_encode($title),
      'subtitle' => utf8_encode($subtitle),
      'xaxis'    => array(
        'labelsAngle' => 45,
        'ticks'       => $datax,
      ),
      'yaxis' => array(
        'min'   => 0,
        'title' => utf8_encode(($left[0] == 'request_time' ? 'Temps de r�ponse' :
                                  ($left[0] == 'cpu_time' ? 'Temps CPU' :
                                    ($left[0] == 'errors' ? 'Erreurs' :
                                      ($left[0] == 'peak_memory' ? 'M�moire' : 'Requetes SQL')))) .
                                ($left[1] == 'mean' ? ' (par hit)' : '')),
        'autoscaleMargin' => 1
      ),
      'y2axis' => array(
        'min'   => 0,
        'title' => utf8_encode(($right[0] == 'hits' ? 'Hits' : 'Bande passante') .
                               ($right[1] == 'mean' ? (($right[0] == 'hits' ? ' (par minute)' : ' (octets/s)')) : '')),
        'autoscaleMargin' => 1
      ),
      'grid' => array(
        'verticalLines' => false
      ),
      /*'mouse' => array(
        'track' => true,
        'relative' => true
      ),*/
      'HtmlText'    => false,
      'spreadsheet' => array(
        'show'             => true,
        'csvFileSeparator' => ';',
        'decimalSeparator' => ','
      )
    );
    
    $series = array();
    
    // Right axis (before in order the lines to be on top)
    if ($right[0] == 'hits') {
      $series[] = array(
       'label' => 'Hits',
       'data'  => $hits,
       'bars'  => array(
         'show' => true
       ),
       'yaxis' => 2
      );
    }
    else {
      $series[] = array(
       'label' => 'Bande passante',
       'data'  => $size,
       'bars'  => array(
         'show' => true
       ),
       'yaxis' => 2
      );
    }
    
    // Left axis
    if ($left[0] == 'request_time') {
      $series[] = array(
       'label' => 'Page (s)',
       'data'  => $duration,
       'lines' => array(
         'show' => true
       ),
      );
        
      $series[] = array(
       'label' => 'DB (s)',
       'data'  => $request,
       'lines' => array(
         'show' => true
       ),
      );
    }
    elseif ($left[0] == 'cpu_time') {
      $series[] = array(
       'label' => 'Page (s)',
       'data'  => $duration,
       'lines' => array(
         'show' => true
       ),
      );
        
      $series[] = array(
       'label' => 'Process (s)',
       'data'  => $processus,
       'lines' => array(
         'show' => true
       ),
      );
        
      $series[] = array(
       'label' => 'CPU (s)',
       'data'  => $processor,
       'lines' => array(
         'show' => true
       ),
      );
        
      $series[] = array(
       'label' => 'DB (s)',
       'data'  => $request,
       'lines' => array(
         'show' => true
       ),
      );
    }
    elseif ($left[0] == 'errors') {
      if ($errors_total == 0) {
        $options['yaxis']['max'] = 1;
      }
      
      $series[] = array(
       'label' => 'Errors',
       'data'  => $errors,
       'color' => 'red',
       'lines' => array(
         'show' => true
       ),
      );
      
      $series[] = array(
       'label' => 'Warnings',
       'data'  => $warnings,
       'color' => 'orange',
       'lines' => array(
         'show' => true
       ),
      );
        
      $series[] = array(
       'label' => 'Notices',
       'data'  => $notices,
       'color' => 'yellow',
       'lines' => array(
         'show' => true
       ),
      );
    }
    elseif ($left[0] == 'memory_peak') {
      $series[] = array(
       'label' => 'Pic (byte)',
       'data'  => $peak_memory,
       'lines' => array(
         'show' => true
       ),
      );
    }
    else {
      $series[] = array(
        'label' => 'Requetes SQL',
        'data'  => $nb_requests,
        'lines' => array(
          'show' => true
        ),
      );

    }
  }
  else {
    $duration = array();
    $requests = array();

    $datetime_by_index = array();
    
    foreach ($datax as $x) {
      // Needed
      foreach ($logs as $log) {
        $duration[$log['datasource']][$x[0]] = array($x[0], 0);
        $requests[$log['datasource']][$x[0]] = array($x[0], 0);
      }
      
      foreach ($logs as $log) {
        if ($x[1] == CMbDT::format($log['period'], $period_format)) {
          $duration[$log['datasource']][$x[0]] = array($x[0], $log['duration']);
          $requests[$log['datasource']][$x[0]] = array($x[0], $log['requests']);

          $datetime_by_index[$x[0]] = $log['period'];
        }
      }
    }

    if ($interval == 'day') {
      foreach ($datax as $i => &$x) {
        if ($i % 4) {
          $x[1] = '';
        }
      }
    }
    
    if ($interval == 'month') {
      foreach ($datax as $i => &$x) {
        if ($i % 2) {
          $x[1] = '';
        }
      }
    }
    
    $title = '';
    if ($module_name) {
      $title .= CAppUI::tr("module-$module_name-court");
    }
    if ($action_name) {
      $title .= " - $action_name";
    }
    
    $subtitle = CMbDT::format($endx, CAppUI::conf("longdate"));
    
    $options = array(
      'title'    => utf8_encode($title),
      'subtitle' => utf8_encode($subtitle),
      'xaxis'    => array(
        'labelsAngle' => 45,
        'ticks'       => $datax,
      ),
      'yaxis' => array(
        'min'             => 0,
        'title'           => utf8_encode('Temps de r�ponse'),
        'autoscaleMargin' => 1
      ),
      'y2axis' => array(
        'min'             => 0,
        'title'           => utf8_encode('Requ�tes'),
        'autoscaleMargin' => 1
      ),
      'grid' => array(
        'verticalLines' => false
      ),
      'HtmlText'    => false,
      'spreadsheet' => array(
        'show'             => true,
        'csvFileSeparator' => ';',
        'decimalSeparator' => ','
      )
    );
    
    $series = array();
    
    // Right axis (before in order the lines to be on top)
    foreach ($requests as $datasource => $_requests) {
      $series[] = array(
       'label' => "$datasource-requetes",
       'data'  => $_requests,
       'bars'  => array(
         'show'    => true,
         'stacked' => true
       ),
       'yaxis' => 2
      );
    }
    
    // Left axis
    foreach ($duration as $datasource => $_duration) {
      $series[] = array(
       'label' => "$datasource-temps moyen",
       'data'  => $_duration,
       'lines' => array(
         'show' => true
       ),
      );
    }
  }
  
  return array('series' => $series, 'options' => $options, 'module' => $module_name, 'datetime_by_index' => $datetime_by_index);
}
