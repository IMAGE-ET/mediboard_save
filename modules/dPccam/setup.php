<?php

/**
 * dPccam
 *
 * @category Ccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id$
 * @link     http://www.mediboard.org
 */

class CSetupdPccam extends CSetup {

  function __construct() {
    parent::__construct();

    $this->mod_name = "dPccam";

    $this->makeRevision("all");
    $query = "CREATE TABLE `ccamfavoris` (
                `favoris_id` bigint(20) NOT NULL auto_increment,
                `favoris_user` int(11) NOT NULL default '0',
                `favoris_code` varchar(7) NOT NULL default '',
                PRIMARY KEY  (`favoris_id`)
              ) /*! ENGINE=MyISAM */ COMMENT='table des favoris'";
    $this->addQuery($query);

    $this->makeRevision("0.1");
    $query = "ALTER TABLE `ccamfavoris` 
                CHANGE `favoris_id` `favoris_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                CHANGE `favoris_user` `favoris_user` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.11");
    $query = "ALTER TABLE `ccamfavoris`
                ADD `object_class` VARCHAR(25) NOT NULL DEFAULT 'COperation';";
    $this->addQuery($query);

    $this->makeRevision("0.12");
    $query = "ALTER TABLE `ccamfavoris` 
                ADD INDEX (`favoris_user`);";
    $this->addQuery($query);

    $this->makeRevision("0.13");
    $query = "CREATE TABLE `frais_divers` (
                `frais_divers_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `type_id` INT (11) UNSIGNED NOT NULL,
                `coefficient` FLOAT NOT NULL DEFAULT '1',
                `quantite` INT (11) UNSIGNED,
                `facturable` ENUM ('0','1') NOT NULL DEFAULT '0',
                `montant_depassement` DECIMAL  (10,3),
                `montant_base` DECIMAL  (10,3),
                `executant_id` INT (11) UNSIGNED NOT NULL,
                `object_id` INT (11) UNSIGNED NOT NULL,
                `object_class` VARCHAR (255) NOT NULL
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `frais_divers` 
                ADD INDEX (`type_id`),
                ADD INDEX (`executant_id`),
                ADD INDEX (`object_id`);";
    $this->addQuery($query);

    $query = "CREATE TABLE `frais_divers_type` (
                `frais_divers_type_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `code` VARCHAR (16) NOT NULL,
                `libelle` VARCHAR (255) NOT NULL,
                `tarif` DECIMAL (10,3) NOT NULL,
                `facturable` ENUM ('0','1') NOT NULL DEFAULT '0'
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("0.14");
    $this->addPrefQuery("new_search_ccam", "1");

    $this->makeRevision("0.15");

    $query = "ALTER TABLE `frais_divers` 
                CHANGE `facturable` `facturable` ENUM ('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("0.16");

    $this->addPrefQuery("multiple_select_ccam", "0");

    $this->makeRevision("0.17");

    $this->addPrefQuery("user_executant", "0");

    $this->makeRevision("0.18");
    $this->addDependency("dPcabinet", "0.1");
    $this->addDependency("dPplanningOp", "0.1");

    $query = "ALTER TABLE `frais_divers`
                ADD `execution` DATETIME NOT NULL;";

    $this->addQuery($query);

    $query = "UPDATE `frais_divers`
                INNER JOIN `consultation` ON (`frais_divers`.`object_id` = `consultation`.`consultation_id`)
                INNER JOIN `plageconsult` ON (`consultation`.`plageconsult_id` = `plageconsult`.`plageconsult_id`)
                SET `frais_divers`.`execution` = CONCAT(`plageconsult`.`date`, ' ', `consultation`.`heure`)
                WHERE `frais_divers`.`object_class` = 'CConsultation';";
    $this->addQuery($query);

    $query = "UPDATE `frais_divers`
                INNER JOIN `operations` ON (`frais_divers`.`object_id` = `operations`.`operation_id`)
                INNER JOIN `plagesop` ON (`operations`.`plageop_id` = `plagesop`.`plageop_id`)
                SET `frais_divers`.`execution` = CONCAT(`plagesop`.`date`, ' ', `operations`.`time_operation`)
                WHERE `frais_divers`.`object_class` = 'COperation'
                AND `operations`.`date` IS NULL;";
    $this->addQuery($query);

    $query = "UPDATE `frais_divers`
                INNER JOIN `operations` ON (`frais_divers`.`object_id` = `operations`.`operation_id`)
                SET `frais_divers`.`execution` = CONCAT(`operations`.`date`, ' ', `operations`.`time_operation`)
                WHERE `frais_divers`.`object_class` = 'COperation'
                AND `operations`.`date` IS NOT NULL;";
    $this->addQuery($query);

    $query = "UPDATE `frais_divers`
                INNER JOIN `sejour` ON (`frais_divers`.`object_id` = `sejour`.`sejour_id`)
                SET `frais_divers`.`execution` = `sejour`.`entree`
                WHERE `frais_divers`.`object_class` = 'CSejour';";
    $this->addQuery($query);

    $this->mod_version = "0.19";

    // Data source query
    $query = "SHOW TABLES LIKE 'convergence'";
    $this->addDatasource("ccamV2", $query);

    /*
    $query = "SELECT *
              FROM modificateur
              WHERE CODE = 'K'
              AND LIBELLE = 'Majoration forfaits modulables accouchements gyneco. et chir sect. 1 ou 2 adherant,pour actes avec J'";

    $query = "SELECT *
              FROM `codes_ngap`
              WHERE `code` LIKE 'MA'";
    $query = "SHOW TABLES LIKE 'forfaits'";
    */

    $query = "SHOW TABLES LIKE 'tarif_ngap';";
    $this->addDatasource("ccamV2", $query);
  }
}
