<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getModuleClass("dPfiles", "files"));

$ajax = mbGetValueFromPost("ajax", 0);
$suppressHeaders = mbGetValueFromPost("suppressHeaders", 0);
unset($_POST["ajax"]);
unset($_POST["suppressHeaders"]);

if(isset($_POST["cat_id_redirect"])){
  $cat_id_redirect = mbGetValueFromPostOrSession("cat_id_redirect"  , 0);
}

function doRedirect($cat_id = 0) {
  global $ajax, $AppUI, $m, $_POST;
  $cat_id   = intval(mbGetValueFromPost("file_category_id"));
  $selKey   = intval(mbGetValueFromPost("file_object_id", 0));
  $selClass = mbGetValueFromPost("file_class"    , "");
  if($ajax) {
    echo $AppUI->getMsg();
    exit(0);
  } else {
    $AppUI->redirect("m=$m&cat_id=$cat_id&selKey=$selKey&selClass=$selClass");
  }
}

$file_id = intval(mbGetValueFromPost("file_id", 0));
$del     = intval(mbGetValueFromPost("del"    , 0));

$obj = new CFile();

if (!$obj->bind($_POST)) {
	$AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
	doRedirect();
}

$AppUI->setMsg("Fichier");
// delete the file
if ($del) {
	$obj->load($file_id);
	if (($msg = $obj->delete())) {
		$AppUI->setMsg($msg, UI_MSG_ERROR);
		doRedirect();
	} else {
		$AppUI->setMsg("supprim�", UI_MSG_ALERT, true);
		doRedirect();
	}
}

set_time_limit(600);
ignore_user_abort(1);

$upload = null;
if(isset($_FILES["formfile"])) {
	$upload = $_FILES["formfile"];
    
	if ($upload["size"] < 1) {
		if (!$file_id) {
			$AppUI->setMsg("Taille de fichier nulle. Echec de l'op�ration.", UI_MSG_ERROR);
			doRedirect($obj->file_category_id);
		}
	} else {

	// store file with a unique name
		$obj->file_name = $upload["name"];
		$obj->file_type = $upload["type"];
		$obj->file_size = $upload["size"];
		$obj->file_date = db_unix2dateTime(time());
		$obj->file_real_filename = uniqid(rand());
        
		$res = $obj->moveTemp($upload);
		if (!$res) {
		    $AppUI->setMsg("Impossible de cr�er le fichier", UI_MSG_ERROR);
		    doRedirect();
		}
		//$obj->indexStrings();
	}
}

if (!$file_id) {
	$obj->file_owner = $AppUI->user_id;
}

if (($msg = $obj->store())) {
	$AppUI->setMsg($msg, UI_MSG_ERROR);
} else {
	$AppUI->setMsg($file_id ? "modifi�" : "ajout�", UI_MSG_OK, true);
  //$obj->indexStrings();
}

doRedirect();

?>
