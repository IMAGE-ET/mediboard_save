<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPetablissement
* @version $Revision: $
* @author Romain Ollivier
*/

$config = array();
$config["mod_name"]        = "dPetablissement";
$config["mod_version"]     = "0.15";
$config["mod_type"]        = "core";

class CSetupdPetablissement extends CSetup {
  function remove() {
    global $AppUI;
    $AppUI->setMsg("Impossible de supprimer le module 'dPetablissement'", UI_MSG_ERROR, true);
    return null;
  }
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPetablissement";
    
    $this->makeRevision("all");
    
    $this->makeRevision("0.1");
    $sql = "ALTER TABLE `groups_mediboard`" .
            "\nADD `raison_sociale` VARCHAR( 50 ) ," .
            "\nADD `adresse` TEXT ," .
            "\nADD `cp` VARCHAR( 5 ) ," .
            "\nADD `ville` VARCHAR( 50 ) ," .
            "\nADD `tel` VARCHAR( 10 ) ," .
            "\nADD `directeur` VARCHAR( 50 ) ," .
            "\nADD `domiciliation` VARCHAR( 9 ) ," .
            "\nADD `siret` VARCHAR( 14 );";
    $this->addQuery($sql);
    
    $this->makeRevision("0.11");
    $sql = "ALTER TABLE `groups_mediboard` ADD `ape` VARCHAR( 4 ) DEFAULT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.12");
    $sql = "ALTER TABLE `groups_mediboard` " .
               "\nCHANGE `group_id` `group_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `cp` `cp` int(5) unsigned zerofill NULL," .
               "\nCHANGE `tel` `tel` bigint(10) unsigned zerofill NULL," .
               "\nCHANGE `text` `text` varchar(255) NOT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.13");
    $sql = "ALTER TABLE `groups_mediboard`" .
            "\nADD `fax` bigint(10) unsigned zerofill NULL AFTER `tel`,".
            "\nADD `mail` varchar(50) DEFAULT NULL," .
            "\nADD `web` varchar(255) DEFAULT NULL;" ;
    $this->addQuery($sql);

    
    $this->makeRevision("0.14");
    $sql = "ALTER TABLE `groups_mediboard`  
            ADD `tel_anesth` BIGINT(10) UNSIGNED ZEROFILL;";
    $this->addQuery($sql);
    
    $this->mod_version = "0.15";
    
  } 
  
}

?>