<?php

/**
 * Configuration de dompdf
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CApp::setMemoryLimit("256M");

// Le type de @media accept� par DOMPDF  
define("DOMPDF_DEFAULT_MEDIA_TYPE", "print");

// Urls pour les images accept�es
define("DOMPDF_ENABLE_REMOTE", true);

// Backend de DOMPDF
define("DOMPDF_PDF_BACKEND", "CPDF");

// Police par d�faut
define("DOMPDF_DEFAULT_FONT", "sans-serif");

// PHP inline desactiv�
define("DOMPDF_ENABLE_PHP", false);

// Hauteur de ligne
define("DOMPDF_FONT_HEIGHT_RATIO", 1.0);

// R�pertoire des fonts
$font_dir = CAppUI::conf("dPcompteRendu CCompteRendu font_dir");
if ($font_dir) {
  define("DOMPDF_FONT_DIR"  , $font_dir);
  define("DOMPDF_FONT_CACHE", $font_dir);
}