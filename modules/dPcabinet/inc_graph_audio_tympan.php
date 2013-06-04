<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

global $can, $m;

CAppUI::requireLibraryFile("jpgraph/src/mbjpgraph");
CAppUI::requireLibraryFile("jpgraph/src/jpgraph_line");

class AudiogrammeTympano extends Graph {
  /** @var self */
  static public $gauche = null;
  /** @var self */
  static public $droite = null;
  
  function setTitle($title) {
    $this->title->Set($title);
  }
  
  function AudiogrammeTympano() {
    $pressions = CExamAudio::$pressions;
    
    // Setup the graph.
    $this->Graph(280, 160, "auto"); 
       
    $this->SetScale("textlin", 0, 15);
    $this->SetMarginColor("lightblue");
    
    // Image setup
    $this->img->SetAntiAliasing();
    $this->img->SetMargin(35, 10, 20, 30);
    
    // Legend setup
    $this->legend->Pos(0.02, 0.5, "right", "center");
    $this->legend->SetShadow("darkgray@0.5", 3);
    $this->legend->SetFont(FF_ARIAL,FS_NORMAL, 7);
    $this->legend->SetFillColor('white@0.3');
  
    // Title setup
    $this->title->SetFont(FF_ARIAL,FS_NORMAL,10);
    $this->title->SetColor("darkred");
    
    // Setup font for axis
    $this->xgrid->Show(true);
    $this->xgrid->SetColor("lightgray", "lightgray:1.8");
    
    $this->xaxis->SetFont(FF_ARIAL, FS_NORMAL, 7);
    $this->xaxis->SetTickLabels($pressions);
    $this->xaxis->title->Set("Pression en mm H�0");
    $this->xaxis->title->SetFont(FF_ARIAL,FS_NORMAL, 7);
    $this->xaxis->title->SetColor("darkred");

    
    // Setup Y-axis labels 
    $this->ygrid->Show(true,true);
    $this->ygrid->SetColor("lightgray", "lightgray:1.8");

    $this->yaxis->SetFont(FF_ARIAL,FS_NORMAL, 7);
    $this->yaxis->scale->ticks->Set(5, 1);
    $this->yaxis->scale->ticks->SupressZeroLabel(false);
    $this->yaxis->scale->ticks->SupressMinorTickMarks(false);
    $this->yaxis->title->Set("Admittance x10 en ml");
    $this->yaxis->title->SetFont(FF_ARIAL,FS_NORMAL, 7);
    $this->yaxis->title->SetColor("darkred");
    
    // Empty plots for scale window
    foreach($pressions as $pression) {
      $datay[] = 20;
    }
    $p1 = new LinePlot($datay);
    $p1->SetCenter();
    
    $this->Add($p1);
    return;
    
  }
  
  function addAudiogramme($values, $mark_color) {
    $pressions = CExamAudio::$pressions;

    // Empty plot case
    $datay = $values;
    CMbArray::removeValue("", $datay);
    if (!count($datay)) {
      return;
    }
    
    $title = $this->title->t;
    $words = explode(" ", $title);
    $cote = $words[1];
    $labels = array();
    $jscalls = array();
      // Remove empty values to connect distant points
      $datax = array();
      $datay = array();
    foreach ($values as $key => $value) {
      if($value !== "" && $value!== null){
      	$pression = $pressions[$key];
        $jstitle = strtr($title, "\n", " ");
        $labels[] = "Modifier l'admittance {$value} ml pour $jstitle � la pression $pression mm H�0";
        $jscalls[] = "javascript:changeTympanValue('$cote',$key)";
        $datay[] = $value;
        $datax[] = "$key"; // Needs to be a string when null
      }
    }

    $p1 = new LinePlot($datay, $datax);

    // Create the first line
    $p1->SetColor($mark_color);
    $p1->SetCenter();
    $p1->SetWeight(1);
    $p1->SetCSIMTargets($jscalls, $labels);

    // Marks
    $p1->mark->SetType(MARK_FILLEDCIRCLE);
    $p1->mark->SetColor($mark_color);
    $p1->mark->SetFillColor("$mark_color@0.6");
    $p1->mark->SetWidth(4);

    $this->Add($p1);
  }
}

global $exam_audio,$reloadGraph;

if(!$reloadGraph || $reloadGraph=="gauche"){
  AudiogrammeTympano::$gauche = new AudiogrammeTympano();
  AudiogrammeTympano::$gauche->setTitle("Oreille gauche");
  AudiogrammeTympano::$gauche->addAudiogramme($exam_audio->_gauche_tympan, "blue");
}

if(!$reloadGraph || $reloadGraph=="droite"){
  AudiogrammeTympano::$droite = new AudiogrammeTympano();
  AudiogrammeTympano::$droite->setTitle("Oreille droite");
  AudiogrammeTympano::$droite->addAudiogramme($exam_audio->_droite_tympan, "red");
}
?>