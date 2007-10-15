<?php /* $Id:  $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision:  $
* @author Thomas Despoix
*/

global $AppUI, $can;

$can->needsRead();

$intermax = mbGetValueFromPostOrSession("intermax", array());

$fonction = @$intermax["FONCTION"]["NOM"];

$AppUI->stepAjax("Fonction Intermax '$fonction' re�ue", UI_MSG_OK);
$AppUI->callbackAjax("Intermax.handleResult", $fonction);

die();

?>