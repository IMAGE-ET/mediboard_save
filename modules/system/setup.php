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
$config["mod_version"]     = "1.0.10";
$config["mod_type"]        = "core";

class CSetupsystem extends CSetup {
  
  function remove() {
    global $AppUI;
    $AppUI->setMsg("Impossible de supprimer le module 'system'", UI_MSG_ERROR, true);
    return null;
  }
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "system";
    
    $this->makeRevision("all");
    
    $this->makeRevision("1.0.0");
    $sql = "CREATE TABLE `access_log` (" .
            "\n`accesslog_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ," .
            "\n`module` VARCHAR( 40 ) NOT NULL ," .
            "\n`action` VARCHAR( 40 ) NOT NULL ," .
            "\n`period` DATETIME NOT NULL ," .
            "\n`hits` TINYINT DEFAULT '0' NOT NULL ," .
            "\n`duration` DOUBLE NOT NULL," .
            "\nPRIMARY KEY ( `accesslog_id` )) TYPE=MyISAM";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `access_log` ADD UNIQUE `triplet` (`module` , `action` , `period`)";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `access_log` ADD INDEX ( `module` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `access_log` ADD INDEX ( `action` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `access_log` ADD INDEX ( `action` )";
    $this->addQuery($sql);
    
    $this->makeRevision("1.0.1");
    $sql = "ALTER TABLE `access_log` CHANGE `hits` `hits` INT UNSIGNED DEFAULT '0' NOT NULL ";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `access_log` ADD `request` DOUBLE NOT NULL ;";
    $this->addQuery($sql);
    
    $this->makeRevision("1.0.2");
    $sql = "ALTER TABLE `access_log` DROP INDEX `action_2` ;";
    $this->addQuery($sql);
    
    $this->makeRevision("1.0.3");
    $this->setTimeLimit(300);
    $sql = "ALTER TABLE `user_log` CHANGE `type` `type` ENUM( 'create', 'store', 'delete' ) NOT NULL; ";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `user_log` ADD `fields` TEXT;";
    $this->addQuery($sql);
    
    $this->makeRevision("1.0.4");
    $this->setTimeLimit(300);
    $sql = "ALTER TABLE `access_log` " .
               "\nCHANGE `accesslog_id` `accesslog_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `module` `module` VARCHAR(255) NOT NULL," .
               "\nCHANGE `action` `action` VARCHAR(255) NOT NULL," .
               "\nCHANGE `hits` `hits` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `duration` `duration` float NOT NULL DEFAULT '0'," .
               "\nCHANGE `request` `request` float NOT NULL DEFAULT '0'; ";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `message` CHANGE `message_id` `message_id` int(11) unsigned NOT NULL AUTO_INCREMENT; ";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `user_log` " .
               "\nCHANGE `user_log_id` `user_log_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `user_id` `user_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `object_id` `object_id` int(11) unsigned NOT NULL DEFAULT '0'; ";
    $this->addQuery($sql);
    
    $this->makeRevision("1.0.5");
    $this->setTimeLimit(300);
    $sql = "DELETE FROM `user_log` WHERE `object_id` = '0'";
    $this->addQuery($sql);
    
    $this->makeRevision("1.0.6");
    $sql = "CREATE TABLE `note` (
              `note_id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
              `user_id` INT( 11 ) UNSIGNED NOT NULL ,
              `object_id` INT( 11 ) UNSIGNED NOT NULL ,
              `object_class` VARCHAR( 25 ) NOT NULL ,
              `public` ENUM('0','1') NOT NULL DEFAULT '0',
              `degre` ENUM('low','high') NOT NULL DEFAULT 'low',
              `date` DATETIME NOT NULL ,
              `libelle` VARCHAR( 255 ) NOT NULL ,
              `text` TEXT NULL ,
              INDEX ( `user_id` , `object_id` , `object_class` , `public` , `degre` , `date` )
            ) ENGINE = MYISAM COMMENT = 'Table des notes sur les objets';";
    $this->addQuery($sql);
    
    $this->makeRevision("1.0.7");
    $sql = "INSERT INTO `user_preferences` ( `pref_user` , `pref_name` , `pref_value` )" .
        "\nVALUES ('0', 'MenuPosition', 'top');";
    $this->addQuery($sql);
    
    $this->makeRevision("1.0.8");
    $sql = "ALTER TABLE `message`
            ADD `urgence` ENUM('normal','urgent') DEFAULT 'normal' NOT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("1.0.9");
    $sql = "ALTER TABLE `message`
            ADD `module_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);
    
    $this->mod_version = "1.0.10";
  }
}
?>