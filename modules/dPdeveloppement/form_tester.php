<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision$
* @author Fabien M�nager
*/

global $can;
$can->needsRead();

class CTestClass extends CMbObject {
  function __construct() {
    foreach (CMbFieldSpecFactEx::$classes as $spec => $class) {
      $this->$spec = null;
    }
    parent::__construct();
  }
  
  function getSpecs() {
    $specs = parent::getSpecs();
    foreach (CMbFieldSpecFactEx::$classes as $spec => $class) {
      $specs[$spec] = $spec;
    }
    $specs['enum'] = 'enum list|1|2|3|4';
    return $specs;
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign('object', new CTestClass());
$smarty->assign('specs', CMbFieldSpecFactEx::$classes);
$smarty->display('form_tester.tpl');

?>