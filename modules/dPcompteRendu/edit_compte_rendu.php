<?php

/**
 * Cr�ation / Modification d'un document (g�n�r� � partir d'un mod�le)
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

$compte_rendu_id = CValue::get("compte_rendu_id", 0);
$modele_id       = CValue::get("modele_id"      , null);
$praticien_id    = CValue::get("praticien_id"   , 0);
$type            = CValue::get("type"           , 0);
$pack_id         = CValue::get("pack_id"        , 0);
$object_id       = CValue::get("object_id"      , 0);
$switch_mode     = CValue::get("switch_mode"    , 0);
$target_id       = CValue::get("target_id");
$target_class    = CValue::get("target_class");
$force_fast_edit = CValue::get("force_fast_edit", 0);

// Faire ici le test des diff�rentes variables dont on a besoin
$compte_rendu = new CCompteRendu();

// Modification d'un document
if ($compte_rendu_id) {
  $compte_rendu->load($compte_rendu_id);
  if (!$compte_rendu->_id) {
    CAppUI::stepAjax(CAppUI::tr("CCompteRendu-alert_doc_deleted"));
    CApp::rip();
  }
  $compte_rendu->loadContent();
  $compte_rendu->loadComponents();
  $compte_rendu->loadFile();
}
// Cr�ation � partir d'un mod�le vide
else if ($modele_id == 0 && !$pack_id) {
  $compte_rendu->valueDefaults();
  $compte_rendu->object_id = $object_id;
  $compte_rendu->object_class = $target_class;
  $compte_rendu->_ref_object = new $target_class;
  $compte_rendu->_ref_object->load($object_id);
  $compte_rendu->updateFormFields();
}
// Cr�ation � partir d'un mod�le
else {
  $compte_rendu->load($modele_id);
  $compte_rendu->loadFile();
  $compte_rendu->loadContent();
  $compte_rendu->_id = null;
  $compte_rendu->function_id = null;
  $compte_rendu->group_id = null;
  $compte_rendu->object_id = $object_id;
  $compte_rendu->_ref_object = null;
  $compte_rendu->modele_id = $modele_id;

  $header_id = null;
  $footer_id = null;
  
  // Utilisation des headers/footers
  if ($compte_rendu->header_id || $compte_rendu->footer_id) {
    $header_id = $compte_rendu->header_id;
    $footer_id = $compte_rendu->footer_id;
  }
  
  // On fournit la cible
  if ($target_id && $target_class) {
    $compte_rendu->object_id = $target_id;
    $compte_rendu->object_class = $target_class;
  }
  
  // A partir d'un pack
  if ($pack_id) {
    $pack = new CPack();
    $pack->load($pack_id);
    
    $pack->loadContent();
    $compte_rendu->nom = $pack->nom;
    $compte_rendu->object_class = $pack->object_class;
    $compte_rendu->fast_edit = $pack->fast_edit;
    $compte_rendu->fast_edit_pdf = $pack->fast_edit_pdf;
    $compte_rendu->_source = $pack->_source;
    $compte_rendu->modele_id = null;
    
    $pack->loadHeaderFooter();
    
    $header_id = $pack->_header_found->_id;
    $footer_id = $pack->_footer_found->_id;
    
    // Marges et format
    /** @var $links CModeleToPack[] */
    $links = $pack->_back['modele_links'];
    $first_modele = reset($links);
    $first_modele = $first_modele->_ref_modele;
    $compte_rendu->factory       = $first_modele->factory;
    $compte_rendu->margin_top    = $first_modele->margin_top;
    $compte_rendu->margin_left   = $first_modele->margin_left;
    $compte_rendu->margin_right  = $first_modele->margin_right;
    $compte_rendu->margin_bottom = $first_modele->margin_bottom;
    $compte_rendu->page_height   = $first_modele->page_height;
    $compte_rendu->page_width    = $first_modele->page_width;
    $compte_rendu->font          = $first_modele->font;
    $compte_rendu->size          = $first_modele->size;
  }
  $compte_rendu->_source = $compte_rendu->generateDocFromModel(null, $header_id, $footer_id);
  $compte_rendu->updateFormFields();
}

if (!$compte_rendu->_id) {
  if (!$compte_rendu->font) {
    $compte_rendu->font = array_search(CAppUI::conf("dPcompteRendu CCompteRendu default_font"), CCompteRendu::$fonts);
  }

  if (!$compte_rendu->size) {
    $compte_rendu->size = CAppUI::conf("dPcompteRendu CCompteRendu default_size");
  }
}

$compte_rendu->loadRefsFwd();

$compte_rendu->_ref_object->loadRefsFwd();
$object =& $compte_rendu->_ref_object;  

$curr_user = CMediusers::get();

// Calcul du user concern�
$user = $curr_user;

// Chargement dans l'ordre suivant pour les listes de choix si null :
// - user courant
// - anesth�siste
// - praticien de la consultation
if (!$user->isPraticien()) {
  $user = new CMediusers();
  $user_id = null;

  switch ($object->_class) {
    case "CConsultAnesth" :
      /** @var $object CConsultAnesth */
      $operation = $object->loadRefOperation();
      $anesth = $operation->_ref_anesth;
      if ($operation->_id && $anesth->_id) {
        $user_id = $anesth->_id;
      }

      if ($user_id == null) {
        $user_id = $object->_ref_consultation->_praticien_id;
      }
      break;

    case "CConsultation" :
      /** @var $object CConsultation */
      $user_id = $object->loadRefPraticien()->_id;
      break;

    case "CSejour" :
      /** @var $object CSejour */
      $user_id = $object->praticien_id;
      break;

    case "COperation" :
      /** @var $object COperation */
      $user_id = $object->chir_id;
      break;

    default :
      $user_id = $curr_user->_id;
  }

  $user->load($user_id);
}

$function = $user->loadRefFunction();

// Chargement des cat�gories
$listCategory = CFilesCategory::listCatClass($compte_rendu->object_class);

// D�compte des imprimantes disponibles pour l'impression serveur
$nb_printers   = $curr_user->loadRefFunction()->countBackRefs("printers");

// Gestion du template
$templateManager = new CTemplateManager($_GET);
$templateManager->isModele = false;
$templateManager->document = $compte_rendu->_source;
$object->fillTemplate($templateManager);
$templateManager->loadHelpers($user->_id, $compte_rendu->object_class, $curr_user->function_id);
$templateManager->loadLists($user->_id, $modele_id ? $modele_id : $compte_rendu->modele_id);
$templateManager->applyTemplate($compte_rendu);

$lists = $templateManager->getUsedLists($templateManager->allLists);

// Afficher le bouton correpondant si on d�tecte un �l�ment de publipostage
$isCourrier = $templateManager->isCourrier();

$destinataires = array();
if ($isCourrier) {
  CDestinataire::makeAllFor($object);
  $destinataires = CDestinataire::$destByClass;
}

$can_lock      = $compte_rendu->canLock();
$can_unclock   = $compte_rendu->canUnlock();
$can_duplicate = $compte_rendu->canDuplicate();
$compte_rendu->isLocked();
$lock_bloked = $compte_rendu->_is_locked ? !$can_unclock : !$can_lock;
if ($compte_rendu->valide && !CAppUI::conf("dPcompteRendu CCompteRendu unlock_doc")) {
  $lock_bloked = 1;
}
$compte_rendu->canDo();
$read_only = $compte_rendu->_is_locked || !$compte_rendu->_can->edit;

if ($compte_rendu->_is_locked) {
  $templateManager->printMode = true;
}
if ($compte_rendu->_id && !$compte_rendu->canEdit()) {
  $templateManager->printMode = true;
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("listCategory"  , $listCategory);
$smarty->assign("compte_rendu"  , $compte_rendu);
$smarty->assign("modele_id"     , $modele_id);
$smarty->assign("curr_user"     , $curr_user);
$smarty->assign("lists"         , $lists);
$smarty->assign("isCourrier"    , $isCourrier);
$smarty->assign("user_id"       , $user->_id);
$smarty->assign("user_view"     , $user->_view);
$smarty->assign("object_id"     , $object_id);
$smarty->assign('object_class'  , CValue::get("object_class", $compte_rendu->object_class));
$smarty->assign("nb_printers"   , $nb_printers);
$smarty->assign("pack_id"       , $pack_id);
$smarty->assign("destinataires" , $destinataires);
$smarty->assign("lock_bloked"   , $lock_bloked);
$smarty->assign("can_duplicate" , $can_duplicate);
$smarty->assign("read_only"     , $read_only);

preg_match_all("/(:?\[\[Texte libre - ([^\]]*)\]\])/i", $compte_rendu->_source, $matches);

$templateManager->textes_libres = $matches[2];

// Suppression des doublons
$templateManager->textes_libres = array_unique($templateManager->textes_libres);

if (isset($compte_rendu->_ref_file->_id)) {
  $smarty->assign("file", $compte_rendu->_ref_file);
}

$smarty->assign("textes_libres", $templateManager->textes_libres);

$exchange_source = CExchangeSource::get("mediuser-".$curr_user->_id);
$smarty->assign("exchange_source", $exchange_source, "smtp");

// Ajout d'ent�te / pied de page � la vol�e
$headers = array();
$footers = array();

if (CAppUI::conf("dPcompteRendu CCompteRendu header_footer_fly")) {
  $headers = CCompteRendu::loadAllModelesFor($user->_id, "prat", $compte_rendu->object_class, "header");
  $footers = CCompteRendu::loadAllModelesFor($user->_id, "prat", $compte_rendu->object_class, "footer");
}

$smarty->assign("headers", $headers);
$smarty->assign("footers", $footers);

// Nettoyage des balises meta et link.
// Pose probl�me lors de la pr�sence d'un ent�te et ou/pied de page
$source = &$templateManager->document;

$source = preg_replace("/<meta\s*[^>]*\s*[^\/]>/", '', $source);
$source = preg_replace("/(<\/meta>)+/i", '', $source);
$source = preg_replace("/<link\s*[^>]*\s*>/", '', $source);

$pdf_thumbnails = CAppUI::conf("dPcompteRendu CCompteRendu pdf_thumbnails");
$pdf_and_thumbs = CAppUI::pref("pdf_and_thumbs");

// Chargement du
if ($compte_rendu->_id) {
  $compte_rendu->loadModele();
}

if (CValue::get("reloadzones") == 1) {
  $smarty->display("inc_zones_fields.tpl");
}
else if (
    !$compte_rendu_id &&
    !$switch_mode &&
    ($compte_rendu->fast_edit || $force_fast_edit || ($compte_rendu->fast_edit_pdf && $pdf_thumbnails && $pdf_and_thumbs))
) {
  $printers = $function->loadBackRefs("printers");
  
  if (is_array($printers)) {
    /** @var $_printer CPrinter */
    foreach ($printers as $_printer) {
        $_printer->loadTargetObject();
    }
  }
  
  $smarty->assign("_source"     , $templateManager->document);
  $smarty->assign("printers"    , $printers);
  $smarty->assign("object_guid" , CValue::get("object_guid"));
  $smarty->assign("unique_id"   , CValue::get("unique_id"));
  
  $smarty->display("fast_mode.tpl");
}
else { 
  // Charger le document pr�c�dent et suivant
  $prevnext = array();
  if ($compte_rendu->_id) {
    $object->loadRefsDocs();
    $prevnext = CMbArray::getPrevNextKeys($object->_ref_documents, $compte_rendu->_id);
  }
  
  $templateManager->initHTMLArea();
  $smarty->assign("switch_mode"    , CValue::get("switch_mode", 0));
  $smarty->assign("templateManager", $templateManager);
  $smarty->assign("prevnext", $prevnext);
  $smarty->display("edit_compte_rendu.tpl");
}
