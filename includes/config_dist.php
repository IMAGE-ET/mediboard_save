<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
  
// Needed for module config file inclusions 
// Beginning of this file or installer will fail on config loading.
global $dPconfig; 

/**
 *  Global hosting settings
 *  WARNING: no config documentation in those files
 *  Use instead locales for UI documentation 
 */

// No trailing slash, no backslashes for Win users (use slashes instead)
$dPconfig["root_dir"]          = "/var/www/mediboard";  
$dPconfig["company_name"]      = "mediboard.org";
$dPconfig["page_title"]        = "Mediboard SIH";
$dPconfig["base_url"]          = "http://localhost/mediboard/";

$dPconfig["offline"]           = "0";     
$dPconfig["instance_role"]     = "qualif";
$dPconfig["http_redirections"] = "0";      
$dPconfig["mb_id"]             = "";
$dPconfig["minify_javascript"] = "0";
$dPconfig["minify_css"]        = "0";
$dPconfig["currency_symbol"]   = "&euro;";
$dPconfig["hide_confidential"] = "0";
$dPconfig["locale_warn"]       = "0";
$dPconfig["locale_alert"]      = "^";
$dPconfig["debug"]             = "1";
$dPconfig["readonly"]          = "0";
$dPconfig["shared_memory"]     = "none";
$dPconfig["session_handler"]   = "files";
$dPconfig["log_js_errors"]     = "1";
$dPconfig["weinre_debug_host"] = "";

// Object merge
$dPconfig["alternative_mode"]  = "0";
$dPconfig["merge_prevent_base_without_idex"]  = "1";

$dPconfig["browser_compat"]    = array(
  'firefox' => '3.0',
  'msie'    => '8.0',
  'opera'   => '9.6',
  'chrome'  => '5.0',
  'safari'  => '525.26', // 3.2
);

// Object handlers
$dPconfig["object_handlers"]   = array (
//  "CMyObjectHandler" => true,
);

// Mode migration
$dPconfig["migration"]["active"] = "0";
$dPconfig["migration"]["intranet_url"] = "http://intranet_server/mediboard/";
$dPconfig["migration"]["extranet_url"] = "http://extranet_server/mediboard/";
$dPconfig["migration"]["limit_date"] = "1970-01-01";

// Time format
$dPconfig["date"]     = "%d/%m/%Y";
$dPconfig["time"]     = "%Hh%M";
$dPconfig["datetime"] = "%d/%m/%Y %Hh%M";
$dPconfig["longdate"] = "%A %d %B %Y";
$dPconfig["longtime"] = "%H heures %M minutes";
$dPconfig["timezone"] = "Europe/Paris";

// PHP config
$dPconfig["php"] = array(
  "memory_limit" => "128M"
);

// Standard database config
$dPconfig["db"]["std"] = array(
  "dbtype" => "mysql",     // Change to use another dbms
  "dbhost" => "localhost", // Change to connect to a distant Database
  "dbname" => "", // Change to match your Mediboard Database Name
  "dbuser" => "", // Change to match your Username
  "dbpass" => "", // Change to match your Password
);

// Compatibility mode
$dPconfig["interop"]["mode_compat"] = "default";

// File parsers to return indexing information about uploaded files
$dPconfig["ft"] = array(
  "default"            => "/usr/bin/strings",
  "application/msword" => "/usr/bin/strings",
  "text/html"          => "/usr/bin/strings",
  "application/pdf"    => "/usr/bin/pdftotext",
);

/********* 
 * Mediboard module-specific active configuration settings
 */

$dPconfig["dPadmissions"] = array (
  "fiche_admission" => "a4",
  "show_dh"         => "1",
);

$dPconfig["dPbloc"]["CPlageOp"] = array (
  "hours_start"          => "8",
  "hours_stop"           => "20",
  "minutes_interval"     => "15",
  "plage_vide"           => "0",
  "libelle_ccam"         => "1", 
  "locked"               => "1",
  "planning"             => array (
    "col1" => "interv",
    "col2" => "sejour",
    "col3" => "patient"
  ),
  "chambre_operation"    => "0",
  "impression_suivi"     => "0",
  "id_salles_impression" => "",
);

$dPconfig["dPsante400"] = array (
  "nb_rows" => "5",
  "mark_row" => "0",
  "cache_hours" => "1",
  "dsn" => "",
  "user" => "",
  "pass" => "",
  "group_id" => "",
	"CSejour" => array(
	  "sibling_hours" => 1,
	),
);

$dPconfig["dPlabo"] = array (
  "CCatalogueLabo" => array (
    "remote_name" => "LABO",
    "remote_url"  => "http://localhost/mediboard/modules/dPlabo/remote/catalogue.xml",
  ),
  
  "CPackExamensLabo" => array (
    "remote_url" => "http://localhost/mediboard/modules/dPlabo/remote/pack.xml",
  )
  ,
  "CPrescriptionLabo" => array (
    "url_ftp_prescription"    => "",
    "url_ws_id_prescription"  => "",
    "pass_ws_id_prescription" => "",
    "login_ftp_prescription"  => "",
    "pass_ftp_prescription"   => "",
  ),
);

$dPconfig["system"] = array(
  "phone_number_format" => "99 99 99 99 99",
  "reverse_proxy"  => "0.0.0.0",
  "website_url"    => "http://www.mediboard.org",
);

$dPconfig["hprim21"] = array (
  "CHprim21Reader" => array(
    "hostname"      => "",
    "username"      => "",
    "userpass"      => "",
    "fileextension" => "hpr",
  ),
);

$dPconfig["dPpmsi"] = array(
  "systeme_facturation" => "",
	"server"              => "0",
  "transmission_actes"  => "pmsi",
  "passage_facture"     => "envoi",
); 

$dPconfig["bloodSalvage"] = array (
    "inLivretTherapeutique" => "1",
    "AntiCoagulantList"     => "",
);

$dPconfig["dmi"] = array (
  "CDMI" => array(
    "product_category_id" => "",
    "active" => 0
  ),
  "CDM" => array(
    "product_category_id" => "",
    "active" => 0
  )
);

$dPconfig["mediusers"] = array (
  "tag_mediuser" => ""
);

// Module config file inclusion
$config_files = glob("./modules/*/config.php");
foreach ($config_files as $file) {
  include_once $file;
}

?>