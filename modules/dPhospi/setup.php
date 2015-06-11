<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CSetupdPhospi extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPhospi";
    
    $this->makeRevision("all");
    $query = "CREATE TABLE `service` (
      `service_id` INT NOT NULL AUTO_INCREMENT ,
      `nom` VARCHAR( 50 ) NOT NULL ,
      `description` TEXT,
      PRIMARY KEY ( `service_id` )) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `chambre` (
      `chambre_id` INT NOT NULL AUTO_INCREMENT ,
      `service_id` INT NOT NULL ,
      `nom` VARCHAR( 50 ) ,
      `caracteristiques` TEXT ,
      PRIMARY KEY ( `chambre_id` ) ,
      INDEX ( `service_id` )) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `lit` (
      `lit_id` INT NOT NULL AUTO_INCREMENT ,
      `chambre_id` INT NOT NULL,
      `nom` VARCHAR( 50 ) NOT NULL ,
      PRIMARY KEY ( `lit_id` ) ,
      INDEX ( `chambre_id` )) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.1");
    $query = "CREATE TABLE `affectation` (
      `affectation_id` INT NOT NULL AUTO_INCREMENT,
      `lit_id` INT NOT NULL ,
      `operation_id` INT NOT NULL ,
      `entree` DATETIME NOT NULL ,
      `sortie` DATETIME NOT NULL ,
      PRIMARY KEY ( `affectation_id` ) ,
      INDEX ( `lit_id` , `operation_id` )) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.11");
    $query = "ALTER TABLE `affectation` 
      ADD `confirme` TINYINT DEFAULT '0' NOT NULL,
      ADD `effectue` TINYINT DEFAULT '0' NOT NULL ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.12");
    $query = "ALTER TABLE `affectation` 
      ADD INDEX ( `entree` );";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `affectation` 
      ADD INDEX ( `sortie` );";
    $this->addQuery($query);
    
    $this->makeRevision("0.13");
    $query = "ALTER TABLE `affectation` 
      ADD INDEX ( `operation_id` ) ;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `affectation` 
      ADD INDEX ( `lit_id` ) ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.14");
    $this->addDependency("dPplanningOp", "0.38");
    $query = "DELETE affectation.* 
      FROM affectation
      LEFT JOIN operations ON affectation.operation_id = operations.operation_id
      WHERE operations.operation_id IS NULL;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `affectation`
      ADD `sejour_id` INT UNSIGNED DEFAULT '0' NOT NULL AFTER `operation_id`;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `affectation` 
      ADD INDEX (`sejour_id`);";
    $this->addQuery($query);
    
    $query = "UPDATE `affectation`,`operations`
      SET `affectation`.`sejour_id` = `operations`.`sejour_id`
      WHERE `affectation`.`operation_id` = `operations`.`operation_id`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.15");
    $this->addDependency("dPetablissement", "0.1");
    $query = "ALTER TABLE `service` 
      ADD `group_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `service_id`;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `service` 
      ADD INDEX ( `group_id` ) ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.16");
    $query = "ALTER TABLE `affectation` DROP `operation_id`";
    $this->addQuery($query);
    
    $this->makeRevision("0.17");
    $query = "ALTER TABLE `affectation`
      CHANGE `affectation_id` `affectation_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      CHANGE `lit_id` `lit_id` int(11) unsigned NOT NULL DEFAULT '0',
      CHANGE `confirme` `confirme` enum('0','1') NOT NULL DEFAULT '0',
      CHANGE `effectue` `effectue` enum('0','1') NOT NULL DEFAULT '0',
      CHANGE `sejour_id` `sejour_id` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `chambre` 
      CHANGE `chambre_id` `chambre_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      CHANGE `service_id` `service_id` int(11) unsigned NOT NULL DEFAULT '0',
      CHANGE `nom` `nom` varchar(255) NOT NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `lit` 
      CHANGE `lit_id` `lit_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      CHANGE `chambre_id` `chambre_id` int(11) unsigned NOT NULL,
      CHANGE `nom` `nom` varchar(255) NOT NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `service` 
      CHANGE `service_id` `service_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      CHANGE `group_id` `group_id` int(11) unsigned NOT NULL DEFAULT '1',
      CHANGE `nom` `nom` varchar(255) NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.18");
    $query = "ALTER TABLE `affectation` 
      ADD `rques` text NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.19");
    $query = "ALTER TABLE `affectation` 
      ADD INDEX ( `confirme` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `affectation` 
      ADD INDEX ( `effectue` )";
    $this->addQuery($query);
    
    $this->makeRevision("0.20");
    $query= "CREATE TABLE `prestation` (
     `prestation_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
     `group_id` INT(11) UNSIGNED NOT NULL, 
     `nom` VARCHAR(255) NOT NULL, 
     `description` TEXT, 
      PRIMARY KEY (`prestation_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.21");
    $this->addPrefQuery("ccam_sejour", "0");

    $this->makeRevision("0.22");
    $query = "ALTER TABLE `service`
      ADD `urgence` ENUM('0','1');";
    $this->addQuery($query);
    
    $this->makeRevision("0.23");
    $query = "CREATE TABLE `observation_medicale` (
      `observation_medicale_id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
      `sejour_id` INT( 11 ) UNSIGNED NOT NULL ,
      `user_id` INT( 11 ) UNSIGNED NOT NULL ,
      `degre` ENUM('low','high') NOT NULL DEFAULT 'low',
      `date` DATETIME NOT NULL ,
      `text` TEXT NULL ,
      INDEX ( `sejour_id`, `user_id` , `degre` , `date` )
    ) /*! ENGINE=MyISAM */ COMMENT = 'Table des observations m�dicales';";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `transmission_medicale` (
      `transmission_medicale_id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
      `sejour_id` INT( 11 ) UNSIGNED NOT NULL ,
      `user_id` INT( 11 ) UNSIGNED NOT NULL ,
      `degre` ENUM('low','high') NOT NULL DEFAULT 'low',
      `date` DATETIME NOT NULL ,
      `text` TEXT NULL ,
      INDEX ( `sejour_id`, `user_id` , `degre` , `date` )
    ) /*! ENGINE=MyISAM */ COMMENT = 'Table des transmissions m�dicales';";
    $this->addQuery($query);
    
    $this->makeRevision("0.24");
    $query = "CREATE TABLE `categorie_cible_transmission` (
      `categorie_cible_transmission_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `libelle` VARCHAR (255) NOT NULL,
      `description` TEXT
    ) /*! ENGINE=MyISAM */ COMMENT = 'Table des cat�gories de cible de transmission m�dicales';";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `cible_transmission` (
      `cible_transmission_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `categorie_cible_transmission_id` INT (11) UNSIGNED NOT NULL,
      `libelle` VARCHAR (255) NOT NULL,
      `description` TEXT,
      INDEX(`categorie_cible_transmission_id`)
    ) /*! ENGINE=MyISAM */ COMMENT = 'Table des cible de transmission m�dicales';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `transmission_medicale` 
      ADD `cible_transmission_id` INT (11) UNSIGNED AFTER user_id;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `transmission_medicale` 
      ADD INDEX (`cible_transmission_id`)";
    $this->addQuery($query);
    
    $this->makeRevision("0.25");
    $query = "DROP TABLE `categorie_cible_transmission`";
    $this->addQuery($query);
    $query = "DROP TABLE `cible_transmission`";
    $this->addQuery($query);
    $query = "ALTER TABLE `transmission_medicale`
      DROP `cible_transmission_id`";
    $this->addQuery($query);
    
        
    $this->makeRevision("0.26");
    $query = "ALTER TABLE `transmission_medicale` 
      ADD `object_id` INT (11) UNSIGNED,
      ADD `object_class` ENUM ('CPrescriptionLineElement','CPrescriptionLineMedicament','CPrescriptionLineComment'),
      ADD INDEX (`object_id`),
      ADD INDEX (`object_class`)";
    $this->addQuery($query);
    
    $this->makeRevision("0.27");
    $query = "ALTER TABLE `transmission_medicale` 
      CHANGE `object_class` `object_class` ENUM ('CPrescriptionLineElement','CPrescriptionLineMedicament','CPrescriptionLineComment','CCategoryPrescription','CAdministration');";
    $this->addQuery($query);
    
    $this->makeRevision("0.28");
    $query = "ALTER TABLE `transmission_medicale` 
       CHANGE `object_class` `object_class` ENUM ('CPrescriptionLineElement','CPrescriptionLineMedicament','CPrescriptionLineComment','CCategoryPrescription','CAdministration','CPerfusion');";
    $this->addQuery($query);
    
    $this->makeRevision("0.29");
    $query = "ALTER TABLE `chambre` 
      ADD `annule` ENUM('0','1') DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("0.30");
    $query = "ALTER TABLE `observation_medicale` 
      CHANGE `degre` `degre` ENUM ('low','high','info') NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.31");
    $query = "ALTER TABLE `transmission_medicale` 
      ADD `type` ENUM ('data','action','result');";
    $this->addQuery($query);
    
    $this->makeRevision("0.32");
    $query = "ALTER TABLE `transmission_medicale` 
      ADD `libelle_ATC` TEXT;";
    $this->addQuery($query);
    
    $this->makeRevision("0.33");
    $query = "ALTER TABLE `operations` 
      ADD INDEX (`type_anesth`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `prestation` 
      ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.34");
    $query = "ALTER TABLE `service`
      ADD `uhcd` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.35");
    $query = "ALTER TABLE `service` 
      ADD `responsable_id` INT (11) UNSIGNED NOT NULL,
      ADD `type_sejour` ENUM ('comp','ambu','exte','seances','ssr','psy','urg','consult') DEFAULT 'ambu',
      ADD `cancelled` ENUM ('0','1') DEFAULT '0',
      ADD `hospit_jour` ENUM ('0','1') DEFAULT '0'";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `service` 
      ADD INDEX (`responsable_id`);";
    $this->addQuery($query);
    
    
    $this->makeRevision("0.36");
    $query = "CREATE TABLE `modele_etiquette` (
              `modele_etiquette_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `nom` VARCHAR (255),
              `texte` TEXT,
              `largeur_page` FLOAT DEFAULT '21',
              `hauteur_page` FLOAT DEFAULT '29.7',
              `nb_lignes` INT (11) DEFAULT '8',
              `nb_colonnes` INT (11) DEFAULT '4',
              `marge_horiz` FLOAT DEFAULT '0.3',
              `marge_vert` FLOAT DEFAULT '1.3',
              `hauteur_ligne` FLOAT DEFAULT '8',
              `object_id` INT (11) DEFAULT NULL,
              `object_class` VARCHAR (255) DEFAULT NULL,
              `font` TEXT DEFAULT NULL
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("0.37");
    $query = "ALTER TABLE `service` 
      CHANGE `responsable_id` `responsable_id` INT (11) UNSIGNED";
    $this->addQuery($query);
    
    $this->makeEmptyRevision("0.38");
    
    $this->makeRevision("0.39");
    $query = "ALTER TABLE `chambre`
              ADD `lits_alpha` ENUM ('0','1') DEFAULT '0' AFTER `caracteristiques`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.40");
    $query = "ALTER TABLE `modele_etiquette`
      ADD texte_2 TEXT AFTER texte,
      ADD texte_3 TEXT AFTER texte_2,
      ADD texte_4 TEXT AFTER texte_3;";
    $this->addQuery($query);
    
    $this->makeRevision("0.41");
    $query = "ALTER TABLE `modele_etiquette`
      ADD `group_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $query = "UPDATE `modele_etiquette`
      SET `group_id` = '".CGroups::loadCurrent()->_id."'
      WHERE `group_id` IS NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.42");
    $query = "ALTER TABLE `modele_etiquette`
      ADD `show_border` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.43");
    $query = "ALTER TABLE `service` 
      CHANGE `urgence` `urgence` ENUM ('0','1') DEFAULT '0',
      ADD `externe` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.44");
    $query = "ALTER TABLE  `chambre` ADD  `plan_x` INT(11) NULL ,
      ADD  `plan_y` INT(11) NULL;";
    $this->addQuery($query);

    $this->makeRevision("0.45");
    $query = "CREATE TABLE `uf` (
              `uf_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `group_id` INT (11) UNSIGNED NOT NULL,
              `code` VARCHAR (255) NOT NULL,
              `libelle` VARCHAR (255) NOT NULL,
              `description` TEXT
               ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `uf` 
              ADD INDEX (`group_id`);";
    $this->addQuery($query);
    $query = "CREATE TABLE `affectation_uf` (
              `affectation_uf_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `uf_id` INT (11) UNSIGNED NOT NULL,
              `debut` DATETIME,
              `fin` DATETIME,
              `object_id` INT (11) UNSIGNED NOT NULL,
              `object_class` ENUM ('CSejour','Clit','CMediuser') NOT NULL
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `affectation_uf` 
              ADD INDEX (`uf_id`),
              ADD INDEX (`debut`),
              ADD INDEX (`fin`),
              ADD INDEX (`object_id`);";
    $this->addQuery($query);
     
    $this->makeRevision("0.46");
    $query = "ALTER TABLE  `prestation`   
                ADD  `code` VARCHAR(12)";
    $this->addQuery($query);
    
    $this->makeRevision("0.47");
    
    $query = "ALTER TABLE  `affectation`   
                ADD `uf_hebergement_id` INT (11) UNSIGNED,
                ADD `uf_medicale_id` INT (11) UNSIGNED,
                ADD `uf_soins_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE  `affectation`   
                ADD INDEX (`uf_hebergement_id`),
                ADD INDEX (`uf_medicale_id`),
                ADD INDEX (`uf_soins_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.48");
    
    $query = "ALTER TABLE `affectation_uf`
                DROP `debut`,
                DROP `fin`,
                CHANGE `object_class` `object_class` ENUM ('CService','CChambre','CLit','CMediusers','CFunctions') NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.49");
    
    $query = "CREATE TABLE `movement` (
                `movement_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `movement_type` ENUM ('PADM','ADMI','MUTA','SATT','SORT') NOT NULL,
                `original_trigger_code` CHAR (3),
                `last_update` DATETIME NOT NULL,
                `object_id` INT (11) UNSIGNED NOT NULL,
                `object_class` VARCHAR (80) NOT NULL,
                `cancel` ENUM ('0','1') DEFAULT '0'
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `movement` 
                ADD INDEX (`movement_type`),
                ADD INDEX (`original_trigger_code`),
                ADD INDEX (`last_update`),
                ADD INDEX (`object_id`),
                ADD INDEX (`object_class`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.50");
    
    $query = "ALTER TABLE `lit` 
              ADD `nom_complet` VARCHAR (255);";
    $this->addQuery($query);
    
    $this->makeRevision("0.51");
    
    $query = "CREATE TABLE `prestation_journaliere` (
               `prestation_journaliere_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
               `nom` VARCHAR (255) NOT NULL,
               `group_id` INT (11) UNSIGNED NOT NULL
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prestation_journaliere` 
      ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `prestation_ponctuelle` (
                `prestation_ponctuelle_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `nom` VARCHAR (255) NOT NULL,
                `group_id` INT (11) UNSIGNED NOT NULL
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prestation_ponctuelle` 
      ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `item_prestation` (
                `item_prestation_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `nom` VARCHAR (255) NOT NULL,
                `rank` INT (11) UNSIGNED DEFAULT '1',
                `object_id` INT (11) UNSIGNED NOT NULL,
                `object_class` ENUM ('CPrestationPonctuelle','CPrestationJournaliere')
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `item_prestation` 
      ADD INDEX (`object_id`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `item_liaison` (
               `item_liaison_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
               `affectation_id` INT (11) UNSIGNED NOT NULL,
               `item_prestation_id` INT (11) UNSIGNED NOT NULL
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `item_liaison` 
      ADD INDEX (`affectation_id`),
      ADD INDEX (`item_prestation_id`);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `item_liaison` 
      ADD `item_prestation_realise_id` INT (11) UNSIGNED,
      ADD `date` DATE,
      ADD `quantite` INT (11) DEFAULT '0';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `item_liaison` 
      ADD INDEX (`item_prestation_realise_id`),
      ADD INDEX (`date`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.52");
    
    $query = "ALTER TABLE `movement` 
                ADD `sejour_id` INT (11) UNSIGNED NOT NULL,
                ADD `affectation_id` INT (11) UNSIGNED,
                DROP `object_id`,
                DROP `object_class`;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `movement` 
                ADD INDEX (`sejour_id`),
                ADD INDEX (`affectation_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.53");
    $query = "ALTER TABLE `affectation` 
      ADD `parent_affectation_id` INT (11) UNSIGNED AFTER `sortie`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.54");
    $query = "CREATE TABLE `secteur` (
              `secteur_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `group_id` INT (11) UNSIGNED NOT NULL,
              `nom` VARCHAR (255) NOT NULL,
              `description` TEXT
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `secteur` 
      ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `service` 
      ADD `secteur_id` INT (11) UNSIGNED AFTER `service_id`;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `service` 
      ADD INDEX (`secteur_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.55");
    $query = "CREATE TABLE `lit_liaison_item` (
              `lit_liaison_item_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `lit_id` INT (11) UNSIGNED NOT NULL,
              `item_prestation_id` INT (11) UNSIGNED NOT NULL
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `lit_liaison_item` 
      ADD INDEX (`lit_id`),
      ADD INDEX (`item_prestation_id`);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `item_liaison` 
      ADD `sejour_id` INT (11) UNSIGNED NOT NULL;";
    $this->addQuery($query);
    
    $query = "UPDATE `item_liaison`
      SET `sejour_id` = ( SELECT `sejour_id`
                          FROM `affectation`
                          WHERE `affectation`.`affectation_id` = `item_liaison`.`affectation_id`
                          LIMIT 1 );";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `item_liaison`
      DROP `affectation_id`";
    $this->addQuery($query);
    
    $this->makeRevision("0.56");
    $this->addDependency("dPplanningOp", "1.33");
    
    $query = "UPDATE `sejour`, `affectation`
                SET `sejour`.`confirme` = '1'
                WHERE `affectation`.`sejour_id` = `sejour`.`sejour_id`
                AND `affectation`.`confirme` = '1';";
    
    $this->makeRevision("0.57");
    $query = "ALTER TABLE `item_liaison`
      CHANGE `item_prestation_id` `item_prestation_id` INT (11) UNSIGNED DEFAULT NULL";
    
    $this->makeRevision("0.58");
    $query = "ALTER TABLE `modele_etiquette`
      ADD `text_align` ENUM ('top','middle','bottom') DEFAULT 'top'";
    $this->addQuery($query);
    
    $this->makeRevision("0.59");
    $query = "ALTER TABLE `movement` 
              ADD `start_of_movement` DATETIME;";
    $this->addQuery($query);           
              
    $query = "ALTER TABLE `movement` 
              ADD INDEX (`start_of_movement`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.60");
    $query = "ALTER TABLE `service`
      ADD `neonatalogie` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.61");
    
    $query = "ALTER TABLE `affectation`
      ADD `function_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("0.62");
    $query = "ALTER TABLE `observation_medicale`
              ADD `object_id` INT (11) UNSIGNED,
              ADD `object_class` ENUM ('CPrescriptionLineElement','CPrescriptionLineMedicament','CPrescriptionLineMix');";
    $this->addQuery($query);
    
    $this->makeRevision("0.63");
    $query = "ALTER TABLE `affectation` 
      ADD `service_id` INT (11) UNSIGNED NOT NULL AFTER `affectation_id`,
      CHANGE `lit_id` `lit_id` INT (11) UNSIGNED";
    $this->addQuery($query);
    
    $query = "UPDATE `affectation`
      LEFT JOIN `lit` ON `affectation`.`lit_id` = `lit`.`lit_id`
      LEFT JOIN `chambre` ON `lit`.`chambre_id` = `chambre`.`chambre_id`
      SET `affectation`.`service_id` = `chambre`.`service_id`";
    $this->addQuery($query);
    
    $this->makeRevision("0.64");
    $query = "ALTER TABLE `transmission_medicale` 
              ADD `date_max` DATETIME;";
    $this->addQuery($query);
    
    $this->makeRevision("0.65");
    $query = "ALTER TABLE `movement` 
                CHANGE `movement_type` `movement_type` VARCHAR (4) NOT NULL";
    $this->addQuery($query);
    
    $this->makeRevision("0.66");
    $query = "ALTER TABLE `prestation_journaliere` 
      ADD `desire` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.67");
    $query = "ALTER TABLE `item_liaison` 
      CHANGE `item_prestation_id`         `item_souhait_id` INT (11) UNSIGNED,
      CHANGE `item_prestation_realise_id` `item_realise_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `item_liaison` 
      ADD INDEX (`sejour_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.68");
    $query = "UPDATE `item_liaison`
      SET `item_souhait_id` = NULL
      WHERE `item_souhait_id` = 0";
    $this->addQuery($query);
    
    $this->makeRevision("0.69");
    $query = "ALTER TABLE `affectation_uf` 
              CHANGE `object_class` `object_class` ENUM ('CService','CChambre','CLit','CMediusers','CFunctions','CSejour','CProtocole') NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.70");
        
    $query = "ALTER TABLE `chambre` 
              DROP `plan_x`,
              DROP `plan_y`;";
    $this->addQuery($query);
    
    $query = "CREATE TABLE IF NOT EXISTS `emplacement`(
             `emplacement_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
             `chambre_id` INT (11) UNSIGNED NOT NULL,
             `plan_x` INT (11) NOT NULL,
             `plan_y` INT (11) NOT NULL,
             `color` VARCHAR(6) NOT NULL DEFAULT 'DDDDDD',
             `hauteur` INT (11) NOT NULL DEFAULT '1',
             `largeur` INT (11) NOT NULL DEFAULT '1',
             PRIMARY KEY (`emplacement_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `emplacement` 
                ADD INDEX (`chambre_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.71");
    
    $query = "ALTER TABLE `uf` 
                ADD `type` ENUM ('hebergement','soins','medicale');";
    $this->addQuery($query);
    
    $query = "UPDATE `uf`
                SET `type` = 'medicale' WHERE `type` IS NULL";
    $this->addQuery($query);
    
    $this->makeRevision("0.72");
    $query = "ALTER TABLE `lit` 
                ADD `annule` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.73");
    $query = "ALTER TABLE `transmission_medicale`
                ADD INDEX (`sejour_id`)";
    $this->addQuery($query);
    $query = "ALTER TABLE `observation_medicale`
                ADD INDEX (`sejour_id`)";
    $this->addQuery($query);
    $this->makeRevision("0.74");
    
    $query = "ALTER TABLE `affectation` 
              ADD `praticien_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    $query = "ALTER TABLE `affectation`
              ADD INDEX (`praticien_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.75");
    $query = "ALTER TABLE `transmission_medicale`
      CHANGE `date` `date` DATETIME NOT NULL,
      ADD `locked` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);


    $this->makeRevision("0.76");
    $this->addUpdateMessage("Mise � jour des blocages de lit en base, 0 => null");
    $query = "ALTER TABLE `affectation`
                CHANGE `sejour_id` `sejour_id` INT (11) UNSIGNED";
    $this->addQuery($query);

    $query = "UPDATE `affectation`
                SET `sejour_id` = NULL
                WHERE `sejour_id` = '0';";
    $this->addQuery($query);

    $this->makeRevision("0.77");
    $query = "ALTER TABLE `affectation`
                ADD INDEX (`service_id`)";
    $this->addQuery($query);

    $this->makeRevision("0.78");

    $query = "UPDATE  `affectation`, `chambre`, `lit`
        SET  `affectation`.`service_id` =  `chambre`.`service_id`
        WHERE  `lit`.`lit_id` = `affectation`.`lit_id`
        AND `chambre`.`chambre_id` = `lit`.`chambre_id`
        AND `affectation`.`service_id` != `chambre`.`service_id`;";
    $this->addQuery($query);

    $this->makeRevision("0.79");
    $this->addUpdateMessage("Suppression de l'index multiple, ajout de l'index de date");
    $query = "ALTER TABLE `transmission_medicale` DROP INDEX sejour_id";
    $this->addQuery($query);
    $query = "ALTER TABLE `transmission_medicale` ADD INDEX (  `date` )";
    $this->addQuery($query);
    $query = "ALTER TABLE  `transmission_medicale` ADD INDEX (  `user_id` )";
    $this->addQuery($query);

    $this->makeRevision("0.80");
    $query = "ALTER TABLE `chambre`
                ADD `is_waiting_room` ENUM ('0','1') DEFAULT '0',
                ADD `is_examination_room` ENUM ('0','1') DEFAULT '0',
                ADD `is_sas_dechoc` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.81");
    $query = "ALTER TABLE `observation_medicale` ADD `type` ENUM ('reevaluation');";
    $this->addQuery($query);

    $this->makeRevision("0.82");
    $query = "ALTER TABLE `uf`
                ADD `type_sejour` ENUM ('comp','ambu','exte','seances','ssr','psy','urg','consult');";
    $this->addQuery($query);

    $this->makeRevision("0.83");
    $query = "ALTER TABLE `service`
                ADD `default_orientation` ENUM ('HDT','HO','SC','SI','REA','UHCD','MED','CHIR','OBST','FUGUE','SCAM','PSA','REO'),
                ADD `default_destination` ENUM ('1','2','3','4','6','7');";
    $this->addQuery($query);

    $this->makeRevision("0.84");

    $query = "ALTER TABLE `service`
                ADD `radiologie` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.85");
    $query = "ALTER TABLE `affectation`
      ADD INDEX (`parent_affectation_id`)";
    $this->addQuery($query);

    $this->makeRevision("0.86");
    $query = "ALTER TABLE `uf`
                CHANGE `group_id` `group_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                CHANGE `type` `type` ENUM ('hebergement','soins','medicale') DEFAULT 'hebergement',
                ADD `date_debut` DATE,
                ADD `date_fin` DATE;";
    $this->addQuery($query);
    $query = "ALTER TABLE `uf`
                ADD INDEX (`date_debut`),
                ADD INDEX (`date_fin`);";
    $this->addQuery($query);

    $this->makeRevision("0.87");
    $query = "ALTER TABLE `item_prestation`
      ADD `color` VARCHAR (6);";
    $this->addQuery($query);

    $this->makeRevision("0.88");
    $query = "ALTER TABLE `uf`
                ADD `type_autorisation_um_id` INT (11) UNSIGNED,
                ADD `type_autorisation_mode_hospitalisation` char(10) ;";
    $this->addQuery($query);

    $this->makeRevision("0.89");
    $query = "ALTER TABLE `uf`
                ADD `nb_lits_um` INT (3) UNSIGNED ;";
    $this->addQuery($query);

    $this->makeRevision("0.90");
    $query = "ALTER TABLE `uf`
                CHANGE `type_autorisation_um_id` `type_autorisation_um` CHAR (3),
                CHANGE `type_autorisation_mode_hospitalisation` `type_autorisation_mode_hospi` CHAR (10);";
    $this->addQuery($query);

    $this->makeRevision("0.91");
    $query = "ALTER TABLE `service`
                ADD `is_soins_continue` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.92");
    $query= "ALTER TABLE `uf`
              DROP `nb_lits_um`,
              DROP `type_autorisation_mode_hospi`;";
    $this->addQuery($query);

    $query = "ALTER TABLE `uf`
                CHANGE `type_autorisation_um` `type_autorisation_um` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $this->makeRevision("0.93");
    $query = "ALTER TABLE `prestation_journaliere`
      ADD `niveau` ENUM ('jour','nuit');";
    $this->addQuery($query);

    $query = "CREATE TABLE `sous_item_prestation` (
      `sous_item_prestation_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `nom` VARCHAR (255),
      `item_prestation_id` INT (11) UNSIGNED
    )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `sous_item_prestation`
      ADD INDEX (`item_prestation_id`);";
    $this->addQuery($query);

    $query = "ALTER TABLE `item_liaison`
      ADD `sous_item_id` INT (11) UNSIGNED AFTER `item_realise_id`;";
    $this->addQuery($query);

    $this->makeRevision("0.94");
    $query = "ALTER TABLE `prestation_journaliere`
      DROP COLUMN `niveau`";
    $this->addQuery($query);

    $query = "ALTER TABLE `sous_item_prestation`
      ADD `niveau` ENUM ('jour','nuit') DEFAULT 'jour';";
    $this->addQuery($query);

    $this->makeRevision("0.95");
    $query = "ALTER TABLE `item_prestation`
      ADD `facturable` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("0.96");
    $query = "ALTER TABLE `service`
                ADD `use_brancardage` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);


    $this->makeRevision("0.97");
    $query = "ALTER TABLE `lit`
                ADD `rank` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $query = "ALTER TABLE `lit`
                ADD INDEX (`rank`);";
    $this->addQuery($query);

    $query = "ALTER TABLE `chambre`
                ADD `rank` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $query = "ALTER TABLE `chambre`
                ADD INDEX (`rank`);";
    $this->addQuery($query);

    $this->makeRevision("0.98");
    $query = "ALTER TABLE `service`
                ADD `typologie` VARCHAR (255),
                ADD `code` VARCHAR (80) NOT NULL,
                ADD `short_name` VARCHAR (255),
                ADD `user_id` INT (11) UNSIGNED,
                ADD `opening_reason` TEXT,
                ADD `opening_date` DATE,
                ADD `closing_reason` TEXT,
                ADD `closing_date` DATE,
                ADD `activation_date` DATE,
                ADD `inactivation_date` DATE;";
    $this->addQuery($query);

    $query = "UPDATE `service` SET `code` = `nom`";
    $this->addQuery($query);

    $query = "ALTER TABLE `service`
                ADD INDEX (`user_id`),
                ADD INDEX (`opening_date`),
                ADD INDEX (`closing_date`),
                ADD INDEX (`activation_date`),
                ADD INDEX (`inactivation_date`);";
    $this->addQuery($query);

    $this->makeRevision("0.99");

    $query = "ALTER TABLE `secteur`
                CHANGE `group_id` `group_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                ADD `typologie` VARCHAR (255),
                ADD `code` VARCHAR (80) NOT NULL,
                ADD `short_name` VARCHAR (255),
                ADD `user_id` INT (11) UNSIGNED,
                ADD `opening_reason` TEXT,
                ADD `opening_date` DATE,
                ADD `closing_reason` TEXT,
                ADD `closing_date` DATE,
                ADD `activation_date` DATE,
                ADD `inactivation_date` DATE";
    $this->addQuery($query);

    $query = "UPDATE `secteur` SET `code` = `nom`";
    $this->addQuery($query);

    $query = "ALTER TABLE `secteur`
                ADD INDEX (`user_id`),
                ADD INDEX (`opening_date`),
                ADD INDEX (`closing_date`),
                ADD INDEX (`activation_date`),
                ADD INDEX (`inactivation_date`);";
    $this->addQuery($query);

    $this->makeRevision("1.00");
    $query = "ALTER TABLE `chambre`
                CHANGE `rank` `rank` MEDIUMINT (9),
                ADD `typologie` VARCHAR (255),
                ADD `code` VARCHAR (80) NOT NULL,
                ADD `short_name` VARCHAR (255),
                ADD `description` TEXT,
                ADD `user_id` INT (11) UNSIGNED,
                ADD `opening_reason` TEXT,
                ADD `opening_date` DATE,
                ADD `closing_reason` TEXT,
                ADD `closing_date` DATE,
                ADD `activation_date` DATE,
                ADD `inactivation_date` DATE;";
    $this->addQuery($query);

    $query = "UPDATE `chambre` SET `code` = `nom`";
    $this->addQuery($query);
    $query = "UPDATE `chambre` SET `description` = `caracteristiques`";
    $this->addQuery($query);

    $query = "ALTER TABLE `chambre`
                ADD INDEX (`user_id`),
                ADD INDEX (`opening_date`),
                ADD INDEX (`closing_date`),
                ADD INDEX (`activation_date`),
                ADD INDEX (`inactivation_date`);";
    $this->addQuery($query);

    $this->makeRevision("1.01");
    $query = "ALTER TABLE `lit`
                CHANGE `chambre_id` `chambre_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                CHANGE `rank` `rank` MEDIUMINT (9),
                ADD `typologie` VARCHAR (255),
                ADD `code` VARCHAR (80) NOT NULL,
                ADD `short_name` VARCHAR (255),
                ADD `description` TEXT,
                ADD `user_id` INT (11) UNSIGNED,
                ADD `opening_reason` TEXT,
                ADD `opening_date` DATE,
                ADD `closing_reason` TEXT,
                ADD `closing_date` DATE,
                ADD `activation_date` DATE,
                ADD `inactivation_date` DATE;";
    $this->addQuery($query);

    $query = "UPDATE `lit` SET `code` = `nom`";
    $this->addQuery($query);
    $query = "UPDATE `lit` SET `description` = `nom_complet`";
    $this->addQuery($query);

    $query = "ALTER TABLE `lit`
                ADD INDEX (`user_id`),
                ADD INDEX (`opening_date`),
                ADD INDEX (`closing_date`),
                ADD INDEX (`activation_date`),
                ADD INDEX (`inactivation_date`);";
    $this->addQuery($query);

    $this->makeRevision("1.02");
    $query = "ALTER TABLE `lit`
                ADD `identifie` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.03");
    $this->addDefaultConfig("dPhospi prestations systeme_prestations", "dPhospi systeme_prestations");

    $query = "ALTER TABLE `prestation_journaliere`
      ADD `type_hospi` ENUM ('comp','ambu','exte','seances','ssr','psy','urg','consult','');";
    $this->addQuery($query);

    $query = "ALTER TABLE `prestation_ponctuelle`
      ADD `type_hospi` ENUM ('comp','ambu','exte','seances','ssr','psy','urg','consult','');";
    $this->addQuery($query);

    $this->mod_version = "1.04";
  }
}
