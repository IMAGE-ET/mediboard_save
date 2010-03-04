<?php /* $Id: ajax_test_dsn.php 6069 2009-04-14 10:17:11Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;

$can->needsAdmin();

// Check params
if (null == $exchange_source_name = CValue::get("exchange_source_name")) {
  CAppUI::stepAjax("Aucun nom de source d'�change sp�cifi�", UI_MSG_ERROR);
}

$exchange_source = CExchangeSource::get($exchange_source_name);

$ftp = new CFTP();
$ftp->init($exchange_source);

if (!$ftp->testSocket()) {
  CAppUI::stepAjax("Connexion au serveur $ftp->hostname' �chou�e", UI_MSG_WARNING);
} else {
  CAppUI::stepAjax("Connect� au serveur $ftp->hostname sur le port $ftp->port");
}

if (!$ftp->connect()) {
  CAppUI::stepAjax("Impossible de se connecter au serveur $ftp->hostname", UI_MSG_ERROR);
} else {
  CAppUI::stepAjax("Connect� au serveur $ftp->hostname et authentifi� en tant que $ftp->username");
}

if($ftp->passif_mode) {
  CAppUI::stepAjax("Activation du mode passif");
}

$local_file = CAppUI::conf('root_dir')."/offline.php";
$remote_file = "offline.php";
if (!$ftp->sendFile($local_file, $remote_file)) {
  CAppUI::stepAjax("Impossible de copier le fichier source $local_file en fichier cible $remote_file", UI_MSG_ERROR);
} else {
  CAppUI::stepAjax("Fichier source $local_file copi� en fichier cible $remote_file");
}

$destination_file = "tmp/offline.php";
if (!$ftp->getFile($remote_file, $destination_file)) {
  CAppUI::stepAjax("Impossible de r�cup�rer le fichier source $remote_file en fichier cible $destination_file", UI_MSG_ERROR);
} else {
  CAppUI::stepAjax("Fichier source $remote_file r�cup�r� en fichier cible $destination_file");
}

if (!$ftp->delFile($remote_file)) {
  CAppUI::stepAjax("Impossible de supprimer le fichier $remote_file", UI_MSG_ERROR);
} else {
  CAppUI::stepAjax("Fichier $remote_file supprim�");
}
