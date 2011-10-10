<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetupsystem extends CSetup {
  
  static function getFieldRenameQueries($object_class, $from, $to) {
    $ds = CSQLDataSource::get("std");
    
    $queries = array();
    
    $queries[] = 
      "UPDATE `user_log` 
       SET   
         `fields` = '$to', 
         `extra`  = REPLACE(`extra`, '\"$from\":', '\"$to\":')
       WHERE 
         `object_class` = '$object_class' AND 
         `fields` = '$from' AND 
         `type` IN('store', 'merge')";
    
    $queries[] = 
      "UPDATE `user_log` 
       SET   
         `fields` = REPLACE(`fields`, ' $from ', ' $to '), 
         `fields` = REPLACE(`fields`, '$from ' , '$to '), 
         `fields` = REPLACE(`fields`, ' $from' , ' $to'), 
         `extra`  = REPLACE(`extra`, '\"$from\":', '\"$to\":')
       WHERE 
         `object_class` = '$object_class' AND 
         `fields` LIKE '%$from%' AND 
         `type` IN('store', 'merge')";
    
    return $queries;
  }
  
  function __construct() {
    parent::__construct();
    
    $this->mod_type = "core";
    $this->mod_name = "system";
    
    $this->makeRevision("all");
    
    $this->makeRevision("1.0.00");
    $query = "CREATE TABLE `access_log` (
      `accesslog_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
      `module` VARCHAR( 40 ) NOT NULL ,
      `action` VARCHAR( 40 ) NOT NULL ,
      `period` DATETIME NOT NULL ,
      `hits` TINYINT DEFAULT '0' NOT NULL ,
      `duration` DOUBLE NOT NULL,
      PRIMARY KEY ( `accesslog_id` )) /*! ENGINE=MyISAM */";
    $this->addQuery($query);
    $query = "ALTER TABLE `access_log` 
      ADD UNIQUE `triplet` (`module` , `action` , `period`)";
    $this->addQuery($query);
    $query = "ALTER TABLE `access_log`
       ADD INDEX ( `module` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `access_log` 
      ADD INDEX ( `action` )";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.01");
    $query = "ALTER TABLE `access_log` CHANGE `
      hits` `hits` INT UNSIGNED DEFAULT '0' NOT NULL ";
    $this->addQuery($query);
    $query = "ALTER TABLE `access_log` 
      ADD `request` DOUBLE NOT NULL ;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.02");
    $query = "ALTER TABLE `access_log` 
      DROP INDEX `action_2` ;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.03");
    $this->setTimeLimit(300);
    $query = "ALTER TABLE `user_log` 
      CHANGE `type` `type` ENUM( 'create', 'store', 'delete' ) NOT NULL; ";
    $this->addQuery($query);
    $query = "ALTER TABLE `user_log` 
      ADD `fields` TEXT;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.04");
    $this->setTimeLimit(300);
    $query = "ALTER TABLE `access_log`
      CHANGE `accesslog_id` `accesslog_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      CHANGE `module` `module` VARCHAR(255) NOT NULL,
      CHANGE `action` `action` VARCHAR(255) NOT NULL,
      CHANGE `hits` `hits` int(11) unsigned NOT NULL DEFAULT '0',
      CHANGE `duration` `duration` float NOT NULL DEFAULT '0',
      CHANGE `request` `request` float NOT NULL DEFAULT '0'; ";
    $this->addQuery($query);
    $query = "ALTER TABLE `message` 
      CHANGE `message_id` `message_id` int(11) unsigned NOT NULL AUTO_INCREMENT; ";
    $this->addQuery($query);
    $query = "ALTER TABLE `user_log`
      CHANGE `user_log_id` `user_log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      CHANGE `user_id` `user_id` int(11) unsigned NOT NULL DEFAULT '0',
      CHANGE `object_id` `object_id` int(11) unsigned NOT NULL DEFAULT '0'; ";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.05");
    $this->setTimeLimit(300);
    $query = "DELETE FROM `user_log` 
      WHERE `object_id` = '0'";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.06");
    $query = "CREATE TABLE `note` (
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
    ) /*! ENGINE=MyISAM */ COMMENT = 'Table des notes sur les objets';";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.07");
    $this->addPrefQuery("MenuPosition", "top");
    
    $this->makeRevision("1.0.08");
    $query = "ALTER TABLE `message`
      ADD `urgence` ENUM('normal','urgent') DEFAULT 'normal' NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.09");
    $query = "ALTER TABLE `message`
      ADD `module_id` INT(11) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.10");
    $query = "ALTER TABLE `user_log`
      CHANGE `object_class` `object_class` VARCHAR(255) NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.11");
    $query = "ALTER TABLE `user_log` 
      CHANGE `type` `type` ENUM ('create','store','merge','delete') NOT NULL,
      ADD INDEX (`date`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.12");
    $query = "ALTER TABLE `access_log` 
      ADD `size` INT (11) UNSIGNED,
      ADD `errors` INT (11) UNSIGNED,
      ADD `warnings` INT (11)  UNSIGNED,
      ADD `notices` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.13");
    $this->addPrefQuery("touchscreen", "0");
    
    $this->makeRevision("1.0.14");
    $this->addPrefQuery("tooltipAppearenceTimeout", "medium");
    
    $this->makeRevision("1.0.15");
    $this->addPrefQuery("showLastUpdate", "0");

    $this->makeRevision("1.0.16");
    $query = "ALTER TABLE `message` 
      ADD INDEX (`module_id`),
      ADD INDEX (`deb`),
      ADD INDEX (`fin`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `modules` 
      DROP `mod_directory`,
      DROP `mod_setup_class`,
      DROP `mod_ui_name`,
      DROP `mod_ui_icon`,
      DROP `mod_description`";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.17");
    $this->addPrefQuery("showTemplateSpans", "0");

    $this->makeRevision("1.0.18");
    $query = "ALTER TABLE `message` 
      ADD `group_id` INT (11) UNSIGNED,
      ADD INDEX (`group_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.0.19");
    $this->setTimeLimit(300);
    $query = "ALTER TABLE `user_log` 
      ADD `ip_address` VARBINARY (16) NULL DEFAULT NULL,
      ADD `extra` TEXT,
      ADD INDEX (`ip_address`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.20");
    $query = "CREATE TABLE `alert` (
      `alert_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `tag` VARCHAR (255) NOT NULL,
      `level` ENUM ('low','medium','high') NOT NULL DEFAULT 'medium',
      `comments` TEXT,
      `handled` ENUM ('0','1') NOT NULL DEFAULT '0',
      `object_id` INT (11) UNSIGNED NOT NULL,
      `object_class` VARCHAR (255) NOT NULL
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `alert` 
      ADD INDEX (`object_id`),
      ADD INDEX (`object_class`),
      ADD INDEX (`tag`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.21");
    $query = "UPDATE user_preferences 
      SET value = 'e-cap' 
      WHERE value = 'tonkin';";
    $this->addQuery($query);
    $query = "UPDATE user_preferences 
      SET value = 'e-cap' 
      WHERE value = 'K-Open';";
    $this->addQuery($query);
    $query = "UPDATE user_preferences 
      SET value = 'mediboard' 
      WHERE value = 'mediboard_lite';";
    $this->addQuery($query);
    $query = "UPDATE user_preferences 
      SET value = 'mediboard' 
      WHERE value = 'mediboard_super_lite';";
    $this->addQuery($query);    
        
    $this->makeRevision("1.0.26");
    $query = "DELETE FROM `modules` 
      WHERE `mod_name` = 'dPinterop'";
    $this->addQuery($query, true);
    
    $this->makeRevision("1.0.27");
    $query = "DELETE FROM `modules` 
      WHERE `mod_name` = 'dPmateriel'";
    $this->addQuery($query, true);
    
    $this->makeRevision("1.0.28");
    $query = "CREATE TABLE IF NOT EXISTS `content_html` (
      `content_id` BIGINT NOT NULL auto_increment PRIMARY KEY,
      `content` TEXT,
      `cr_id` INT
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `content_xml` (
      `content_id` BIGINT NOT NULL auto_increment PRIMARY KEY,
      `content` TEXT,
      `import_id` INT
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.29");
    $query = "ALTER TABLE `content_html`
      CHANGE `content` `content` mediumtext NULL";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.30");
    $this->addPrefQuery("directory_to_watch", "");
    
    $this->makeRevision("1.0.31");
    $this->addPrefQuery("debug_yoplet", "0");
    
    $this->makeRevision("1.0.32");
    $query = "ALTER TABLE `access_log` 
      ADD INDEX ( `period` )";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.34");
    $query = "CREATE TABLE `source_smtp` (
      `source_smtp_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `port` INT (11) DEFAULT '25',
      `email` VARCHAR (50),
      `ssl` ENUM ('0','1') DEFAULT '0',
      `name` VARCHAR  (255) NOT NULL,
      `role` ENUM ('prod','qualif') NOT NULL DEFAULT 'qualif',
      `host` TEXT NOT NULL,
      `user` VARCHAR  (255),
      `password` VARCHAR (50),
      `type_echange` VARCHAR  (255)
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.35");
    $query = "CREATE TABLE `ex_class` (
      `host_class` VARCHAR (255) NOT NULL,
      `event` VARCHAR (255) NOT NULL,
      `ex_class_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "CREATE TABLE `ex_class_field` (
      `ex_class_field_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `ex_class_id` INT (11) UNSIGNED NOT NULL,
      `name` VARCHAR (255) NOT NULL,
      `prop` VARCHAR (255) NOT NULL
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field` 
              ADD INDEX (`ex_class_id`);";
    $this->addQuery($query);
    $query = "CREATE TABLE `ex_class_constraint` (
      `ex_class_constraint_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `ex_class_id` INT (11) UNSIGNED NOT NULL,
      `field` VARCHAR  (255) NOT NULL,
      `operator` ENUM ('=','!=','>','>=','<','<=','startsWith','endsWith','contains') NOT NULL DEFAULT '=',
      `value` VARCHAR  (255) NOT NULL
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_constraint` 
      ADD INDEX (`ex_class_id`);";
    $this->addQuery($query);
    $query = "CREATE TABLE `ex_class_field_translation` (
      `ex_class_field_translation_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `ex_class_field_id` INT (11) UNSIGNED NOT NULL,
      `lang` CHAR  (2),
      `std` VARCHAR  (255),
      `desc` VARCHAR  (255),
      `court` VARCHAR  (255)
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field_translation` 
      ADD INDEX (`ex_class_field_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.36");
    $query = "CREATE TABLE `ex_class_field_enum_translation` (
      `ex_class_field_enum_translation_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `ex_class_field_id` INT (11) UNSIGNED NOT NULL,
      `lang` CHAR  (2),
      `key` VARCHAR  (40),
      `value` VARCHAR  (255)
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field_enum_translation` 
      ADD INDEX (`ex_class_field_id`),
      ADD INDEX (`lang`),
      ADD INDEX (`key`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.37");
    $query = "ALTER TABLE `ex_class` 
      ADD `name` VARCHAR  (255) NOT NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field_translation` 
      ADD INDEX (`lang`)";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.38");
    $query = "ALTER TABLE `ex_class_field` 
      ADD `coord_field_x` TINYINT (4) UNSIGNED,
      ADD `coord_field_y` TINYINT (4) UNSIGNED,
      ADD `coord_label_x` TINYINT (4) UNSIGNED,
      ADD `coord_label_y` TINYINT (4) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.39");
    $query = "CREATE TABLE `ex_class_host_field` (
      `ex_class_host_field_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `ex_class_id` INT (11) UNSIGNED NOT NULL,
      `field` VARCHAR (80) NOT NULL,
      `coord_label_x` TINYINT (4) UNSIGNED,
      `coord_label_y` TINYINT (4) UNSIGNED,
      `coord_value_x` TINYINT (4) UNSIGNED,
      `coord_value_y` TINYINT (4) UNSIGNED
      ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_host_field` 
      ADD INDEX (`ex_class_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.40");
    $query = "ALTER TABLE `ex_class_field` 
      CHANGE `ex_class_id` `ex_class_id` INT (11) UNSIGNED,
      ADD `concept_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field` 
      ADD INDEX (`concept_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.41");
    $query = "ALTER TABLE `ex_class` 
      ADD `disabled` ENUM ('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($query);
      
    $this->makeRevision("1.0.42");
    $query = "CREATE TABLE `content_tabular` (
      `content_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `content` TEXT,
      `import_id` INT (11),
      `separator` CHAR (1)
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.43");
    $query = "CREATE TABLE `tag` (
              `tag_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `parent_id` INT (11) UNSIGNED,
              `object_class` VARCHAR (80),
              `name` VARCHAR (255) NOT NULL
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `tag` 
              ADD INDEX (`parent_id`),
              ADD INDEX (`object_class`);";
    $this->addQuery($query);
    $query = "CREATE TABLE `tag_item` (
              `tag_item_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `tag_id` INT (11) UNSIGNED NOT NULL,
              `object_id` INT (11) UNSIGNED NOT NULL,
              `object_class` VARCHAR (80) NOT NULL
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `tag_item` 
              ADD INDEX (`tag_id`),
              ADD INDEX (`object_id`),
              ADD INDEX (`object_class`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.44");
    $query = "ALTER TABLE `tag` 
              ADD `color` VARCHAR (20);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.45");
    $query = "CREATE TABLE `ex_list` (
              `ex_list_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `name` VARCHAR (255) NOT NULL
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "CREATE TABLE `ex_list_item` (
              `ex_list_item_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `list_id` INT (11) UNSIGNED NOT NULL,
              `name` VARCHAR (255) NOT NULL,
              `value` INT (11)
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_list_item` 
              ADD INDEX (`list_id`);";
    $this->addQuery($query);
    $query = "CREATE TABLE `ex_concept` (
              `ex_concept_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `ex_list_id` INT (11) UNSIGNED,
              `name` VARCHAR (255) NOT NULL,
              `prop` VARCHAR (255) NOT NULL
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_concept` ADD INDEX (`ex_list_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.0.46");
    $this->addPrefQuery("pdf_and_thumbs", "1");
    
    $this->makeRevision("1.0.47");
    $query = "ALTER TABLE `ex_list` 
              ADD `coded` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_list_item` 
              ADD `code` CHAR (20);";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_list_item` 
              DROP `value`";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_list_item` 
              CHANGE `list_id` `list_id` INT (11) UNSIGNED,
              ADD `concept_id` INT (11) UNSIGNED,
              ADD `field_id` INT (11) UNSIGNED";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_list_item` 
              ADD INDEX (`concept_id`),
              ADD INDEX (`field_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.48");
    $query = "CREATE TABLE `ex_class_field_group` (
              `ex_class_field_group_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `ex_class_id` INT (11) UNSIGNED,
              `name` VARCHAR (255) NOT NULL
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field_group` 
              ADD INDEX (`ex_class_id`);";
    $this->addQuery($query);
    $query = "INSERT INTO `ex_class_field_group` (`name`, `ex_class_id`)
              SELECT 'Groupe principal', `ex_class`.`ex_class_id` FROM `ex_class`";
    $this->addQuery($query);
    
    // class field
    $query = "ALTER TABLE `ex_class_field` 
              ADD `ex_group_id` INT (11) UNSIGNED";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field` 
              ADD INDEX (`ex_group_id`)";
    $this->addQuery($query);
    $query = "UPDATE `ex_class_field` 
              SET `ex_group_id` = (
                SELECT `ex_class_field_group`.`ex_class_field_group_id` 
                FROM `ex_class_field_group` 
                WHERE `ex_class_field_group`.`ex_class_id` = `ex_class_field`.`ex_class_id`
                LIMIT 1
              )";
    $this->addQuery($query);
              
    // class host field
    $query = "ALTER TABLE `ex_class_host_field` 
              ADD `ex_group_id` INT (11) UNSIGNED";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_host_field` 
              ADD INDEX (`ex_group_id`)";
    $this->addQuery($query);
    $query = "UPDATE `ex_class_host_field` 
              SET `ex_group_id` = (
                SELECT `ex_class_field_group`.`ex_class_field_group_id` 
                FROM `ex_class_field_group` 
                WHERE `ex_class_field_group`.`ex_class_id` = `ex_class_host_field`.`ex_class_id`
                LIMIT 1
              )";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.49");
    $query = "ALTER TABLE `ex_class_field` 
              CHANGE `prop` `prop` TEXT NOT NULL";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.50");
    $query = "CREATE TABLE `source_file_system` (
                `source_file_system_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `name` VARCHAR (255) NOT NULL,
                `role` ENUM ('prod','qualif') NOT NULL DEFAULT 'qualif',
                `host` TEXT NOT NULL,
                `user` VARCHAR (255),
                `password` VARCHAR (50),
                `type_echange` VARCHAR (255)
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.51");
    function update_ex_object_tables(){
      $ds = CSQLDataSource::get("std");
      
      if ($ds) {
        $ex_classes = $ds->loadList("SELECT * FROM ex_class");
        foreach($ex_classes as $_ex_class) {
          $_ex_class['host_class'] = strtolower($_ex_class['host_class']);
          
          $old_name = "ex_{$_ex_class['host_class']}_{$_ex_class['event']}_{$_ex_class['ex_class_id']}";
          $new_name = "ex_object_{$_ex_class['ex_class_id']}";
          
          $query = "RENAME TABLE `$old_name` TO `$new_name`";
          $ds->query($query);
        }
      }
      
      return true;
    }
    
    $this->addFunction("update_ex_object_tables");

    $this->makeRevision("1.0.52");
    $query = "CREATE TABLE `view_sender` (
      `sender_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `source_id` INT (11) UNSIGNED,
      `name` VARCHAR (255) NOT NULL,
      `description` TEXT,
      `params` TEXT NOT NULL,
      `period` ENUM ('1','2','3','4','5','6','10','15','20','30'),
      `offset` INT (11) UNSIGNED
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `view_sender` 
      ADD INDEX (`source_id`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `view_sender_source` (
      `source_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `name` VARCHAR (255) NOT NULL
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `view_sender` 
      ADD INDEX (`source_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.53");
    
    $query = "ALTER TABLE `source_smtp` 
      ADD `active` ENUM ('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `source_file_system` 
      ADD `active` ENUM ('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.54");
    
    $query = "ALTER TABLE `view_sender` 
      ADD `active` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.55");
    
    $query = "ALTER TABLE `view_sender` 
      CHANGE `offset` `offset` INT (11) UNSIGNED NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.56");
    $query = "ALTER TABLE `ex_class` 
              ADD `conditional` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.57");
    $query = "CREATE TABLE `ex_class_field_trigger` (
              `ex_class_field_trigger_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `ex_class_field_id` INT (11) UNSIGNED NOT NULL,
              `ex_class_triggered_id` INT (11) UNSIGNED NOT NULL,
              `trigger_value` VARCHAR (255) NOT NULL
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field_trigger` 
              ADD INDEX (`ex_class_field_id`),
              ADD INDEX (`ex_class_triggered_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.58");
    $query = "ALTER TABLE `ex_class_field_group` 
              ADD `formula` TEXT;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.59");
    $query = "ALTER TABLE `ex_class_field_group` 
              ADD `formula_result_field_id` INT (11) UNSIGNED";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field_group` 
              ADD INDEX (`formula_result_field_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.60");
    $query = "ALTER TABLE `ex_class_field`
              ADD `formula` TEXT;";
    $this->addQuery($query);
    $query = "UPDATE `ex_class_field` 
      LEFT JOIN `ex_class_field_group` ON `ex_class_field_group`.`formula_result_field_id` = `ex_class_field`.`ex_class_field_id`
      SET `ex_class_field`.`formula` = `ex_class_field_group`.`formula`";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field_group` 
      DROP `formula`, 
      DROP `formula_result_field_id`;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.61");
    $query = "ALTER TABLE `ex_list` 
              ADD `multiple` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.62");
    $query = "ALTER TABLE `ex_class` 
              ADD `required` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.63");
    $query = "CREATE TABLE `http_redirection` (
               `http_redirection_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
               `priority` INT (11) NOT NULL DEFAULT '0',
               `from` VARCHAR (255) NOT NULL,
               `to` VARCHAR (255) NOT NULL
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.64");
    $query = "CREATE TABLE `ex_class_message` (
              `ex_class_message_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `ex_group_id` INT (11) UNSIGNED NOT NULL,
              `type` ENUM ('info','warning','error'),
              `title` VARCHAR (255) NOT NULL,
              `text` TEXT NOT NULL,
              `coord_title_x` TINYINT (4) UNSIGNED,
              `coord_title_y` TINYINT (4) UNSIGNED,
              `coord_text_x` TINYINT (4) UNSIGNED,
              `coord_text_y` TINYINT (4) UNSIGNED
              ) /*! ENGINE=MyISAM */";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_message` 
              ADD INDEX (`ex_group_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.65");
    function setup_system_addExReferenceFields(){
      set_time_limit(1800);
      ignore_user_abort(1);
      
      $ds = CSQLDataSource::get("std");
 
      // Changement des chirurgiens
      $query = "SELECT ex_class_id FROM ex_class";
      $list_ex_class = $ds->loadHashAssoc($query);
      foreach($list_ex_class as $key => $hash) {
        $query = "ALTER TABLE `ex_object_$key` 
              ADD `reference_id` INT (11) UNSIGNED AFTER `object_class`,
              ADD `reference_class` VARCHAR(80) AFTER `object_class`";
        $ds->exec($query);
      }
      return true;
    }
    $this->addFunction("setup_system_addExReferenceFields");
    $query = "ALTER TABLE `ex_class_field`
              ADD `reported` ENUM ('0','1') NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.66");
    $query = "ALTER TABLE `ex_class_message` 
              CHANGE `type` `type` ENUM ('title','info','warning','error');";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.67");
    function setup_system_addExReferenceFields2(){
      set_time_limit(1800);
      ignore_user_abort(1);
      
      $ds = CSQLDataSource::get("std");
 
      // Changement des chirurgiens
      $query = "SELECT ex_class_id FROM ex_class";
      $list_ex_class = $ds->loadHashAssoc($query);
      foreach($list_ex_class as $key => $hash) {
        $query = "ALTER TABLE `ex_object_$key` 
              ADD `reference2_id` INT (11) UNSIGNED AFTER `reference_id`,
              ADD `reference2_class` VARCHAR(80) AFTER `reference_id`";
        $ds->exec($query);
      }
      return true;
    }
    $this->addFunction("setup_system_addExReferenceFields2");
    $query = "ALTER TABLE `ex_class_field` 
              CHANGE `reported` `report_level` ENUM ('1','2')";
    $this->addQuery($query);

    $this->makeRevision("1.0.68");
    $this->addPrefQuery("autocompleteDelay", "short");
    
    $this->makeRevision("1.0.69");
    
    $query = "ALTER TABLE `view_sender_source` 
                ADD `libelle` VARCHAR (255),
                ADD `group_id` INT (11) UNSIGNED NOT NULL,
                ADD `actif` ENUM ('0','1') NOT NULL DEFAULT '0';"; 
    $this->addQuery($query);
    
    $query = "ALTER TABLE `view_sender_source` 
                ADD INDEX (`group_id`);"; 
    $this->addQuery($query);
    
    $this->makeRevision("1.0.70");
    
    $query = "ALTER TABLE `view_sender` 
                DROP `source_id`;";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `source_to_view_sender` (
                `source_to_view_sender_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `source_id` INT (11) UNSIGNED NOT NULL,
                `sender_id` INT (11) UNSIGNED NOT NULL
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.71");
    
    $query = "ALTER TABLE `view_sender` 
              ADD `max_archives` INT (11) UNSIGNED NOT NULL DEFAULT '10';";
    $this->addQuery($query);

    $query = "ALTER TABLE `source_to_view_sender` 
              ADD `last_datetime` DATETIME,
              ADD `last_status` ENUM ('triggered','uploaded','checked'),
              ADD `last_count` INT (11) UNSIGNED;";
    $this->addQuery($query);
           
    $query = "ALTER TABLE `source_to_view_sender` 
              ADD INDEX (`last_datetime`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.72");

    $query = "ALTER TABLE `source_to_view_sender` 
              ADD `last_duration` FLOAT,
              ADD `last_size` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.73");
    
    $this->addPrefQuery("moduleFavicon", "0");
    
    $this->makeRevision("1.0.74");
    
    $this->addPrefQuery("showCounterTip", "1");
    
    $this->makeRevision("1.0.75");
    
    $this->setTimeLimit(300);
    $query = "ALTER TABLE `access_log`
              ADD `processus` FLOAT,
              ADD `processor` FLOAT,
              ADD `peak_memory` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $this->makeRevision("1.0.76");
    $query = "ALTER TABLE `note` 
      CHANGE `degre` `degre` ENUM ('low','medium','high') NOT NULL DEFAULT 'low',
      CHANGE `object_class` `object_class` VARCHAR (80) NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.77");
    $query = "ALTER TABLE `note` 
      CHANGE `user_id` `user_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.78");
    $query = "CREATE TABLE `content_any` (
                `content_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `content` TEXT,
                `import_id` INT (11)
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.79");
    $query = "CREATE TABLE `session` (
               `session_id` VARCHAR(32) NOT NULL PRIMARY KEY,
               `date_creation` INT(11),
               `date_modification` INT(11),
               `user_id` INT (11) NOT NULL DEFAULT '0',
               `user_ip` VARBINARY (16),
               `user_agent` VARCHAR(100),
               `data` BLOB
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.80");
    $query = "ALTER TABLE `ex_class` 
              ADD `unicity` ENUM ('no','host','reference1','reference2') NOT NULL DEFAULT 'no';";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.81");
    
    $query = "ALTER TABLE `source_smtp` 
              ADD `loggable` ENUM ('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `source_file_system` 
              ADD `loggable` ENUM ('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.82");
    $queries = array();
    $queries = self::getFieldRenameQueries("CSejour", "etablissement_entree_transfert_id", "etablissement_entree_id");
    $queries = array_merge($queries, self::getFieldRenameQueries("CSejour", "etablissement_transfert_id", "etablissement_sortie_id"));
    $queries = array_merge($queries, self::getFieldRenameQueries("CSejour", "service_entree_mutation_id", "service_entree_id"));
    $queries = array_merge($queries, self::getFieldRenameQueries("CSejour", "service_mutation_id", "service_sortie_id"));
    
    foreach ($queries as $query) {
      $this->addQuery($query);
    }
    
    $this->makeRevision("1.0.83");
    $query = "ALTER TABLE `ex_class` 
              ADD `group_id` INT (11) UNSIGNED,
              ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $this->mod_version = "1.0.84";
  }
}
