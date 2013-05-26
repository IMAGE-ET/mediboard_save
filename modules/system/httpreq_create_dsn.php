<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

global $dPconfig;

CCanDo::checkAdmin();

// Check params
if (null == $dsn = CValue::get("dsn")) {
  CAppUI::stepAjax("Aucun DSN sp�cifi�", UI_MSG_ERROR);
}

if (null == @$dPconfig["db"][$dsn]) {
  CAppUI::stepAjax("Configuration pour le DSN '$dsn' inexistante", UI_MSG_ERROR);
}

$dsConfig =& $dPconfig["db"][$dsn];
if ("mysql" != $dbtype = $dsConfig["dbtype"]) {
  CAppUI::stepAjax("Seules les DSN MySQL peuvent �tre cr��es par un acc�s administrateur", UI_MSG_ERROR);
}

// Substitute admin access
$user = $dsConfig["dbuser"];
$pass = $dsConfig["dbpass"];
$name = $dsConfig["dbname"];

$dsConfig["dbuser"] = CValue::get("master_user");
$dsConfig["dbpass"] = CValue::get("master_pass");
$dsConfig["dbname"] = "";

if (null == $ds = @CSQLDataSource::get($dsn)) {
  CAppUI::stepAjax("Connexion en tant qu'administrateur �chou�e", UI_MSG_ERROR);
}

CAppUI::stepAjax("Connexion en tant qu'administrateur r�ussie");

foreach ($ds->queriesForDSN($user, $pass, $name) as $key => $query) {
  if (!$ds->exec($query)) {
    CAppUI::stepAjax("Requ�te '$key' �chou�e", UI_MSG_WARNING);
    continue;
  }
  
  CAppUI::stepAjax("Requ�te '$key' effectu�e");
}

