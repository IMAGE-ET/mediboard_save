<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$function = mbGetValueFromGet("action" , "release");
$timeout  = mbGetValueFromGet("timeout", 5);

$sem1 = new CMbSemaphore("montest");
$sem2 = new CMbSemaphore("montest");
if($function == "acquire") {
  if($sem1->acquire($timeout)) {
    mbTrace("Aquire du s�maphore 1 r�ussi");
  } else {
    mbTrace("Aquire s�maphore 1 �chou�");
  }
  if($sem2->acquire($timeout)) {
    mbTrace("Aquire s�maphore 2 r�ussi");
  } else {
    mbTrace("Aquire s�maphore 2 �chou�");
  }
} else {
  if($sem1->release()) {
    mbTrace("Release s�maphore 1 r�ussi");
  } else {
    mbTrace("release s�maphore 1 �chou�");
  }
  if($sem2->release()) {
    mbTrace("Release s�maphore 2 r�ussi");
  } else {
    mbTrace("release s�maphore 2 �chou�");
  }
}


?>