<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPplanningOp";
$config["mod_version"]     = "0.69";
$config["mod_type"]        = "user";

class CSetupdPplanningOp extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPplanningOp";
    
    $this->makeRevision("all");
    $sql = "CREATE TABLE operations ( " .
          "  operation_id bigint(20) unsigned NOT NULL auto_increment" .
          ", pat_id bigint(20) unsigned NOT NULL default '0'" .
          ", chir_id bigint(20) unsigned NOT NULL default '0'" .
          ", plageop_id bigint(20) unsigned NOT NULL default '0'" .
          ", CIM10_code varchar(5) default NULL" .
          ", CCAM_code varchar(7) default NULL" .
          ", cote enum('droit','gauche','bilat�ral','total') NOT NULL default 'total'" .
          ", temp_operation time NOT NULL default '00:00:00'" .
          ", time_operation time NOT NULL default '00:00:00'" .
          ", examen text" .
          ", materiel text" .
          ", commande_mat enum('o', 'n') NOT NULL default 'n'" .
          ", info enum('o','n') NOT NULL default 'n'" .
          ", date_anesth date NOT NULL default '0000-00-00'" .
          ", time_anesth time NOT NULL default '00:00:00'" .
          ", type_anesth tinyint(4) default NULL" .
          ", date_adm date NOT NULL default '0000-00-00'" .
          ", time_adm time NOT NULL default '00:00:00'" .
          ", duree_hospi tinyint(4) unsigned NOT NULL default '0'" .
          ", type_adm enum('comp','ambu','exte') default 'comp'" .
          ", chambre enum('o','n') NOT NULL default 'o'" .
          ", ATNC enum('o','n') NOT NULL default 'n'" .
          ", rques text" .
          ", rank tinyint(4) NOT NULL default '0'" .
          ", admis enum('n','o') NOT NULL default 'n'" .
          ", PRIMARY KEY  (operation_id)" .
          ", UNIQUE KEY operation_id (operation_id)" .
          ") TYPE=MyISAM;";
    $this->addQuery($sql);
    $sql = "INSERT INTO sysvals" .
          "\nVALUES ('', '1', 'AnesthType', '1|Rachi\n2|Rachi + bloc\n3|Anesth�sie loco-r�gionnale\n4|Anesth�sie locale\n5|Neurolept\n6|Anesth�sie g�n�rale\n7|Anesthesie generale + bloc\n8|Anesthesie peribulbaire\n0|Non d�finie')";
    $this->addQuery($sql);
    
    $this->makeRevision("0.1");
    $sql = "ALTER TABLE operations " .
          "\nADD entree_bloc TIME AFTER temp_operation ," .
          "\nADD sortie_bloc TIME AFTER entree_bloc ," .
          "\nADD saisie ENUM( 'n', 'o' ) DEFAULT 'n' NOT NULL ," .
          "\nCHANGE plageop_id plageop_id BIGINT( 20 ) UNSIGNED";
    $this->addQuery($sql);
    
    $this->makeRevision("0.2");
    $sql = "ALTER TABLE `operations` ADD `convalescence` TEXT AFTER `materiel` ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.21");
    $sql = "ALTER TABLE `operations` ADD `depassement` INT( 4 );";
    $this->addQuery($sql);
    
    $this->makeRevision("0.22");
    $sql = "ALTER TABLE `operations` " .
          "\nADD `CCAM_code2` VARCHAR( 7 ) AFTER `CCAM_code`," .
          "\nADD INDEX ( `CCAM_code2` )," .
          "\nADD INDEX ( `CCAM_code` )," .
          "\nADD INDEX ( `pat_id` )," .
          "\nADD INDEX ( `chir_id` )," .
          "\nADD INDEX ( `plageop_id` );";
    $this->addQuery($sql);
    
    $this->makeRevision("0.23");
    $sql = "ALTER TABLE `operations` " .
         "\nADD `modifiee` TINYINT DEFAULT '0' NOT NULL AFTER `saisie`," .
         "\nADD `annulee` TINYINT DEFAULT '0' NOT NULL ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.24");
    $sql = "ALTER TABLE `operations` " .
          "\nADD `compte_rendu` TEXT," .
          "\nADD `cr_valide` TINYINT( 4 ) DEFAULT '0' NOT NULL ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.25");
    $sql = "ALTER TABLE `operations` " .
          "\nADD `pathologie` VARCHAR( 8 ) DEFAULT NULL," .
          "\nADD `septique` TINYINT DEFAULT '0' NOT NULL ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.26");
    $sql = "ALTER TABLE `operations` " .
          "\nADD `libelle` TEXT DEFAULT NULL AFTER `CCAM_code2` ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.27");
    $this->setTimeLimit(1800);
    
    function setup_moveDocs(){
      $ds = CSQLDataSource::get("std");
      
      $document_types = array (
      array ("name" => "compte_rendu", "valide" => "cr_valide"));
      foreach ($document_types as $document_type) {
        $document_name = $document_type["name"];
        $document_valide = $document_type["valide"];
  
        $sql = "SELECT *" .
            "\nFROM `operations`" .
            "\nWHERE `$document_name` IS NOT NULL" .
            "\nAND `$document_name` != ''";
        $res = $ds->exec( $sql );
    
        while ($obj = $ds->fetchObject($res)) {
          $document = new CCompteRendu;
          $document->type = "operation";
          $document->nom = $document_name;
          $document->object_id = $obj->operation_id;
          $document->source = $obj->$document_name;
          $document->valide = $obj->$document_valide;
          $document->store();
        }
      }
      return true;
    }
    $this->addFunctions("setup_moveDocs");
    
    $this->makeRevision("0.28");
    $sql = "ALTER TABLE `operations` ADD `codes_ccam` VARCHAR( 160 ) AFTER `CIM10_code`";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `operations` ADD INDEX ( `codes_ccam` )";
    $this->addQuery($sql);
    
    $this->makeRevision("0.29");
    function setup_ccam(){
      $ds = CSQLDataSource::get("std");
      $sql = "SELECT `operation_id` , `CCAM_code` , `CCAM_code2`" .
             "\nFROM `operations`";
      $res = $ds->exec( $sql );
      while ($obj = $ds->fetchObject($res)) {
        $obj->codes_ccam = $obj->CCAM_code;
        if ($obj->CCAM_code2) {
          $obj->codes_ccam .= "|$obj->CCAM_code2";
        }
          
        $sql2 = "UPDATE `operations` " .
          "\nSET `codes_ccam` = '$obj->codes_ccam' " .
          "\nWHERE `operation_id` = $obj->operation_id";
        $ds->exec($sql2); $ds->error();
      }
      return true;
    }
    $this->addFunctions("setup_ccam");
    
    $this->makeRevision("0.30");
    $sql = "ALTER TABLE `operations`
          ADD `pose_garrot` TIME AFTER `entree_bloc` ,
          ADD `debut_op` TIME AFTER `pose_garrot` ,
          ADD `fin_op` TIME AFTER `debut_op` ,
          ADD `retrait_garrot` TIME AFTER `fin_op` ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.31");
    $sql = "ALTER TABLE `operations`" .
          "\nADD `salle_id` BIGINT AFTER `plageop_id` ," .
          "\nADD `date` DATE AFTER `salle_id` ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.32");
    $sql = "ALTER TABLE `operations` ADD `venue_SHS` VARCHAR( 8 ) AFTER `chambre`;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `operations` ADD INDEX ( `venue_SHS` );";
    $this->addQuery($sql);
    
    $this->makeRevision("0.33");
    $sql = "ALTER TABLE `operations` ADD `code_uf` VARCHAR( 3 ) AFTER `venue_SHS`;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `operations` ADD `libelle_uf` VARCHAR( 40 ) AFTER `code_uf`;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.34");
    $sql = "ALTER TABLE `operations`" .
            "\nADD `entree_reveil` TIME AFTER `sortie_bloc` ," .
            "\nADD `sortie_reveil` TIME AFTER `entree_reveil` ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.35");
    $sql = "ALTER TABLE `operations` ADD `entree_adm` DATETIME AFTER `admis`;";
    $this->addQuery($sql);
    $sql = "UPDATE `operations` SET" .
            "\n`entree_adm` = ADDTIME(date_adm, time_adm)" .
            "\nWHERE `admis` = 'o'";
    $this->addQuery($sql);
    
    $this->makeRevision("0.36");
    $this->addDependency("dPbloc", "0.15");
    // R�paration des op�rations avec `duree_hospi` = '255'
    $sql = "UPDATE `operations`, `plagesop` SET" .
            "\n`operations`.`date_adm` = `plagesop`.`date`," .
            "\n`operations`.`duree_hospi` = '1'" .
            "\nWHERE `operations`.`duree_hospi` = '255'" .
            "\nAND `operations`.`plageop_id` = `plagesop`.`plageop_id`";
    $this->addQuery($sql);
    // Cr�ation de la table
    $sql = "CREATE TABLE `sejour` (" .
            "\n`sejour_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ," .
            "\n`patient_id` INT UNSIGNED NOT NULL ," .
            "\n`praticien_id` INT UNSIGNED NOT NULL ," .
            "\n`entree_prevue` DATETIME NOT NULL ," .
            "\n`sortie_prevue` DATETIME NOT NULL ," .
            "\n`entree_reelle` DATETIME," .
            "\n`sortie_reelle` DATETIME," .
            "\n`chambre_seule` ENUM('o','n') NOT NULL DEFAULT 'o'," .
            "\nPRIMARY KEY ( `sejour_id` )) TYPE=MyISAM";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `sejour` ADD INDEX ( `patient_id` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `sejour` ADD INDEX ( `praticien_id` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `sejour` ADD INDEX ( `entree_prevue` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `sejour` ADD INDEX ( `sortie_prevue` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `sejour` ADD INDEX ( `entree_reelle` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `sejour` ADD INDEX ( `sortie_reelle` )";
    $this->addQuery($sql);
    // Migration de l'ancienne table
    $sql = "ALTER TABLE `sejour` ADD `tmp_operation_id` INT UNSIGNED NOT NULL AFTER `sejour_id`";
    $this->addQuery($sql);
    $sql = "INSERT INTO `sejour` ( " .
            "\n  `sejour_id` , " .
            "\n  `tmp_operation_id` , " .
            "\n  `patient_id` , " .
            "\n  `praticien_id` , " .
            "\n  `entree_prevue` , " .
            "\n  `sortie_prevue` , " .
            "\n  `entree_reelle` , " .
            "\n  `sortie_reelle` , " .
            "\n  `chambre_seule` ) " .
            "\nSELECT " .
            "\n  '', " .
            "\n  `operation_id`, " .
            "\n  `pat_id`, " .
            "\n  `chir_id`, " .
            "\n  ADDTIME(`date_adm`, `time_adm`), " .
            "\n  ADDDATE(ADDTIME(`date_adm`, `time_adm`), `duree_hospi`), " .
            "\n  `entree_adm` , " .
            "\n  NULL , " .
            "\n `chambre` " .
            "\nFROM `operations`" .
            "\nWHERE `operations`.`pat_id` != 0";
    $this->addQuery($sql);
    // Ajout d'une r�f�rence vers les sejour
    $sql = "ALTER TABLE `operations` ADD `sejour_id` INT UNSIGNED NOT NULL AFTER `operation_id`";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `operations` ADD INDEX ( `sejour_id` )";
    $this->addQuery($sql);
    $sql = "UPDATE `operations`, `sejour` " .
            "\nSET `operations`.`sejour_id` = `sejour`.`sejour_id`" .
            "\nWHERE `sejour`.`tmp_operation_id` = `operations`.`operation_id`";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `sejour` DROP `tmp_operation_id` ";
    $this->addQuery($sql);
    
    $this->makeRevision("0.37");
    // Migration de nouvelles propri�t�s
    $sql = "ALTER TABLE `sejour` " .
            "\nADD `type` ENUM( 'comp', 'ambu', 'exte' ) DEFAULT 'comp' NOT NULL AFTER `praticien_id` ," .
            "\nADD `annule` TINYINT DEFAULT '0' NOT NULL AFTER `type` ," .
            "\nADD `venue_SHS` VARCHAR( 8 ) AFTER `annule` ," .
            "\nADD `saisi_SHS` ENUM( 'o', 'n' ) DEFAULT 'n' NOT NULL AFTER `venue_SHS` ," .
            "\nADD `modif_SHS` TINYINT DEFAULT '0' NOT NULL AFTER `saisi_SHS`";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `sejour` ADD INDEX ( `type` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `sejour` ADD INDEX ( `annule` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `sejour` ADD INDEX ( `venue_SHS` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `sejour` ADD INDEX ( `saisi_SHS` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `sejour` ADD INDEX ( `modif_SHS` )";
    $this->addQuery($sql);
    $sql = "UPDATE `sejour`, `operations` SET" .
            "\n`sejour`.`type` = `operations`.`type_adm`," .
            "\n`sejour`.`annule` = `operations`.`annulee`," .
            "\n`sejour`.`venue_SHS` = `operations`.`venue_SHS`," .
            "\n`sejour`.`saisi_SHS` = `operations`.`saisie`," .
            "\n`sejour`.`modif_SHS` = `operations`.`modifiee`" .
            "\nWHERE `operations`.`sejour_id` = `sejour`.`sejour_id`";
    $this->addQuery($sql);
    
    $this->makeRevision("0.38");
    $sql = "UPDATE `sejour`, `operations` SET" .
            "\n`sejour`.`entree_reelle` = NULL" .
            "\nWHERE `operations`.`sejour_id` = `sejour`.`sejour_id`" .
            "\nAND `operations`.`admis` = 'n'";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `operations` CHANGE `date_anesth` `date_anesth` DATE";
    $this->addQuery($sql);
    $sql = "UPDATE `operations` " .
            "\nSET `date_anesth` = NULL" .
            "\nWHERE `date_anesth` = '0000-00-00'";
    $this->addQuery($sql);
    
    $this->makeRevision("0.39");
    $sql = "ALTER TABLE sejour ADD rques text;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.40");
    $sql = "ALTER TABLE operations" .
            "\nADD pause time NOT NULL default '00:00:00' AFTER temp_operation";
    $this->addQuery($sql);
    
    $this->makeRevision("0.41");
    $sql = "ALTER TABLE `sejour` " .
          "\nADD `pathologie` VARCHAR( 8 ) DEFAULT NULL," .
          "\nADD `septique` TINYINT DEFAULT '0' NOT NULL ;";
    $this->addQuery($sql);
    $sql = "UPDATE `sejour`, `operations` SET" .
            "\n`sejour`.`pathologie` = `operations`.`pathologie`," .
            "\n`sejour`.`septique` = `operations`.`septique`" .
            "\nWHERE `operations`.`sejour_id` = `sejour`.`sejour_id`";
    $this->addQuery($sql);
    
    $this->makeRevision("0.42");
    $sql = "ALTER TABLE `sejour` " .
          "\nADD `code_uf` VARCHAR( 8 ) DEFAULT NULL AFTER venue_SHS," .
          "\nADD `libelle_uf` TINYINT DEFAULT '0' NOT NULL AFTER code_uf;";
    $this->addQuery($sql);
    $sql = "UPDATE `sejour`, `operations` SET" .
            "\n`sejour`.`code_uf` = `operations`.`code_uf`," .
            "\n`sejour`.`libelle_uf` = `operations`.`libelle_uf`" .
            "\nWHERE `operations`.`sejour_id` = `sejour`.`sejour_id`";
    $this->addQuery($sql);
    
    $this->makeRevision("0.43");
    $sql = "ALTER TABLE `sejour` ADD `convalescence` TEXT DEFAULT NULL;";
    $this->addQuery($sql);
    $sql = "UPDATE `sejour`, `operations` SET" .
            "\n`sejour`.`convalescence` = `operations`.`convalescence`" .
            "\nWHERE `operations`.`sejour_id` = `sejour`.`sejour_id`";
    $this->addQuery($sql);
    
    $this->makeRevision("0.44");
    $sql = "ALTER TABLE `sejour` DROP `code_uf`;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `sejour` DROP `libelle_uf`;";
    $this->addQuery($sql);
    $sql = " ALTER TABLE `sejour` " .
            "\nADD `modalite_hospitalisation` ENUM( 'office', 'libre', 'tiers' ) NOT NULL DEFAULT 'libre' AFTER `type`;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.45");
    $sql = "ALTER TABLE `operations` DROP `entree_adm`;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `operations` DROP `admis`;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.46");
    $sql = "ALTER TABLE `sejour` ADD `DP`  varchar(5) default NULL AFTER `rques`;";
    $this->addQuery($sql);
    $sql = "UPDATE `sejour`, `operations` SET" .
            "\n`sejour`.`DP` = `operations`.`CIM10_code`" .
            "\nWHERE `operations`.`sejour_id` = `sejour`.`sejour_id`";
    $this->addQuery($sql);
    
    $this->makeRevision("0.47");
    $sql = "CREATE TABLE protocole ( " .
          "  protocole_id INT UNSIGNED NOT NULL auto_increment" .
          ", chir_id INT UNSIGNED NOT NULL DEFAULT '0'" .
          ", type ENUM('comp','ambu','exte') DEFAULT 'comp'" .
          ", DP VARCHAR(5) DEFAULT NULL" .
          ", convalescence TEXT DEFAULT NULL" .
          ", rques_sejour TEXT DEFAULT NULL" .
          ", pathologie VARCHAR(8) DEFAULT NULL" .
          ", septique TINYINT DEFAULT '0' NOT NULL" .
          ", codes_ccam VARCHAR(160) DEFAULT NULL" .
          ", libelle TEXT DEFAULT NULL" .
          ", temp_operation TIME NOT NULL DEFAULT '00:00:00'" .
          ", examen TEXT DEFAULT NULL" .
          ", materiel TEXT DEFAULT NULL" .
          ", duree_hospi TINYINT(4) UNSIGNED NOT NULL DEFAULT '0'" .
          ", rques_operation TEXT DEFAULT NULL" .
          ", depassement TINYINT DEFAULT NULL" .
          ", PRIMARY KEY  (protocole_id)" .
          ") TYPE=MyISAM;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `protocole` ADD INDEX (`chir_id`)";
    $this->addQuery($sql);
    $sql = "INSERT INTO `protocole` (" .
            " `protocole_id`, `chir_id`," .
            " `type`, `DP`, `convalescence`, `rques_sejour`, `pathologie`, `septique`," .
            " `codes_ccam`, `libelle`, `temp_operation`, `examen`, `materiel`," .
            " `duree_hospi`, `rques_operation`,  `depassement`)" .
            "\nSELECT '', `operations`.`chir_id`," .
            " `operations`.`type_adm`, `operations`.`CIM10_code`, `operations`.`convalescence`," .
            " '', '', '', `operations`.`codes_ccam`, `operations`.`libelle`," .
            " `operations`.`temp_operation`, `operations`.`examen`," .
            " `operations`.`materiel`, `operations`.`duree_hospi`, `operations`.`rques`," .
            " `operations`.`depassement`" .
            "\nFROM `operations`" .
            "\nWHERE `operations`.`pat_id` = 0";
    $this->addQuery($sql);
    $sql = "DELETE FROM `operations` WHERE `pat_id` = 0";
    $this->addQuery($sql);
    
    $this->makeRevision("0.48");
    $sql = "ALTER TABLE `sejour` CHANGE `modalite_hospitalisation` " .
            "\n`modalite` ENUM( 'office', 'libre', 'tiers' ) DEFAULT 'libre' NOT NULL";
    $this->addQuery($sql);
    
    $this->makeRevision("0.49");
    $sql = "UPDATE `operations` SET `date` = NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.50");
    $sql = "ALTER TABLE `operations` ADD `anesth_id` INT UNSIGNED DEFAULT NULL AFTER `chir_id`";
    $this->addQuery($sql);
    
    $this->makeRevision("0.51");
    $this->addDependency("dPetablissement", "0.1");
    $sql = "ALTER TABLE `sejour` ADD `group_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `praticien_id`";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `sejour` ADD INDEX ( `group_id` ) ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.52");
    $sql = "ALTER TABLE `operations` DROP INDEX `operation_id` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `operations` ADD INDEX ( `anesth_id` ) ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `operations`" .
            "\nDROP `pat_id`, DROP `CCAM_code`, DROP `CCAM_code2`," .
            "\nDROP `compte_rendu`, DROP `cr_valide`, DROP `date_adm`," .
            "\nDROP `time_adm`, DROP `chambre`, DROP `type_adm`," .
            "\nDROP `venue_SHS`, DROP `saisie`, DROP `modifiee`," .
            "\nDROP `CIM10_code`, DROP `convalescence`, DROP `pathologie`," .
            "\nDROP `septique` ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.53");
    $sql = "CREATE TABLE `type_anesth` ( " .
          "`type_anesth_id` INT UNSIGNED NOT NULL auto_increment," .
          "`name` VARCHAR(50) DEFAULT NULL," .
          "PRIMARY KEY  (type_anesth_id)" .
          ") TYPE=MyISAM;";
    $this->addQuery($sql);
    $sql = "INSERT INTO `type_anesth` VALUES ('1', 'Non d�finie');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `type_anesth` VALUES ('2', 'Rachi');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `type_anesth` VALUES ('3', 'Rachi + bloc');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `type_anesth` VALUES ('4', 'Anesth�sie loco-r�gionale');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `type_anesth` VALUES ('5', 'Anesth�sie locale');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `type_anesth` VALUES ('6', 'Neurolept');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `type_anesth` VALUES ('7', 'Anesth�sie g�n�rale');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `type_anesth` VALUES ('8', 'Anesthesie generale + bloc');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `type_anesth` VALUES ('9', 'Anesthesie peribulbaire');";
    $this->addQuery($sql);
    $sql = "UPDATE `operations` SET `type_anesth`=`type_anesth`+1;";
    $this->addQuery($sql);
    $sql = "DROP TABLE sysvals;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.54");
    $sql = "ALTER TABLE `operations`" .
            "\nADD `induction` TIME AFTER `sortie_reveil`";
    $this->addQuery($sql);
    
    $this->makeRevision("0.55");
    $sql = "CREATE TABLE `naissance` (" .
            "\n`naissance_id` INT UNSIGNED NOT NULL auto_increment," .
            "\n`operation_id` INT UNSIGNED NOT NULL ," .
            "\n`nom_enfant` VARCHAR( 50 ) ," .
            "\n`prenom_enfant` VARCHAR( 50 ) ," .
            "\n`date_prevue` DATE," .
            "\n`date_reelle` DATETIME," .
            "\n`debut_grossesse` DATE," .
            "\nPRIMARY KEY ( `naissance_id` ) ," .
            "\nINDEX ( `operation_id` ))";
    $this->addQuery($sql);
    
    $this->makeRevision("0.56");
    $sql = "ALTER TABLE `naissance` " .
               "\nCHANGE `naissance_id` `naissance_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `operation_id` `operation_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `nom_enfant` `nom_enfant` varchar(255) NOT NULL," .
               "\nCHANGE `prenom_enfant` `prenom_enfant` varchar(255) NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `operations` " .
               "\nCHANGE `operation_id` `operation_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `sejour_id` `sejour_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `anesth_id` `anesth_id` int(11) unsigned NULL," .
               "\nCHANGE `plageop_id` `plageop_id` int(11) unsigned NULL," .
               "\nCHANGE `code_uf` `code_uf` varchar(3) NULL," .
               "\nCHANGE `libelle_uf` `libelle_uf` varchar(35) NULL," .
               "\nCHANGE `salle_id` `salle_id` int(11) unsigned NULL," .
               "\nCHANGE `codes_ccam` `codes_ccam` varchar(255) NULL," .
               "\nCHANGE `libelle` `libelle` varchar(255) NULL," .
               "\nCHANGE `type_anesth` `type_anesth` int(11) unsigned NULL," .
               "\nCHANGE `rank` `rank` tinyint NOT NULL DEFAULT '0'," .
               "\nCHANGE `annulee` `annulee` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `depassement` `depassement` float NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `operations` DROP `duree_hospi`;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `protocole` " .
               "\nCHANGE `protocole_id` `protocole_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `pathologie` `pathologie` varchar(3) NULL," .
               "\nCHANGE `codes_ccam` `codes_ccam` varchar(255) NULL," .
               "\nCHANGE `libelle` `libelle` varchar(255) NULL," .
               "\nCHANGE `duree_hospi` `duree_hospi` mediumint NOT NULL DEFAULT '0'," .
               "\nCHANGE `septique` `septique` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `depassement` `depassement` float NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `sejour` " .
               "\nCHANGE `sejour_id` `sejour_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `patient_id` `patient_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `praticien_id` `praticien_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `group_id` `group_id` int(11) unsigned NOT NULL DEFAULT '1'," .
               "\nCHANGE `venue_SHS` `venue_SHS` int(8) unsigned zerofill NULL," .
               "\nCHANGE `annule` `annule` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `modif_SHS` `modif_SHS` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `septique` `septique` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `pathologie` `pathologie` varchar(3) NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `type_anesth` " .
               "\nCHANGE `type_anesth_id` `type_anesth_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `name` `name` varchar(255) NOT NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `sejour` " .
               "\nCHANGE `saisi_SHS` `saisi_SHS` enum('o','n','0','1') NOT NULL DEFAULT 'n'," .
               "\nCHANGE `chambre_seule` `chambre_seule` enum('o','n','0','1') NOT NULL DEFAULT 'o';";
    $this->addQuery($sql);
    $sql = "UPDATE `sejour` SET `saisi_SHS`='0' WHERE `saisi_SHS`='n';"; $this->addQuery($sql);
    $sql = "UPDATE `sejour` SET `saisi_SHS`='1' WHERE `saisi_SHS`='o';"; $this->addQuery($sql);
    $sql = "UPDATE `sejour` SET `chambre_seule`='0' WHERE `chambre_seule`='n';"; $this->addQuery($sql);
    $sql = "UPDATE `sejour` SET `chambre_seule`='1' WHERE `chambre_seule`='o';"; $this->addQuery($sql);
    $sql = "ALTER TABLE `sejour` " .
               "\nCHANGE `saisi_SHS` `saisi_SHS` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `chambre_seule` `chambre_seule` enum('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `operations` " .
               "\nCHANGE `ATNC` `ATNC` enum('o','n','0','1') NOT NULL DEFAULT 'n'," .
               "\nCHANGE `commande_mat` `commande_mat` enum('o','n','0','1') NOT NULL DEFAULT 'n'," .
               "\nCHANGE `info` `info` enum('o','n','0','1') NOT NULL DEFAULT 'n';";
    $this->addQuery($sql);
    $sql = "UPDATE `operations` SET `ATNC`='0' WHERE `ATNC`='n';"; $this->addQuery($sql);
    $sql = "UPDATE `operations` SET `ATNC`='1' WHERE `ATNC`='o';"; $this->addQuery($sql);
    $sql = "UPDATE `operations` SET `info`='0' WHERE `info`='n';"; $this->addQuery($sql);
    $sql = "UPDATE `operations` SET `info`='1' WHERE `info`='o';"; $this->addQuery($sql);
    $sql = "UPDATE `operations` SET `commande_mat`='0' WHERE `commande_mat`='n';"; $this->addQuery($sql);
    $sql = "UPDATE `operations` SET `commande_mat`='1' WHERE `commande_mat`='o';"; $this->addQuery($sql);
    $sql = "ALTER TABLE `operations` " .
               "\nCHANGE `ATNC` `ATNC` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `commande_mat` `commande_mat` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `info` `info` enum('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.57");
    $sql = "ALTER TABLE `operations` " .
               "\nDROP `date_anesth`," .
               "\nDROP `time_anesth`;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `operations` " .
               "\nCHANGE `entree_bloc` `entree_salle` time NULL," .
               "\nCHANGE `sortie_bloc` `sortie_salle` time NULL," .
               "\nADD `entree_bloc` time NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.58");
    $sql = "ALTER TABLE `sejour`" .
               "\nADD `ATNC` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nADD `hormone_croissance` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nADD `lit_accompagnant` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nADD `isolement` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nADD `television` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nADD `repas_diabete` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nADD `repas_sans_sel` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nADD `repas_sans_residu` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `type` `type` enum('comp','ambu','exte','seances','ssr','psy') NOT NULL DEFAULT 'comp';";
    $this->addQuery($sql);
    $sql = "UPDATE sejour SET ATNC = '1' WHERE sejour_id IN (SELECT sejour_id FROM `operations` WHERE ATNC = '1');";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `operations` DROP `ATNC`;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `protocole` CHANGE `type` `type` enum('comp','ambu','exte','seances','ssr','psy') NOT NULL DEFAULT 'comp';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.59");
    $sql = "UPDATE `operations` SET annulee = 0 WHERE annulee = ''";
    $this->addQuery($sql);
    $sql = "UPDATE `sejour` SET annule = 0 WHERE annule = ''";
    $this->addQuery($sql);
    
    $this->makeRevision("0.60");
    $sql = "ALTER TABLE `operations` ADD INDEX ( `salle_id` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `operations` ADD INDEX ( `date` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `operations` ADD INDEX ( `time_operation` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `operations` ADD INDEX ( `annulee` )";
    $this->addQuery($sql);
    
    $this->makeRevision("0.61");
    $sql = "ALTER TABLE `operations`" .
            "\nCHANGE `induction` `induction_debut` TIME";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `operations`" .
            "\nADD `induction_fin` TIME AFTER `induction_debut`";
    $this->addQuery($sql);
    
    $this->makeRevision("0.62");
    $sql = "ALTER TABLE `operations`" .
            "\nADD `anapath` enum('0','1') NOT NULL DEFAULT '0'," .
            "\nADD `labo` enum('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.63");
    $sql = "UPDATE `operations` SET `anesth_id` = NULL WHERE `anesth_id` = '0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.64");
    $sql = "ALTER TABLE `operations`" .
            "\nADD `forfait` FLOAT NULL AFTER `depassement`," .
            "\nADD `fournitures` FLOAT NULL AFTER `forfait`;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `protocole`" .
            "\nADD `forfait` FLOAT NULL AFTER `depassement`," .
            "\nADD `fournitures` FLOAT NULL AFTER `forfait`;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.65");
    $sql = "ALTER TABLE `sejour` ADD `codes_ccam` VARCHAR(255);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.66");
    $sql = "INSERT INTO `user_preferences` ( `pref_user` , `pref_name` , `pref_value` )" .
        "\nVALUES ('0', 'mode', '1');";
    $this->addQuery($sql);
    
    $this->makeRevision("0.67");
    $sql = "UPDATE `user_preferences` SET `pref_name` = 'mode_dhe' WHERE `pref_name` = 'mode';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.68");
    $sql = "ALTER TABLE `sejour` ADD `mode_sortie` ENUM( 'normal', 'transfert', 'deces' );";
    $this->addQuery($sql);
    
    
    $this->mod_version = "0.69";
  }
}
?>