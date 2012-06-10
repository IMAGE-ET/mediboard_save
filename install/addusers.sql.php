<?php
/**
 * Generate systtem user SQL queries
 * 
 * @package    Mediboard
 * @subpackage install
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Id$
 */

$queries = array();
foreach ($dbConfigs as $dbConfigName => $dbConfig) {
  $host = $dbConfig["dbhost"];
  $name = $dbConfig["dbname"];
  $user = $dbConfig["dbuser"];
  $pass = $dbConfig["dbpass"];
  
  // Create database
  $queries[] = "CREATE DATABASE `$name` ;";

  // Create user with global permissions
  $queries[] = 
   "GRANT USAGE".($dbConfigName == "std" ? ", RELOAD " : "")."
    ON * . * 
    TO '$user'@'$host'
    IDENTIFIED BY '$pass';";
      
  // Grant user with database permissions
  $queries[] = 
   "GRANT SELECT , INSERT , UPDATE , DELETE , CREATE , DROP , INDEX , ALTER , CREATE TEMPORARY TABLES
    ON `$name` . *
    TO '$user'@'$host';";
}

?>