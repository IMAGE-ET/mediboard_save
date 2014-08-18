<?php

/**
 * Pr�f�rences utilisateur
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */


// Pr�f�rences par Module
CPreferences::$modules["dPcompteRendu"] = array(
  "saveOnPrint",
  "choicepratcab",
  "listDefault",
  "listBrPrefix",
  "listInlineSeparator",
  "aideTimestamp",
  "aideOwner",
  "aideFastMode",
  "aideAutoComplete",
  "aideShowOver",
  "pdf_and_thumbs",
  "mode_play",
  "multiple_docs",
  "auto_capitalize",
  "auto_replacehelper",
  'hprim_med_header',
  "show_old_print",
);
