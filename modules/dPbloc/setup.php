<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Bloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Setup du module de bloc op�ratoire
 * Class CSetupdPbloc
 */
class CSetupdPbloc extends CSetup {
  /**
   * Change prat usernames to prat ids
   *
   * @return bool
   */
  protected function swapPratIds() {
    $ds = CSQLDataSource::get("std");

    CApp::setTimeLimit(1800);
    $user = new CUser;

    // Changement des chirurgiens
    $query = "SELECT id_chir
        FROM plagesop
        GROUP BY id_chir";
    $listPlages = $ds->loadList($query);
    foreach ($listPlages as $plage) {
      $where["user_username"] = "= '".$plage["id_chir"]."'";
      $user->loadObject($where);
      if ($user->user_id) {
        $query = "UPDATE plagesop
            SET chir_id = '$user->user_id'
            WHERE id_chir = '$user->user_username'";
        $ds->exec($query);
        $ds->error();
      }
    }

    //Changement des anesth�sistes
    $query = "SELECT id_anesth
         FROM plagesop
         GROUP BY id_anesth";
    $listPlages = $ds->loadList($query);
    foreach ($listPlages as $plage) {
      $where["user_username"] = "= '".$plage["id_anesth"]."'";
      $user->loadObject($where);
      if ($user->user_id) {
        $query = "UPDATE plagesop
            SET anesth_id = '$user->user_id'
            WHERE id_anesth = '$user->user_username'";
        $ds->exec($query);
        $ds->error();
      }
    }
    return true;
  }
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPbloc";
    
    $this->makeRevision("all");
    $query = "CREATE TABLE plagesop (
                id bigint(20) NOT NULL auto_increment,
                id_chir varchar(20) NOT NULL default '0',
                id_anesth varchar(20) default NULL,
                id_spec tinyint(4) default NULL,
                id_salle tinyint(4) NOT NULL default '0',
                date date NOT NULL default '0000-00-00',
                debut time NOT NULL default '00:00:00',
                fin time NOT NULL default '00:00:00',
                PRIMARY KEY  (id)
              ) /*! ENGINE=MyISAM */ COMMENT='Table des plages d op�ration';";
    $this->addQuery($query);
    $query = "CREATE TABLE sallesbloc (
                id tinyint(4) NOT NULL auto_increment,
                nom varchar(50) NOT NULL default '',
                PRIMARY KEY  (id)
              ) /*! ENGINE=MyISAM */ COMMENT='Table des salles d op�ration du bloc';";
    $this->addQuery($query);         
              
    $this->makeRevision("0.1");
    $query = "ALTER TABLE `plagesop`
                ADD INDEX ( `id_chir` ),
                ADD INDEX ( `id_anesth` ),
                ADD INDEX ( `id_spec` ),
                ADD INDEX ( `id_salle` ),
                ADD INDEX ( `date` )";
    $this->addQuery($query);
    
    $this->makeRevision("0.11");
    $query = "ALTER TABLE `plagesop`
                ADD `chir_id` BIGINT DEFAULT '0' NOT NULL AFTER `id`,
                ADD `anesth_id` BIGINT DEFAULT '0' NOT NULL AFTER `chir_id`,
                ADD INDEX ( `chir_id` ),
                ADD INDEX ( `anesth_id` );";
    $this->addQuery($query);
    $this->addMethod("swapPratIds");

    $this->makeRevision("0.12");
    $query = "ALTER TABLE `sallesbloc` ADD `stats` TINYINT DEFAULT '0' NOT NULL AFTER `nom` ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.13");
    $this->addDependency("dPetablissement", "0.1");
    $query = "ALTER TABLE `sallesbloc` ADD `group_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `id`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `sallesbloc` ADD INDEX ( `group_id` ) ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.14");
    $query = "ALTER TABLE `plagesop` DROP `id_chir` ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `plagesop` DROP `id_anesth` ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.15");
    $query = "ALTER TABLE `plagesop` CHANGE `id` `plageop_id` BIGINT( 20 ) NOT NULL AUTO_INCREMENT ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `sallesbloc` CHANGE `id` `salle_id` BIGINT( 20 ) NOT NULL AUTO_INCREMENT ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `plagesop` CHANGE `id_spec` `spec_id` INT( 10 ) DEFAULT NULL ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `plagesop` CHANGE `id_salle` `salle_id` INT( 10 ) DEFAULT '0' NOT NULL ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.16");
    $query = "ALTER TABLE `plagesop` ADD `temps_inter_op` TIME NOT NULL DEFAULT '00:15:00' ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.17");
    $query = "ALTER TABLE `plagesop`
                CHANGE `plageop_id` `plageop_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                CHANGE `chir_id` `chir_id` int(11) unsigned NOT NULL DEFAULT '0',
                CHANGE `anesth_id` `anesth_id` int(11) unsigned NOT NULL DEFAULT '0',
                CHANGE `spec_id` `spec_id` int(11) unsigned NULL,
                CHANGE `salle_id` `salle_id` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `sallesbloc`
                CHANGE `salle_id` `salle_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                CHANGE `group_id` `group_id` int(11) unsigned NOT NULL DEFAULT '1',
                CHANGE `stats` `stats` enum('0','1') NOT NULL DEFAULT '0',
                CHANGE `nom` `nom` varchar(255) NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.18");
    $query = "ALTER TABLE `plagesop` ADD INDEX ( `debut` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `plagesop` ADD INDEX ( `fin` )";
    $this->addQuery($query);
    
    $this->makeRevision("0.19");
    $query = "ALTER TABLE `plagesop`
                CHANGE `chir_id` `chir_id` int(11) unsigned NULL DEFAULT NULL,
                CHANGE `anesth_id` `anesth_id` int(11) unsigned NULL DEFAULT NULL;";
    $this->addQuery($query);
    $query = "UPDATE `plagesop` SET `chir_id` = NULL WHERE `chir_id` = '0';";
    $this->addQuery($query);
    $query = "UPDATE `plagesop` SET `anesth_id` = NULL WHERE `anesth_id` = '0';";
    $this->addQuery($query);
    $query = "UPDATE `plagesop` SET `spec_id` = NULL WHERE `spec_id` = '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.20");
    $query = "ALTER TABLE `plagesop`
            ADD `max_intervention` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.21");
    $query = "ALTER TABLE `plagesop`
            CHANGE `max_intervention` `max_intervention` INT(11);";
    $this->addQuery($query);
    
    $this->makeRevision("0.22");
    $query = "CREATE TABLE `bloc_operatoire` (
            `bloc_operatoire_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
            `group_id` INT (11) UNSIGNED NOT NULL,
            `nom` VARCHAR (255) NOT NULL) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `bloc_operatoire` ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $query = "INSERT INTO `bloc_operatoire` (`nom`, `group_id`)
            SELECT 'Bloc principal', `group_id`
            FROM `groups_mediboard`";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `sallesbloc` 
            CHANGE `group_id` `bloc_id` INT( 11 ) UNSIGNED NOT NULL";
    $this->addQuery($query);
    
    $query = "UPDATE `sallesbloc` 
            SET `bloc_id` = (
              SELECT `bloc_operatoire_id` 
              FROM `bloc_operatoire` 
              WHERE `sallesbloc`.`bloc_id` = `bloc_operatoire`.`group_id`
              LIMIT 1
            );";
    $this->addQuery($query);
    
    $this->makeRevision("0.23");
    $query = "ALTER TABLE `plagesop` 
            ADD `spec_repl_id` INT (11) UNSIGNED,
            ADD `delay_repl` INT (11);";
    $this->addQuery($query);
    $query = "ALTER TABLE `plagesop`
            ADD INDEX (`spec_repl_id`),
            ADD INDEX (`delay_repl`)";
    $this->addQuery($query);
    
    $this->makeRevision("0.24");
    $query = "ALTER TABLE `plagesop` 
            ADD `actes_locked` ENUM('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.25");
    $query = "ALTER TABLE `plagesop` ADD `unique_chir` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);
    
    $this->makeRevision("0.26");
    $query = "ALTER TABLE `sallesbloc`
              ADD `dh` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);
   
    $this->makeRevision("0.27");
    $query = "ALTER TABLE `bloc_operatoire`
      ADD `days_locked` INT (11) UNSIGNED DEFAULT '0';";
    $this->addQuery($query);
    
    global $dPconfig;
    $days_locked = (isset($dPconfig["dPbloc"]["CPlageOp"]["days_locked"]) ?
        CAppUI::conf("dPbloc CPlageOp days_locked") : 0);
    
    $query = "UPDATE `bloc_operatoire`
     SET days_locked = '$days_locked'";
    $this->addQuery($query);
    
    $this->makeRevision("0.28");
    $query = "ALTER TABLE `plagesop` 
      ADD `verrouillage` ENUM ('defaut','non','oui') DEFAULT 'defaut' AFTER max_intervention;";
    $this->addQuery($query);
    
    $this->makeRevision("0.29");
    $this->addPrefQuery("suivisalleAutonome", 0);

    $this->makeRevision("0.30");
    $query = "CREATE TABLE `blocage` (
      `blocage_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `salle_id` INT (11) UNSIGNED NOT NULL,
      `libelle` VARCHAR (255),
      `deb` DATE NOT NULL,
      `fin` DATE NOT NULL
      ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `blocage` 
      ADD INDEX (`salle_id`),
      ADD INDEX (`deb`),
      ADD INDEX (`fin`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.31");
    
    $query = "CREATE TABLE `ressource_materielle` (
      `ressource_materielle_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `type_ressource_id` INT (11) UNSIGNED NOT NULL,
      `group_id` INT (11) UNSIGNED NOT NULL,
      `libelle` VARCHAR (255) NOT NULL,
      `deb_activite` DATE,
      `fin_activite` DATE,
      `retablissement` ENUM ('0','1') DEFAULT '0'
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `ressource_materielle`
      ADD INDEX (`type_ressource_id`),
      ADD INDEX (`group_id`),
      ADD INDEX (`deb_activite`),
      ADD INDEX (`fin_activite`),
      ADD INDEX (`retablissement`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `type_ressource` (
      `type_ressource_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `group_id` INT (11) UNSIGNED NOT NULL,
      `libelle` VARCHAR (255) NOT NULL,
      `description` TEXT
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `type_ressource` 
      ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `usage_ressource` (
      `usage_ressource_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `ressource_materielle_id` INT (11) UNSIGNED NOT NULL,
      `besoin_id` INT (11) UNSIGNED NOT NULL,
      `commentaire` TEXT
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `usage_ressource` 
      ADD INDEX (`ressource_materielle_id`),
      ADD INDEX (`besoin_id`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `indispo_ressource` (
      `indispo_ressource_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `ressource_materielle_id` INT (11) UNSIGNED NOT NULL,
      `deb` DATE NOT NULL,
      `fin` DATE NOT NULL,
      `commentaire` TEXT
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `besoin_ressource` (
      `besoin_ressource_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `type_ressource_id` INT (11) UNSIGNED NOT NULL,
      `protocole_id` INT (11) UNSIGNED NOT NULL,
      `operation_id` INT (11) UNSIGNED NOT NULL,
      `commentaire` TEXT
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `besoin_ressource` 
      ADD INDEX (`type_ressource_id`),
      ADD INDEX (`protocole_id`),
      ADD INDEX (`operation_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.32");
    $query = "ALTER TABLE `usage_ressource`
      CHANGE `besoin_id` `besoin_ressource_id` INT (11) UNSIGNED NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.33");
    $query = "ALTER TABLE `ressource_materielle` 
      CHANGE `retablissement` `retablissement` TIME;";
    $this->addQuery($query);
    
    $this->makeRevision("0.34");
    $query = "UPDATE `ressource_materielle`
      SET `retablissement` = '00:00:00'
      WHERE `retablissement` IS NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.35");
    $query = "CREATE TABLE `poste_sspi` (
      `poste_sspi_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `group_id` INT (11) UNSIGNED NOT NULL,
      `nom` VARCHAR (255) NOT NULL
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `poste_sspi`
      ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `bloc_operatoire` 
      ADD `poste_sspi_id` INT (11) UNSIGNED AFTER `group_id`;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `bloc_operatoire` 
      ADD INDEX (`poste_sspi_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.36");
    $query = "ALTER TABLE `bloc_operatoire`
      DROP `poste_sspi_id`;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `poste_sspi`
                ADD `bloc_id` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $this->makeRevision("0.37");
    $query = "ALTER TABLE `plagesop`
                ADD `secondary_function_id` INT (11) UNSIGNED AFTER `chir_id`,
                ADD INDEX (`secondary_function_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.38");
    $query = "ALTER TABLE `bloc_operatoire`
                ADD `tel` VARCHAR (20),
                ADD `fax` VARCHAR (20);";
    $this->addQuery($query);

    $this->makeRevision("0.39");
    $query = "ALTER TABLE `indispo_ressource`
      CHANGE `deb` `deb` DATETIME NOT NULL,
      CHANGE `fin` `fin` DATETIME NOT NULL;";
    $this->addQuery($query);

    $this->makeRevision("0.40");
    $query = "ALTER TABLE `bloc_operatoire`
                ADD `type` ENUM ('chir','obst') NOT NULL DEFAULT 'chir' AFTER `nom`;";
    $this->addQuery($query);

    $this->makeEmptyRevision("0.41");
    $this->addPrefQuery("startAutoRefreshAtStartup", 0);

    $this->makeRevision("0.42");
    $query = "ALTER TABLE `bloc_operatoire`
                ADD `cheklist_man` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.43");

    $query = "ALTER TABLE `bloc_operatoire`
                DROP `cheklist_man`;";
    $this->addQuery($query);

    $query = "ALTER TABLE `sallesbloc`
                ADD `cheklist_man` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.44");
    $query = "ALTER TABLE `plagesop`
      ADD `original_owner_id` INT (11) UNSIGNED AFTER `salle_id`,
      ADD `original_function_id` INT (11) UNSIGNED AFTER `original_owner_id`,
      ADD INDEX (`original_owner_id`),
      ADD INDEX (`original_function_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.45");
    $query = "ALTER TABLE `poste_sspi`
                ADD `type` ENUM ('sspi','preop') DEFAULT 'sspi';";
    $this->addQuery($query);

    $this->makeRevision("0.46");
    $query = "UPDATE `plagesop`
      SET `original_owner_id` = `chir_id`
      WHERE `original_owner_id` IS NULL";
    $this->addQuery($query);

    $query = "UPDATE `plagesop`
      SET `original_function_id` = `spec_id`
      WHERE `original_function_id` IS NULL";
    $this->addQuery($query);
    $this->makeRevision("0.47");
    $query = "ALTER TABLE `bloc_operatoire`
                ADD `use_brancardage` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->mod_version = "0.48";
  }
}
