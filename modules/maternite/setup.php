<?php

/**
 * maternite
 *  
 * @category maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CSetupmaternite extends CSetup {
  
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
      ADD `active` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->mod_version = "0.02";
  }
}
?>