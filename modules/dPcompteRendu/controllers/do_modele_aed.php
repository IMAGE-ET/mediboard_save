<?php

/**
 * CCompteRendu aed
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

if (isset($_POST["_do_empty_pdf"])) {
  $compte_rendu_id = CValue::post("compte_rendu_id");
  $compte_rendu = new CCompteRendu();
  $compte_rendu->load($compte_rendu_id);

  $compte_rendu->loadRefsFiles();
  foreach ($compte_rendu->_ref_files as $_file) {
    $_file->fileEmpty();
  }
  CApp::rip();
}

$do = new CDoObjectAddEdit("CCompteRendu");
$do->redirectDelete = "m=dPcompteRendu&new=1";

// R�cup�ration des marges du modele en fast mode
if (isset($_POST["fast_edit"]) && $_POST["fast_edit"] == 1 && isset($_POST["object_id"]) && $_POST["object_id"] != '') {
  $compte_rendu = new CCompteRendu();
  $compte_rendu->load($_POST["modele_id"]);
  
  if ($compte_rendu->_id) {
    $do->request["margin_top"]    = $compte_rendu->margin_top;
    $do->request["margin_bottom"] = $compte_rendu->margin_bottom;
    $do->request["margin_left"]   = $compte_rendu->margin_left;
    $do->request["margin_right"]  = $compte_rendu->margin_right;
  }
}

if (isset($_POST["_source"])) {
  $_POST["_source"] = stripslashes($_POST["_source"]);
}

$check_to_empty_field = CAppUI::conf("dPcompteRendu CCompteRendu check_to_empty_field");

// Remplacement des zones de texte libre
if (isset($_POST["_texte_libre"])) {
  $compte_rendu = new CCompteRendu();
  $fields = array();
  $values = array();

  // Remplacement des \n par des <br>
  foreach ($_POST["_texte_libre"] as $key=>$_texte_libre) {
    $is_empty = false;
    if (($check_to_empty_field && isset($_POST["_empty_texte_libre"][$key])) ||
        (!$check_to_empty_field && !isset($_POST["_empty_texte_libre"][$key]))
    ) {
      $values[] = "";
      $is_empty = true;
    }
    else {
      if ($_POST["_texte_libre"][$key] === "") {
        continue;
      }
      $values[] = nl2br($_POST["_texte_libre"][$key]);
    }
    if ($is_empty) {
      $fields[] = "<span class=\"field\">[[Texte libre - " . $_POST["_texte_libre_md5"][$key] . "]]</span>";
    }
    else {
      $fields[] = "[[Texte libre - " . $_POST["_texte_libre_md5"][$key] . "]]";
    }
  }
  
  $_POST["_source"] = str_ireplace($fields, $values, $_POST["_source"]);
  $_POST["_texte_libre"] = null;
}

$destinataires = array();
$ids_corres    = "";
$do_merge = CValue::post("do_merge", 0);

if (isset($_POST["_source"])) {
  // Ajout d'ent�te / pied de page � la vol�e
  if (CAppUI::conf("dPcompteRendu CCompteRendu header_footer_fly") && $_POST["object_id"] && !isset($_POST["fast_edit"])) {
    $modele = new CCompteRendu();
    $modele->load($_POST["modele_id"]);

    $header_id = CValue::post("header_id");
    $footer_id = CValue::post("footer_id");

    // Depuis un mod�le
    if (!$_POST["compte_rendu_id"]) {
      // Pr�sence d'un header / footer
      if ($modele->header_id || $modele->footer_id) {
        if ($header_id != $modele->header_id) {
          $_POST["_source"] = CCompteRendu::replaceComponent($_POST["_source"], $header_id);
        }
        if ($footer_id != $modele->footer_id) {
          $_POST["_source"] = CCompteRendu::replaceComponent($_POST["_source"], $footer_id, "footer");
        }
      }
      else {
        $_POST["_source"] = $modele->generateDocFromModel($_POST["_source"], $header_id, $footer_id);
      }
    }
    else {
      // Document existant
      $cr = new CCompteRendu();
      $cr->load($_POST["compte_rendu_id"]);

      if (!$cr->header_id && !$cr->footer_id && !$header_id && !$footer_id) {
        $_POST["_source"] = $cr->generateDocFromModel($_POST["_source"], $header_id, $footer_id);
      }
      else {
        if ($header_id != $cr->header_id) {
          $_POST["_source"] = CCompteRendu::replaceComponent($_POST["_source"], $header_id);
        }
        if ($footer_id != $cr->footer_id) {
          $_POST["_source"] = CCompteRendu::replaceComponent($_POST["_source"], $footer_id, "footer");
        }
      }
    }
  }

  // Application des listes de choix
  $fields = array();
  $values = array();
  if (isset($_POST["_CListeChoix"])) {
    $listes = $_POST["_CListeChoix"];
    foreach ($listes as $list_id => $options) {
      $options = array_map(array('CMbString','htmlEntities'), $options);
      $list = new CListeChoix;
      $list->load($list_id);
      $is_empty = false;
      if (($check_to_empty_field && isset($_POST["_empty_list"][$list_id])) ||
          (!$check_to_empty_field && !isset($_POST["_empty_list"][$list_id]))
      ) {
        $values[] = "";
        $is_empty = true;
      }
      else {
        if ($options === array(0 => "undef")) {
          continue;
        }
        CMbArray::removeValue("undef", $options);
        $values[] = nl2br(implode(", ", $options));
      }
      $nom = CMbString::htmlEntities($list->nom, ENT_QUOTES);
      if ($is_empty) {
        $fields[] = "<span class=\"name\">[Liste - ".$nom."]</span>";
      }
      else {
        $fields[] = "[Liste - ".$nom."]";
      }
    }
  }

  $_POST["_source"] = str_ireplace($fields, $values, $_POST["_source"]);
  
  // Si purge_field est valu�, on effectue l'op�ration de nettoyage des lignes
  
  if (isset($_POST["purge_field"]) && $_POST["purge_field"] != "" ) {
    $purge_field = $_POST["purge_field"];
    $purge_field = str_replace("/", "\/", $purge_field);
    $purge_field = str_replace("<", "\<", $purge_field);
    $purge_field = str_replace(">", "\>", $purge_field);
    $purge_field .= "\s*\<br\s*\/\>";
    $_POST["_source"] = preg_replace("/\n$purge_field/", "", $_POST["_source"]);
    
  }
  
  // Application des destinataires
  foreach ($_POST as $key => $value) {
    // Remplacement des destinataires
    if (preg_match("/_dest_([\w]+)_([0-9]+)/", $key, $dest)) {
      $destinataires[] = $dest;
    }
  }
  
  if (count($destinataires) && $do_merge) {
    $object = new $_POST["object_class"];
    /** @var $object CMbObject */
    $object->load($_POST["object_id"]);
    CDestinataire::makeAllFor($object);
    $allDest = CDestinataire::$destByClass;
    
    // R�cup�ration des correspondants ajout�s par l'autocomplete
    $cr_dest = new CCompteRendu;
    $cr_dest->load($_POST["compte_rendu_id"]);
    $cr_dest->mergeCorrespondantsCourrier($allDest);
    
    $bodyTag = '<div id="body">';

    // On sort l'en-t�te et le pied de page
    $posBody      = strpos($_POST["_source"], $bodyTag);

    if ($posBody) {
      $headerfooter = substr($_POST["_source"], 0, $posBody);
      $index_div    = strrpos($_POST["_source"], "</div>")-($posBody+strlen($bodyTag));
      $body         = substr($_POST["_source"], $posBody+strlen($bodyTag), $index_div);
    }
    else {
      $headerfooter = "";
      $body         = $_POST["_source"];
    }
    
    // On fait le doBind avant le foreach si la config est � 1.
    if (CAppUI::conf("dPcompteRendu CCompteRendu multiple_doc_correspondants")) {
      $do->doBind();
    }
    
    $allSources = array();
    $modele_base = clone $do->_obj;
    $source_base = $body;
    
    foreach ($destinataires as &$curr_dest) {
      $fields = array(
        CMbString::htmlEntities("[Courrier - Formule de politesse - D�but]"),
        CMbString::htmlEntities("[Courrier - Formule de politesse - Fin]"),
        CMbString::htmlEntities("[Courrier - nom destinataire]"),
        CMbString::htmlEntities("[Courrier - nom destinataire]"),
        CMbString::htmlEntities("[Courrier - adresse destinataire]"),
        CMbString::htmlEntities("[Courrier - cp ville destinataire]"),
        CMbString::htmlEntities("[Courrier - confraternite]"),
        CMbString::htmlEntities("[Courrier - copie � - simple]"),
        CMbString::htmlEntities("[Courrier - copie � - simple (multiligne)]"),
        CMbString::htmlEntities("[Courrier - copie � - complet]"),
        CMbString::htmlEntities("[Courrier - copie � - complet (multiligne)]")
      );
      
      // Champ copie � : on reconstruit en omettant le destinataire.
      $confraternie = "";
      $copyTo = "";
      $copyToMulti = "";
      $copyToComplet = "";
      $copyToCompletMulti = "";

      foreach ($destinataires as $_dest) {
        if ($curr_dest[0] == $_dest[0]) {
          continue;
        }
        $_destinataire = $allDest[$_dest[1]][$_dest[2]];
        $_destinataire->nom = preg_replace("/(.*)(\([^\)]+\))/", '$1', $_destinataire->nom);
        $_destinataire->confraternite = $_destinataire->confraternite ? $_destinataire->confraternite."," : null;

        $copyTo .= $_destinataire->nom."; ";
        $copyToMulti .= $_destinataire->nom."<br />";
        $copyToComplet .= $_destinataire->nom. " - " .
                          preg_replace("/\n\r\t/", " ", $_destinataire->adresse). " ".
                          $_destinataire->cpville;
        
        $copyToCompletMulti .= $_destinataire->nom. " - " . preg_replace("/\n\r\t/", " ", $_destinataire->adresse) . " " .
                               $_destinataire->cpville;
        if (end($destinataires) !== $_dest) {
          $copyToComplet .= " ; ";
          $copyToCompletMulti .= "<br />";
        }
      }
      
      $values = array(
        $allDest[$curr_dest[1]][$curr_dest[2]]->starting_formula,
        $allDest[$curr_dest[1]][$curr_dest[2]]->closing_formula,
        preg_replace("/(.*)(\([^\)]+\))/", '$1', $allDest[$curr_dest[1]][$curr_dest[2]]->nom),
        nl2br($allDest[$curr_dest[1]][$curr_dest[2]]->adresse),
        $allDest[$curr_dest[1]][$curr_dest[2]]->cpville,
        $allDest[$curr_dest[1]][$curr_dest[2]]->confraternite,
        $copyTo,
        $copyToMulti,
        $copyToComplet,
        $copyToCompletMulti
      );
      
      if (!CAppUI::conf("dPcompteRendu CCompteRendu multiple_doc_correspondants")) {
        $max = 1;
        if (isset($_POST["_count_".$curr_dest[1]."_".$curr_dest[2]])) {
          $max = $_POST["_count_".$curr_dest[1]."_".$curr_dest[2]];
        }
        for ($i = 0 ; $i < $max ; $i++) {
          $allSources[] = str_ireplace($fields, $values, $body);
        }
      }
      else {
        // Cr�ation d'un document par correspondant
        $body = str_ireplace($fields, $values, $source_base);
        
        $content = $body;
        
        if ($headerfooter) {
          $content = $headerfooter . "<div id=\"body\">" . $body . "</div>";
        }
        
        // Si le compte-rendu a d�j� �t� enregistr� et que l'on applique le premier destinataire,
        // on modifie le compte-rendu existant
        if ($do->_obj->_id && $curr_dest === reset($destinataires)) {
          $compte_rendu = $do->_obj;
        }
        else {
          // Sinon clone du mod�le
          $compte_rendu = clone $modele_base;
          $compte_rendu->_id     = null;
          $compte_rendu->_ref_content = null;
          $compte_rendu->user_id    = null;
          $compte_rendu->function_id = null;
          $compte_rendu->group_id   = null;
          $do->_obj = $compte_rendu;
        }
        
        $compte_rendu->_source = $content;
        $compte_rendu->nom    .= " � {$allDest[$curr_dest[1]][$curr_dest[2]]->nom}";
        $compte_rendu->modele_id  = $modele_base->_id;
        
        $do->doStore();
        $ids_corres .= "{$do->_obj->_id}-";
      }
    }
    if (!CAppUI::conf("dPcompteRendu CCompteRendu multiple_doc_correspondants")) {
      // On concat�ne les en-t�te, pieds de page et body's
      if ($headerfooter) {
        $_POST["_source"] = $headerfooter;
        $_POST["_source"] .= "<div id=\"body\">";
        $_POST["_source"] .= implode("<hr class=\"pageBreak\" />", $allSources);
        $_POST["_source"] .= "</div>";
      }
      else {
        $_POST["_source"] = implode("<hr class=\"pageBreak\" />", $allSources);
      }
    }
  }
}

if (!count($destinataires) || !$do_merge || !CAppUI::conf("dPcompteRendu CCompteRendu multiple_doc_correspondants")) {
  $do->doBind();
  if (intval(CValue::post("del"))) {
    $do->doDelete();
  }
  else {
    $do->doStore();
    // Pour le fast mode en impression navigateur, on envoie la source du document complet.
    $margins = array(
      $do->_obj->margin_top,
      $do->_obj->margin_right,
      $do->_obj->margin_bottom,
      $do->_obj->margin_left);
    $do->_obj->_entire_doc = CCompteRendu::loadHTMLcontent(
      $do->_obj->_source,
      "",
      $margins,
      CCompteRendu::$fonts[$do->_obj->font],
      $do->_obj->size);
  }
}

// On supprime les correspondants
$correspondants_courrier = $do->_obj->loadRefsCorrespondantsCourrier();
/** @var $_corres CCorrespondantCourrier */
foreach ($correspondants_courrier as $_corres) {
  if ($msg = $_corres->delete()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }
}

// Gestion des CCorrespondantCourrier
if (!$do_merge && !intval(CValue::post("del")) && strpos($do->_obj->_source, "[Courrier -")) {
  
  // On stocke les correspondants coch�s
  $object = new $_POST["object_class"];
  $object->load($_POST["object_id"]);
  CDestinataire::makeAllFor($object);
  $allDest = CDestinataire::$destByClass;
  
  foreach ($allDest as $class => $_dest_by_class) {
    foreach ($_dest_by_class as $i => $_dest) {
      if (!isset($_POST["_dest_{$class}_$i"])) {
        continue;
      }
      list($object_class, $object_id) = split("-", $_dest->_guid_object);
      $corres = new CCorrespondantCourrier;
      $corres->compte_rendu_id = $do->_obj->_id;
      $corres->tag = $_dest->tag;
      $corres->object_id = $object_id;
      $corres->object_class = $object_class;
      $corres->quantite = $_POST["_count_{$class}_$i"];
      
      if ($msg = $corres->store()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
      }
      unset($_POST["_dest_{$class}_$i"]);
    }
  }
  
  // Correspondants courrier ajout�s par autocomplete
  foreach ($_POST as $key => $value) {
    if (preg_match("/_dest_([a-zA-Z]*)_([0-9]+)/", $key, $matches)) {
      $corres = new CCorrespondantCourrier;
      $corres->compte_rendu_id = $do->_obj->_id;
      $corres->tag = "correspondant";
      $corres->object_id = $matches[2];
      $corres->object_class = $matches[1];
      
      if ($msg = $corres->store()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
      }
    }
  }
}

if (strlen($ids_corres)) {
  $do->_obj->_ids_corres = $ids_corres;
}

if ($do->ajax) {
  $do->doCallback();
}
else {
  // Si c'est un compte rendu
  if ($do->_obj->object_id && !intval(CValue::post("del"))) {
    $do->redirect = "m=compteRendu&a=edit_compte_rendu&dialog=1&compte_rendu_id=".$do->_obj->_id;
  }
  else if (intval(CValue::post("del") && isset($_POST["_tab"]))) {
    $do->redirect = "m=compteRendu&a=vw_modeles";
  }
  else {
    // Si c'est un mod�le de compte rendu
    $do->redirect = "m=compteRendu&a=addedit_modeles&dialog=1&compte_rendu_id=".$do->_obj->_id;
  }
  $do->doRedirect();
}
