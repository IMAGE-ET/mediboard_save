<?php

/**
 * Setup Maternit�
 *  
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CSetupmaternite
 * Setup Maternit�
 */
class CSetupmaternite extends CSetup {

  /**
   * @see parent::__construct()
   */
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "maternite";
    
    $this->makeRevision("all");
    $query = "CREATE TABLE `naissance` (
      `naissance_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `operation_id` INT (11) UNSIGNED,
      `grossesse_id` INT (11) UNSIGNED,
      `sejour_enfant_id` INT (11) UNSIGNED NOT NULL,
      `hors_etab` ENUM ('0','1') DEFAULT '0',
      `heure` TIME NOT NULL,
      `rang` INT (11) UNSIGNED NOT NULL
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `naissance`
      ADD INDEX (`operation_id`),
      ADD INDEX (`grossesse_id`),
      ADD INDEX (`sejour_enfant_id`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `grossesse` (
      `grosssesse_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `parturiente_id` INT (11) UNSIGNED NOT NULL,
      `terme_prevu` DATE NOT NULL
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.01");
    $query = "ALTER TABLE `grossesse`
      ADD `active` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);
    
    $this->makeRevision("0.02");
    $query = "ALTER TABLE `naissance`
      CHANGE `heure` `heure` TIME,
      CHANGE `rang` `rang` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `grossesse`
      ADD `date_dernieres_regles` DATE;";
    $this->addQuery($query);
    
    $this->makeRevision("0.03");
    $query = "ALTER TABLE `grossesse`
      CHANGE `grosssesse_id` `grossesse_id` INT (11) UNSIGNED";
    $this->addQuery($query);
    
    $this->makeRevision("0.04");
    $query = "ALTER TABLE `grossesse`
      CHANGE `grossesse_id` `grossesse_id` INT (11) UNSIGNED NOT NULL auto_increment";
    $this->addQuery($query);
    
    $this->makeRevision("0.05");
    $query = "ALTER TABLE `naissance`
      ADD `sejour_maman_id` INT (11) UNSIGNED NOT NULL,
      ADD INDEX (`sejour_maman_id`)";
    $this->addQuery($query);

    $this->makeRevision("0.06");
    $query = "ALTER TABLE `grossesse` 
      ADD `multiple` ENUM ('0','1') DEFAULT '0',
      ADD INDEX (`parturiente_id`),
      ADD INDEX (`terme_prevu`),
      ADD INDEX (`date_dernieres_regles`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.07");
    $query = "ALTER TABLE `grossesse` 
      ADD `allaitement_maternel` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);
    
    $this->makeRevision("0.08");
    $query = "ALTER TABLE `grossesse` 
      ADD `date_fin_allaitement` DATE;";
    $this->addQuery($query);

    $this->makeRevision("0.09");
    $query = "ALTER TABLE `naissance`
      ADD `num_naissance` INT (11) UNSIGNED,
      ADD `lieu_accouchement` ENUM ('sur_site','exte') DEFAULT 'sur_site',
      ADD `fausse_couche` ENUM ('inf_15','sup_15'),
      ADD `rques` TEXT;";
    $this->addQuery($query);

    $this->makeRevision("0.10");
    $query = "ALTER TABLE `naissance`
      DROP `lieu_accouchement`;";
    $this->addQuery($query);

    $query = "ALTER TABLE `grossesse`
      ADD `lieu_accouchement` ENUM ('sur_site','exte') DEFAULT 'sur_site',
      ADD `fausse_couche` ENUM ('inf_15','sup_15'),
      ADD `rques` TEXT;";
    $this->addQuery($query);

    $this->makeRevision("0.11");
    $query = "ALTER TABLE `grossesse`
      ADD `group_id` INT (11) UNSIGNED AFTER `grossesse_id`,
      ADD INDEX (`group_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.12");
    $query = "ALTER TABLE `grossesse`
                ADD `datetime_debut_travail` DATETIME,
                ADD `datetime_accouchement` DATETIME;";
    $this->addQuery($query);

    $this->makeRevision("0.13");
    $query = "CREATE TABLE `allaitement` (
      `allaitement_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `patient_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
      `grossesse_id` INT (11) UNSIGNED,
      `date_debut` DATETIME NOT NULL,
      `date_fin` DATETIME
    )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `allaitement`
      ADD INDEX (`patient_id`),
      ADD INDEX (`grossesse_id`),
      ADD INDEX (`date_debut`),
      ADD INDEX (`date_fin`);";
    $this->addQuery($query);

    $query = "INSERT INTO `allaitement`
      SELECT null, `parturiente_id`, `grossesse_id`, `terme_prevu`, `date_fin_allaitement`
      FROM `grossesse`
      WHERE `date_fin_allaitement` IS NOT NULL;
    ";
    $this->addQuery($query);


    $query = "ALTER TABLE `grossesse`
      DROP `date_fin_allaitement`;";
    $this->addQuery($query);

    $this->makeRevision("0.14");
    $query = "ALTER TABLE `naissance`
                ADD `date_time` DATETIME AFTER `heure`,
                ADD `by_caesarean` ENUM ('0','1') DEFAULT '0' NOT NULL;";
    $this->addQuery($query);

    $query = "ALTER TABLE `naissance` ADD INDEX (`date_time`)";
    $this->addQuery($query);

    $this->makeRevision("0.15");
    $query = "
      UPDATE naissance
      INNER JOIN sejour ON sejour.sejour_id = naissance.sejour_enfant_id
      INNER JOIN patients ON sejour.patient_id = patients.patient_id
      SET naissance.date_time = CONCAT(patients.naissance, ' ', naissance.heure)
      WHERE naissance.heure IS NOT NULL
        AND patients.naissance IS NOT NULL";
    $this->addQuery($query);

    $this->makeRevision("0.16");
    $query = "ALTER TABLE `naissance` DROP `heure`";
    $this->addQuery($query);

    $this->mod_version = "0.17";

  }
}
