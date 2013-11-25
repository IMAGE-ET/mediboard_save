<?php

/**
 * Configurations du module
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */
$dPconfig["dPcompteRendu"] = array (
  "CCompteRendu" => array (
    "pdf_thumbnails" => '0',
    "same_print" => '0',
    "timestamp" => '-- %n %p - dd/MM/y HH:mm',
    "time_before_thumbs" => '3',
    "multiple_doc_correspondants" => "0",
    "header_footer_fly" => "0",
    "clean_word" => "1",
    "arch_wkhtmltopdf" => "i386",
    "check_to_empty_field" => "1",
    "default_font" => "Georgia",
    "default_size" => "small",
    "dompdf_host" => "0",
    "days_to_lock" => array(
      "base" => "30",
    ),
    "pass_lock" => "0"
  ),
);
