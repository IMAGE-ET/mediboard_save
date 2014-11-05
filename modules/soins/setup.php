<?php
/**
 * $Id$
 *
 * @category Soins
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

class CSetupsoins extends CSetup {

  function __construct() {
    parent::__construct();

    $this->mod_name = 'soins';

    $this->makeRevision('all');
    
    $this->makeRevision("0.1");
    
    $query = "CREATE TABLE `sejour_task` (
      `sejour_task_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `sejour_id` INT (11) UNSIGNED NOT NULL,
      `description` TEXT NOT NULL,
      `realise` ENUM ('0','1') DEFAULT '0',
      `resultat` TEXT
      ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `sejour_task` ADD INDEX (`sejour_id`);";
    $this->addQuery($query);  
    
    $this->makeRevision("0.11");
    $query = "CREATE TABLE `ressource_soin` (
              `ressource_soin_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `libelle` TEXT NOT NULL,
              `cout` FLOAT
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `indice_cout` (
              `indice_cout_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `nb` INT (11) NOT NULL,
              `ressource_soin_id` INT (11) UNSIGNED NOT NULL,
              `element_prescription_id` INT (11) UNSIGNED NOT NULL
             ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `indice_cout` 
              ADD INDEX (`ressource_soin_id`),
              ADD INDEX (`element_prescription_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.12");
    $query = "ALTER TABLE `ressource_soin` 
              CHANGE `libelle` `libelle` VARCHAR (255) NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.13");
    $query = "ALTER TABLE `ressource_soin`
              ADD `code` VARCHAR (255) NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.14");
    $this->moveConf("dPprescription CPrescription max_time_modif_suivi_soins", "soins max_time_modif_suivi_soins");

    $this->makeRevision("0.15");
    $query = "ALTER TABLE `sejour_task`
      ADD `consult_id` INT (11) UNSIGNED,
      ADD INDEX (`consult_id`)";
    $this->addQuery($query);

    $this->makeRevision("0.16");
    $query = "UPDATE perm_module
              LEFT JOIN modules ON perm_module.mod_id = modules.mod_id
              SET permission = '2'
              WHERE modules.mod_name = 'soins'";
    $this->addQuery($query);

    $this->makeRevision("0.17");
    $this->addPrefQuery("vue_sejours", "standard");

    $this->makeRevision('0.18');

    $query = "ALTER TABLE `sejour_task`
                ADD `date` DATETIME,
                ADD `author_id` INT(11);";
    $this->addQuery($query);
    $this->makeRevision('0.19');

    $this->addDefaultConfig("soins CLit align_right"                    , "soins CLit align_right");
    $this->addDefaultConfig("soins CConstantesMedicales constantes_show", "soins constantes_show");
    $this->addDefaultConfig("soins Pancarte transmissions_hours"        , "soins transmissions_hours");
    $this->addDefaultConfig("soins Pancarte soin_refresh_pancarte_service", "soins soin_refresh_pancarte_service");
    $this->addDefaultConfig("soins Transmissions cible_mandatory_trans" , "soins cible_mandatory_trans");
    $this->addDefaultConfig("soins Transmissions trans_compact"         , "soins trans_compact");
    $this->addDefaultConfig("soins Sejour refresh_vw_sejours_frequency" , "soins refresh_vw_sejours_frequency");
    $this->addDefaultConfig("soins Other show_charge_soins"             , "soins show_charge_soins");
    $this->addDefaultConfig("soins Other max_time_modif_suivi_soins"    , "soins max_time_modif_suivi_soins");
    $this->addDefaultConfig("soins Other show_only_lit_bilan"           , "soins show_only_lit_bilan");
    $this->addDefaultConfig("soins Other ignore_allergies"              , "soins ignore_allergies");
    $this->addDefaultConfig("soins Other vue_condensee_dossier_soins"   , "soins vue_condensee_dossier_soins");
    $this->makeRevision('0.20');

    $query = "INSERT INTO `configuration` (`feature`, `value`) VALUES (?1, ?2)";
    $query = $this->ds->prepare($query, "soins Other default_motif_observation", "Observation d'entr�e");
    $this->addQuery($query);
    $this->makeRevision('0.21');

    $query = "ALTER TABLE `sejour_task`
                ADD `date_realise` DATETIME,
                ADD `author_realise_id` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $query = "ALTER TABLE `sejour_task`
                ADD INDEX (`date`),
                ADD INDEX (`author_id`),
                ADD INDEX (`date_realise`),
                ADD INDEX (`author_realise_id`);";
    $this->addQuery($query);
    $this->makeRevision("0.22");

    $this->addPrefQuery("default_services_id", "{}");
    $this->makeRevision("0.23");

    $query = "INSERT INTO `user_preferences` ( `user_id` , `key` , `value` , `pref_id` , `restricted` )
      SELECT user_id, 'default_services_id', value, null, '0'
      FROM `user_preferences`
      WHERE `key` = 'services_ids_hospi'
      AND `value` != '{}'
      AND `user_id` IS NOT NULL
      GROUP BY user_id ;";
    $this->addQuery($query);
    $this->makeRevision("0.24");

    $this->addPrefQuery("hide_line_inactive", "0");
    $this->mod_version = '0.25';
  }
}
