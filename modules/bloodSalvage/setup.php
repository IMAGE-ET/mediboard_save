<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage bloodSalvage
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

class CSetupbloodSalvage extends CSetup {

  function __construct() {
    parent::__construct();

    $this->mod_name = 'bloodSalvage';
    $this->makeRevision('all');
    $query ='CREATE TABLE `blood_salvage` (
      `blood_salvage_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `operation_id` INT (11) UNSIGNED NOT NULL,
      `cell_saver_id` INT (11) UNSIGNED NOT NULL,
      `incident_file_id` INT (11) UNSIGNED,
      `wash_volume` INT (11),
      `saved_volume` INT (11),
      `hgb_pocket` INT (11),
      `hgb_patient` INT (11),
      `transfused_volume`INT(11),
      `anticoagulant_cip` VARCHAR(7),
      `recuperation_start` DATETIME,
      `recuperation_end` DATETIME,
      `transfusion_start` DATETIME,
      `transfusion_end` DATETIME
    ) /*! ENGINE=MyISAM */;';
    $this->addQuery($query);

    $query='ALTER TABLE `blood_salvage`
      ADD INDEX (`operation_id`),
      ADD INDEX (`cell_saver_id`),
      ADD INDEX (`incident_file_id`),
      ADD INDEX (`recuperation_start`),
      ADD INDEX (`recuperation_end`),
      ADD INDEX (`transfusion_start`),
      ADD INDEX (`transfusion_end`);';
    $this->addQuery($query);

    $this->makeRevision('0.01');
    
    $query = 'ALTER TABLE `blood_salvage` 
            ADD `receive_kit` VARCHAR( 32 ),
            ADD `wash_kit` VARCHAR( 32 ) ' ;
    $this->addQuery($query);
    
    $query = 'CREATE TABLE `cell_saver` (
    `cell_saver_id`  INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
    `marque` VARCHAR(50) NOT NULL,
    `modele` VARCHAR( 30 ) NOT NULL
     ) /*! ENGINE=MyISAM */;';
    
    $this->addQuery($query);
    
    $query="CREATE TABLE `type_ei` (
          `type_ei_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
          `concerne` ENUM( 'pat', 'vis', 'pers', 'med', 'mat' ) NOT NULL ,
          `desc` TEXT NULL,
          `name` VARCHAR( 30 ) NOT NULL
          ) /*! ENGINE=MyISAM */ ;";
    $this->addQuery($query);
    
    $query = " ALTER TABLE `blood_salvage` 
             CHANGE `incident_file_id` `type_ei_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ;";
    $this->addQuery($query);
    
    $this->makeRevision('0.02');
    
    $query = " ALTER TABLE `type_ei`
             ADD `evenements` VARCHAR( 255 ) NOT NULL,
             ADD `type_signalement` ENUM('inc','ris') NOT NULL ;";
    
    $this->addQuery($query);
    $this->makeRevision('0.03');
    $query = " ALTER TABLE `blood_salvage` 
             ADD `sample` ENUM( 'non', 'prel', 'trans' ) NOT NULL DEFAULT 'non' ;";
    $this->addQuery($query);
    
    $this->makeRevision('0.1');
    $query = "ALTER TABLE `blood_salvage` 
            CHANGE `receive_kit` `receive_kit_ref` VARCHAR( 32 ) NULL DEFAULT NULL ,
            CHANGE `wash_kit` `wash_kit_ref` VARCHAR( 32 ) NULL DEFAULT NULL ,
            ADD `receive_kit_lot` VARCHAR( 32 ) NULL DEFAULT NULL AFTER `receive_kit_ref` ,
            ADD `wash_kit_lot` VARCHAR( 32 ) NULL DEFAULT NULL AFTER `wash_kit_ref`";
    $this->addQuery($query);
    
    $this->mod_version='0.2'; 
    
  }
}
?>