<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPetablissement
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetupdPetablissement extends CSetup {
  function __construct() {
    parent::__construct();
    
    $this->mod_type = "core";
    $this->mod_name = "dPetablissement";
    
    $this->makeRevision("all");
    
    $this->makeRevision("0.1");
    $query = "ALTER TABLE `groups_mediboard`
		  ADD `raison_sociale` VARCHAR( 50 ) ,
			ADD `adresse` TEXT ,
			ADD `cp` VARCHAR( 5 ) ,
			ADD `ville` VARCHAR( 50 ) ,
			ADD `tel` VARCHAR( 10 ) ,
			ADD `directeur` VARCHAR( 50 ) ,
			ADD `domiciliation` VARCHAR( 9 ) ,
			ADD `siret` VARCHAR( 14 );";
    $this->addQuery($query);
    $query = "INSERT INTO `groups_mediboard` ( `group_id` , `text` )
			VALUES (NULL , 'Etablissement');";
    $this->addQuery($query);

    $this->makeRevision("0.11");
    $query = "ALTER TABLE `groups_mediboard` 
		  ADD `ape` VARCHAR( 4 ) DEFAULT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.12");
    $query = "ALTER TABLE `groups_mediboard`
		  CHANGE `group_id` `group_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			CHANGE `cp` `cp` int(5) unsigned zerofill NULL,
			CHANGE `tel` `tel` bigint(10) unsigned zerofill NULL,
			CHANGE `text` `text` varchar(255) NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.13");
    $query = "ALTER TABLE `groups_mediboard`
		  ADD `fax` bigint(10) unsigned zerofill NULL AFTER `tel`,
			ADD `mail` varchar(50) DEFAULT NULL,
			ADD `web` varchar(255) DEFAULT NULL;" ;
    $this->addQuery($query);

    
    $this->makeRevision("0.14");
    $query = "ALTER TABLE `groups_mediboard`
      ADD `tel_anesth` BIGINT(10) UNSIGNED ZEROFILL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.15");
    $query = "CREATE TABLE `etab_externe` (
			`etab_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
			`nom` VARCHAR(255) NOT NULL, 
			`raison_sociale` VARCHAR(50), 
			`adresse` TEXT, 
			`cp` INT(5) UNSIGNED ZEROFILL, 
			`ville` VARCHAR(50),  
			`tel` BIGINT(10) UNSIGNED ZEROFILL, 
			`fax` BIGINT(10) UNSIGNED ZEROFILL, 
			`finess` INT(9) UNSIGNED ZEROFILL, 
			PRIMARY KEY (`etab_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.16");
    $query = "ALTER TABLE `etab_externe`
      ADD `siret` CHAR(14), 
      ADD `ape` CHAR(4);";
    $this->addQuery($query);
    
    $this->makeRevision("0.17");
    $query = "ALTER TABLE `groups_mediboard`
      ADD `service_urgences_id` INT(11) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("0.18");
    $query = "ALTER TABLE `etab_externe`
      CHANGE `ape` `ape` VARCHAR(6);";
    $this->addQuery($query);
    $query = "ALTER TABLE `groups_mediboard`
      CHANGE `ape` `ape` VARCHAR(6);";
    $this->addQuery($query);
    
    $this->makeRevision("0.19");
    $query = "ALTER TABLE `groups_mediboard` 
      ADD INDEX (`service_urgences_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.20");
    $query = "ALTER TABLE `groups_mediboard` 
      ADD `finess` INT (9) UNSIGNED ZEROFILL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.21");
    $query = "ALTER TABLE `groups_mediboard` 
      ADD `pharmacie_id` INT (11) UNSIGNED,
      ADD INDEX (`pharmacie_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.22");
    $query = "ALTER TABLE `groups_mediboard` 
      ADD `chambre_particuliere` ENUM ('0','1') NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("0.23");
    $query = "CREATE TABLE `groups_config` (
      `groups_config_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `object_id` INT (11) UNSIGNED,
      `max_comp` INT (11) UNSIGNED,
      `max_ambu` INT (11) UNSIGNED
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `groups_config` 
      ADD INDEX (`object_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.24");
    $query = "ALTER TABLE `groups_config` 
      ADD `codage_prat` ENUM ('0','1');";
    $this->addQuery($query);
    $query = "ALTER TABLE `groups_config` 
      CHANGE `codage_prat` `codage_prat` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.25");
    $query = "ALTER TABLE `groups_config` 
                ADD `sip_notify_all_actors` ENUM ('0','1') DEFAULT '0',
                ADD `sip_idex_generator` ENUM ('0','1') DEFAULT '0',
                ADD `smp_notify_all_actors` ENUM ('0','1') DEFAULT '0',
                ADD `smp_idex_generator` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->mod_version = "0.26";
  } 
}

?>