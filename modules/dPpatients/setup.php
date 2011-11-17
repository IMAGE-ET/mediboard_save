<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author Romain Ollivier
 */

class CSetupdPpatients extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPpatients";

    $this->makeRevision("all");
    $query = "CREATE TABLE `patients` (
              `patient_id` INT(11) NOT NULL AUTO_INCREMENT,
              `nom` VARCHAR(50) NOT NULL DEFAULT '',
              `prenom` VARCHAR(50) NOT NULL DEFAULT '',
              `naissance` DATE NOT NULL DEFAULT '0000-00-00',
              `sexe` ENUM('m','f') NOT NULL DEFAULT 'm',
              `adresse` VARCHAR(50) NOT NULL DEFAULT '',
              `ville` VARCHAR(50) NOT NULL DEFAULT '',
              `cp` VARCHAR(5) NOT NULL DEFAULT '',
              `tel` VARCHAR(10) NOT NULL DEFAULT '',
              `medecin_traitant` INT(11) NOT NULL DEFAULT '0',
              `incapable_majeur` ENUM('o','n') NOT NULL DEFAULT 'n',
              `ATNC` ENUM('o','n') NOT NULL DEFAULT 'n',
              `matricule` VARCHAR(15) NOT NULL DEFAULT '',
              `SHS` VARCHAR(10) NOT NULL DEFAULT '',
              PRIMARY KEY  (`patient_id`),
              UNIQUE KEY `patient_id` (`patient_id`),
              KEY `matricule` (`matricule`,`SHS`),
              KEY `nom` (`nom`,`prenom`)
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.1");
    $query = "ALTER TABLE patients" .
            "\nADD tel2 VARCHAR( 10 ) AFTER tel ," .
            "\nADD medecin1 INT( 11 ) AFTER medecin_traitant ," .
            "\nADD medecin2 INT( 11 ) AFTER medecin1 ," .
            "\nADD medecin3 INT( 11 ) AFTER medecin2 ," .
            "\nADD rques TEXT;";
    $this->addQuery($query);
    $query = "CREATE TABLE medecin (" .
            "\nmedecin_id INT(11) NOT NULL AUTO_INCREMENT," .
            "\nnom VARCHAR(50) NOT NULL DEFAULT ''," .
            "\nprenom VARCHAR(50) NOT NULL DEFAULT ''," .
            "\ntel VARCHAR(10) DEFAULT NULL," .
            "\nfax VARCHAR(10) DEFAULT NULL," .
            "\nemail VARCHAR(50) DEFAULT NULL," .
            "\nadresse VARCHAR(50) DEFAULT NULL," .
            "\nville VARCHAR(50) DEFAULT NULL," .
            "\ncp VARCHAR(5) DEFAULT NULL," .
            "\nPRIMARY KEY  (`medecin_id`))" .
            "\n/*! ENGINE=MyISAM */ COMMENT='Table des medecins correspondants';";
    $this->addQuery($query);
    
    $this->makeRevision("0.2");
    $query = "ALTER TABLE medecin ADD specialite TEXT AFTER prenom ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.21");
    $query = "ALTER TABLE medecin ADD disciplines TEXT AFTER prenom ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.22");
    $query = "ALTER TABLE `medecin` CHANGE `adresse` `adresse` TEXT DEFAULT NULL ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.23");
    $query = "ALTER TABLE `medecin` ADD INDEX ( `nom` ) ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `medecin` ADD INDEX ( `prenom` ) ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `medecin` ADD INDEX ( `cp` ) ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.24");
    $query = "ALTER TABLE `patients` ADD `nom_jeune_fille` VARCHAR( 50 ) NOT NULL AFTER `nom` ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `patients`" .
            "\nCHANGE `sexe` `sexe` ENUM( 'm', 'f', 'j' )" .
            "\nDEFAULT 'm' NOT NULL ";
    $this->addQuery($query);
    
    $this->makeRevision("0.25");
    $query = "ALTER TABLE `patients` CHANGE `adresse` `adresse` TEXT NOT NULL ";
    $this->addQuery($query);
    
    $this->makeRevision("0.26");
    $query = "CREATE TABLE `antecedent` (
                `antecedent_id` BIGINT NOT NULL AUTO_INCREMENT ,
                `patient_id` BIGINT NOT NULL ,
                `type` ENUM( 'trans', 'obst', 'chir', 'med' ) DEFAULT 'med' NOT NULL ,
                `date` DATE,
                `rques` TEXT,
                PRIMARY KEY ( `antecedent_id` ) ,
                INDEX ( `patient_id` )
                ) /*! ENGINE=MyISAM */ COMMENT = 'antecedents des patients';";
    $this->addQuery($query);
    
    $this->makeRevision("0.27");
    $query = "ALTER TABLE `antecedent`" .
            "CHANGE `type` `type`" .
            "ENUM( 'trans', 'obst', 'chir', 'med', 'fam' )" .
            "DEFAULT 'med' NOT NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `patients` ADD `listCim10` TEXT DEFAULT NULL ;";
    $this->addQuery($query);
    $query = "CREATE TABLE `traitement` (
                `traitement_id` BIGINT NOT NULL AUTO_INCREMENT ,
                `patient_id` BIGINT NOT NULL ,
                `debut` DATE DEFAULT '0000-00-00' NOT NULL ,
                `fin` DATE,
                `traitement` TEXT,
                PRIMARY KEY ( `traitement_id` ) ,
                INDEX ( `patient_id` )
                ) /*! ENGINE=MyISAM */ COMMENT = 'traitements des patients';";
    $this->addQuery($query);
    
    $this->makeRevision("0.28");
    $query = "ALTER TABLE `patients` CHANGE `SHS` `regime_sante` VARCHAR( 40 );";
    $this->addQuery($query);
    $query = "ALTER TABLE `patients` ADD `SHS` VARCHAR( 8 ) AFTER `matricule`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `patients` ADD INDEX ( `SHS` );";
    $this->addQuery($query);
    
    $this->makeRevision("0.29");
    $query = "ALTER TABLE `patients` DROP INDEX `patient_id` ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `patients` DROP INDEX `nom` ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `patients` ADD INDEX ( `nom` ) ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `patients` ADD INDEX ( `prenom` ) ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.30");
    $query = "ALTER TABLE `antecedent` CHANGE `type` `type` ENUM( 'trans', 'obst', 'chir', 'med', 'fam', 'alle' ) NOT NULL DEFAULT 'med';";
    $this->addQuery($query);
    
    $this->makeRevision("0.31");
    $query = "ALTER TABLE `patients` ADD `cmu` date NULL AFTER `matricule` ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `patients` ADD `ald` text AFTER `rques` ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.32");
    $query = "UPDATE `medecin` SET `tel` = NULL WHERE `tel`='NULL' ;";
    $this->addQuery($query);
    $query = "UPDATE `medecin` SET `fax` = NULL WHERE `fax`='NULL' ;";
    $this->addQuery($query);
    $query = "UPDATE `medecin` SET `email` = NULL WHERE `email`='NULL' ;";
    $this->addQuery($query);
    $query = "UPDATE `medecin` SET `specialite` = NULL WHERE `specialite`='NULL' ;";
    $this->addQuery($query);
    $query = "UPDATE `medecin` SET `disciplines` = NULL WHERE `disciplines`='NULL' ;";
    $this->addQuery($query);
    $query = "UPDATE `medecin` SET `adresse` = NULL WHERE `adresse`='NULL' ;";
    $this->addQuery($query);
    $query = "UPDATE `medecin` SET `ville` = NULL WHERE `ville`='NULL' ;";
    $this->addQuery($query);
    $query = "UPDATE `medecin` SET `cp` = NULL WHERE `cp` LIKE 'NULL%' ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.33");
    $query = "ALTER TABLE `medecin` ADD `jeunefille` VARCHAR( 50 ) AFTER `prenom` ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `medecin` ADD `complementaires` TEXT AFTER `disciplines` ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `medecin` ADD `orientations` TEXT AFTER `disciplines` ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `medecin` DROP `specialite` ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.34");
    $query = "ALTER TABLE `patients` " .
           "\nADD `pays` VARCHAR( 50 )," .
           "\nADD `nationalite` ENUM( 'local', 'etranger' ) NOT NULL DEFAULT 'local'," .
           "\nADD `lieu_naissance` VARCHAR( 50 )," .
           "\nADD `profession` VARCHAR( 50 )," .
           "\nADD `employeur_nom` VARCHAR( 50 )," .
           "\nADD `employeur_adresse` TEXT," .
           "\nADD `employeur_cp` VARCHAR( 5 )," .
           "\nADD `employeur_ville` VARCHAR( 50 )," .
           "\nADD `employeur_tel` VARCHAR( 10 )," .
           "\nADD `employeur_urssaf` VARCHAR( 11 )," .
           "\nADD `prevenir_nom` VARCHAR( 50 )," .
           "\nADD `prevenir_prenom` VARCHAR( 50 )," .
           "\nADD `prevenir_adresse` TEXT," .
           "\nADD `prevenir_cp` VARCHAR( 5 )," .
           "\nADD `prevenir_ville` VARCHAR( 50 )," .
           "\nADD `prevenir_tel` VARCHAR( 10 )," .
           "\nADD `prevenir_parente` ENUM( 'conjoint', 'enfant', 'ascendant', 'colateral', 'divers' ) ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.35");
    $query = "ALTER TABLE `antecedent` CHANGE `type` `type` ENUM( 'med', 'alle', 'trans', 'obst', 'chir', 'fam', 'anesth' ) NOT NULL DEFAULT 'med';";
    $this->addQuery($query);
    
    $this->makeRevision("0.36");
    $this->setTimeLimit(1800);
    $query = "ALTER TABLE `antecedent` " .
               "\nCHANGE `antecedent_id` `antecedent_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `patient_id` `patient_id` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `medecin` " .
               "\nCHANGE `medecin_id` `medecin_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL," .
               "\nCHANGE `prenom` `prenom` varchar(255) NOT NULL," .
               "\nCHANGE `jeunefille` `jeunefille` varchar(255) NULL," .
               "\nCHANGE `ville` `ville` varchar(255) NULL," .
               "\nCHANGE `cp` `cp` int(5) unsigned zerofill NULL," .
               "\nCHANGE `tel` `tel` bigint(10) unsigned zerofill NULL," .
               "\nCHANGE `fax` `fax` bigint(10) unsigned zerofill NULL," .
               "\nCHANGE `email` `email` varchar(255) NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `patients` " .
               "\nCHANGE `patient_id` `patient_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL," .
               "\nCHANGE `nom_jeune_fille` `nom_jeune_fille` varchar(255) NULL," .
               "\nCHANGE `prenom` `prenom` varchar(255) NOT NULL," .
               "\nCHANGE `ville` `ville` varchar(255) NOT NULL," .
               "\nCHANGE `medecin_traitant` `medecin_traitant` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `medecin1` `medecin1` int(11) unsigned NULL," .
               "\nCHANGE `medecin2` `medecin2` int(11) unsigned NULL," .
               "\nCHANGE `medecin3` `medecin3` int(11) unsigned NULL," .
               "\nCHANGE `regime_sante` `regime_sante` varchar(255) NULL," .
               "\nCHANGE `pays` `pays` varchar(255) NULL," .
               "\nCHANGE `cp` `cp` int(5) unsigned zerofill NULL," .
               "\nCHANGE `tel` `tel` bigint(10) unsigned zerofill NULL," .
               "\nCHANGE `tel2` `tel2` bigint(10) unsigned zerofill NULL," .
               "\nCHANGE `SHS` `SHS` int(8) unsigned zerofill NULL," .
               "\nCHANGE `employeur_cp` `employeur_cp` int(5) unsigned zerofill NULL," .
               "\nCHANGE `employeur_tel` `employeur_tel` bigint(10) unsigned zerofill NULL," .
               "\nCHANGE `employeur_urssaf` `employeur_urssaf` bigint(11) unsigned zerofill NULL," .
               "\nCHANGE `prevenir_cp` `prevenir_cp` int(5) unsigned zerofill NULL," .
               "\nCHANGE `prevenir_tel` `prevenir_tel` bigint(10) unsigned zerofill NULL," .
               "\nCHANGE `lieu_naissance` `lieu_naissance` varchar(255) NULL," .
               "\nCHANGE `profession` `profession` varchar(255) NULL," .
               "\nCHANGE `employeur_nom` `employeur_nom` varchar(255) NULL," .
               "\nCHANGE `employeur_ville` `employeur_ville` varchar(255) NULL," .
               "\nCHANGE `prevenir_nom` `prevenir_nom` varchar(255) NULL," .
               "\nCHANGE `prevenir_prenom` `prevenir_prenom` varchar(255) NULL," .
               "\nCHANGE `prevenir_ville` `prevenir_ville` varchar(255) NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `traitement` " .
               "\nCHANGE `traitement_id` `traitement_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `patient_id` `patient_id` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `patients` " .
               "\nCHANGE `ATNC` `ATNC` enum('o','n','0','1') NOT NULL DEFAULT 'n'," .
               "\nCHANGE `incapable_majeur` `incapable_majeur` enum('o','n','0','1') NOT NULL DEFAULT 'n';";
    $this->addQuery($query);
    $query = "UPDATE `patients` SET `ATNC`='0' WHERE `ATNC`='n';";                             $this->addQuery($query);
    $query = "UPDATE `patients` SET `ATNC`='1' WHERE `ATNC`='o';";                         $this->addQuery($query);
    $query = "UPDATE `patients` SET `incapable_majeur`='0' WHERE `incapable_majeur`='n';"; $this->addQuery($query);
    $query = "UPDATE `patients` SET `incapable_majeur`='1' WHERE `incapable_majeur`='o';"; $this->addQuery($query);
    $query = "ALTER TABLE `patients` " .
               "\nCHANGE `ATNC` `ATNC` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `incapable_majeur` `incapable_majeur` enum('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.37");
    $this->setTimeLimit(1800);
    $query = "ALTER TABLE `patients` " .
               "\nADD `nom_soundex2`    VARCHAR(255) DEFAULT NULL AFTER `nom_jeune_fille`," .
               "\nADD `prenom_soundex2` VARCHAR(255) DEFAULT NULL AFTER `nom_soundex2`," .
               "\nADD `nomjf_soundex2`  VARCHAR(255) DEFAULT NULL AFTER `prenom_soundex2`;";
    $this->addQuery($query);
    function setup_soundex(){
      $where = array("nom_soundex2" => "IS NULL", "nom" => "!= ''");
      $limit = "0,1000";
      $pat = new CPatient;
      $listPat = $pat->loadList($where, null, $limit);
      while (count($listPat)) {
        foreach ($listPat as &$pat) {
          if ($msg = $pat->store()) {
            trigger_error("Erreur store [$pat->_id] : $msg");
            return false;
          }
        }
        $listPat = $pat->loadList($where, null, $limit);
      }
      return true;
    }
    $this->addFunction("setup_soundex");
    
    $this->makeRevision("0.38");
    $query = "ALTER TABLE `patients` ADD `rang_beneficiaire` enum('1','2','11','12','13') NULL AFTER `ald`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.39");
    $query = "ALTER TABLE `traitement` CHANGE `debut` `debut` date NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.40");
    $query = "ALTER TABLE `antecedent` " .
           "\nCHANGE `patient_id` `object_id` int(11) unsigned NOT NULL DEFAULT '0'," .
           "\nADD `object_class` enum('CPatient','CConsultAnesth') NOT NULL DEFAULT 'CPatient';";
    $this->addQuery($query);
    $query = "ALTER TABLE `traitement` " .
           "\nCHANGE `patient_id` `object_id` int(11) unsigned NOT NULL DEFAULT '0'," .
           "\nADD `object_class` enum('CPatient','CConsultAnesth') NOT NULL DEFAULT 'CPatient';";
    $this->addQuery($query);
    
    $this->makeRevision("0.41");
    $query = "ALTER TABLE `patients` CHANGE `medecin_traitant` `medecin_traitant` int(11) unsigned NULL DEFAULT NULL;";
    $this->addQuery($query);
    $query = "UPDATE `patients` SET `medecin_traitant` = NULL WHERE `medecin_traitant`='0';";
    $this->addQuery($query);
    $query = "UPDATE `patients` SET `medecin1` = NULL WHERE `medecin1`='0';";
    $this->addQuery($query);
    $query = "UPDATE `patients` SET `medecin2` = NULL WHERE `medecin2`='0';";
    $this->addQuery($query);
    $query = "UPDATE `patients` SET `medecin3` = NULL WHERE `medecin3`='0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.42");
    $this->addDependency("dPcabinet", "0.60");
    $query = "ALTER TABLE `addiction` CHANGE `object_class` `object_class` enum('CPatient','CConsultAnesth') NOT NULL DEFAULT 'CPatient';";
    $this->addQuery($query);
    
    $this->makeRevision("0.43");
    $query = "ALTER TABLE `antecedent` CHANGE `type` `type` enum('med','alle','trans','obst','chir','fam','anesth','gyn') NOT NULL DEFAULT 'med';";
    $this->addQuery($query);
    
    $this->makeRevision("0.44");
    $query = "ALTER TABLE `patients`
            ADD `assure_nom` VARCHAR(255), 
            ADD `assure_nom_jeune_fille` VARCHAR(255), 
            ADD `assure_prenom` VARCHAR(255), 
            ADD `assure_naissance` DATE, 
            ADD `assure_sexe` ENUM('m','f','j'), 
            ADD `assure_adresse` TEXT, 
            ADD `assure_ville` VARCHAR(255), 
            ADD `assure_cp` INT(5) UNSIGNED ZEROFILL, 
            ADD `assure_tel` BIGINT(10) UNSIGNED ZEROFILL, 
            ADD `assure_tel2` BIGINT(10) UNSIGNED ZEROFILL, 
            ADD `assure_pays` VARCHAR(255), 
            ADD `assure_nationalite` ENUM('local','etranger') NOT NULL, 
            ADD `assure_lieu_naissance` VARCHAR(255), 
            ADD `assure_profession` VARCHAR(255),
            ADD `assure_rques` TEXT;";
    $this->addQuery($query);
    
    $this->makeRevision("0.45");
    $query = "ALTER TABLE `patients`
            CHANGE `rang_beneficiaire` `rang_beneficiaire` ENUM('01','02','11','12','13');";
    $this->addQuery($query);
    
    $this->makeRevision("0.46");
    $query = "ALTER TABLE `patients`
            ADD `assure_matricule` VARCHAR(15);";
    $this->addQuery($query);

    $this->makeRevision("0.47");
    $query = "ALTER TABLE `patients`
            ADD `rang_naissance` ENUM('1','2','3','4','5','6');";
    $this->addQuery($query);

    $this->makeRevision("0.48");
    $query = "ALTER TABLE `patients`
            ADD `code_regime` TINYINT(2) UNSIGNED ZEROFILL, 
            ADD `caisse_gest` MEDIUMINT(3) UNSIGNED ZEROFILL, 
            ADD `centre_gest` MEDIUMINT(4) UNSIGNED ZEROFILL, 
            ADD `fin_validite_vitale` DATE;";
    $this->addQuery($query);

    $this->makeRevision("0.49");
    $query = "ALTER TABLE `patients`
            CHANGE `rang_beneficiaire` `rang_beneficiaire` ENUM('01','02','09','11','12','13','14','15','16','31');";
    $this->addQuery($query);
    
    
    // Creation de la table dossier medical
    $this->makeRevision("0.50");
    
    set_time_limit(60);
    
    $this->addDependency("dPcabinet", "0.78");
    
    $query = "CREATE TABLE `dossier_medical` (
            `dossier_medical_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
            `listCim10` TEXT, 
            `object_id` INT(11) UNSIGNED NOT NULL, 
            `object_class` VARCHAR(25) NOT NULL, 
            PRIMARY KEY (`dossier_medical_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    
    
    // Insertion des patients dans la table dossier_medical
    $query = "INSERT INTO `dossier_medical`
            SELECT '', patients.listCim10, patients.patient_id, 'CPatient' 
            FROM `patients`;";
    $this->addQuery($query);
    
    
    // Insertion des sejours dans la table dossier_medical
    $query = "INSERT INTO `dossier_medical`
            SELECT '', GROUP_CONCAT(consultation_anesth.listCim10 SEPARATOR '|'), sejour.sejour_id, 'CSejour'
            FROM `consultation_anesth`, `operations`,`sejour`   
            WHERE consultation_anesth.operation_id = operations.operation_id
            AND operations.sejour_id = sejour.sejour_id
            GROUP BY sejour.sejour_id;";
    $this->addQuery($query);

    
    // Suppression des '|' en debut de liste
    $query = "UPDATE `dossier_medical` SET `listCim10` = TRIM(LEADING '|' FROM listCim10)
            WHERE listCim10 LIKE '|%'";
    $this->addquery($query);

   
    // Ajout du champ dossier_medical_id aux tables addiction/antecedent/traitement
    $query = "ALTER TABLE `addiction`
            ADD `dossier_medical_id` INT(11) UNSIGNED;";
    $this->addQuery($query);
  
    $query = "ALTER TABLE `addiction` ADD INDEX ( `dossier_medical_id` ) ;";
    $this->addQuery($query);
    
    
    $query = "ALTER TABLE `antecedent`
            ADD `dossier_medical_id` INT(11) UNSIGNED;";
    $this->addQuery($query);

    $query = "ALTER TABLE `antecedent` ADD INDEX ( `dossier_medical_id` ) ;";
    $this->addQuery($query);
    
    
    $query = "ALTER TABLE `traitement`
            ADD `dossier_medical_id` INT(11) UNSIGNED;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `traitement` ADD INDEX ( `dossier_medical_id` ) ;";
    $this->addQuery($query);

    
    // Mise a jour du champ dossier_medical_id dans le cas du Patient
    // Table addiction
    $query = "ALTER TABLE `addiction` ADD INDEX ( `object_id` ) ;";
    $this->addQuery($query);
    
    $query = "UPDATE `addiction`, `dossier_medical` SET addiction.dossier_medical_id = dossier_medical.dossier_medical_id
            WHERE dossier_medical.object_class = 'CPatient'
            AND dossier_medical.object_id = addiction.object_id
            AND addiction.object_class = 'CPatient'";
    $this->addQuery($query);
  
    // Table antecedent
    $query = "UPDATE `antecedent`, `dossier_medical` SET antecedent.dossier_medical_id = dossier_medical.dossier_medical_id
            WHERE dossier_medical.object_class = 'CPatient'
            AND dossier_medical.object_id = antecedent.object_id
            AND antecedent.object_class = 'CPatient'";
    $this->addQuery($query);
    
    
    // Table Traitement
    $query = "UPDATE `traitement`, `dossier_medical` SET traitement.dossier_medical_id = dossier_medical.dossier_medical_id
            WHERE dossier_medical.object_class = 'CPatient'
            AND dossier_medical.object_id = traitement.object_id
            AND traitement.object_class = 'CPatient'";
    $this->addQuery($query);
    
    
    
    // Mise a jour du champs dossier_medical_id dans le cas du Sejour
    // Table addiction
    $query = "UPDATE `addiction`, `dossier_medical`, `consultation_anesth`, `sejour`, `operations` 
            SET addiction.dossier_medical_id = dossier_medical.dossier_medical_id
            WHERE addiction.object_id = consultation_anesth.consultation_anesth_id
            AND addiction.object_class = 'CConsultAnesth'
            AND consultation_anesth.operation_id = operations.operation_id
            AND operations.sejour_id = sejour.sejour_id
            AND dossier_medical.object_class = 'CSejour' 
            AND dossier_medical.object_id = sejour.sejour_id;";
    $this->addQuery($query);    

    
    // Table antecedent
    $query = "UPDATE `antecedent`, `dossier_medical`, `consultation_anesth`, `sejour`, `operations` 
            SET antecedent.dossier_medical_id = dossier_medical.dossier_medical_id
            WHERE antecedent.object_id = consultation_anesth.consultation_anesth_id
            AND antecedent.object_class = 'CConsultAnesth'
            AND consultation_anesth.operation_id = operations.operation_id
            AND operations.sejour_id = sejour.sejour_id
            AND dossier_medical.object_class = 'CSejour' 
            AND dossier_medical.object_id = sejour.sejour_id;";
    $this->addQuery($query);    
    
    
    // Table traitement
    $query = "UPDATE `traitement`, `dossier_medical`, `consultation_anesth`, `sejour`, `operations` 
            SET traitement.dossier_medical_id = dossier_medical.dossier_medical_id
            WHERE traitement.object_id = consultation_anesth.consultation_anesth_id
            AND traitement.object_class = 'CConsultAnesth'
            AND consultation_anesth.operation_id = operations.operation_id
            AND operations.sejour_id = sejour.sejour_id
            AND dossier_medical.object_class = 'CSejour' 
            AND dossier_medical.object_id = sejour.sejour_id;";
    $this->addQuery($query);    
    
    
    // Mise a jour du champ examen de la consultation dans le cas d'antecendent sans operation_id
    $query = "CREATE TEMPORARY TABLE ligneAntecedent (
             consultation_id INT( 11 ) ,
             ligne_antecedent TEXT
            ) AS 
              SELECT consultation_anesth.consultation_id, CONCAT_WS(' - ', antecedent.type, antecedent.date, antecedent.rques ) AS ligne_antecedent
              FROM `antecedent`, `consultation_anesth`
              WHERE antecedent.object_id = consultation_anesth.consultation_anesth_id
              AND antecedent.dossier_medical_id IS NULL;";
    $this->addQuery($query);    
    
    $query = "CREATE TEMPORARY TABLE blocAntecedent (
             consultation_id INT( 11 ) ,
             bloc_antecedent TEXT
            ) AS
              SELECT consultation_id, GROUP_CONCAT(ligne_antecedent SEPARATOR '\n') AS bloc_antecedent
              FROM `ligneAntecedent`
              GROUP BY consultation_id;";
    $this->addQuery($query);
    
    $query = "UPDATE `consultation`, `blocAntecedent`
            SET consultation.examen = CONCAT_WS('\n', consultation.examen, blocAntecedent.bloc_antecedent)
            WHERE consultation.consultation_id = blocAntecedent.consultation_id;";
    $this->addQuery($query);    
    

    // Mise a jour du champ examen de la consultation dans le cas d'une addiction sans operation_id
    $query = "CREATE TEMPORARY TABLE ligneAddiction (
             consultation_id INT( 11 ) ,
             ligne_addiction TEXT
            ) AS 
              SELECT consultation_anesth.consultation_id, CONCAT_WS(' - ', addiction.type, addiction.addiction ) AS ligne_addiction
              FROM `addiction`, `consultation_anesth`
              WHERE addiction.object_id = consultation_anesth.consultation_anesth_id
              AND addiction.dossier_medical_id IS NULL;";
    $this->addQuery($query);
            
    $query = "CREATE TEMPORARY TABLE blocAddiction (
             consultation_id INT( 11 ) ,
             bloc_addiction TEXT
            ) AS
              SELECT consultation_id, GROUP_CONCAT(ligne_addiction SEPARATOR '\n') AS bloc_addiction
              FROM `ligneAddiction`
              GROUP BY consultation_id;";
    $this->addQuery($query);
    
    $query = "UPDATE `consultation`, `blocAddiction`
            SET consultation.examen = CONCAT_WS('\n', consultation.examen, blocAddiction.bloc_addiction)
            WHERE consultation.consultation_id = blocAddiction.consultation_id;";
    $this->addQuery($query);    

  
    // Mise a jour du champ examen de la consultation dans le cas d'un traitement sans operation_id
    $query = "CREATE TEMPORARY TABLE ligneTraitement (
             consultation_id INT( 11 ) ,
             ligne_traitement TEXT
            ) AS 
              SELECT consultation_anesth.consultation_id, CONCAT_WS(' - ', traitement.debut, traitement.fin, traitement.traitement ) AS ligne_traitement
              FROM `traitement`, `consultation_anesth`
              WHERE traitement.object_id = consultation_anesth.consultation_anesth_id
              AND traitement.dossier_medical_id IS NULL;";
    $this->addQuery($query);
    
    
    $query = "CREATE TEMPORARY TABLE blocTraitement (
             consultation_id INT( 11 ) ,
             bloc_traitement TEXT
            ) AS
              SELECT consultation_id, GROUP_CONCAT(ligne_traitement SEPARATOR '\n') AS bloc_traitement
              FROM `ligneTraitement`
              GROUP BY consultation_id;";
    $this->addQuery($query);
    
    $query = "UPDATE `consultation`, `blocTraitement`
            SET consultation.examen = CONCAT_WS('\n', consultation.examen, blocTraitement.bloc_traitement)
            WHERE consultation.consultation_id = blocTraitement.consultation_id;";
    $this->addQuery($query); 

    $query = "ALTER TABLE `addiction`
            DROP `object_id`, 
            DROP `object_class`;";
    $this->addQuery($query);
        
    $query = "ALTER TABLE `antecedent`
            DROP `object_id`, 
            DROP `object_class`;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `traitement`
            DROP `object_id`, 
            DROP `object_class`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.51");
    $query = "ALTER TABLE `patients`
            DROP `listCim10`;";
    $this->addQuery($query);
    
    
    $this->makeRevision("0.52");
    $query = "ALTER TABLE `patients` 
           CHANGE `naissance` `naissance` CHAR( 10 ) NULL DEFAULT NULL ";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `patients` 
           CHANGE `assure_naissance` `assure_naissance` CHAR( 10 ) NULL DEFAULT NULL ";
    $this->addQuery($query);

    
    $this->makeRevision("0.53");
    $query = "ALTER TABLE `dossier_medical` CHANGE `listCim10` `codes_cim` TEXT;";
    $this->addQuery($query);
    
    $this->makeRevision("0.54");
    $query = "ALTER TABLE `patients` ADD INDEX ( `nom_soundex2` );";
    $this->addQuery($query);
    $query = "ALTER TABLE `patients` ADD INDEX ( `prenom_soundex2` );";
    $this->addQuery($query);
    $query = "ALTER TABLE `patients` ADD INDEX ( `naissance` );";
    $this->addQuery($query);
    
    
    $this->makeRevision("0.55");
    $query = "ALTER TABLE `patients` ADD INDEX ( `nom_jeune_fille` );";
    $this->addQuery($query);
    
    $this->makeRevision("0.56");
    $query = "ALTER TABLE `dossier_medical` ADD INDEX ( `object_id` );";
    $this->addQuery($query);
    $query = "ALTER TABLE `dossier_medical` ADD INDEX ( `object_class` );";
    $this->addQuery($query);
    
    $this->makeRevision("0.57");
    $query = "ALTER TABLE `antecedent`
            CHANGE `type` `type`
            ENUM('med','alle','trans','obst','chir','fam','anesth','gyn','cardio','pulm','stomato','plast','ophtalmo','digestif','gastro','stomie','uro','ortho','traumato','amput','neurochir','greffe','thrombo','cutane','hemato','rhumato','neuropsy','infect','endocrino','carcino')
            NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.58");
    $query = "ALTER TABLE `patients` ADD INDEX ( `medecin_traitant` );";
    $this->addQuery($query);
    $query = "ALTER TABLE `patients` ADD INDEX ( `medecin1` );";
    $this->addQuery($query);
    $query = "ALTER TABLE `patients` ADD INDEX ( `medecin2` );";
    $this->addQuery($query);
    $query = "ALTER TABLE `patients` ADD INDEX ( `medecin3` );";
    $this->addQuery($query);
    
    $this->makeRevision("0.59");
    $query = "ALTER TABLE `patients` CHANGE `ald` `notes_amo` TEXT DEFAULT NULL";
    $this->addQuery($query);
    
    $this->makeRevision("0.60");
    $query = "ALTER TABLE `patients`
            ADD `ald` ENUM('0','1'), 
            ADD `code_exo` ENUM('0','5','9') DEFAULT '0', 
            ADD `deb_amo` DATE, 
            ADD `fin_amo` DATE;";
    $this->addQuery($query);
    
    $this->makeRevision("0.61");
    $query = "UPDATE `patients`
            SET `fin_amo` = `cmu`";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `patients`
            CHANGE `cmu` `cmu` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $query = "UPDATE `patients`
            SET `cmu` = '1'
            WHERE `fin_amo` IS NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.62");
    $query = "ALTER TABLE `antecedent`
            CHANGE `type` `type`
            ENUM('med','alle','trans','obst','chir','fam','anesth','gyn','cardio','pulm','stomato','plast','ophtalmo','digestif','gastro','stomie','uro','ortho','traumato','amput','neurochir','greffe','thrombo','cutane','hemato','rhumato','neuropsy','infect','endocrino','carcino','orl');";
    $this->addQuery($query);
    $query = "ALTER TABLE `addiction`
            CHANGE `type` `type`
            ENUM('tabac', 'oenolisme', 'cannabis');";
    $this->addQuery($query);
    
    $this->makeRevision("0.63");
    $query = "CREATE TABLE `etat_dent` (
            `etat_dent_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `dossier_medical_id` INT NOT NULL ,
            `dent` TINYINT UNSIGNED NOT NULL ,
            `etat` ENUM('bridge', 'pivot', 'mobile', 'appareil') NULL
            ) /*! ENGINE=MyISAM */ ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.64");
    $this->addDependency("dPsante400", "0.1");
    $query = "INSERT INTO `id_sante400` (id_sante400_id, object_class, object_id, tag, last_update, id400)
            SELECT NULL, 'CPatient', `patient_id`, 'SHS group:1', NOW(), `SHS`
            FROM `patients` 
            WHERE `SHS` IS NOT NULL 
            AND `SHS` != 0";
    $this->addQuery($query);
    
    $this->makeRevision("0.65");
    $query = "ALTER TABLE `patients` DROP `SHS";
    $this->addQuery($query);
    
    $this->makeRevision("0.66");
    $this->addDependency("dPcabinet", "0.30");
    $query = "CREATE TABLE `constantes_medicales` (
      `constantes_medicales_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `patient_id` INT (11) UNSIGNED NOT NULL,
      `datetime` DATETIME NOT NULL,
      `context_class` VARCHAR (255),
      `context_id` INT (11) UNSIGNED,
      `poids` FLOAT UNSIGNED,
      `taille` FLOAT,
      `ta` VARCHAR (10),
      `pouls` INT (11) UNSIGNED,
      `spo2` FLOAT
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `constantes_medicales` 
      ADD INDEX (`patient_id`),
      ADD INDEX (`datetime`),
      ADD INDEX (`context_id`);";
    $this->addQuery($query);
    
    $query = "INSERT INTO `constantes_medicales` (
        `context_class`, 
        `context_id`, 
        `patient_id`, 
        `datetime`, 
        `poids`, 
        `taille`, 
        `ta`, 
        `pouls`, 
        `spo2`
      )
      SELECT 
        'CConsultation', 
        `consultation`.`consultation_id`, 
        `consultation`.`patient_id`,
        CONCAT(`plageconsult`.`date`, ' ', `consultation`.`heure`), 
        `consultation_anesth`.`poid`, 
        `consultation_anesth`.`taille`, 
        IF(`consultation_anesth`.`tasys`, CONCAT(`consultation_anesth`.`tasys`, '|', `consultation_anesth`.`tadias`), NULL),
        `consultation_anesth`.`pouls`,
        `consultation_anesth`.`spo2`
      FROM 
        `consultation_anesth`, `consultation`, `plageconsult`
      WHERE 
        `consultation`.`consultation_id` = `consultation_anesth`.`consultation_id` AND
        `plageconsult`.`plageconsult_id` = `consultation`.`plageconsult_id`";
    $this->addQuery($query);
    
    /*$query = "ALTER TABLE `consultation_anesth` 
      DROP `poid`
      DROP `taille`
      DROP `tasys`
      DROP `tadias`
      DROP `pouls`
      DROP `spo2`;";
    $this->addQuery($query);*/
    
    $repl = array("Patient - poids",
                  "Patient - taille",
                  "Patient - Pouls",
                  "Patient - IMC",
                  "Patient - TA");
    
    $find = array("Anesth�sie - poids",
                  "Anesth�sie - taille",
                  "Anesth�sie - Pouls",
                  "Anesth�sie - IMC",
                  "Anesth�sie - TA");
    $count = count($repl);
    for ($i = 0; $i < $count; $i++) {
      $query = CSetupdPcompteRendu::renameTemplateFieldQuery($find[$i], $repl[$i]);
      $this->addQuery($query);
    }
    
    $this->makeRevision("0.67");
    $query = 'ALTER TABLE `constantes_medicales` ADD `temperature` FLOAT';
    $this->addQuery($query);
    
    $this->makeRevision("0.68");
    $query = "ALTER TABLE `patients` 
            ADD `code_sit` MEDIUMINT (4) UNSIGNED ZEROFILL,
            ADD `regime_am` ENUM ('0','1');";
    $this->addQuery($query);
    
    $this->makeRevision("0.69");
    $this->addQuery(CSetupdPcompteRendu::renameTemplateFieldQuery("Patient - ant�c�dents", "Patient - Ant�c�dents -- tous"));
    $this->addQuery(CSetupdPcompteRendu::renameTemplateFieldQuery("Patient - traitements", "Patient - Traitements"));
    $this->addQuery(CSetupdPcompteRendu::renameTemplateFieldQuery("Patient - addictions" , "Patient - Addictions -- toutes"));
    $this->addQuery(CSetupdPcompteRendu::renameTemplateFieldQuery("Patient - diagnostics", "Patient - Diagnotics" ));
    
    $this->addQuery(CSetupdPcompteRendu::renameTemplateFieldQuery("Sejour - ant�c�dents", "Sejour - Ant�c�dents -- tous"));
    $this->addQuery(CSetupdPcompteRendu::renameTemplateFieldQuery("Sejour - traitements", "Sejour - Traitements"));
    $this->addQuery(CSetupdPcompteRendu::renameTemplateFieldQuery("Sejour - addictions" , "Sejour - Addictions -- toutes"));
    $this->addQuery(CSetupdPcompteRendu::renameTemplateFieldQuery("Sejour - diagnostics", "Sejour - Diagnotics" ));
    
    $this->makeRevision("0.70");
    $query = "ALTER TABLE `patients` ADD `email` VARCHAR (255) AFTER tel2;";
    $this->addQuery($query);

    $this->makeRevision("0.71");
    $query = "CREATE TABLE `correspondant` (
      `correspondant_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `medecin_id` INT (11) UNSIGNED NOT NULL,
      `patient_id` INT (11) UNSIGNED NOT NULL,
      KEY (`medecin_id`),
      KEY (`patient_id`)
      ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.72");
    $query = "ALTER TABLE `antecedent`
            CHANGE `type` `type`
            ENUM('med','alle','trans','obst','chir','fam','anesth','gyn','cardio','pulm','stomato','plast','ophtalmo','digestif','gastro','stomie','uro','ortho','traumato','amput','neurochir','greffe','thrombo','cutane','hemato','rhumato','neuropsy','infect','endocrino','carcino','orl','addiction','habitus');";
    $this->addQuery($query);
    
    // If there is a type
    $query = "INSERT INTO `antecedent` (`type`, `rques`, `dossier_medical_id`)
            SELECT 'addiction', CONCAT(UPPER(LEFT(`type`, 1)), LOWER(SUBSTRING(`type`, 2)), ': ', `addiction`), `dossier_medical_id`
            FROM `addiction`
            WHERE `type` IS NOT NULL AND `type` <> '0'";
    $this->addQuery($query);
    
    // If there is no type
    $query = "INSERT INTO `antecedent` (`type`, `rques`, `dossier_medical_id`)
            SELECT 'addiction', `addiction`, `dossier_medical_id`
            FROM `addiction`
            WHERE `type` IS NULL OR `type` = '0'";
    $this->addQuery($query);
    
    // If there is a type
    // @todo : A v�rifier
    /*$query = "UPDATE `aide_saisie` SET 
              `class` = 'CAntecedent', 
              `field` = 'rques', 
              `name` = CONCAT(UPPER(LEFT(`depend_value`, 1)), LOWER(SUBSTRING(`depend_value`, 2)), ': ', `name`),
              `text` = CONCAT(UPPER(LEFT(`depend_value`, 1)), LOWER(SUBSTRING(`depend_value`, 2)), ': ', `text`),
              `depend_value` = 'addiction'
            WHERE 
              `class` = 'CAddiction'
               AND `depend_value` IS NOT NULL";
    $this->addQuery($query);*/
    
    // If there is no type
    /*$query = "UPDATE `aide_saisie` SET 
              `class` = 'CAntecedent', 
              `field` = 'rques', 
              `depend_value` = 'addiction'
            WHERE 
              `class` = 'CAddiction'
               AND `depend_value` IS NULL";
    $this->addQuery($query);*/
    
    $this->addQuery(CSetupdPcompteRendu::renameTemplateFieldQuery("Sejour - Addictions -- toutes", "Sejour - Ant�c�dents - Addictions"));
    $this->addQuery(CSetupdPcompteRendu::renameTemplateFieldQuery("Patient - Addictions -- toutes", "Patient - Ant�c�dents - Addictions"));
    
    $addiction_types = array('tabac', 'oenolisme', 'cannabis');
    foreach ($addiction_types as $type) {
      $typeTrad = CAppUI::tr("CAddiction.type.$type");
      $this->addQuery(CSetupdPcompteRendu::renameTemplateFieldQuery("Sejour - Addictions - $typeTrad", "Sejour - Ant�c�dents - Addictions"));
      $this->addQuery(CSetupdPcompteRendu::renameTemplateFieldQuery("Patient - Addictions - $typeTrad", "Patient - Ant�c�dents - Addictions"));
    }
    
    /*$query = "DROP TABLE `addiction`";
    $this->addQuery($query);*/

    $this->makeRevision("0.73");
    $query = "ALTER TABLE `constantes_medicales` 
            ADD `score_sensibilite` FLOAT,
            ADD `score_motricite` FLOAT,
            ADD `EVA` FLOAT,
            ADD `score_sedation` FLOAT,
            ADD `frequence_respiratoire` FLOAT;";
    $this->addQuery($query);
    
    
    $this->makeRevision("0.74");
    for ($i = 1; $i <= 3; $i++) {
      $query = "INSERT INTO `correspondant` (`medecin_id`, `patient_id`)
              SELECT `medecin$i`, `patient_id`
              FROM `patients`
              WHERE `medecin$i` IS NOT NULL";
      $this->addQuery($query);
    }
    $query = "ALTER TABLE `patients`
            DROP `medecin1`,
            DROP `medecin2`,
            DROP `medecin3`";
    $this->addQuery($query);
    
    $this->makeRevision("0.75");
    $query = "UPDATE `constantes_medicales` SET `poids` = NULL WHERE `poids` = 0";
    $this->addQuery($query);
    
    $query = "UPDATE `constantes_medicales` SET `taille` = NULL WHERE `taille` = 0";
    $this->addQuery($query);
    
    $query = "DELETE FROM `constantes_medicales` WHERE 
            `poids` IS NULL AND
            `taille` IS NULL AND
            `ta` IS NULL AND
            `pouls` IS NULL AND
            `spo2` IS NULL AND
            `temperature` IS NULL AND
            `score_sensibilite` IS NULL AND
            `score_motricite` IS NULL AND
            `EVA` IS NULL AND
            `score_sedation` IS NULL AND
            `frequence_respiratoire` IS NULL";
    $this->addQuery($query);
    
    $this->makeRevision("0.76");
    $query = "ALTER TABLE `medecin` ADD `type` ENUM ('medecin','kine','sagefemme','infirmier') NOT NULL DEFAULT 'medecin';";
    $this->addQuery($query);
    
    $this->makeRevision("0.77");
    $query = "ALTER TABLE `antecedent` ADD `annule` ENUM('0','1') DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("0.78");
    $query = "ALTER TABLE `medecin` ADD `portable` BIGINT(10) UNSIGNED ZEROFILL NULL";
    $this->addQuery($query);
    
    $this->makeRevision("0.79");
    $query = "ALTER TABLE `antecedent` 
            ADD `appareil` ENUM ('cardiovasculaire','endocrinien','neuro_psychiatrique','uro_nephrologique','digestif','pulmonaire');";
    $this->addQuery($query);
    
    $this->makeRevision("0.80");
    $query = "ALTER TABLE patients
            ADD pays_insee INT(11) AFTER pays ,
            ADD prenom_2 VARCHAR(50) AFTER prenom ,
            ADD prenom_3 VARCHAR(50) AFTER prenom_2 ,
            ADD prenom_4 VARCHAR(50) AFTER prenom_3 ,
            ADD cp_naissance VARCHAR(5) AFTER lieu_naissance ,
            ADD pays_naissance_insee INT(11) AFTER cp_naissance, 
            ADD assure_pays_insee INT(11) AFTER assure_pays ,
            ADD assure_prenom_2 VARCHAR(50) AFTER assure_prenom ,
            ADD assure_prenom_3 VARCHAR(50) AFTER assure_prenom_2 ,
            ADD assure_prenom_4 VARCHAR(50) AFTER assure_prenom_3 ,
            ADD assure_cp_naissance VARCHAR(5) AFTER assure_lieu_naissance,
            ADD assure_pays_naissance_insee INT(11) AFTER assure_cp_naissance;";
     
    $this->addQuery($query);
    
    $this->makeRevision("0.81");
    
    $query = "ALTER TABLE `patients` 
            CHANGE `prenom_2` `prenom_2` VARCHAR (255),
            CHANGE `prenom_3` `prenom_3` VARCHAR (255),
            CHANGE `prenom_4` `prenom_4` VARCHAR (255),
            CHANGE `sexe` `sexe` ENUM ('m','f','j'),
            CHANGE `adresse` `adresse` TEXT,
            CHANGE `ville` `ville` VARCHAR (255),
            CHANGE `incapable_majeur` `incapable_majeur` ENUM ('0','1'),
            CHANGE `ATNC` `ATNC` ENUM ('0','1'),
            CHANGE `matricule` `matricule` VARCHAR (15),
            CHANGE `assure_prenom_2` `assure_prenom_2` VARCHAR (255),
            CHANGE `assure_prenom_3` `assure_prenom_3` VARCHAR (255),
            CHANGE `assure_prenom_4` `assure_prenom_4` VARCHAR (255);";
     
    $this->addQuery($query);
    
    $this->makeRevision("0.82");
    $query = "ALTER TABLE `antecedent`
            CHANGE `appareil` `appareil`
            ENUM('cardiovasculaire','digestif','endocrinien','neuro_psychiatrique','pulmonaire','uro_nephrologique','orl','gyneco_obstetrique','orthopedique');";
    $this->addQuery($query);
    
    $this->makeRevision("0.83");
    $query = "ALTER TABLE `patients`
            CHANGE `pays_insee` `pays_insee` INT(3) UNSIGNED ZEROFILL,
            CHANGE `pays_naissance_insee` `pays_naissance_insee` INT(3) UNSIGNED ZEROFILL,
            CHANGE `assure_pays_insee` `assure_pays_insee` INT(3) UNSIGNED ZEROFILL,
            CHANGE `assure_pays_naissance_insee` `assure_pays_naissance_insee` INT(3) UNSIGNED ZEROFILL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.84");
    
    $query = "ALTER TABLE `patients` 
        ADD `libelle_exo` TEXT AFTER `rques`,
        ADD `medecin_traitant_declare` ENUM('0', '1') AFTER `email`";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `patients` 
        CHANGE `code_exo` `code_exo` ENUM('0', '4', '5', '9') NULL DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("0.85");
    
    $query = "ALTER TABLE `patients` 
            ADD `civilite` ENUM ('m','mme','melle','enf','dr','pr','me','vve') AFTER `sexe`,
            ADD `assure_civilite` ENUM ('m','mme','melle','enf','dr','pr','me','vve') AFTER `assure_sexe`";
    $this->addQuery($query);
    
    $query = "UPDATE `patients` SET `civilite` = 'm' WHERE `sexe` = 'm'";
    $this->addQuery($query);
    
    $query = "UPDATE `patients` SET `civilite` = 'mme' WHERE `sexe` = 'f'";
    $this->addQuery($query);
    
    $query = "UPDATE `patients` SET `civilite` = 'melle' WHERE `sexe` = 'j'";
    $this->addQuery($query);
    
    $query = "UPDATE `patients` SET `civilite` = 'enf' WHERE `naissance` >= ".(date('Y')-15);
    $this->addQuery($query);
    
    $query = "UPDATE `patients` SET `assure_civilite` = 'm' WHERE `assure_sexe` = 'm'";
    $this->addQuery($query);
    
    $query = "UPDATE `patients` SET `assure_civilite` = 'mme' WHERE `assure_sexe` = 'f'";
    $this->addQuery($query);
    
    $query = "UPDATE `patients` SET `assure_civilite` = 'melle' WHERE `assure_sexe` = 'j'";
    $this->addQuery($query);

    $query = "UPDATE `patients` SET `assure_civilite` = 'enf' WHERE `assure_naissance` >= ".(date('Y')-15);
    $this->addQuery($query);
    
    $query = "UPDATE `patients` SET `sexe` = 'f' WHERE `sexe` = 'j'";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `patients` 
            CHANGE `sexe` `sexe` ENUM ('m','f'),
            CHANGE `assure_sexe` `assure_sexe` ENUM ('m','f')";
    $this->addQuery($query);
    
    $this->makeRevision("0.86");
    
    $query = "ALTER TABLE `medecin` 
            ADD `adeli` INT (9) UNSIGNED ZEROFILL;";
        
    $this->makeRevision("0.87");
    
    $query = "ALTER TABLE `medecin` 
            ADD `adeli` INT (9) UNSIGNED ZEROFILL;";
    $this->addQuery($query);
    
    // Data source query
    $query = "SELECT *
      FROM `communes_france`
      WHERE `INSEE` = '99138'";
    $this->addDatasource("INSEE", $query);
    
    $this->makeRevision("0.88");
    $query = "ALTER TABLE `constantes_medicales` 
      ADD `glycemie` FLOAT UNSIGNED";
    $this->addQuery($query);
    
    $this->makeRevision("0.89");
    $query = "ALTER TABLE `medecin` 
      CHANGE `type` `type` ENUM ('medecin','kine','sagefemme','infirmier','dentiste','autre')";
    $this->addQuery($query);
    
    $this->makeRevision("0.90");
    $query = "ALTER TABLE `medecin` 
      CHANGE `type` `type` ENUM ('medecin','kine','sagefemme','infirmier','dentiste','podologue','autre');";
    $this->addQuery($query);

    $this->makeRevision("0.91");
    $query = "ALTER TABLE `patients` 
      ADD `notes_amc` TEXT;";
    $this->addQuery($query);

    $this->makeRevision("0.92");
    $query = "ALTER TABLE `antecedent` 
                ADD INDEX (`date`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `etat_dent` 
                ADD INDEX (`dossier_medical_id`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `traitement` 
                ADD INDEX (`debut`),
                ADD INDEX (`fin`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `patients` 
                ADD INDEX (`deb_amo`),
                ADD INDEX (`fin_amo`),
                ADD INDEX (`fin_validite_vitale`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.93");
    $query = "ALTER TABLE `antecedent` 
              CHANGE `type` `type` VARCHAR (80),
              CHANGE `appareil` `appareil` VARCHAR (80);";
    $this->addQuery($query);
    
    $this->makeRevision("0.94");
    $query = "ALTER TABLE `dossier_medical` 
              ADD `risque_thrombo_patient` ENUM ('faible','modere','eleve','majeur','NR') DEFAULT 'NR',
              ADD `risque_MCJ_patient` ENUM ('sans','avec','suspect','atteint','NR') DEFAULT 'NR',
              ADD `risque_thrombo_chirurgie` ENUM ('faible','modere','eleve','NR') DEFAULT 'NR',
              ADD `risque_antibioprophylaxie` ENUM ('oui','non','NR') DEFAULT 'NR',
              ADD `risque_prophylaxie` ENUM ('oui','non','NR') DEFAULT 'NR',
              ADD `risque_MCJ_chirurgie` ENUM ('sans','avec','NR') DEFAULT 'NR';";
    $this->addQuery($query);
    
    $this->makeRevision("0.95");
    $query = "ALTER TABLE `constantes_medicales` 
              ADD `redon` FLOAT UNSIGNED,
              ADD `diurese` FLOAT UNSIGNED,
              ADD `injection` VARCHAR (10);";
    $this->addQuery($query);
    
    $this->makeRevision("0.96");
    $query = "ALTER TABLE `patients` 
              ADD `code_gestion` MEDIUMINT (4) UNSIGNED ZEROFILL,
              ADD `mutuelle_types_contrat` TEXT";
    $this->addQuery($query);
    
    $this->makeRevision("0.97");
    $this->setTimeLimit(1800);
    $query = "ALTER TABLE `patients` ADD `code_gestion2` MEDIUMINT (2) UNSIGNED ZEROFILL";
    $this->addQuery($query);
    $query = "ALTER TABLE `patients` CHANGE `code_gestion` `centre_carte` MEDIUMINT (4) UNSIGNED ZEROFILL";
    $this->addQuery($query);
    $query = "ALTER TABLE `patients` CHANGE `code_gestion2` `code_gestion` MEDIUMINT (2) UNSIGNED ZEROFILL";
    $this->addQuery($query);
    $query = "ALTER TABLE `patients` ADD `qual_beneficiaire` ENUM ('0','1','2','3','4','5','6','7','8','9')";
    $this->addQuery($query);
    
    foreach(CPatient::$rangToQualBenef as $from => $to) {
      $query = "UPDATE `patients` SET `qual_beneficiaire` = '$to' WHERE `rang_beneficiaire` = '$from'";
      $this->addQuery($query);
    }
    
    $this->makeRevision("0.98");
    $query = "ALTER TABLE `patients` CHANGE `code_gestion` `code_gestion` CHAR (2)";
    $this->addQuery($query);
              
    $this->makeRevision("0.99");
    
    $query = "ALTER TABLE `patients`
             CHANGE `civilite` `civilite` ENUM ('m','mme','melle','mlle','enf','dr','pr','me','vve') DEFAULT 'm',
             CHANGE `assure_civilite` `assure_civilite` ENUM ('m','mme','melle','mlle','enf','dr','pr','me','vve') DEFAULT 'm';";
    $this->addQuery($query);
    
    $query = "UPDATE `patients` SET `civilite` = 'mlle' WHERE `civilite` = 'melle'";
    $this->addQuery($query);
    
    $query = "UPDATE `patients` SET `assure_civilite` = 'mlle' WHERE `assure_civilite` = 'melle'";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `patients`
             CHANGE `civilite` `civilite` ENUM ('m','mme','mlle','enf','dr','pr','me','vve') DEFAULT 'm',
             CHANGE `assure_civilite` `assure_civilite` ENUM ('m','mme','mlle','enf','dr','pr','me','vve') DEFAULT 'm';";
    $this->addQuery($query);
    
    $this->makeRevision("1.0");
    $query = "ALTER TABLE `constantes_medicales` ADD `ta_droit` VARCHAR (10) AFTER `ta`";
    $this->addQuery($query);
    
    $this->makeRevision("1.01");
    $query = "ALTER TABLE `constantes_medicales`
              ADD `redon_2` FLOAT UNSIGNED AFTER `redon`,
              ADD `redon_3` FLOAT UNSIGNED AFTER `redon_2`";
    $this->addQuery($query);
    
    $this->makeRevision("1.02");
    $query = "ALTER TABLE `patients` 
      ADD `confiance_nom` VARCHAR( 50 ) AFTER `prevenir_parente`,
      ADD `confiance_prenom` VARCHAR( 50 ) AFTER `confiance_nom`,
      ADD `confiance_adresse` TEXT AFTER `confiance_prenom`,
      ADD `confiance_cp` VARCHAR( 5 ) AFTER `confiance_adresse`,
      ADD `confiance_ville` VARCHAR( 50 ) AFTER `confiance_cp`,
      ADD `confiance_tel` VARCHAR( 10 ) AFTER `confiance_ville`,
      ADD `confiance_parente` ENUM( 'conjoint', 'enfant', 'ascendant', 'colateral', 'divers' ) AFTER `confiance_tel`;";
    $this->addQuery($query);
    
    $this->makeRevision("1.03");
    $query = "ALTER TABLE `patients` ADD `tel_autre` VARCHAR (20) AFTER `tel2`";
    $this->addQuery($query);
    
    $this->makeRevision("1.04");
    $this->addPrefQuery("vCardExport", "0");
    
    $this->makeRevision("1.05");
    $query = "ALTER TABLE `patients` ADD `vip` ENUM ('0','1') NOT NULL DEFAULT '0' AFTER `email`";
    $this->addQuery($query);
    
    $this->makeRevision("1.06");
    $query = "ALTER TABLE `patients` ADD `date_lecture_vitale` DATETIME";
    $this->addQuery($query);

    $this->makeRevision("1.07");
    $query = "ALTER TABLE `patients`
      DROP `nationalite`,
      DROP `assure_nationalite`;";
    $this->addQuery($query);
    
    $this->makeRevision("1.08");
    $query = "ALTER TABLE `medecin` 
      CHANGE `type` `type` ENUM ('medecin','kine','sagefemme','infirmier','dentiste','podologue', 'pharmacie', 'maison_medicale', 'autre');";
    $this->addQuery($query);
    
    $this->makeRevision("1.09");
    $query = "ALTER TABLE `groups_config`
      ADD `dPpatients_CPatient_nom_jeune_fille_mandatory` ENUM ('0', '1') NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("1.10");
    $query = "ALTER TABLE `constantes_medicales`
      CHANGE `ta` `ta_gauche` VARCHAR (10),
      ADD `ta` VARCHAR(10) AFTER `taille`,
      ADD PVC FLOAT UNSIGNED,
      ADD perimetre_abdo FLOAT UNSIGNED,
      ADD perimetre_cuisse FLOAT UNSIGNED,
      ADD perimetre_cou FLOAT UNSIGNED,
      ADD perimetre_thoracique FLOAT UNSIGNED";
    $this->addQuery($query);
    
    $this->makeRevision("1.11");
    
    /*function changeTa() {
      $mbConfig = new CMbConfig;
      $mbConfig->load();
      $important_constants = $mbConfig->get("dPpatients CConstantesMedicales important_constantes");
      $important_constants = preg_replace("/^ta$/", "ta_gauche", $important_constants);
      $important_constants = preg_replace("/^ta\|/", "ta_gauche|" , $important_constants);
      $important_constants = preg_replace("/\|ta\|/", "|ta_gauche|", $important_constants);
      $important_constants = preg_replace("/\|ta$/", "|ta_gauche", $important_constants);

      $mbConfig->update(array("dPpatients"=> array("CConstantesMedicales" => array("important_constantes" => $important_constants))));
      return true;
    }
    $this->addFunction("changeTa");*/
    
    $query = "UPDATE constantes_medicales
              SET ta_gauche = ta, ta = NULL
              WHERE ta IS NOT NULL AND ta_gauche IS NULL";
    $this->addQuery($query);
    
    $this->makeRevision("1.12");
    $query = "ALTER TABLE `constantes_medicales` 
              ADD `diurese_miction` FLOAT UNSIGNED AFTER `diurese`;";
    $this->addQuery($query);
    
    $this->makeRevision("1.13");
    $query = "ALTER TABLE `patients`
      ADD `deces` DATE AFTER `naissance`;";
    $this->addQuery($query);
    
    $this->makeRevision("1.14");
    $query = "ALTER TABLE `medecin`
      CHANGE `prenom` `prenom` varchar(255);";
    $this->addQuery($query);
    
    $this->makeRevision("1.15");
    $query = "ALTER TABLE `constantes_medicales` 
              ADD `comment` TEXT";
    $this->addQuery($query);
    
    $this->makeRevision("1.16");
    $query = "ALTER TABLE `patients` 
      ADD INDEX ( `nomjf_soundex2` );";
    $this->addQuery($query);
    
    $this->makeRevision("1.17");
    $query = "ALTER TABLE `patients`
      ADD `INS` CHAR(22) AFTER `matricule`";
    $this->addQuery($query);
    
    $this->makeRevision("1.18");
    $query = "ALTER TABLE `patients`
      CHANGE `INS` `INSC` CHAR(22),
      ADD `INSC_date` DATETIME AFTER `INSC`";
    $this->addQuery($query);
    
    $this->makeRevision("1.19");
    $query = "ALTER TABLE `medecin` 
               ADD `rpps` BIGINT (11) UNSIGNED ZEROFILL;";
    $this->addQuery($query);
    
    $this->makeRevision("1.20");
    $query = "CREATE TABLE `correspondant_patient` (
      `correspondant_patient_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `patient_id` INT (11) UNSIGNED NOT NULL,
      `relation` ENUM ('confiance','prevenir','employeur'),
      `nom` VARCHAR (255),
      `prenom` VARCHAR (255),
      `adresse` TEXT,
      `cp` INT (5) UNSIGNED ZEROFILL,
      `ville` VARCHAR (255),
      `tel` BIGINT (10) UNSIGNED ZEROFILL,
      `urssaf` BIGINT (11) UNSIGNED ZEROFILL,
      `parente` ENUM ('conjoint','enfant','ascendant','colateral','divers'),
      `email` VARCHAR (255),
      `remarques` TEXT
      ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `correspondant_patient` 
              ADD INDEX (`patient_id`);";
    $this->addQuery($query);
    
    $query = "INSERT INTO correspondant_patient (patient_id, relation, nom, adresse, cp, ville, tel, urssaf)
      SELECT patient_id, 'employeur', employeur_nom, employeur_adresse, employeur_cp, employeur_ville, employeur_tel, employeur_urssaf
      FROM patients";
    $this->addQuery($query);
    
    $query = "INSERT INTO correspondant_patient (patient_id, relation, nom, prenom, adresse, cp, ville, tel, parente)
      SELECT patient_id, 'prevenir', prevenir_nom, prevenir_prenom, prevenir_adresse, prevenir_cp, prevenir_ville, prevenir_tel, prevenir_parente
      FROM patients";
    $this->addQuery($query);
    
    $query = "INSERT INTO correspondant_patient (patient_id, relation, nom, prenom, adresse, cp, ville, tel, parente)
      SELECT patient_id, 'confiance', confiance_nom, confiance_prenom, confiance_adresse, confiance_cp, confiance_ville, confiance_tel, confiance_parente
      FROM patients";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `patients`
      DROP `employeur_nom`,
      DROP `employeur_adresse`,
      DROP `employeur_cp`,
      DROP `employeur_ville`,
      DROP `employeur_tel`,
      DROP `employeur_urssaf`,
      DROP `prevenir_nom`,
      DROP `prevenir_prenom`,
      DROP `prevenir_adresse`,
      DROP `prevenir_cp`,
      DROP `prevenir_ville`,
      DROP `prevenir_tel`,
      DROP `prevenir_parente`,
      DROP `confiance_nom`,
      DROP `confiance_prenom`,
      DROP `confiance_adresse`,
      DROP `confiance_cp`,
      DROP `confiance_ville`,
      DROP `confiance_tel`,
      DROP `confiance_parente`";
    $this->addQuery($query);
    
    $this->makeRevision("1.21");
    $query = "CREATE TABLE `devenir_dentaire` (
      `devenir_dentaire_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `patient_id` INT (11) UNSIGNED NOT NULL
      ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `devenir_dentaire` 
      ADD INDEX (`patient_id`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `acte_dentaire` (
      `acte_dentaire_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `devenir_dentaire_id` INT (11) UNSIGNED NOT NULL,
      `code` VARCHAR (10) NOT NULL,
      `commentaire` TEXT
      ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `acte_dentaire` 
      ADD INDEX (`devenir_dentaire_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.22");
    $query = "DELETE FROM `correspondant_patient`
      WHERE `nom` IS NULL
      AND `prenom` IS NULL
      AND `adresse` IS NULL
      AND `cp` IS NULL
      AND `ville` IS NULL
      AND `tel` IS NULL
      AND `urssaf` IS NULL
      AND `parente` IS NULL
      AND `email` IS NULL
      AND `remarques` IS NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("1.23");
    $query = "ALTER TABLE `constantes_medicales` 
      ADD `redon_4` FLOAT UNSIGNED AFTER `redon_3`,
      ADD `sng` FLOAT UNSIGNED,
      ADD `lame_1` FLOAT UNSIGNED,
      ADD `lame_2` FLOAT UNSIGNED,
      ADD `lame_3` FLOAT UNSIGNED,
      ADD `drain_1` FLOAT UNSIGNED,
      ADD `drain_2` FLOAT UNSIGNED,
      ADD `drain_3` FLOAT UNSIGNED,
      ADD `drain_thoracique_1` FLOAT UNSIGNED,
      ADD `drain_thoracique_2` FLOAT UNSIGNED,
      ADD `drain_pleural_1` FLOAT UNSIGNED,
      ADD `drain_pleural_2` FLOAT UNSIGNED,
      ADD `drain_mediastinal` FLOAT UNSIGNED,
      ADD `sonde_ureterale_1` FLOAT UNSIGNED,
      ADD `sonde_ureterale_2` FLOAT UNSIGNED,
      ADD `sonde_vesicale` FLOAT UNSIGNED";
    $this->addQuery($query);
		
		$this->makeRevision("1.24");
		$query = "CREATE TABLE `config_constantes_medicales` (
              `service_id` INT (11) UNSIGNED,
              `group_id` INT (11) UNSIGNED,
              `config_constantes_medicales_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `important_constantes` TEXT,
              `diuere_24_reset_hour` TINYINT (4) UNSIGNED,
              `redon_cumul_reset_hour` TINYINT (4) UNSIGNED,
              `sng_cumul_reset_hour` TINYINT (4) UNSIGNED,
              `lame_cumul_reset_hour` TINYINT (4) UNSIGNED,
              `drain_cumul_reset_hour` TINYINT (4) UNSIGNED,
              `drain_thoracique_cumul_reset_hour` TINYINT (4) UNSIGNED,
              `drain_pleural_cumul_reset_hour` TINYINT (4) UNSIGNED,
              `drain_mediastinal_cumul_reset_hour` TINYINT (4) UNSIGNED,
              `sonde_ureterale_cumul_reset_hour` TINYINT (4) UNSIGNED,
              `sonde_vesicale_cumul_reset_hour` TINYINT (4) UNSIGNED,
							`show_cat_tabs` ENUM ('0','1')
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `config_constantes_medicales` 
              ADD INDEX (`service_id`),
              ADD INDEX (`group_id`);";
    $this->addQuery($query);
		
		$conf = CAppUI::conf("dPpatients CConstantesMedicales");
    $query = $this->ds->prepare("INSERT INTO `config_constantes_medicales` (
								`important_constantes` ,
								`diuere_24_reset_hour` ,
								`redon_cumul_reset_hour` ,
								`sng_cumul_reset_hour` ,
								`lame_cumul_reset_hour` ,
								`drain_cumul_reset_hour` ,
								`drain_thoracique_cumul_reset_hour` ,
								`drain_pleural_cumul_reset_hour` ,
								`drain_mediastinal_cumul_reset_hour` ,
								`sonde_ureterale_cumul_reset_hour` ,
								`sonde_vesicale_cumul_reset_hour` ,
								`show_cat_tabs` 
							)
							VALUES (
							%1 , %2 , %3 , %4 , %5 , %6 , %7 , %8 , %9 , %10 , %11 , '0'
							);", 
	              $conf["important_constantes"],
	              $conf["diuere_24_reset_hour"],
	              $conf["redon_cumul_reset_hour"],
	              $conf["sng_cumul_reset_hour"],
	              $conf["lame_cumul_reset_hour"],
	              $conf["drain_cumul_reset_hour"],
	              $conf["drain_thoracique_cumul_reset_hour"],
	              $conf["drain_pleural_cumul_reset_hour"],
	              $conf["drain_mediastinal_cumul_reset_hour"],
	              $conf["sonde_ureterale_cumul_reset_hour"],
	              $conf["sonde_vesicale_cumul_reset_hour"]
							);
    $this->addQuery($query);
    
    $this->makeRevision("1.25");
    $query = "ALTER TABLE `devenir_dentaire` 
              ADD `etudiant_id` INT (11) UNSIGNED, 
              ADD INDEX (`etudiant_id`),
              ADD `description` TEXT NOT NULL;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `acte_dentaire` 
              ADD `ICR` INT (11) UNSIGNED, 
              ADD `consult_id` INT (11) UNSIGNED,
              ADD INDEX (`consult_id`),
              ADD `rank` INT (11) UNSIGNED NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("1.26");
    $query = "ALTER TABLE `correspondant_patient` 
              CHANGE `relation` `relation` ENUM ('assurance','autre','confiance','employeur','inconnu','prevenir'),
              ADD `relation_autre` VARCHAR (255),
              CHANGE `parente` `parente` ENUM ('ami','ascendant','autre','beau_fils','colateral','collegue','compagnon','conjoint','directeur','divers','employeur','employe','enfant','enfant_adoptif','entraineur','epoux','frere','grand_parent','mere','pere','petits_enfants','proche','proprietaire','soeur','tuteur'),
              ADD `parente_autre` VARCHAR (255);";
    $this->addQuery($query);
    
    $this->mod_version = "1.27";
    
  }
}

?>