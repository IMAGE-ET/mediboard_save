<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$ex_class_id = CValue::getOrSession("ex_class_id");

$ex_class = new CExClass;
$ex_class->load($ex_class_id);
$ex_class->loadRefsFields();
$ex_class->loadRefsConstraints();
$ex_class->loadRefsNotes();

list($grid, $out_of_grid) = $ex_class->getGrid(4, 20, false);

$ex_object = new CExObject;
$ex_object->_ex_class_id = $ex_class->_id;
$ex_object->setExClass();

$host_object = null;

if ($ex_class->_id)
  $host_object = new $ex_class->host_class;

foreach($ex_class->_ref_constraints as $_ex_constraint) {
  $_ex_constraint->loadRefExClass();
  $_ex_constraint->loadTargetObject();
}

$classes = array();

if (!$ex_class->_id) {
  $classes = CApp::getMbClasses(array(), $instances);
  
  foreach($instances as $class => $instance) {
    if (empty($instance->_spec->events)) {
      unset($instances[$class]);
      continue;
    }
    
    $instances[$class] = $instance->_spec->events;
  }
  
  $classes = $instances;
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("ex_class", $ex_class);
$smarty->assign("ex_object", $ex_object);
$smarty->assign("host_object", $host_object);
$smarty->assign("classes", $classes);
$smarty->assign("grid", $grid);
$smarty->assign("out_of_grid", $out_of_grid);
$smarty->display("inc_edit_ex_class.tpl");
