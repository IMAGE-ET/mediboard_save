<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPurgences
 *	@version $Revision: $
 *  @author Sébastien Fillonneau
 */

global $AppUI;
 
// MODULE CONFIGURATION 
// redundant now but mandatory until end of refactoring
$config = array();
$config["mod_name"]        = "dPurgences";
$config["mod_version"]     = "0.12";
$config["mod_type"]        = "user";

class CSetupdPurgences extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPurgences";
    
    $this->makeRevision("all");
    
    $sql = "CREATE TABLE `rpu` (
              `rpu_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
              `sejour_id` INT(11) UNSIGNED NOT NULL, 
              `diag_infirmier` TEXT, 
              `mode_entree` ENUM('6','7','8'), 
              `provenance` ENUM('1','2','3','4','5','8'), 
              `transport` ENUM('perso','ambu','vsab','smur','heli','fo'), 
              `prise_en_charge` ENUM('med','paramed','aucun'), 
              `motif` TEXT, 
              `ccmu` ENUM('1','2','3','4','5','P','D') NOT NULL, 
              `sortie` DATETIME, 
              `mode_sortie` ENUM('6','7','8','9'), 
              `destination` ENUM('1','2','3','4','6','7'), 
              `orientation` ENUM('HDT','HO','SC','SI','REA','UHCD','MED','CHIR','OBST','FUGUE','SCAM','PSA','REO'), 
             KEY `sejour_id` (`sejour_id`),
             KEY `ccmu` (`ccmu`),
             KEY `sortie` (`sortie`),
             PRIMARY KEY (`rpu_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.1");
    $sql = "ALTER TABLE `rpu` CHANGE `ccmu` `ccmu` ENUM( '1', 'P', '2', '3', '4', '5', 'D' )";
    $this->addQuery($sql);
    
    $this->makeRevision("0.11");
    $sql = "ALTER TABLE `rpu`
            ADD `radio_debut` DATETIME, 
            ADD `radio_fin` DATETIME;";
    $this->addQuery($sql);
    
    $this->mod_version = "0.12";
  }  
}

?>