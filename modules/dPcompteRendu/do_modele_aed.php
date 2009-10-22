<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m;

$do = new CDoObjectAddEdit("CCompteRendu", "compte_rendu_id");
$do->redirectDelete = "m=$m&new=1";

if (isset($_POST["source"])) {
  // Application des listes de choix
  $fields = array();
  $values = array();
  if (isset($_POST["_CListeChoix"])) {
    $listes = $_POST["_CListeChoix"];
    CMbArray::removeValue(array(0 => "undef"), $listes);
    foreach ($listes as $list_id => $options) {
      $options = array_map('htmlentities', $options);
	    $list = new CListeChoix;
	    $list->load($list_id);
	    CMbArray::removeValue("undef", $options);
	    $fields[] = "[Liste - ".htmlentities($list->nom)."]";
	    $values[] = nl2br(implode(", ", $options));
	  }
  }
  
  $_POST["source"] = str_ireplace($fields, $values, $_POST["source"]);

  // Application des destinataires
  $destinataires = array();
  foreach($_POST as $key => $value) {
    // Remplacement des destinataires
    if(preg_match("/_dest_([\w]+)_([0-9]+)/", $key, $dest)) {
      $destinataires[] = $dest;
    }
  }
  
  if(count($destinataires)) {
    $object = new $_POST["object_class"];
    $object->load($_POST["object_id"]);
    CDestinataire::makeAllFor($object);
    $allDest = CDestinataire::$destByClass;
    $bodyTag = '<div id=\"body\">';
    
    // On sort l'en-t�te et le pied de page
    $posBody      = strpos($_POST["source"], $bodyTag);
    if($posBody) {
      $headerfooter = substr($_POST["source"], 0, $posBody);
      $body         = substr($_POST["source"], $posBody+strlen($bodyTag), -strlen("</div>"));
    } else {
      $headerfooter = "";
      $body         = $_POST["source"];
    }
    // On cr�e les fichiers pour chaque destinataire
    $copyTo = "";
    foreach($destinataires as $curr_dest) {
      $copyTo .= $allDest[$curr_dest[1]][$curr_dest[2]]->nom."; ";
    }
    $allSources = array();
    foreach($destinataires as &$curr_dest) {
      $fields = array(
        htmlentities("[Courrier - nom destinataire]"),
        htmlentities("[Courrier - adresse destinataire]"),
        htmlentities("[Courrier - cp ville destinataire]"),
        htmlentities("[Courrier - copie �]")
      );
      $values = array(
        $allDest[$curr_dest[1]][$curr_dest[2]]->nom,
        $allDest[$curr_dest[1]][$curr_dest[2]]->adresse,
        $allDest[$curr_dest[1]][$curr_dest[2]]->cpville,
        $copyTo
      );
      $allSources[] = str_ireplace($fields, $values, $body);
    }
    // On concat�ne les en-t�te, pieds de page et body's
    if($headerfooter) {
      $_POST["source"] = $headerfooter;
      $_POST["source"] .= "<div id=\"body\">";
      $_POST["source"] .= implode("<hr class=\"pageBreak\" />", $allSources);
      $_POST["source"] .= "</div>";
    } else {
      $_POST["source"] = implode("<hr class=\"pageBreak\" />", $allSources);
    }
  }
  
}

$do->doBind();
if (intval(mbGetValueFromPost("del"))) {
  $do->doDelete();
} else {
  $do->doStore();
}


if($do->ajax){
  $idName   = $do->objectKeyGetVarName;
  $callBack = $do->callBack;
  $idValue  = $do->_obj->$idName;
  echo $AppUI->getMsg();
  if ($callBack) {
    echo "\n<script type='text/javascript'>$callBack($idValue);</script>";
  }
  CApp::rip();
}

else {
  // Si c'est un compte rendu
  if($do->_obj->object_id && !intval(mbGetValueFromPost("del"))) {
    $do->redirect = "m=$m&a=edit_compte_rendu&dialog=1&compte_rendu_id=".$do->_obj->_id;
  } 
  // Si c'est un mod�le de compte rendu
  else { 
    $do->redirect = "m=$m&compte_rendu_id=".$do->_obj->_id;
  }
  $do->doRedirect();
}
?>