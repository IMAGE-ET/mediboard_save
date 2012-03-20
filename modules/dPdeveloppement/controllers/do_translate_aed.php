<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision$
* @author Poiron Yohann
*/

function redirect() {
  if (CValue::post("ajax")) {
    echo CAppUI::getMsg();
    CApp::rip();
  }
  CAppUI::redirect();
}

global $can;

$user = CUser::get();

// only user_type of Administrator (1) can access this page
$can->edit |= ($user->user_type != 1);
$can->needsEdit();

$module   = CValue::post("module");
$strings  = CValue::post("s");
$language = CValue::post("language");

if(!$module || !$strings || !is_array($strings)){
  CAppUI::setMsg( "Certaines informations sont manquantes au traitement de la traduction.", UI_MSG_ERROR );
  redirect();
  return;
}

$translateModule = new CMbConfig;
$translateModule->sourcePath = null;

// Ecriture du fichier
$translateModule->options = array("name" => "locales");
$translateModule->targetPath = "modules/$module/locales/$language.php";

if (!is_file($translateModule->targetPath)) {
  CMbPath::forceDir(dirname($translateModule->targetPath));
  file_put_contents($translateModule->targetPath, '<?php $locales["module-'.$module.'-court"] = "'.$module.'"; ?>');
}

$translateModule->load();

foreach ($strings as $key => $valChaine){
  if ($valChaine !== ""){
    $translateModule->values[$key] = stripslashes($valChaine);
  }
  else {
    unset($translateModule->values[$key]);
  }
}

uksort($translateModule->values, "strnatcmp");

$error = $translateModule->update($translateModule->values, false);

SHM::rem("locales-$language");

if ($error instanceof PEAR_Error) {
  CAppUI::setMsg("Error while saving locales file : {$error->message}", UI_MSG_ERROR);
} else {
  CAppUI::setMsg("Locales file saved", UI_MSG_OK );
  redirect();
}

?>