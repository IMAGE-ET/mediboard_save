<?php

/**
 * Pack multiple docs aed
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

// G�n�ration d'un document pour chaque mod�le du pack
$pack_id      = CValue::post("pack_id");
$object_id    = CValue::post("object_id");
$object_class = CValue::post("object_class");
$callback     = CValue::post('callback');
 
$user_id = CMediusers::get()->_id;

$pack = new CPack;
$pack->load($pack_id);

/** @var $object CMbObject */
$object = new $object_class;
$object->load($object_id);

$modele_to_pack = new CModeleToPack();
$modeles_to_pack = $modele_to_pack->loadAllModelesFor($pack_id);

// Sauvegarde du premier compte-rendu pour
// l'afficher dans la popup d'�dition de compte-rendu
$first = reset($modeles_to_pack);

$cr_to_push = null;

$modeles = CMbObject::massLoadFwdRef($modeles_to_pack, "modele_id");
CMbObject::massLoadFwdRef($modeles, "content_id");

/** @var $_modele_to_pack  CModeleToPack */
foreach ($modeles_to_pack as $_modele_to_pack) {
  $modele = $_modele_to_pack->loadRefModele();
  $modele->loadContent();
  
  $template = new CTemplateManager();
  $template->isModele = false;
  
  $object->fillTemplate($template);
  
  $cr = new CCompteRendu();
  
  $cr->modele_id     = $modele->_id;
  $cr->object_class  = $object_class;
  $cr->object_id     = $object_id;
  $cr->author_id     = $user_id;
  $cr->nom           = $modele->nom;
  $cr->margin_right  = $modele->margin_right;
  $cr->margin_left   = $modele->margin_left;
  $cr->margin_top    = $modele->margin_top;
  $cr->margin_bottom = $modele->margin_bottom;
  $cr->file_category_id = $modele->file_category_id;
  $cr->font          = $modele->font;
  $cr->size          = $modele->size;
  $cr->factory       = $modele->factory;

  $cr->loadContent(false);
  
  $cr->_source = $modele->generateDocFromModel();
  $template->applyTemplate($cr);
  $cr->_source = $template->document;
  
  if ($msg = $cr->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }
  
  if ($_modele_to_pack === $first) {
    $cr_to_push = $cr;
  }
}

if ($callback && $cr_to_push) {
  $fields = $cr_to_push->getProperties();
  CAppUI::callbackAjax($callback, $cr_to_push->_id, $fields);
}

CAppUI::setMsg(CAppUI::tr("CPack-msg-create"), UI_MSG_OK);

echo CAppUI::getMsg();

CApp::rip();
