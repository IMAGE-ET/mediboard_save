<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision:$
* @author SARL Openxtrem
*/

$compte_rendu_id = CValue::post("compte_rendu_id");

// On s'arr�te l� si pas d'id
if (!$compte_rendu_id) {
  CApp::rip();
}

$compte_rendu = new CCompteRendu();
$compte_rendu->load($compte_rendu_id);
$compte_rendu->loadContent();
$generate_thumbs = CValue::post("generate_thumbs");
$mode        = CValue::post("mode","doc");
$print       = CValue::post("print", 0);
$type        = CValue::post("type", $compte_rendu->type);
$footer_id   = CValue::post("footer_id", $compte_rendu->footer_id);
$header_id   = CValue::post("header_id", $compte_rendu->header_id);
$stream      = CValue::post("stream");
$content     = stripslashes(urldecode(CValue::post("content", $compte_rendu->_source)));
$save_content = $content;

$ids_corres  = CValue::post("_ids_corres");
$write_page  = CValue::post("write_page", 0);
$page_format = CValue::post("page_format",$compte_rendu->_page_format);
$orientation = CValue::post("orientation",$compte_rendu->_orientation);
$first_time  = CValue::post("first_time");
$user_id     = CValue::post("user_id", CAppUI::$user->_id);
$page_width  = CValue::post("page_width", $compte_rendu->page_width);
$page_height = CValue::post("page_height", $compte_rendu->page_height);
$margins     = CValue::post("margins", array($compte_rendu->margin_top,
                                             $compte_rendu->margin_right,
                                             $compte_rendu->margin_bottom,
                                             $compte_rendu->margin_left));

if ($ids_corres) {
  $ids = explode("-", $ids_corres);
  $_GET["nbDoc"] = array();
  foreach($ids as $doc_id) {
    if ($doc_id) {
      $_GET["nbDoc"][$doc_id] = 1;
    }
  }
  include_once "modules/dPcompteRendu/print_docs.php";
  CApp::rip();
} 

$file = new CFile;
if ($compte_rendu->_id) {
  $compte_rendu->loadFile();
  $file = $compte_rendu->_ref_file;
}

// S'il n'y a pas de pdf ou qu'il est vide, on consid�re qu'il n'est pas g�n�r�
// pour le mode document
if ($mode != "modele" &&
     (((!$file || !$file->_id) && $first_time == 1 && !$compte_rendu->object_id) ||
     ($file && $file->_id && $first_time == 1 && (!is_file($file->_file_path) || file_get_contents($file->_file_path) == "")))) {
  CAppUI::stepAjax(CAppUI::tr("CCompteRendu-no-pdf-generated"));
  return;
}
else if ($file && $file->_id && $first_time == 1 && is_file($file->_file_path) &&
         !$compte_rendu->object_id && $mode == "doc" && file_get_contents($file->_file_path) != '') {
  // Rien
}
else {
  if($mode == "modele") {
    switch($type) {
      case "header":
      case "footer":
        $height = CValue::post("height",$compte_rendu->height);
        $content = $compte_rendu->loadHTMLcontent($content, $mode, $type, '', $height, '', '', $margins);
        break;
      case "body":
        $compte_rendu_h_f = new CCompteRendu;
        $header = ''; $sizeheader = 0;
        $footer = ''; $sizefooter = 0;
        if($header_id) {
          $compte_rendu_h_f->load($header_id);
          $compte_rendu_h_f->loadContent();
          $header = $compte_rendu_h_f->_source;
          $sizeheader = $compte_rendu_h_f->height;
        }
        if($footer_id) {
          $compte_rendu_h_f = new CCompteRendu;
          $compte_rendu_h_f->load($footer_id);
          $compte_rendu_h_f->loadContent();
          $footer = $compte_rendu_h_f->_source;
          $sizefooter = $compte_rendu_h_f->height;
        }
        $content = $compte_rendu->loadHTMLcontent($content, $mode, $type, $header, $sizeheader, $footer, $sizefooter, $margins);
      }
    }
  else {
    if ($textes_libres = CValue::post("texte_libre") && CAppUI::conf("dPcompteRendu CCompteRendu pdf_thumbnails") && CAppUI::pref("pdf_and_thumbs")) {
      $compte_rendu->_source = $compte_rendu->replaceFreeTextFields($content, $_POST["texte_libre"]);
    }
    $content = $compte_rendu->loadHTMLcontent($content, $mode,'','','','','',$margins);
  }
  
  // Traitement du format de la page
  if($orientation == "landscape") {
    $a = $page_width;
    $page_width = $page_height;
    $page_height = $a;
  }
  if($page_format == "") {
    $page_width  = round((72 / 2.54) * $page_width, 2);
    $page_height = round((72 / 2.54) * $page_height, 2);
    $page_format = array(0, 0, $page_width, $page_height);
  }

  
  // Cr�ation du CFile si inexistant
  if (!$file->_id) {
    $file = new CFile();
    $file->setObject($compte_rendu);
    $file->private = 0;
    $file->file_type  = "application/pdf";
    $file->file_owner = $user_id;
    $file->fillFields();
    $file->updateFormFields();
    $file->forceDir();
  }
  
  if ($file->_id && !file_exists($file->_file_path)) {
    $file->forceDir();
  }
  
  $file->file_name  = $compte_rendu->nom . ".pdf";
  
  $c1 = preg_replace("!\s!",'',$save_content);
  $c2 = preg_replace("!\s!",'',$compte_rendu->_source);
  
  // Si la source envoy�e et celle pr�sente en base sont diff�rentes, on reg�n�re le PDF
  // Suppression des espaces, tabulations, retours chariots et sauts de lignes pour effectuer le md5
  if (md5($c1) != md5($c2) || !$file->_id || !file_exists($file->_file_path) || file_get_contents($file->_file_path) == "") {
    $htmltopdf = new CHtmlToPDF;
    $htmltopdf->generatePDF($content, 0, $page_format, $orientation, $file);
    $file->file_size = filesize($file->_file_path);
  }
  
  // Il peut y avoir plusieurs cfiles pour un m�me compte-rendu, � cause 
  // de n requ�tes simultan�es pour la g�n�ration du pdf.
  // On supprime donc les autres cfiles.
  $compte_rendu->loadRefsFiles();
  $files = $compte_rendu->_ref_files;
  
  if ($file->_id) {
    unset($files[$file->_id]);
  }
  
  foreach($files as $_file) {
    $_file->delete();
  }
  
  $file->store();
}

if ($stream) {
  $file->streamFile();
  CApp::rip();
}

if ($write_page) {
  $file->loadNbPages();
  $smarty = new CSmartyDP;
  $smarty->assign("file_id", $file->_id);
  $smarty->assign("_nb_pages", $file->_nb_pages);
  $smarty->assign("print", $print);
  $smarty->display("inc_thumbnail.tpl");
}

?>