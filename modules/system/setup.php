<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "system";
$config["mod_version"]     = "1.0.4";
$config["mod_type"]        = "core";
$config["mod_config"]      = true;

if(@$a == "setup") {
  echo dPshowModuleConfig($config);
}

class CSetupsystem {

  function configure() {
    global $AppUI;
    $AppUI->redirect("m=system&a=configure");
    return true;
  }

  function remove() {
    return "impossible de supprimer le module 'system'";
  }

  function upgrade($old_version) {
    switch ($old_version) {
      case "all":
      case "1.0.0":
        $sql = "CREATE TABLE `access_log` (" .
            "\n`accesslog_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ," .
            "\n`module` VARCHAR( 40 ) NOT NULL ," .
            "\n`action` VARCHAR( 40 ) NOT NULL ," .
            "\n`period` DATETIME NOT NULL ," .
            "\n`hits` TINYINT DEFAULT '0' NOT NULL ," .
            "\n`duration` DOUBLE NOT NULL," .
            "\nPRIMARY KEY ( `accesslog_id` )) TYPE=MyISAM";
         db_exec($sql); db_error();

         $sql = "ALTER TABLE `access_log` " .
            "\nADD UNIQUE `triplet` (`module` , `action` , `period`)";
         db_exec($sql); db_error();

         $sql = "ALTER TABLE `access_log` " .
            "\nADD INDEX ( `module` )";
         db_exec($sql); db_error();
         
         $sql = "ALTER TABLE `access_log` " .
            "\nADD INDEX ( `action` )";
         db_exec($sql); db_error();

         $sql = "ALTER TABLE `access_log` " .
            "\nADD INDEX ( `action` )";
         db_exec($sql); db_error();

      case "1.0.1":
         $sql = "ALTER TABLE `access_log` CHANGE `hits` `hits` INT UNSIGNED DEFAULT '0' NOT NULL ";
         db_exec($sql); db_error();
         $sql = "ALTER TABLE `access_log` ADD `request` DOUBLE NOT NULL ;";
         db_exec($sql); db_error();

      case "1.0.2":
        $sql = "ALTER TABLE `access_log` DROP INDEX `action_2` ;";
        db_exec($sql); db_error();
         
      case "1.0.3":
        $old = set_time_limit(300);

        $sql = "ALTER TABLE `user_log` DROP INDEX `type` ;";
        db_exec($sql); db_error();
        
        $sql = "ALTER TABLE `user_log` CHANGE `type` `type` ENUM( 'create', 'store', 'delete' ) NOT NULL; ";
        db_exec($sql); db_error();
        
        $sql = "ALTER TABLE `user_log` ADD `fields` TEXT;";
        db_exec($sql); db_error();

        set_time_limit($old);
      case "1.0.4":
        return "1.0.4";
    }
    return false;
  }
}

?>