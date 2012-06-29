<?php

/**  
 * @category cli
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

require_once( "utils.php" );
require_once( "Task.php" );

class Menu extends Task {
  
  // Liste des taches associ�es au menu
  public $task_list;
  
  public $name;
  
  function Menu( $name ) {
    $this->task_list  = array();
    $this->name       = $name;
  }
  
  // Ajouter une tache � un menu
  function appendTask( $task = null ) {
    if ( $task instanceof Task ) {
      $this->task_list[] = $task;
    }
    else {
      cecho( "Given parameter is not a Task object.", "red", "bold" );
      echo "\n";
    }
  }
  
  // R�cup�re la liste des taches d'un menu, si ID sp�cifi�, r�cup�re cette tache pr�cise
  function getTaskList($id = null) {
    if ( is_null( $id ) ) {
      return $this->task_list;
    }
    else {
      return $this->task_list[$id];
    }
  }
  
  function showTasks() {
    foreach ( $this->getTaskList() as $nb => $oneTask ) {
      if ($nb !== 0) {
        echo "[".$nb."] ".$oneTask->description."\n";
      }
    }
  }
  
}

?>