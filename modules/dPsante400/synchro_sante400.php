<?php 

global $AppUI, $m;

require_once($AppUI->getModuleClass("dPsante400", "mouvsejourtonkin"));

$mouv = new CMouvSejourTonkin();
$mouv->load();
$mouv->proceed();

// Cr�ation du template
$smarty = new CSmartyDP(1);

$smarty->display("synchro_sante400.tpl");

?>
