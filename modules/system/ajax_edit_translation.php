<?php
/**
 * Edit Translation
 *
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */
 
CCanDo::checkEdit();

$translation_id = CValue::getOrSession("trad_id");
$language = CValue::getOrSession("language", CAppUI::pref("LOCALE", "fr"));
$languages = CAppUI::getAvailableLanguages();

$translation = new CTranslationOverwrite();
$translation->load($translation_id);

if ($translation->_id) {
  $translation->loadOldTranslation();
}

//smarty
$smarty = new CSmartyDP();
$smarty->assign("translation", $translation);
$smarty->assign("language", $language);
$smarty->assign("languages", $languages);
$smarty->display("inc_edit_translation.tpl");