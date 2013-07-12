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

class CSetupsystem extends CSetup {

  function __construct() {
    parent::__construct();

    $this->mod_type = "core";
    $this->mod_name = "system";

    $this->makeRevision("all");

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
    $query = "ALTER TABLE `ex_concept` 
      ADD INDEX (`ex_list_id`);";
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
    function update_ex_object_tables() {
      $ds = CSQLDataSource::get("std");

      if ($ds) {
        $ex_classes = $ds->loadList("SELECT * FROM ex_class");
        foreach ($ex_classes as $_ex_class) {
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
      CApp::setTimeLimit(1800);
      ignore_user_abort(1);

      $ds = CSQLDataSource::get("std");

      // Changement des chirurgiens
      $query = "SELECT ex_class_id FROM ex_class";
      $list_ex_class = $ds->loadHashAssoc($query);
      foreach ($list_ex_class as $key => $hash) {
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

    function setup_system_addExReferenceFields2() {
      CApp::setTimeLimit(1800);
      ignore_user_abort(1);

      $ds = CSQLDataSource::get("std");

      // Changement des chirurgiens
      $query = "SELECT ex_class_id FROM ex_class";
      $list_ex_class = $ds->loadHashAssoc($query);
      foreach ($list_ex_class as $key => $hash) {
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

    $this->addPrefQuery("showCounterTip", 1);

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

    $this->makeEmptyRevision("1.0.82");

    $this->makeRevision("1.0.83");
    $query = "ALTER TABLE `ex_class` 
      ADD `group_id` INT (11) UNSIGNED,
      ADD INDEX (`group_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.0.84");
    $this->addPrefQuery("textareaToolbarPosition", "right");

    $this->makeRevision("1.0.85");
    $query = "ALTER TABLE `view_sender_source` 
      ADD `archive` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.0.86");
    $query = "ALTER TABLE `view_sender` 
      CHANGE `period` `period` ENUM ('1','2','3','4','5','6','10','15','20','30','60');";
    $this->addQuery($query);

    $this->makeRevision("1.0.87");
    $query = "ALTER TABLE `source_file_system` 
      ADD `fileextension` VARCHAR (255)";
    $this->addQuery($query);

    $this->makeRevision("1.0.88");
    $query = "ALTER TABLE `source_smtp` 
      ADD `timeout` INT (11) DEFAULT '5',
      ADD `debug` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    /* 
    $query = "ALTER TABLE `ex_class_field` 
      ADD `coord_field_colspan` TINYINT (4) UNSIGNED NOT NULL DEFAULT '1' AFTER `coord_field_y`,
      ADD `coord_field_rowspan` TINYINT (4) UNSIGNED NOT NULL DEFAULT '1' AFTER `coord_field_colspan`";
    $this->addQuery($query);
    */

    $this->makeRevision("1.0.89");
    $query = "ALTER TABLE `source_file_system` 
      ADD `fileextension_write_end` VARCHAR (255);";
    $this->addQuery($query);

    $this->makeRevision("1.0.90");
    $query = "ALTER TABLE `ex_class_field` 
      CHANGE `report_level` `report_level` ENUM ('1','2','host')";
    $this->addQuery($query);

    $this->makeRevision("1.0.91");
    $query = "ALTER TABLE `ex_class_constraint` 
      ADD INDEX (`field`)";
    $this->addQuery($query);
    function setup_system_addExReferenceFieldsIndex() {
      CApp::setTimeLimit(1800);
      ignore_user_abort(1);

      $ds = CSQLDataSource::get("std");

      // Changement des chirurgiens
      $query = "SELECT ex_class_id FROM ex_class";
      $list_ex_class = $ds->loadHashAssoc($query);
      foreach ($list_ex_class as $key => $hash) {
        $query = "ALTER TABLE `ex_object_$key` 
          ADD INDEX(`reference_id`),
          ADD INDEX(`reference_class`),
          ADD INDEX(`reference2_id`),
          ADD INDEX(`reference2_class`)";
        $ds->exec($query);
      }
      return true;
    }
    $this->addFunction("setup_system_addExReferenceFieldsIndex");

    $this->makeRevision("1.0.92");

    $query = "CREATE TABLE `sender_file_system` (
        `sender_file_system_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
        `user_id` INT (11) UNSIGNED,
        `nom` VARCHAR (255) NOT NULL,
        `libelle` VARCHAR (255),
        `group_id` INT (11) UNSIGNED NOT NULL,
        `actif` ENUM ('0','1') NOT NULL DEFAULT '0'
      ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `sender_file_system` 
      ADD INDEX (`user_id`),
      ADD INDEX (`group_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.0.93");
    $query = "ALTER TABLE `ex_class_host_field` 
      ADD `host_type` ENUM ('host','reference1','reference2') DEFAULT 'host'";
    $this->addQuery($query);

    $this->makeRevision("1.0.94");
    $query = "ALTER TABLE `ex_class_message` 
      CHANGE `title` `title` VARCHAR (255)";
    $this->addQuery($query);

    $this->makeRevision("1.0.95");
    $query = "ALTER TABLE `sender_file_system` 
        ADD `save_unsupported_message` ENUM ('0','1') DEFAULT '1',
        ADD `create_ack_file` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("1.0.96");

    function setup_system_addExObjectGroupId() {
      CApp::setTimeLimit(1800);
      ignore_user_abort(1);

      $ds = CSQLDataSource::get("std");

      // Changement des ExClasses
      $query = "SELECT ex_class_id, host_class FROM ex_class";
      $list_ex_class = $ds->loadHashAssoc($query);
      foreach ($list_ex_class as $key => $hash) {
        $query = "ALTER TABLE `ex_object_$key` 
      ADD `group_id` INT (11) UNSIGNED NOT NULL AFTER `ex_object_id`";
        $ds->exec($query);

        $field_class = null;
        $field_id = null;
        switch ($hash["host_class"]) {
          case "CMbObject":
            break;

          case "CPrescriptionLineElement":
          case "CPrescriptionLineMedicament":
          case "COperation":
          case "CConsultation":
          case "CConsultAnesth":
          case "CAdministration":
            $field_class = "reference_class";
            $field_id    = "reference_id";
            break;

          case "CSejour":
            $field_class = "object_class";
            $field_id    = "object_id";
        }

        if ($field_class && $field_id) {
          $query = "UPDATE `ex_object_$key` 
            LEFT JOIN `sejour` ON `ex_object_$key`.`$field_id`    = `sejour`.`sejour_id` AND 
                  `ex_object_$key`.`$field_class` = 'CSejour'
            SET `ex_object_$key`.`group_id` = `sejour`.`group_id`";
          $ds->exec($query);
        }
      }

      return true;
    }
    $this->addFunction("setup_system_addExObjectGroupId");

    $this->makeRevision("1.0.97");
    $query = "ALTER TABLE `view_sender` 
      ADD `last_duration` FLOAT,
      ADD `last_size` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $this->makeRevision("1.0.98");
    $query = "CREATE TABLE `configuration` (
      `configuration_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `feature` VARCHAR (255) NOT NULL,
      `value` VARCHAR (255),
      `object_id` INT (11) UNSIGNED,
      `object_class` VARCHAR (80)
      ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `configuration` 
      ADD INDEX (`object_id`, `object_class`),
      ADD UNIQUE (`feature`, `object_id`, `object_class`);";
    $this->addQuery($query);

    $this->makeRevision("1.0.99");
    $this->addPrefQuery("MobileUI", 1);

    $this->makeRevision("1.1.00");
    $query = "ALTER TABLE `source_smtp` 
      ADD `auth` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("1.1.0");
    $query = "CREATE TABLE `ex_class_field_predicate` (
      `ex_class_field_predicate_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `ex_class_field_id` INT (11) UNSIGNED NOT NULL,
      `operator` ENUM ('=','!=','>','>=','<','<=','startsWith','endsWith','contains') NOT NULL DEFAULT '=',
      `value` VARCHAR (255) NOT NULL
      ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field_predicate` 
      ADD INDEX (`ex_class_field_id`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field` 
      ADD `predicate_id` INT (11) UNSIGNED,
      ADD INDEX (`predicate_id`)";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class` 
      ADD `native_views` VARCHAR(255),
      ADD INDEX (`native_views`)";
    $this->addQuery($query);

    $this->makeRevision("1.1.01");
    $this->addPrefQuery("MobileDefaultModuleView", 1);

    $this->makeRevision("1.1.02");
    $query = "ALTER TABLE `ex_class_field_group` 
      ADD `rank` TINYINT (4) UNSIGNED;";
    $this->addQuery($query);

    $this->makeRevision("1.1.03");

    $query = "ALTER TABLE `sender_file_system` 
        ADD `delete_file` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("1.1.04");
    $query = "ALTER TABLE `ex_class_field_predicate`
      CHANGE `operator` `operator`
        ENUM ('=','!=','>','>=','<','<=','startsWith','endsWith','contains','hasValue') NOT NULL DEFAULT '=';";
    $this->addQuery($query);

    $this->makeRevision("1.1.05");
    $query = "CREATE TABLE `datasource_log` (
      `datasourcelog_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
      `datasource` VARCHAR( 40 ) NOT NULL ,
      `requests` INT (11) UNSIGNED NOT NULL,
      `duration` FLOAT NOT NULL ,
      `accesslog_id` INT UNSIGNED NOT NULL ,
      PRIMARY KEY ( `datasourcelog_id` )) /*! ENGINE=MyISAM */";
    $this->addQuery($query);

    $this->makeRevision("1.1.06");
    $query = "DELETE FROM `datasource_log`";
    $this->addQuery($query);
    $query = "ALTER TABLE `datasource_log` 
        ADD UNIQUE `doublon` (`datasource` , `accesslog_id`)";
    $this->addQuery($query);

    $this->makeRevision("1.1.07");
    $query = "CREATE TABLE `source_http` (
      `source_http_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `name` VARCHAR (255) NOT NULL,
      `role` ENUM ('prod','qualif') NOT NULL DEFAULT 'qualif',
      `host` TEXT NOT NULL,
      `user` VARCHAR (255),
      `password` VARCHAR (50),
      `type_echange` VARCHAR (255),
      `active` ENUM ('0','1') NOT NULL DEFAULT '1',
      `loggable` ENUM ('0','1') NOT NULL DEFAULT '1'
      ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("1.1.08");
    $query = "ALTER TABLE `source_file_system` 
        ADD `fileprefix` VARCHAR (255),
        ADD `sort_files_by` ENUM ('date','name','size') DEFAULT 'name';";
    $this->addQuery($query);

    $this->makeRevision("1.1.09");
    $query = "CREATE TABLE `echange_http` (
        `echange_http_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
        `http_fault` ENUM ('0','1') DEFAULT '0',
        `emetteur` VARCHAR (255),
        `destinataire` VARCHAR (255),
        `date_echange` DATETIME NOT NULL,
        `function_name` VARCHAR (255) NOT NULL,
        `input` MEDIUMTEXT,
        `output` MEDIUMTEXT,
        `purge` ENUM ('0','1') DEFAULT '0',
        `response_time` FLOAT
      ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `echange_http` 
        ADD INDEX (`date_echange`);";
    $this->addQuery($query);

    $this->makeRevision("1.1.10");
    $query = "CREATE TABLE `view_access_token` (
      `view_access_token_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `user_id` INT (11) UNSIGNED NOT NULL,
      `datetime_start` DATETIME NOT NULL,
      `ttl_hours` INT (11) UNSIGNED NOT NULL,
      `params` VARCHAR (255) NOT NULL,
      `hash` CHAR (40) NOT NULL
             ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `view_access_token` 
      ADD INDEX (`user_id`),
      ADD INDEX (`datetime_start`),
      ADD INDEX (`hash`);";
    $this->addQuery($query);

    // Moved to "admin"
    $this->makeRevision("1.1.11");
    $query = "DROP TABLE `view_access_token`;";
    $this->addQuery($query);

    $this->makeRevision("1.1.12");
    $this->addPrefQuery("notes_anonymous", "0");

    $this->makeRevision("1.1.13");
    $query = "CREATE TABLE `ex_class_event` (
      `ex_class_event_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `ex_class_id` INT (11) UNSIGNED NOT NULL,
      `host_class` VARCHAR (255) NOT NULL,
      `event_name` VARCHAR (255) NOT NULL,
      `disabled` ENUM ('0','1') NOT NULL DEFAULT '1',
      `unicity` ENUM ('no','host') NOT NULL DEFAULT 'no'
             ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_event` 
        ADD INDEX (`ex_class_id`), 
        ADD INDEX (`host_class`), 
        ADD INDEX (`event_name`), 
        ADD INDEX (`disabled`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_constraint` 
        ADD `ex_class_event_id` INT (11) UNSIGNED NOT NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field` 
        ADD `report_class` VARCHAR(80) AFTER `report_level`";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_host_field` 
        ADD `host_class` VARCHAR(80) AFTER `host_type`";
    $this->addQuery($query);

    $this->makeRevision("1.1.14");
    function createExClassEvents($setup) {
      /** @var CSQLDataSource $ds */
      $ds = $setup->ds;

      $ex_classes = $ds->loadList("SELECT * FROM ex_class");

      $specs = array();

      foreach ($ex_classes as $_ex_class) {
        $_ex_class = array_map(array($ds, "escape"), $_ex_class);
        extract($_ex_class);

        // Insert events
        $query = "INSERT INTO ex_class_event(ex_class_id, host_class, event_name, disabled, unicity)
                 VALUES ('$ex_class_id', '$host_class', '$event', '$disabled', '$unicity')";
        $ds->query($query);
        $event_id = $ds->insertId();

        // Update constraints to stick to the event
        $query = "UPDATE ex_class_constraint 
          SET ex_class_constraint.ex_class_event_id = '$event_id' 
          WHERE ex_class_id = '$ex_class_id'";
        $ds->query($query);

        $spec = null;
        if (isset($specs[$host_class])) {
          $spec = $specs[$host_class];
        }
        elseif ($host_class) {
          $instance = new $host_class;
          $spec = $specs[$host_class] = $instance->_spec->events;
        }

        if (!$spec) {
          continue;
        }

        // Update host fields to stick to the event and ex_group_id
        $ex_groups = $ds->loadList("SELECT * FROM ex_class_field_group WHERE ex_class_id = '$ex_class_id'");
        foreach ($ex_groups as $_ex_group) {
          $_ex_group = array_map(array($ds, "escape"), $_ex_group);
          $_ex_group_id = $_ex_group["ex_class_field_group_id"];

          // Ex class field report level (HOST)
          $query = "UPDATE ex_class_field
            SET report_class = '$host_class' 
            WHERE ex_group_id = '$_ex_group_id' AND report_level = 'host'";
          $ds->query($query);

          // Ex class host field (HOST)
          $query = "UPDATE ex_class_host_field
            SET host_class = '$host_class' 
            WHERE ex_group_id = '$_ex_group_id' AND host_type = 'host'";
          $ds->query($query);

          // Ex class field report levl (ref 1 and 2)
          foreach (array(1, 2) as $i) {
            $_class = $spec[$event]["reference$i"][0];

            // Ex class field report level (REF)
            $query = "UPDATE ex_class_field
              SET report_class = '$_class' 
              WHERE ex_group_id = '$_ex_group_id' AND report_level = '$i'";
            $ds->query($query);

            // Ex class host field (REF)
            $query = "UPDATE ex_class_host_field
              SET host_class = '$_class' 
              WHERE ex_group_id = '$_ex_group_id' AND host_type = 'reference$i'";
            $ds->query($query);
          }
        }
      }

      return true;
    }

    //createExClassEvents($this);
    $this->addFunction("createExClassEvents");

    $this->makeRevision("1.1.15");
    $query = "CREATE TABLE `ex_class_field_property` (
        `ex_class_field_property_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
        `object_class` VARCHAR (80),
        `object_id` INT (11) UNSIGNED NOT NULL,
        `type` VARCHAR (60),
        `value` VARCHAR (255),
        `predicate_id` INT (11) UNSIGNED
      ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field_property`
        ADD INDEX (`object_class`),
        ADD INDEX (`object_id`),
        ADD INDEX (`predicate_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.1.16");
    $query = "ALTER TABLE `ex_class_field` 
        ADD `prefix` VARCHAR (255),
        ADD `suffix` VARCHAR (255)";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field` 
        ADD `coord_left` INT (11) AFTER `coord_label_x`,
        ADD `coord_top` INT (11) AFTER `coord_left`,
        ADD `coord_width` INT (11) UNSIGNED AFTER `coord_top`,
        ADD `coord_height` INT (11) UNSIGNED AFTER `coord_width`,
        ADD `subgroup_id` INT (11) UNSIGNED AFTER `ex_group_id`,
        ADD `show_label` ENUM ('0','1') NOT NULL DEFAULT '1',
        ADD `tab_index` INT (11)";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class` 
        ADD `pixel_positionning` ENUM ('0','1') NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_message` 
        ADD `coord_left` INT (11),
        ADD `coord_top` INT (11),
        ADD `coord_width` INT (11) UNSIGNED,
        ADD `coord_height` INT (11) UNSIGNED;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_host_field` 
        ADD `coord_left` INT (11),
        ADD `coord_top` INT (11),
        ADD `coord_width` INT (11) UNSIGNED,
        ADD `coord_height` INT (11) UNSIGNED;";
    $this->addQuery($query);
    $query = "CREATE TABLE `ex_class_field_subgroup` (
        `ex_class_field_subgroup_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
        `parent_class` ENUM ('CExClassFieldGroup','CExClassFieldSubgroup') NOT NULL,
        `parent_id` INT (11) UNSIGNED NOT NULL,
        `title` VARCHAR (255),
        `coord_left` INT (11),
        `coord_top` INT (11),
        `coord_width` INT (11) UNSIGNED,
        `coord_height` INT (11) UNSIGNED
      ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field_subgroup` 
        ADD INDEX (`parent_class`),
        ADD INDEX (`parent_id`)";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_message`
        ADD `subgroup_id` INT (11) UNSIGNED AFTER `ex_group_id`";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_message`
        ADD `predicate_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_message`
        ADD INDEX (`predicate_id`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field_subgroup`
        ADD `predicate_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field_subgroup`
        ADD INDEX (`predicate_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.1.17");
    $query = "ALTER TABLE `content_tabular`
        CHANGE `content` `content` LONGTEXT;";
    $this->addQuery($query);

    $this->makeRevision("1.1.18");
    $query = "ALTER TABLE `ex_class_field_predicate`
      CHANGE `operator` `operator`
        ENUM ('=','!=','>','>=','<','<=','startsWith','endsWith','contains','hasValue','hasNoValue') NOT NULL DEFAULT '=';";
    $this->addQuery($query);

    $this->makeRevision("1.1.19");

    $query = "CREATE TABLE `source_pop` (
        `source_pop_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
        `name` VARCHAR (255) NOT NULL,
        `user` VARCHAR (255) NOT NULL,
        `password` VARCHAR (255) NOT NULL,
        `role` ENUM ('prod', 'qualif') NOT NULL DEFAULT 'qualif',
        `type` ENUM ('pop3','imap') NOT NULL DEFAULT 'imap',
        `active` ENUM ('0','1') NOT NULL DEFAULT '1',
        `loggable` ENUM ('0','1') NOT NULL DEFAULT '1',
        `port` INT (11) NOT NULL,
        `host` VARCHAR (50) NOT NULL,
        `auth_ssl` ENUM ('None','SSL/TLS','STARTTLS') NOT NULL,
        `timeout` INT NOT NULL
      ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("1.1.20");

    $query = "ALTER TABLE `source_pop`
        ADD `libelle` VARCHAR (255) NOT NULL AFTER `name`;";
    $this->addQuery($query);

    $this->makeRevision("1.1.21");
    $query = "ALTER TABLE `source_pop`
        ADD `object_class` VARCHAR (80) NOT NULL,
        ADD `object_id` INT (11) UNSIGNED NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.1.22");
    $query = "CREATE TABLE `translation` (
      `translation_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `source` VARCHAR (255) NOT NULL,
      `translation` TEXT NOT NULL
      ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("1.1.23");
    $query = "ALTER TABLE `translation`
      ADD `language` CHAR (2) NOT NULL DEFAULT 'fr';";
    $this->addQuery($query);

    $this->makeRevision("1.1.24");
    $query = "ALTER TABLE `source_pop`
      ADD `last_update` DATETIME,
      ADD `type_echange` VARCHAR (255);";
    $this->addQuery($query);
    $query = "ALTER TABLE `source_pop`
      ADD INDEX (`last_update`);";
    $this->addQuery($query);

    $this->makeRevision("1.1.25");
    $query = "ALTER TABLE `source_smtp`
        CHANGE `password` `password` VARCHAR (255),
        ADD `iv` VARCHAR (16) AFTER `password`;";
    $this->addQuery($query);

    $query = "ALTER TABLE `source_file_system`
        CHANGE `password` `password` VARCHAR (255),
        ADD `iv` VARCHAR (16) AFTER `password`;";
    $this->addQuery($query);

    $query = "ALTER TABLE `source_http`
        CHANGE `password` `password` VARCHAR (255),
        ADD `iv` VARCHAR (16) AFTER `password`;";
    $this->addQuery($query);

    $query = "ALTER TABLE `source_pop`
        CHANGE `password` `password` VARCHAR (255),
        ADD `iv` VARCHAR (16) AFTER `password`;";
    $this->addQuery($query);

    $this->makeRevision("1.1.26");
    $query = "ALTER TABLE `ex_class_field`
        ADD `result_in_title` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.1.27");
    $query = "ALTER TABLE `ex_concept`
        ADD `native_field` VARCHAR (255);";
    $this->addQuery($query);

    $this->makeRevision("1.1.28");
    $query = "CREATE TABLE `config_db` (
      `key` VARCHAR (255) PRIMARY KEY,
      `value` VARCHAR(255)
      )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("1.1.29");
    $this->addPrefQuery("sessionLifetime", "");

    $this->makeRevision("1.1.30");
    $this->addPrefQuery("planning_dragndrop", "0");
    $this->addPrefQuery("planning_resize", "0");

    $this->makeRevision("1.1.31");
    $query = "UPDATE `user_preferences`
      SET `value` = 'aero-blue'
      WHERE `key` = 'UISTYLE'
      AND `value` = 'aero'";
    $this->addQuery($query, true);

    $this->makeRevision("1.1.32");
    $query = "DELETE user_preferences
      FROM user_preferences
      LEFT JOIN users ON users.user_id = user_preferences.user_id
      WHERE user_preferences.user_id IS NOT NULL
      AND  users.user_id IS NULL";
    $this->addQuery($query, true);

    $this->makeRevision("1.1.33");
    $query = "ALTER TABLE `session`
      DROP `date_creation`,
      DROP `date_modification`,
      ADD `expire` INT(11) NOT NULL DEFAULT '0'";
    $this->addQuery($query);

    $this->makeRevision("1.1.34");
    $query = "ALTER TABLE `session`
      CHANGE `data` `data` LONGBLOB";
    $this->addQuery($query);

    $this->makeRevision("1.1.35");
    $query = "CREATE TABLE `long_request_log` (
        `long_request_log_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
        `datetime`            DATETIME NOT NULL,
        `duration`            FLOAT UNSIGNED NOT NULL,
        `server_addr`         VARCHAR (255) NOT NULL,
        `user_id`             INT (11) UNSIGNED NOT NULL,
        `query_params_get`    TEXT,
        `query_params_post`   TEXT,
        `session_data`        TEXT
      ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `long_request_log`
        ADD INDEX (`datetime`),
        ADD INDEX (`user_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.1.36");
    $this->addPrefQuery("accessibility_dyslexic", "0");

    $this->makeRevision("1.1.37");
    $query = "ALTER TABLE `view_sender`
      CHANGE `period` `period` ENUM ('1','2','3','4','5','6','10','15','20','30','60') NOT NULL DEFAULT '30',
      ADD `every` ENUM ('1','2','3','4','6','8','12','24') NOT NULL DEFAULT '1';";
    $this->addQuery($query);

    $query = "ALTER TABLE `view_sender` 
      ADD INDEX (`name`);";
    $this->addQuery($query);

    $this->makeRevision("1.1.38");
    $query = "ALTER TABLE `ex_class_field`
                ADD `disabled` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.1.39");
    $query = "ALTER TABLE `long_request_log`
                ADD `requestUID` VARCHAR (255);";
    $this->addQuery($query);

    $query = "ALTER TABLE `long_request_log`
                ADD INDEX (`requestUID`);";
    $this->addQuery($query);

    $this->makeRevision("1.1.40");
    $this->addPrefQuery("planning_hour_division", "2");

    // Cr�ation des deux nouveaux champs
    $this->makeRevision("1.1.41");
    $this->setTimeLimit(1200);

    $query = "ALTER TABLE `access_log`
      ADD `aggregate` INT(11) UNSIGNED NOT NULL DEFAULT '10',
      ADD `bot` BOOL NOT NULL DEFAULT 0;";
    $this->addQuery($query);

    // Mise � jour du champ
    $query = "UPDATE `access_log`
      SET `aggregate` = '60';";
    $this->addQuery($query);

    /**
     * Suppression de l'index UNIQUE triplet
     * Cr�ation d'un index unique portant sur le pr�c�dent triplet + l'agr�gat et le bool�en bot
     * Cr�ation d'un simple index triplet
     */
    $query = "ALTER TABLE `access_log`
      DROP INDEX `triplet`,
      ADD UNIQUE `aggregate` (`module`, `action`, `period`, `aggregate`, `bot`),
      ADD INDEX `triplet` (`module`, `action`, `period`);";
    $this->addQuery($query);

    // Ajout de l'index sur l'ID du journal d'acc�s concern�
    $query = "ALTER TABLE `datasource_log`
      ADD INDEX `agregat` (`accesslog_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.1.42");
    function setup_system_addExObjectAdditionalObject($setup) {
      /** @var CSQLDataSource $ds */
      $ds = $setup->ds;

      // Changement des ExClasses
      $query = "SELECT ex_class_id, ex_class_id FROM ex_class";
      $list_ex_class = $ds->loadHashAssoc($query);

      foreach ($list_ex_class as $key => $hash) {
        $query = "ALTER TABLE `ex_object_$key`
                    ADD `additional_id` INT (11) UNSIGNED AFTER `reference2_class`,
                    ADD `additional_class` VARCHAR(80) AFTER `additional_id`,
                    ADD  INDEX `additional` ( `additional_class`, `additional_id` ),

                    DROP INDEX `object_id`,
                    DROP INDEX `object_class`,
                    ADD  INDEX `object` ( `object_class`, `object_id` ),

                    DROP INDEX `reference_id`,
                    DROP INDEX `reference_class`,
                    ADD  INDEX `reference1` ( `reference_class`, `reference_id` ),

                    DROP INDEX `reference2_id`,
                    DROP INDEX `reference2_class`,
                    ADD  INDEX `reference2` ( `reference2_class`, `reference2_id` );";
        $ds->exec($query);
      }

      return true;
    }
    $this->addFunction("setup_system_addExObjectAdditionalObject");

    $this->makeRevision("1.1.43");
    $query = "CREATE TABLE `error_log` (
                `error_log_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `user_id` INT (11) UNSIGNED,
                `server_ip` VARCHAR (80),
                `datetime` DATETIME NOT NULL,
                `request_uid` VARCHAR (255),
                `error_type` ENUM (
                  'exception','error','warning','parse','notice','core_error','core_warning','compile_error',
                  'compile_warning','user_error','user_warning','user_notice','strict','recoverable_error','deprecated',
                  'user_deprecated','js_error'
                 ),
                `text` TEXT,
                `file_name` VARCHAR (255),
                `line_number` INT (11),

                `stacktrace_id` INT (11) UNSIGNED,
                `param_GET_id` INT (11) UNSIGNED,
                `param_POST_id` INT (11) UNSIGNED,
                `session_data_id` INT (11) UNSIGNED
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `error_log`
                ADD INDEX (`user_id`),
                ADD INDEX (`server_ip`),
                ADD INDEX (`datetime`),
                ADD INDEX (`error_type`);";
    $this->addQuery($query);
    $query = "CREATE TABLE `error_log_data` (
                `error_log_data_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `value` LONGTEXT NOT NULL,
                `value_hash` CHAR(32) NOT NULL,
                UNIQUE (`value_hash`)
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("1.1.44");
    $query = "ALTER TABLE `source_pop`
                CHANGE `port` `port` INT (11) DEFAULT '25',
                CHANGE `timeout` `timeout` TINYINT (4) DEFAULT '5',
                CHANGE `type` `type` ENUM ('pop3','imap'),
                ADD `extension` VARCHAR (255);";
    $this->addQuery($query);
    $query = "ALTER TABLE `source_pop`
                ADD INDEX (`object_class`),
                ADD INDEX (`object_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.1.45");
    $query = "ALTER TABLE `source_pop`
                ADD `cron_update` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->mod_version = "1.1.46";

    /*$query = "ALTER TABLE `user_log`
        DROP INDEX `object_id`,
        ADD INDEX `object` (`object_id`, `object_class`)";
    $this->addQuery($query);*/
  }
}
