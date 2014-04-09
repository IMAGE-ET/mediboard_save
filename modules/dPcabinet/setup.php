<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CSetupdPcabinet extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPcabinet";
    
    $this->makeRevision("all");
    $query = "CREATE TABLE consultation (
                    consultation_id bigint(20) NOT NULL auto_increment,
                    plageconsult_id bigint(20) NOT NULL default '0',
                    patient_id bigint(20) NOT NULL default '0',
                    heure time NOT NULL default '00:00:00',
                    duree time NOT NULL default '00:00:00',
                    motif text,
                    secteur1 smallint(6) NOT NULL default '0',
                    secteur2 smallint(6) NOT NULL default '0',
                    rques text,
                    PRIMARY KEY  (consultation_id),
                    KEY plageconsult_id (plageconsult_id,patient_id)
                    ) /*! ENGINE=MyISAM */ COMMENT='Table des consultations';";
    $this->addQuery($query);
    $query = "CREATE TABLE plageconsult (
                    plageconsult_id bigint(20) NOT NULL auto_increment,
                    chir_id bigint(20) NOT NULL default '0',
                    date date NOT NULL default '0000-00-00',
                    debut time NOT NULL default '00:00:00',
                    fin time NOT NULL default '00:00:00',
                    PRIMARY KEY  (plageconsult_id),
                    KEY chir_id (chir_id)
                    ) /*! ENGINE=MyISAM */ COMMENT='Table des plages de consultation des m�decins';";
    $this->addQuery($query);
    
    
    $this->makeRevision("0.1");
    $query = "ALTER TABLE plageconsult ADD freq TIME DEFAULT '00:15:00' NOT NULL AFTER date ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.2");
    $query = "ALTER TABLE consultation ADD compte_rendu TEXT DEFAULT NULL";
    $this->addQuery($query);
    
    $this->makeRevision("0.21");
    $query = "ALTER TABLE consultation CHANGE duree duree TINYINT DEFAULT '1' NOT NULL ";
    $this->addQuery($query);
    $query = "UPDATE consultation SET duree='1' ";
    $this->addQuery($query);
    
    $this->makeRevision("0.22");
    $query = "ALTER TABLE `consultation` " .
            "\nADD `chrono` TINYINT DEFAULT '16' NOT NULL," .
            "\nADD `annule` TINYINT DEFAULT '0' NOT NULL," .
            "\nADD `paye` TINYINT DEFAULT '0' NOT NULL," .
            "\nADD `cr_valide` TINYINT DEFAULT '0' NOT NULL," .
            "\nADD `examen` TEXT," .
            "\nADD `traitement` TEXT";
    $this->addQuery($query);
    
    $this->makeRevision("0.23");
    $query = "ALTER TABLE `consultation` ADD `premiere` TINYINT NOT NULL";
    $this->addQuery($query);
    
    $this->makeRevision("0.24");
    $query = "CREATE TABLE `tarifs` (
                `tarif_id` BIGINT NOT NULL AUTO_INCREMENT ,
                `chir_id` BIGINT DEFAULT '0' NOT NULL ,
                `function_id` BIGINT DEFAULT '0' NOT NULL ,
                `description` VARCHAR( 50 ) ,
                `valeur` TINYINT,
                PRIMARY KEY ( `tarif_id` ) ,
                INDEX ( `chir_id`),
                INDEX ( `function_id` )
                ) /*! ENGINE=MyISAM */ COMMENT = 'table des tarifs de consultation';";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation` ADD `tarif` TINYINT,
            ADD `type_tarif` ENUM( 'cheque', 'CB', 'especes', 'tiers', 'autre' ) ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.25");
    $query = "ALTER TABLE `tarifs` CHANGE `valeur` `secteur1` FLOAT( 6 ) DEFAULT NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `tarifs` ADD `secteur2` FLOAT( 6 ) NOT NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation` CHANGE `secteur1` `secteur1` FLOAT( 6 ) DEFAULT '0' NOT NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation` CHANGE `secteur2` `secteur2` FLOAT( 6 ) DEFAULT '0' NOT NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation` CHANGE `tarif` `tarif` VARCHAR( 50 ) DEFAULT NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `plageconsult` ADD `libelle` VARCHAR( 50 ) DEFAULT NULL AFTER `chir_id` ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.26");
    $query = "ALTER TABLE `consultation` " .
            "\nADD `ordonnance` TEXT DEFAULT NULL," .
            "\nADD `or_valide` TINYINT DEFAULT '0' NOT NULL"; 
    $this->addQuery($query);
    
    $this->makeRevision("0.27");
    $query = "ALTER TABLE `consultation` " .
            "\nADD `courrier1` TEXT DEFAULT NULL," .
            "\nADD `c1_valide` TINYINT DEFAULT '0' NOT NULL"; 
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation` " .
            "\nADD `courrier2` TEXT DEFAULT NULL," .
            "\nADD `c2_valide` TINYINT DEFAULT '0' NOT NULL"; 
    $this->addQuery($query);
    
    $this->makeRevision("0.28");
    $query = "ALTER TABLE `consultation` ADD `date_paiement` DATE AFTER `paye` ;";
    $this->addQuery($query);
    $query = "UPDATE consultation, plageconsult
          SET consultation.date_paiement = plageconsult.date
          WHERE consultation.plageconsult_id = plageconsult.plageconsult_id
          AND consultation.paye = 1"; 
    $this->addQuery($query);
    
    $this->makeRevision("0.29");
    $query = "CREATE TABLE `consultation_anesth` (
          `consultation_anesth_id` BIGINT NOT NULL AUTO_INCREMENT ,
          `consultation_id` BIGINT DEFAULT '0' NOT NULL ,
          `operation_id` BIGINT DEFAULT '0' NOT NULL ,
          `poid` FLOAT,
          `taille` FLOAT,
          `groupe` ENUM( '0', 'A', 'B', 'AB' ) ,
          `rhesus` ENUM( '+', '-' ) ,
          `antecedents` TEXT,
          `traitements` TEXT,
          `tabac` ENUM( '-', '+', '++' ) ,
          `oenolisme` ENUM( '-', '+', '++' ) ,
          `transfusions` ENUM( '-', '+' ) ,
          `tasys` TINYINT,
          `tadias` TINYINT,
          `listCim10` TEXT,
          `intubation` ENUM( 'dents', 'bouche', 'cou' ) ,
          `biologie` ENUM( 'NF', 'COAG', 'IONO' ) ,
          `commande_sang` ENUM( 'clinique', 'CTS', 'autologue' ) ,
          `ASA` TINYINT,
          PRIMARY KEY ( `consultation_anesth_id` ) ,
          INDEX ( `consultation_id`) ,
          INDEX ( `operation_id` )
          ) /*! ENGINE=MyISAM */ COMMENT = 'Consultations d\'anesth�sie';";
    $this->addQuery($query);
    
    // CR passage des champs � enregistrements supprim� car regressifs
    // $this->makeRevision("0.30");
    
    $this->makeRevision("0.31");
    $query = "CREATE TABLE `examaudio` (" .
          "\n`examaudio_id` INT NOT NULL AUTO_INCREMENT ," .
          "\n`consultation_id` INT NOT NULL ," .
          "\n`gauche_aerien` VARCHAR( 64 ) ," .
          "\n`gauche_osseux` VARCHAR( 64 ) ," .
          "\n`droite_aerien` VARCHAR( 64 ) ," .
          "\n`droite_osseux` VARCHAR( 64 ) ," .
          "\nPRIMARY KEY ( `examaudio_id` ) ," .
          "\nINDEX ( `consultation_id` )) /*! ENGINE=MyISAM */";
    $this->addQuery($query);
    
    $this->makeRevision("0.32");
    $query = "ALTER TABLE `examaudio` ADD UNIQUE (`consultation_id`)";
    $this->addQuery($query);
    
    $this->makeRevision("0.33");
    $query = "ALTER TABLE `examaudio` " .
          "\nADD `remarques` TEXT AFTER `consultation_id`," .
          "\nADD `gauche_conlat` VARCHAR( 64 ) ," .
          "\nADD `gauche_ipslat` VARCHAR( 64 ) ," .
          "\nADD `gauche_pasrep` VARCHAR( 64 ) ," .
          "\nADD `gauche_vocale` VARCHAR( 64 ) ," .
          "\nADD `gauche_tympan` VARCHAR( 64 ) ," .
          "\nADD `droite_conlat` VARCHAR( 64 ) ," .
          "\nADD `droite_ipslat` VARCHAR( 64 ) ," .
          "\nADD `droite_pasrep` VARCHAR( 64 ) ," .
          "\nADD `droite_vocale` VARCHAR( 64 ) ," .
          "\nADD `droite_tympan` VARCHAR( 64 )";
    $this->addQuery($query);
    
    $this->makeRevision("0.34");
    $query = "ALTER TABLE `consultation_anesth`
          CHANGE `groupe` `groupe` ENUM( '?', '0', 'A', 'B', 'AB' ) DEFAULT '?' NOT NULL ,
          CHANGE `rhesus` `rhesus` ENUM( '?', '+', '-' ) DEFAULT '?' NOT NULL ,
          CHANGE `tabac` `tabac` ENUM( '?', '-', '+', '++' ) DEFAULT '?' NOT NULL ,
          CHANGE `oenolisme` `oenolisme` ENUM( '?', '-', '+', '++' ) DEFAULT '?' NOT NULL ,
          CHANGE `transfusions` `transfusions` ENUM( '?', '-', '+' ) DEFAULT '?' NOT NULL ,
          CHANGE `intubation` `intubation` ENUM( '?', 'dents', 'bouche', 'cou' ) DEFAULT '?' NOT NULL ,
          CHANGE `biologie` `biologie` ENUM( '?', 'NF', 'COAG', 'IONO' ) DEFAULT '?' NOT NULL ,
          CHANGE `commande_sang` `commande_sang` ENUM( '?', 'clinique', 'CTS', 'autologue' ) DEFAULT '?' NOT NULL ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation_anesth`
          CHANGE `tasys` `tasys` INT( 5 ) DEFAULT NULL ,
          CHANGE `tadias` `tadias` INT( 5 ) DEFAULT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.35");
    $query = "ALTER TABLE `consultation` ADD `arrivee` DATETIME AFTER `type_tarif` ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.36");
    $query = "ALTER TABLE `consultation_anesth`" .
          "CHANGE `groupe` `groupe` ENUM( '?', 'O', 'A', 'B', 'AB' )" .
          "DEFAULT '?' NOT NULL ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.37");
    $this->makeRevision("0.38");
    
    $this->makeRevision("0.39");
    $query = "ALTER TABLE `consultation_anesth`
              ADD `mallampati` ENUM( 'classe1', 'classe2', 'classe3', 'classe4' ),
              ADD `bouche` ENUM( 'm20', 'm35', 'p35' ),
              ADD `distThyro` ENUM( 'm65', 'p65' ),
              ADD `etatBucco` VARCHAR(50),
              ADD `conclusion` VARCHAR(50),
              ADD `position` ENUM( 'DD', 'DV', 'DL', 'GP', 'AS', 'TO' );";
    $this->addQuery($query);
    
    $this->makeRevision("0.40");
    $this->makeRevision("0.41");
    
    $this->makeRevision("0.42");
    $this->setTimeLimit(1800);
    $query = "ALTER TABLE `consultation` DROP INDEX `plageconsult_id`  ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation` ADD INDEX ( `plageconsult_id` ) ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation` ADD INDEX ( `patient_id` ) ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `tarifs` DROP INDEX `chir_id` ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `tarifs` ADD INDEX ( `chir_id` ) ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `tarifs` ADD INDEX ( `function_id` ) ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.43");
    $query = "ALTER TABLE `consultation_anesth`" .
          "CHANGE `position` `position` ENUM( 'DD', 'DV', 'DL', 'GP', 'AS', 'TO', 'GYN');";
    $this->addQuery($query);
    $query = "CREATE TABLE `techniques_anesth` (
               `technique_id` INT NOT NULL AUTO_INCREMENT ,
               `consultAnesth_id` INT NOT NULL ,
               `technique` TEXT NOT NULL ,
               PRIMARY KEY ( `technique_id` )) /*! ENGINE=MyISAM */";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation_anesth`
            ADD `rai` float default NULL,
            ADD `hb` float default NULL,
            ADD `tp` float default NULL,
            ADD `tca` time NOT NULL default '00:00:00',
            ADD `creatinine` float default NULL,
            ADD `na` float default NULL,
            ADD `k` float default NULL,
            ADD `tsivy` time NOT NULL default '00:00:00',
            ADD `plaquettes` INT(7) default NULL,
            ADD `ht` float default NULL,
            ADD `ecbu` ENUM( '?', 'NEG', 'POS' ) DEFAULT '?' NOT NULL,
            ADD `ecbu_detail` TEXT,
            ADD `pouls` INT(4) default NULL,
            ADD `spo2` float default NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation_anesth` CHANGE `operation_id` `operation_id` BIGINT( 20 ) NULL DEFAULT NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation_anesth` " .
            "\nCHANGE `etatBucco` `etatBucco` TEXT DEFAULT NULL ," .
            "\nCHANGE `conclusion` `conclusion` TEXT DEFAULT NULL ";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation_anesth` " .
            "\nCHANGE `tabac` `tabac` TEXT DEFAULT NULL ," .
            "\nCHANGE `oenolisme` `oenolisme` TEXT DEFAULT NULL ";
    $this->addQuery($query);
    $query = "CREATE TABLE `exams_comp` (
               `exam_id` INT NOT NULL AUTO_INCREMENT ,
               `consult_id` INT NOT NULL ,
               `examen` TEXT NOT NULL ,
               `fait` tinyint(1) NOT NULL default 0,
               PRIMARY KEY ( `exam_id` )) /*! ENGINE=MyISAM */";
    $this->addQuery($query);
    
    $this->makeRevision("0.44");
    $this->addDependency("mediusers", "0.1");
    function setup_consultAnesth(){
      $ds = CSQLDataSource::get("std");
 
      $utypes_flip = array_flip(CUser::$types);
      $id_anesth = $utypes_flip["Anesth�siste"];
      $query = "SELECT users.user_id" .
             "\nFROM users, users_mediboard" .
             "\nWHERE users.user_id = users_mediboard.user_id" .
             "\nAND users.user_type='$id_anesth'";
      $result = $ds->loadList($query);
      $listAnesthid = array();
      foreach ($result as $keyresult => $resultAnesth) {
        $listAnesthid[$keyresult] = $result[$keyresult]["user_id"];
      } 
       
      $query = "SELECT consultation.consultation_id FROM consultation" .
             "\nLEFT JOIN consultation_anesth ON consultation.consultation_id = consultation_anesth.consultation_id" .
             "\nLEFT JOIN plageconsult ON consultation.plageconsult_id = plageconsult.plageconsult_id" .
             "\nWHERE plageconsult.chir_id " . CSQLDataSource::prepareIn($listAnesthid) .
             "\nAND consultation_anesth.consultation_anesth_id IS NULL" ;  
      $result = $ds->loadList($query);

      foreach ($result as $keyresult => $resultAnesth) {
        $consultAnesth = new CConsultAnesth;
        $consultAnesth->consultation_anesth_id = 0;
        $consultAnesth->consultation_id = $result[$keyresult]["consultation_id"];
        $consultAnesth->store();
      }
      return true;
    }
    $this->addFunction("setup_consultAnesth");
    
    $this->makeRevision("0.45");
    $query = "ALTER TABLE `exams_comp` CHANGE `consult_id` `consultation_id` INT NOT NULL ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `techniques_anesth` CHANGE `consultAnesth_id` `consultation_anesth_id` INT NOT NULL ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.46");
    $query = "ALTER TABLE `consultation_anesth` CHANGE `tca` `tca` TINYINT(2) NULL ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation_anesth` " .
            "\nADD `tca_temoin` TINYINT(2) NULL AFTER `tca`," .
            "\nADD `ht_final` FLOAT DEFAULT NULL AFTER `ht`;" ;
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation_anesth` DROP `transfusions`";
    $this->addQuery($query);
    
    $this->makeRevision("0.47");
    $query = "ALTER TABLE `consultation_anesth` CHANGE `rhesus` `rhesus` ENUM( '?', '+', '-', 'POS', 'NEG') DEFAULT '?' NOT NULL ;";
    $this->addQuery($query);
    $query = "UPDATE `consultation_anesth` SET `rhesus`='POS' WHERE `rhesus`='+';";
    $this->addQuery($query);
    $query = "UPDATE `consultation_anesth` SET `rhesus`='NEG' WHERE `rhesus`='-';";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation_anesth` CHANGE `rhesus` `rhesus` ENUM( '?', 'POS', 'NEG') DEFAULT '?' NOT NULL ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation_anesth` CHANGE `rai` `rai` ENUM( '?', 'POS', 'NEG') DEFAULT '?' NOT NULL ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation_anesth` DROP `ecbu_detail`";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation_anesth` ".
               "\nADD `premedication` TEXT," .
               "\nADD `prepa_preop` TEXT;" ;
    $this->addQuery($query);
    
    $this->makeRevision("0.48");
    $this->setTimeLimit(1800);
    $query = "ALTER TABLE `consultation_anesth` " .
               "\nCHANGE `consultation_anesth_id` `consultation_anesth_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `consultation_id` `consultation_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `operation_id` `operation_id` int(11) unsigned NULL," .
               "\nCHANGE `poid` `poid` float unsigned NULL," .
               "\nCHANGE `rhesus` `rhesus` enum('?','NEG','POS') NOT NULL DEFAULT '?'," .
               "\nCHANGE `rai` `rai` enum('?','NEG','POS') NOT NULL DEFAULT '?'," .
               "\nCHANGE `tasys` `tasys` int(5) unsigned zerofill NULL," .
               "\nCHANGE `tadias` `tadias` int(5) unsigned zerofill NULL," .
               "\nCHANGE `plaquettes` `plaquettes` int(7) unsigned zerofill NULL," .
               "\nCHANGE `pouls` `pouls` mediumint(4) unsigned zerofill NULL," .
               "\nCHANGE `ASA` `ASA` enum('1','2','3','4','5') NULL," .
               "\nCHANGE `tca` `tca` tinyint(2) unsigned zerofill NULL," .
               "\nCHANGE `tca_temoin` `tca_temoin` tinyint(2) unsigned zerofill NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation_anesth` " .
               "\nDROP `listCim10`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation` " .
               "\nCHANGE `consultation_id` `consultation_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `plageconsult_id` `plageconsult_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `patient_id` `patient_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `duree` `duree` tinyint(1) unsigned zerofill NOT NULL DEFAULT '1'," .
               "\nCHANGE `annule` `annule` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `chrono` `chrono` enum('16','32','48','64') NOT NULL DEFAULT '16'," .
               "\nCHANGE `paye` `paye` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `premiere` `premiere` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `tarif` `tarif` varchar(255) NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation` " .
               "\nDROP `compte_rendu`," .
               "\nDROP `cr_valide`," .
               "\nDROP `ordonnance`," .
               "\nDROP `or_valide`," .
               "\nDROP `courrier1`," .
               "\nDROP `c1_valide`," .
               "\nDROP `courrier2`," .
               "\nDROP `c2_valide`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `examaudio` " .
               "\nCHANGE `examaudio_id` `examaudio_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `consultation_id` `consultation_id` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `exams_comp` " .
               "\nCHANGE `exam_id` `exam_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `consultation_id` `consultation_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `fait` `fait` tinyint(4) NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `plageconsult` " .
               "\nCHANGE `plageconsult_id` `plageconsult_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `libelle` `libelle` varchar(255) NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `tarifs` " .
               "\nCHANGE `tarif_id` `tarif_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `function_id` `function_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `description` `description` varchar(255) NOT NULL," .
               "\nCHANGE `secteur1` `secteur1` float NOT NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `techniques_anesth` " .
               "\nCHANGE `technique_id` `technique_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `consultation_anesth_id` `consultation_anesth_id` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.49");
    $query = "ALTER TABLE `consultation_anesth` " .
               "\nCHANGE `tasys` `tasys` TINYINT(4) NULL," .
               "\nCHANGE `tadias` `tadias` TINYINT(4) NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.50");
    $query = "ALTER TABLE `consultation` CHANGE `patient_id` `patient_id` int(11) unsigned NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.51");
    $query = "UPDATE `consultation` SET `annule` = '0' WHERE (`annule` = '' OR `annule` IS NULL );";
    $this->addQuery($query);
    
    $this->makeRevision("0.52");
    $query = "UPDATE `consultation` SET `patient_id` = NULL WHERE (`patient_id` = 0 );";
    $this->addQuery($query);
    
    $this->makeRevision("0.53");
    $query = "CREATE TABLE `exampossum` (
                    `exampossum_id` int(11) unsigned NOT NULL auto_increment,
                    `consultation_id` int(11) unsigned NOT NULL DEFAULT '0',
                    `age` enum('inf60','61','sup71') NULL,
                    `ouverture_yeux` enum('spontane','bruit','douleur','jamais') NULL,
                    `rep_verbale` enum('oriente','confuse','inapproprie','incomprehensible','aucune') NULL,
                    `rep_motrice` enum('obeit','oriente','evitement','decortication','decerebration','rien') NULL,
                    `signes_respiratoires` enum('aucun','dyspnee_effort','bpco_leger','dyspnee_inval','bpco_modere','dyspnee_repos','fibrose') NULL,
                    `uree` enum('inf7.5','7.6','10.1','sup15.1') NULL,
                    `freq_cardiaque` enum('inf39','40','50','81','101','sup121') NULL,
                    `signes_cardiaques` enum('aucun','diuretique','antiangineux','oedemes','cardio_modere','turgescence','cardio') NULL,
                    `hb` enum('inf9.9','10','11.5','13','16.1','17.1','sup18.1') NULL,
                    `leucocytes` enum('inf3000','3100','4000','10100','sup20100') NULL,
                    `ecg` enum('normal','fa','autre','sup5','anomalie') NULL,
                    `kaliemie` enum('inf2.8','2.9','3.2','3.5','5.1','5.4','sup6.0') NULL,
                    `natremie` enum('inf125','126','131','sup136') NULL,
                    `pression_arterielle` enum('inf89','90','100','110','131','sup171') NULL,
                    `gravite` enum('min','moy','maj','maj+') NULL,
                    `nb_interv` enum('1','2','sup2') NULL,
                    `pertes_sanguines` enum('inf100','101','501','sup1000') NULL,
                    `contam_peritoneale` enum('aucune','mineure','purulente','diffusion') NULL,
                    `cancer` enum('absense','tumeur','ganglion','metastases') NULL,
                    `circonstances_interv` enum('reglee','urg','prgm','sansdelai') NULL,
                    PRIMARY KEY  (`exampossum_id`),
                    KEY `consultation_id` (`consultation_id`)
                    ) /*! ENGINE=MyISAM */ COMMENT='Table pour le calcul possum';";
    $this->addQuery($query);
    
    $this->makeRevision("0.54");
    $query = "CREATE TABLE `examnyha` (
                    `examnyha_id` int(11) unsigned NOT NULL auto_increment,
                    `consultation_id` int(11) unsigned NOT NULL DEFAULT '0',
                    `q1` enum('0','1') NULL,
                    `q2a` enum('0','1') NULL,
                    `q2b` enum('0','1') NULL,
                    `q3a` enum('0','1') NULL,
                    `q3b` enum('0','1') NULL,
                    `hesitation` enum('0','1') NOT NULL DEFAULT '0',
                    PRIMARY KEY  (`examnyha_id`),
                    KEY `consultation_id` (`consultation_id`)
                    ) /*! ENGINE=MyISAM */ COMMENT='Table pour la classe NYHA';";
    $this->addQuery($query);
    
    $this->makeRevision("0.55");
    $query = "ALTER TABLE `consultation_anesth` ADD `listCim10` TEXT DEFAULT NULL ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.56");
    $this->addDependency("dPplanningOp", "0.63");
    function setup_cleanOperationIdError(){
      $ds = CSQLDataSource::get("std");
      $where = array();
      $where["consultation_anesth.operation_id"] = "!= 0";
      $where[] = "consultation_anesth.operation_id IS NOT NULL";
      $where[] = "(SELECT COUNT(operations.operation_id) FROM operations WHERE operation_id=consultation_anesth.operation_id)=0";
      
      $query = new CRequest();
      $query->addSelect("consultation_anesth_id");
      $query->addTable("consultation_anesth");
      $query->addWhere($where);
      $aKeyxAnesth = $ds->loadColumn($query->makeSelect());
      if ($aKeyxAnesth === false) {
        return false;
      }
      if (count($aKeyxAnesth)) {
        $query = "UPDATE consultation_anesth SET operation_id = NULL WHERE (consultation_anesth_id ".
          CSQLDataSource::prepareIn($aKeyxAnesth).")";
        if (!$ds->exec($query)) {
          return false;
        }
        return true;
      }
      return true;
    }
    $this->addFunction("setup_cleanOperationIdError");
    
    $this->makeRevision("0.57");
    $this->setTimeLimit(1800);
    $query = "ALTER TABLE `consultation` ADD INDEX ( `heure` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation` ADD INDEX ( `annule` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation` ADD INDEX ( `paye` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation` ADD INDEX ( `date_paiement` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `plageconsult` ADD INDEX ( `date` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `plageconsult` ADD INDEX ( `debut` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `plageconsult` ADD INDEX ( `fin` )";
    $this->addQuery($query);
    
    $this->makeRevision("0.58");
    $this->setTimeLimit(1800);
    $this->addDependency("dPpatients", "0.41");
    $query = "INSERT INTO antecedent
            SELECT '', consultation_anesth.consultation_anesth_id, antecedent.type,
              antecedent.date, antecedent.rques, 'CConsultAnesth'
            FROM antecedent, consultation_anesth, consultation
            WHERE antecedent.object_class = 'CPatient'
              AND antecedent.object_id = consultation.patient_id
              AND consultation.consultation_id = consultation_anesth.consultation_id";
    $this->addQuery($query);
    $query = "INSERT INTO traitement
            SELECT '', consultation_anesth.consultation_anesth_id, traitement.debut,
              traitement.fin, traitement.traitement, 'CConsultAnesth'
            FROM traitement, consultation_anesth, consultation
            WHERE traitement.object_class = 'CPatient'
              AND traitement.object_id = consultation.patient_id
              AND consultation.consultation_id = consultation_anesth.consultation_id";
    $this->addQuery($query);
    $query = "UPDATE consultation_anesth, consultation, patients
            SET consultation_anesth.listCim10 = patients.listCim10
            WHERE consultation_anesth.consultation_id = consultation.consultation_id
              AND consultation.patient_id = patients.patient_id";
    $this->addQuery($query);
    
    $this->makeRevision("0.59");
    $query = "ALTER TABLE `exams_comp` ADD `realisation` ENUM( 'avant', 'pendant' ) NOT NULL DEFAULT 'avant' AFTER `consultation_id`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.60");
    $query = "CREATE TABLE `addiction` (
            `addiction_id` int(11) unsigned NOT NULL auto_increment,
            `object_id` int(11) unsigned NOT NULL default '0',
            `object_class` enum('CConsultAnesth') NOT NULL default 'CConsultAnesth',
            `type` enum('tabac', 'oenolisme', 'cannabis') NOT NULL default 'tabac',
            `addiction` text,
            PRIMARY KEY  (`addiction_id`)
            ) /*! ENGINE=MyISAM */ COMMENT = 'Addictions pour le dossier anesth�sie';";
    $this->addQuery($query);
    
    $this->makeRevision("0.61");
    $this->addPrefQuery("DefaultPeriod", "month");
    
    $this->makeRevision("0.62");
    $query = "ALTER TABLE `tarifs` " .
           "\nCHANGE `chir_id` `chir_id` int(11) unsigned NULL DEFAULT NULL," .
           "\nCHANGE `function_id` `function_id` int(11) unsigned NULL DEFAULT NULL;";
    $this->addQuery($query);
    $query = "UPDATE `tarifs` SET function_id = NULL WHERE function_id='0';";
    $this->addQuery($query);
    $query = "UPDATE `tarifs` SET chir_id = NULL WHERE chir_id='0';";
    $this->addQuery($query);
    $query = "DELETE FROM `consultation_anesth` WHERE `consultation_id`= '0'";
    $this->addQuery($query);
    $query = "UPDATE `consultation_anesth` SET operation_id = NULL WHERE operation_id='0';";
    $this->addQuery($query);
    $query = "DELETE FROM `exams_comp` WHERE `consultation_id`= '0'";
    $this->addQuery($query);
    
    $this->makeRevision("0.63");
    $this->addPrefQuery("simpleCabinet", "0");
    
    $this->makeRevision("0.64");
    $this->addPrefQuery("GestionFSE", "0");
    
    $this->makeRevision("0.65");
    $this->addPrefQuery("DossierCabinet", "dPcabinet");
    
    $this->makeRevision("0.66");
    $query = "UPDATE `consultation` SET  `rques` = NULL  WHERE `rques` = 'NULL'";
    $this->addQuery($query);
    $query = "UPDATE `consultation` SET  `motif` = NULL  WHERE `motif` = 'NULL'";
    $this->addQuery($query);
    $query = "UPDATE `consultation` SET  `traitement` = NULL  WHERE `traitement` = 'NULL'";
    $this->addQuery($query);
    $query = "UPDATE `consultation` SET  `examen` = NULL  WHERE `examen` = 'NULL'";
    $this->addQuery($query);

    $this->makeRevision("0.67");
    $query = "ALTER TABLE `consultation` ADD `codes_ccam` VARCHAR(255);";
    $this->addQuery($query);
    
    $this->makeRevision("0.68");
    $this->addPrefQuery("ccam", "0");
    
    $this->makeRevision("0.69");
    $query = "ALTER TABLE `tarifs` ADD `codes_ccam` VARCHAR(255);";
    $this->addQuery($query);
    
    $this->makeRevision("0.70");
    $query = "UPDATE `consultation_anesth` SET  `plaquettes` = `plaquettes`/1000";
    $this->addQuery($query);
    
    $this->makeRevision("0.71");
    $query = "ALTER TABLE `consultation_anesth` " .
           "CHANGE `plaquettes` `plaquettes` int(4) unsigned zerofill NULL";
    $this->addQuery($query);
    
    $this->makeRevision("0.72");
    $query = "CREATE TABLE `banque` (
             `banque_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
             `nom` VARCHAR(255) NOT NULL, 
             `description` VARCHAR(255), 
              PRIMARY KEY (`banque_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.73");
    $query = "ALTER TABLE `consultation` ADD `banque_id` INT(11) UNSIGNED;";
    $this->addQuery($query);

    $this->makeRevision("0.74");
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'AXA Banque', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Banque accord', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'LCL', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Banque Populaire', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Natexis', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'La banque Postale', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'BNP Paribas', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Caisse d\'epargne', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Ixis', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Oc�or', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Banque Palatine', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Cr�dit Foncier', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Compagnie 1818', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Caisse des d�p�ts', 'Caisse des d�p�ts et consignations') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Cr�dit Agricole', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'HSBC', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Cr�dit coop�ratif', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Cr�dit Mutuel', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'CIC', 'Cr�dit Industriel et Commercial') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Dexia', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Soci�t� g�n�rale', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Groupama Banque', '') ;";
    $this->addQuery($query); 
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Cr�dit du Nord', '') ;"; 
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Banque Courtois', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Banque Tarneaud', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Banque Kolb', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Banque Laydernier', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Banque Nuger', '') ;";
    $this->addQuery($query);
    $query = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Banque Rh�ne-Alpes', '') ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.75");
    $query = "CREATE TABLE `consultation_cat` (
            `categorie_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
            `function_id` INT(11) UNSIGNED NOT NULL, 
            `nom_categorie` VARCHAR(255) NOT NULL, 
            `nom_icone` VARCHAR(255) NOT NULL, 
             PRIMARY KEY (`categorie_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.76");
    $query = "ALTER TABLE `consultation`
      ADD `categorie_id` INT(11) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("0.77");
    // Tranfert des addictions tabac vers la consultation d'anesth�sie
    $query = "INSERT INTO `addiction` ( `addiction_id` , `object_id` , `object_class` , `type` , `addiction` )
      SELECT null,`consultation_anesth_id`, 'CConsultAnesth', 'tabac', `tabac` 
      FROM `consultation_anesth` 
      WHERE `tabac` IS NOT NULL
      AND `tabac` <> ''";
    $this->addQuery($query);

    // Tranfert des addictions tabac vers le dossier patient
    $query = "INSERT INTO `addiction` ( `addiction_id` , `object_id` , `object_class` , `type` , `addiction` )
      SELECT null,`patient_id`, 'CPatient', 'tabac', `tabac`
      FROM `consultation_anesth`, `consultation`
      WHERE `tabac` IS NOT NULL
      AND `tabac` <> ''
      AND `consultation`.`consultation_id` = `consultation_anesth`.`consultation_id`";
    $this->addQuery($query);

    // Tranfert des addictions oenolisme vers la consultation d'anesth�sie
    $query = "INSERT INTO `addiction` ( `addiction_id` , `object_id` , `object_class` , `type` , `addiction` )
      SELECT null,`consultation_anesth_id`, 'CConsultAnesth', 'oenolisme', `oenolisme` 
      FROM `consultation_anesth` 
      WHERE `oenolisme` IS NOT NULL
      AND `oenolisme` <> ''";
    $this->addQuery($query);

    // Tranfert des addictions oenolisme vers le dossier patient
    $query = "INSERT INTO `addiction` ( `addiction_id` , `object_id` , `object_class` , `type` , `addiction` )
      SELECT null,`patient_id`, 'CPatient', 'oenolisme', `oenolisme`
      FROM `consultation_anesth`, `consultation`
      WHERE `oenolisme` IS NOT NULL
      AND `oenolisme` <> ''
      AND `consultation`.`consultation_id` = `consultation_anesth`.`consultation_id`";
    $this->addQuery($query);

    // Transfert des aides � la saisie
    // @todo : A v�rifier
    /*$this->addDependency("dPcompteRendu", "0.30", true);
    $query = "UPDATE `aide_saisie`
      SET `class`='CAddiction',`depend_value`=`field`, `field`='addiction'
      WHERE `class` = 'CConsultAnesth'
      AND `field` IN('oenolisme','tabac')";
    $this->addQuery($query);*/
    
    $this->makeRevision("0.78");
    $query = "ALTER TABLE `consultation` ADD `adresse` enum('0','1') NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    
    
    $this->makeRevision("0.79");
    // Ne pas supprimer le champs listCim10 de la consultAnesth afin d'avoir fait l'import dans dPpatient
    $this->addDependency("dPpatients", "0.51");
    $query = "ALTER TABLE `consultation_anesth`
            DROP `listCim10`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.80");
    $query = "CREATE TABLE `acte_ngap` (
            `acte_ngap_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
            `code` VARCHAR(3) NOT NULL, 
            `quantite` INT(11) NOT NULL, 
            `coefficient` FLOAT NOT NULL, 
            `consultation_id` INT(11) UNSIGNED NOT NULL, 
            PRIMARY KEY (`acte_ngap_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.81");
    $query = "ALTER TABLE `tarifs` ADD `codes_ngap` VARCHAR(255);";
    $this->addQuery($query);
    
    $this->makeRevision("0.82");
    $query = "ALTER TABLE `acte_ngap`
            ADD `montant_depassement` FLOAT, 
            ADD `montant_base` FLOAT;";
    $this->addQuery($query);
    
    $this->makeRevision("0.83");
    $query = "ALTER TABLE `consultation`
            ADD `valide` ENUM('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $query = "UPDATE `consultation`
            SET `valide` = '1'
             WHERE `tarif` IS NOT NULL;";
    $this->addQuery($query);
     
    
    $this->makeRevision("0.84");
    $query = "ALTER TABLE `consultation`
            CHANGE `type_tarif` `mode_reglement` ENUM( 'cheque', 'CB', 'especes', 'tiers', 'autre' ),
            CHANGE `paye` `patient_regle` ENUM('0','1');";
    $this->addQuery($query);
         
    $query = "ALTER TABLE `consultation`
            ADD `total_amc` FLOAT,
            ADD `total_amo` FLOAT,
            ADD `total_assure` FLOAT,
            ADD `facture_acquittee` ENUM('0','1'), 
            ADD `a_regler` FLOAT DEFAULT '0.0';"; 
    $this->addQuery($query); 
       
    $query = "UPDATE `consultation`
            SET `a_regler` = `secteur1` + `secteur2`
            WHERE `mode_reglement` <> 'tiers'
            OR `mode_reglement` IS NULL;";
    $this->addQuery($query);
    
    $query = "UPDATE `consultation`
            SET `patient_regle` = '1'
            WHERE `mode_reglement` = 'tiers';";
    $this->addQuery($query);
    
    $query = "UPDATE `consultation`
            SET `facture_acquittee` = '1'
            WHERE `a_regler` = `secteur1` + `secteur2`
            AND `patient_regle` = '1';";
    $this->addQuery($query);
    
    $this->makeRevision("0.85");
    $query = "ALTER TABLE `consultation`
            ADD `sejour_id` INT(11) UNSIGNED;";
    $this->addQuery($query);

    $this->makeRevision("0.86");
    $this->setTimeLimit(300);
    $query = "UPDATE `consultation`
            SET `patient_regle` = '1'
            WHERE ROUND(`a_regler`,2) = ROUND(0,2);";
    $this->addQuery($query);
     
    $query = "UPDATE `consultation`
            SET `facture_acquittee` = '1'
            WHERE ROUND(`a_regler`,2) = ROUND(`secteur1` + `secteur2`, 2)
            AND `patient_regle` = '1'
            AND (`facture_acquittee` <> '1'
                  OR `facture_acquittee` IS NULL);";
    $this->addQuery($query);
    
    $query = "UPDATE `consultation`, `plageconsult`
            SET `date_paiement` = `plageconsult`.`date`  
            WHERE `patient_regle` = '1'
            AND `date_paiement` IS NULL
            AND `consultation`.`plageconsult_id` = `plageconsult`.`plageconsult_id`;";
    $this->addQuery($query);
    
    $query = "UPDATE `consultation`
            SET date_paiement = NULL
            WHERE date_paiement IS NOT NULL
            AND patient_regle <> '1';";
    $this->addQuery($query);
    
    $this->makeRevision("0.87");
    $query = "ALTER TABLE `consultation` 
            CHANGE `date_paiement` `date_reglement` DATE,
            DROP `patient_regle`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.88");
    $query = "ALTER TABLE `acte_ngap`
            CHANGE `consultation_id` `object_id` INT(11) UNSIGNED NOT NULL, 
            ADD `object_class` ENUM('COperation','CSejour','CConsultation') NOT NULL default 'CConsultation';";
    $this->addQuery($query);
    
    $this->makeRevision("0.89");
    $query = "UPDATE `consultation`
            SET `date_reglement` = NULL, `facture_acquittee` = NULL
            WHERE `a_regler` = '0'
            AND (`mode_reglement` IS NULL OR `mode_reglement` = '');";
    $this->addQuery($query);
    
    $this->makeRevision("0.90");
    $query = "ALTER TABLE `consultation` 
            CHANGE `facture_acquittee` `reglement_AM` ENUM('0','1');";
    $this->addQuery($query);
    
    $query = "UPDATE `consultation`
            SET `reglement_AM` = '1'
            WHERE ROUND(`a_regler`,2) = ROUND(`secteur1` + `secteur2`, 2)
            AND `valide` = '1';";
    $this->addQuery($query);
    
    $this->makeRevision("0.91");
    $query = "DELETE FROM `user_preferences` 
      WHERE `key` = 'ccam_consultation'";
    $this->addQuery($query);
    $query = "UPDATE `user_preferences`
      SET `key` = 'ccam_consultation'
      WHERE `key` = 'ccam'";
    $this->addQuery($query);
    
    $this->makeRevision("0.92");
    $query = "ALTER TABLE `acte_ngap` 
            ADD `demi` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.93");
    $query = "CREATE TABLE `examigs` (
           `examigs_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `consultation_id` INT(11) UNSIGNED NOT NULL, 
           `age` ENUM('0','7','12','15','16','18'), 
           `FC` ENUM('11','2','0','4','7'), 
           `TA` ENUM('13','5','0','2'), 
           `temperature` ENUM('0','3'), 
           `PAO2_FIO2` ENUM('11','9','6'), 
           `diurese` ENUM('12','4','0'), 
           `uree` ENUM('0','6','10'), 
           `globules_blancs` ENUM('12','0','3'), 
           `kaliemie` ENUM('3a','0','3b'), 
           `natremie` ENUM('5','0','1'), 
           `HCO3` ENUM('6','3','0'), 
           `billirubine` ENUM('0','4','9'), 
           `glascow` ENUM('26','13','7','5','0'), 
           `maladies_chroniques` ENUM('9','10','17'), 
           `admission` ENUM('0','6','8'), 
           `scoreIGS` INT(11), 
             PRIMARY KEY (`examigs_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    
    
    $this->makeRevision("0.94");
    CApp::setTimeLimit(180);
    
    // Ajout de du_tiers
    $query = "ALTER TABLE `consultation`
            ADD `du_tiers` FLOAT DEFAULT 0.0";
    $this->addQuery($query);
    
    // Calcul de du_tiers
    $query = "UPDATE `consultation`
            SET `du_tiers` = ROUND(`secteur1` + `secteur2` - `a_regler`, 2);";
    $this->addQuery($query);
    
    // mode_reglement � NULL quand mode_reglement = tiers
    $query = "UPDATE `consultation`
            SET `mode_reglement` = ''
            WHERE `mode_reglement` = 'tiers';";
    $this->addQuery($query);
    
    // Modification de l'enum de mode_reglement
    $query = "ALTER TABLE `consultation`
            CHANGE `mode_reglement` `mode_reglement` ENUM('cheque','CB','especes','virement','autre');";
    $this->addQuery($query);
    
    // date_reglement => patient_date_reglement
    $query = "ALTER TABLE `consultation` 
            CHANGE `date_reglement` `patient_date_reglement` DATE;";
    $this->addQuery($query);
    
    // mode_reglement => patient_mode_reglement
    $query = "ALTER TABLE `consultation` 
            CHANGE `mode_reglement` `patient_mode_reglement` ENUM('cheque','CB','especes','virement','autre');";
    $this->addQuery($query);
    
    // a_regler => du_patient
    $query = "ALTER TABLE `consultation`
            CHANGE `a_regler` `du_patient` FLOAT DEFAULT '0.0';";
    $this->addQuery($query);
    
    // Creation d'un tiers_mode_reglement
    $query = "ALTER TABLE `consultation`
            ADD `tiers_mode_reglement` ENUM('cheque','CB','especes','virement','autre');";
    $this->addQuery($query);
    
    // Creation d'un tiers_date_reglement
    $query = "ALTER TABLE `consultation`
            ADD `tiers_date_reglement` DATE;";
    $this->addQuery($query);
     
    // On considere que toutes les anciennes consultations ont reglement_AM � 1
    $query = "UPDATE `consultation`, `plageconsult`
            SET `reglement_AM` = '1'  
            WHERE `consultation`.`plageconsult_id` = `plageconsult`.`plageconsult_id`
            AND `plageconsult`.`date` < '2007-12-01';";
    $this->addQuery($query);
         
    // On met � jour reglement_AM (reglement_AM � 0 si pas de du_tiers)
    $query = "UPDATE `consultation`
            SET `reglement_AM` = '0'
            WHERE ROUND(`du_tiers`,2)  = ROUND(0,2);";
    $this->addQuery($query);
    
    // Mise � jour des reglements AM � 1
    $query = "UPDATE `consultation`, `plageconsult`
            SET `tiers_mode_reglement` = 'virement',
                `tiers_date_reglement` = `plageconsult`.`date`
            WHERE `consultation`.`plageconsult_id` = `plageconsult`.`plageconsult_id`
            AND `consultation`.`reglement_AM` = '1';";
    $this->addQuery($query);
   
    // Suppression du champ reglement_AM
    $query = "ALTER TABLE `consultation`
            DROP `reglement_AM`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.95");
    $query = "UPDATE consultation
            SET patient_date_reglement = NULL, patient_mode_reglement = NULL
            WHERE du_patient = 0;";
    $this->addQuery($query);

    $this->makeRevision("0.96");
    $query = "UPDATE consultation 
            SET valide = '0' 
            WHERE valide = '';";
    $this->addQuery($query);
    
    $this->makeRevision("0.97");
    $query = "ALTER TABLE `consultation`
            ADD `accident_travail` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.98");
    $this->addPrefQuery("view_traitement", "1");
    
    $this->makeRevision("0.99");
    // Table temporaire contenant les consultation_id des accident_travail � 1
    $query = "CREATE TEMPORARY TABLE tbl_accident_travail (
             consultation_id INT( 11 )
            ) AS 
              SELECT consultation_id
              FROM `consultation`
              WHERE accident_travail = '1';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `consultation`
            CHANGE `accident_travail` `accident_travail` DATE DEFAULT NULL";
    $this->addQuery($query);
    
    $query = "UPDATE `consultation`, `plageconsult`, `tbl_accident_travail`
            SET `consultation`.`accident_travail` = `plageconsult`.`date`
            WHERE `consultation`.`plageconsult_id` = `plageconsult`.`plageconsult_id`
            AND `consultation`.`consultation_id` = `tbl_accident_travail`.`consultation_id`;";
    $this->addQuery($query);
    
    $query = "UPDATE `consultation`
            SET accident_travail = NULL
            WHERE accident_travail = '0000-00-00';";
    $this->addQuery($query);
    
    $this->makeRevision("1.00");
    $this->addPrefQuery("autoCloseConsult", "0");
    
    $this->makeRevision("1.01");
    $query = "ALTER TABLE `acte_ngap` 
            ADD `complement` ENUM('N','F','U');";
    $this->addQuery($query);
    
    $this->makeRevision("1.02");
    $query = "ALTER TABLE `consultation_anesth`
            ADD `sejour_id` INT(11) UNSIGNED AFTER `operation_id`";
    $this->addQuery($query);
    
    $this->makeRevision("1.03");
    $query = "ALTER TABLE `consultation_anesth` 
      ADD `examenCardio` TEXT NULL AFTER `etatBucco` ,
      ADD `examenPulmo` TEXT NULL AFTER `examenCardio` ;";
    $this->addQuery($query);
    
    $this->makeRevision("1.04");
    $query = "CREATE TABLE `reglement` (
      `reglement_id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
      `consultation_id` INT( 11 ) UNSIGNED NOT NULL ,
      `banque_id` INT( 11 ) UNSIGNED ,
      `date` DATETIME NOT NULL ,
      `montant` FLOAT NOT NULL ,
      `emetteur` ENUM( 'patient', 'tiers' ) ,
      `mode` ENUM( 'cheque', 'CB', 'especes', 'virement', 'autre' ) ,
      PRIMARY KEY ( `reglement_id` )
      ) /*! ENGINE=MyISAM */ ;";
    $this->addQuery($query);
    
    // On cr�e les r�glements des patients
    $query = "INSERT INTO `reglement` (
      `emetteur`,
      `consultation_id`, 
      `banque_id`, 
      `date`, 
      `montant`,
      `mode`)
      
      SELECT 
        'patient',
        `consultation_id`, 
        `banque_id`, 
        `patient_date_reglement`, 
        `du_patient`, 
        `patient_mode_reglement`
      FROM 
        `consultation`
      WHERE 
        `patient_date_reglement` IS NOT NULL;";
    $this->addQuery($query);
    
    
    // On cr�e les r�glements des tiers
    $query = "INSERT INTO `reglement` (
      `emetteur`,
      `consultation_id`, 
      `date`, 
      `montant`,
      `mode`)
      
      SELECT 
        'tiers',
        `consultation_id`, 
        `tiers_date_reglement`, 
        `du_tiers`, 
        `tiers_mode_reglement`
      FROM 
        `consultation`
      WHERE 
        `tiers_date_reglement` IS NOT NULL;";
    $this->addQuery($query);

    $this->makeRevision("1.05");
    $query = "ALTER TABLE `acte_ngap` 
      ADD `executant_id` int(11) unsigned NOT NULL DEFAULT '0',
      ADD INDEX (`executant_id`)";
    $this->addQuery($query);
    
    // COperation : executant_id = operations -> chir_id
    // CSejour : executant_id = sejour -> praticien_id
    // CConsultation : executant_id = consultation -> plageconsult -> chir_id
    $query = "UPDATE `acte_ngap` 
       SET `acte_ngap`.`executant_id` = 
        (SELECT `chir_id` 
         FROM `operations` 
         WHERE `operations`.`operation_id` = `acte_ngap`.`object_id`
         LIMIT 1)
       WHERE 
        `acte_ngap`.`object_class` = 'COperation' AND 
        `acte_ngap`.`executant_id` = 0";
    $this->addQuery($query);
    
    $query = "UPDATE `acte_ngap` 
       SET `acte_ngap`.`executant_id` = 
        (SELECT `praticien_id` 
         FROM `sejour` 
         WHERE `sejour`.`sejour_id` = `acte_ngap`.`object_id`
         LIMIT 1)
       WHERE 
        `acte_ngap`.`object_class` = 'CSejour' AND 
        `acte_ngap`.`executant_id` = 0";
    $this->addQuery($query);
    
    $query = "UPDATE `acte_ngap` 
       SET `acte_ngap`.`executant_id` = 
        (SELECT `plageconsult`.`chir_id` 
         FROM `plageconsult`, `consultation`
         WHERE 
           `consultation`.`consultation_id` = `acte_ngap`.`object_id` AND
           `plageconsult`.`plageconsult_id` = `consultation`.`plageconsult_id`
         LIMIT 1)
       WHERE 
        `acte_ngap`.`object_class` = 'CConsultation' AND 
        `acte_ngap`.`executant_id` = 0";
    $this->addQuery($query);
    
    $this->makeRevision("1.07");
    $this->addPrefQuery("resumeCompta", "1");
    
    $this->makeRevision("1.08");
    $this->addPrefQuery("VitaleVisionDir", "");
    $this->addPrefQuery("VitaleVision", "0");
    
    $this->makeRevision("1.09");
    $query = "UPDATE `consultation` 
      SET du_tiers = ROUND(secteur1 + secteur2 - du_patient, 2)
      WHERE ROUND(secteur1 + secteur2 - du_tiers - du_patient, 2) != 0
      AND ABS(ROUND(secteur1 + secteur2 - du_tiers - du_patient, 2)) > 1
      AND valide = '1'";
    $this->addQuery($query);
    
    $this->makeRevision("1.10");
    $this->addPrefQuery("showDatesAntecedents", "1");
    
    $this->makeRevision("1.11");
    $query = "ALTER TABLE `consultation_anesth` 
              ADD `chir_id` INT (11) UNSIGNED AFTER `sejour_id`,
              ADD `date_interv` DATE AFTER `chir_id`,
              ADD `libelle_interv` VARCHAR (255) AFTER `date_interv`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `consultation_anesth` 
              ADD INDEX (`sejour_id`),
              ADD INDEX (`chir_id`),
              ADD INDEX (`date_interv`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.12");
    $query = "ALTER TABLE `consultation`
              ADD `histoire_maladie` TEXT AFTER `traitement`,
              ADD `conclusion` TEXT AFTER `histoire_maladie`";
    $this->addQuery($query);

    $this->makeRevision("1.13");
    $query = "ALTER TABLE `consultation` 
            ADD `concerne_ALD` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("1.14");
    $query = "ALTER TABLE `consultation_anesth` 
            ADD `date_analyse` DATE;";
    $this->addQuery($query);
        
    $this->makeRevision("1.15");
    $query = "ALTER TABLE `consultation` 
              ADD `facture` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);  
    
    $this->makeRevision("1.16");
    $this->addPrefQuery("dPcabinet_show_program", "1");
    
    
    $this->makeRevision("1.17");
    $query = "ALTER TABLE `acte_ngap` 
              ADD INDEX (`object_id`),
              ADD INDEX (`object_class`);";
    $this->addQuery($query);
              
    $query = "ALTER TABLE `consultation` 
              ADD INDEX (`sejour_id`),
              ADD INDEX (`tiers_date_reglement`),
              ADD INDEX (`arrivee`),
              ADD INDEX (`categorie_id`),
              ADD INDEX (`accident_travail`);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `consultation_anesth` 
              ADD INDEX (`date_analyse`);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `consultation_cat` 
              ADD INDEX (`function_id`);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `examigs` 
              ADD INDEX (`consultation_id`);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `exams_comp` 
              ADD INDEX (`consultation_id`);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `reglement` 
              ADD INDEX (`consultation_id`),
              ADD INDEX (`banque_id`),
              ADD INDEX (`date`);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `techniques_anesth` 
              ADD INDEX (`consultation_anesth_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.18");
    $this->addPrefQuery("pratOnlyForConsult", "1");
    
    $this->makeRevision("1.19");
    $query = "ALTER TABLE `consultation_anesth` 
      DROP `intubation`";
    $this->addQuery($query);
    
    $this->makeRevision("1.20");
    $query = "UPDATE plageconsult SET fin = '23:59:59' WHERE fin = '00:00:00'";
    $this->addquery($query);
    
    $this->makeRevision("1.21");
    $query = "ALTER TABLE `consultation_anesth`
              DROP `biologie`,
              DROP `commande_sang`;";
    $this->addquery($query);
    
    $this->makeRevision("1.22");
    $query = "ALTER TABLE `consultation_anesth`
              ADD `groupe_ok` ENUM ('0','1') NOT NULL DEFAULT '0' AFTER `groupe`,
              ADD `fibrinogene` FLOAT AFTER `creatinine`,
              ADD `result_ecg` TEXT AFTER `ht_final`,
              ADD `result_rp` TEXT AFTER `result_ecg`;";
    $this->addquery($query);
    
    $this->makeRevision("1.23");
    $query = "ALTER TABLE `consultation`
              ADD `adresse_par_prat_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("1.24");
    $query = "ALTER TABLE `consultation` ADD `si_desistement` ENUM ('0','1') NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("1.25");
    $query = "ALTER TABLE `plageconsult` ADD `locked` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("1.26");
    $this->addPrefQuery("AFFCONSULT", "0");
    $this->addPrefQuery("MODCONSULT", "0");

     
    $this->makeRevision("1.27");
    $query = "ALTER TABLE `acte_ngap` ADD `lettre_cle` ENUM ('0','1') NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    
    $query = "UPDATE `acte_ngap` 
       SET `acte_ngap`.`lettre_cle` = '1'
       WHERE 
        `acte_ngap`.`code` IN ('C','K','KA','KC','KCC','KE','KFA','KFB','KFD','ORT','PRO','PRA'
                               'SCM','V','Z','ZN','LC','LCM','LFA','LFB','LFD','LK','LKC','LKE'
                               'LRA','LRO','LV','LZ','LZM','LZN','CS','VS','LCC','LCS','LVS',
                               'AMI','AIS','DI')";
    $this->addQuery($query);
    
    $this->makeRevision("1.28");
    $query = "ALTER TABLE `consultation` CHANGE `accident_travail` `date_at` DATE DEFAULT NULL";
    $this->addQuery($query);
    
    $this->makeRevision("1.29");
    $query = "ALTER TABLE `consultation`
      ADD `fin_at` DATETIME DEFAULT NULL,
      ADD `pec_at` ENUM ('soins', 'arret') DEFAULT NULL,
      ADD `reprise_at` DATETIME DEFAULT NULL";
    $this->addQuery($query);
    
    $this->makeRevision("1.30");
    $query = "ALTER TABLE `plageconsult`
      ADD `remplacant_id` BIGINT DEFAULT '0' NOT NULL,
      ADD `desistee` ENUM ('0', '1') NOT NULL DEFAULT '0',
      ADD `remplacant_ok` ENUM ('0', '1') NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("1.33");
    $query = "ALTER TABLE `plageconsult`
     CHANGE `remplacant_id` `remplacant_id` BIGINT DEFAULT NULL";
    $this->addQuery($query);
    
    $this->makeRevision("1.34");
    $query = "ALTER TABLE `plageconsult`
     CHANGE `remplacant_id` `remplacant_id` INT(11) UNSIGNED";
    $this->addQuery($query);
    
    $query = "UPDATE `plageconsult`
     SET `remplacant_id`=NULL WHERE `remplacant_id` = '0'";

    $this->addQuery($query);
    $this->makeRevision("1.35");
    $this->addPrefQuery("displayDocsConsult", "1");
    $this->addPrefQuery("displayPremedConsult", "1");
    $this->addPrefQuery("displayResultsConsult", "1");
    
    $this->makeRevision("1.36");
    $query = "UPDATE `consultation`
      SET chrono = '16'
      WHERE annule = '1'";
    $this->addQuery($query);
    
    $this->makeRevision("1.37");
    $query = "ALTER TABLE `consultation_anesth`
              ADD `apfel_femme` ENUM ('0','1') DEFAULT '0',
              ADD `apfel_non_fumeur` ENUM ('0','1') DEFAULT '0',
              ADD `apfel_atcd_nvp` ENUM ('0','1') DEFAULT '0',
              ADD `apfel_morphine` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("1.38");
    $query = "ALTER TABLE `consultation_anesth` 
      ADD `examenAutre` TEXT NULL AFTER `examenPulmo` ,
      ADD `examenDigest` TEXT NULL AFTER `examenPulmo` ;";
    $this->addQuery($query);
    
    $this->makeRevision("1.39");
    $query = "ALTER TABLE `consultation` 
      ADD `type` ENUM ('classique','entree') DEFAULT 'classique'";
    $this->addQuery($query);
    
    $this->makeRevision("1.40");
   
    $query = "ALTER TABLE `tarifs` ADD `codes_tarmed` VARCHAR(255);";
    $this->addQuery($query);
    
    $this->makeRevision("1.41");
    
    $query = "ALTER TABLE `consultation` 
      ADD `grossesse_id` INT (11) UNSIGNED";
    $this->addQuery($query);
    
    $this->makeRevision("1.42");
    $query = "ALTER TABLE `consultation`
      CHANGE `type` `type` ENUM ('classique','entree','chimio') DEFAULT 'classique';";
    $this->addQuery($query);
    
    $this->makeRevision("1.43");
    $this->addPrefQuery("choosePatientAfterDate", "0");
    
    $this->makeRevision("1.44");
    
    $query = "ALTER TABLE `consultation` 
      ADD `remise`  VARCHAR(10) NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("1.45");
    $query="ALTER TABLE `consultation_cat` 
      CHANGE `function_id` `function_id` INT (11) UNSIGNED NOT NULL,
      ADD `duree` TINYINT (4) UNSIGNED NOT NULL DEFAULT '1',
      ADD `commentaire` TEXT;";
    $this->addQuery($query);
    
    $this->makeRevision("1.46");
    $query="ALTER TABLE `consultation`
      ADD `at_sans_arret` ENUM ('0','1') DEFAULT '0',
      ADD `arret_maladie` ENUM ('0','1') DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("1.47");
    
    $query = "ALTER TABLE `consultation` 
      DROP `remise`;";
    $this->addQuery($query);
    
    $this->makeRevision("1.48");
    
    $this->addPrefQuery("viewFunctionPrats", "0");
    
    $this->makeRevision("1.49");
    
    $query = "CREATE TABLE `factureconsult` (
         `factureconsult_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
         `patient_id` int(11) unsigned NOT NULL,
         `rabais` FLOAT DEFAULT '0' NOT NULL,
         `ouverture` date NOT NULL,
         `cloture` date,
         `du_patient` float NOT NULL DEFAULT '0.0',
         `du_tiers` float NOT NULL DEFAULT '0.0',
         PRIMARY KEY (`factureconsult_id`),
         INDEX (`patient_id`) )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE factureconsult 
              ADD INDEX (`ouverture`), 
              ADD INDEX (`cloture`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.50");
    
    $query = "ALTER TABLE `factureconsult` 
              ADD `type_facture` enum ('maladie','accident') NOT NULL default 'maladie';";
    $this->addQuery($query);
    
    $query="ALTER TABLE `reglement`
      CHANGE `consultation_id` `object_id` INT(11) UNSIGNED ,
      ADD `object_class`  ENUM ('CConsultation','CFactureConsult') NOT NULL default 'CConsultation';";
    $this->addQuery($query);
    
    $this->makeRevision("1.51");
    
    $query = "ALTER TABLE `consultation` 
              ADD `factureconsult_id` INT(11) UNSIGNED NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("1.52");
    
    $query = "ALTER TABLE `factureconsult` 
              ADD `patient_date_reglement` DATE,
              ADD `tiers_date_reglement` DATE;";
    $this->addQuery($query);
    
    $query="ALTER TABLE `factureconsult`
      CHANGE `rabais` `remise` FLOAT DEFAULT '0' NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("1.53");
    
    $this->addPrefQuery("viewWeeklyConsultCalendar", "0");
    
    $this->makeRevision("1.54");
    
    $query="ALTER TABLE `consultation` 
              ADD INDEX (`grossesse_id`),
              ADD INDEX (`factureconsult_id`);";
    $this->addQuery($query);
    $this->makeRevision("1.55");
    
    $query="ALTER TABLE `factureconsult` 
              CHANGE `remise` `remise` DECIMAL (10,2) DEFAULT  '0';";
    $this->addQuery($query);
    $this->makeRevision("1.56");
    
    $query = "ALTER TABLE `tarifs` ADD `codes_caisse` VARCHAR(255);";
    $this->addQuery($query);
    $this->makeRevision("1.57");
    
    $query = "ALTER TABLE `reglement` 
              CHANGE `mode` `mode` ENUM( 'cheque', 'CB', 'especes', 'virement', 'BVR', 'autre' ),
              ADD `num_bvr` VARCHAR(50);";
    $this->addQuery($query);
    
    $this->makeRevision("1.58");
        
    $query = "ALTER TABLE `plageconsult`
              ADD `color` VARCHAR(6) NOT NULL DEFAULT 'DDDDDD' ;";
    $this->addQuery($query);
    
    $this->makeRevision("1.59");
    
    $query = "ALTER TABLE `factureconsult`
              ADD `npq`  ENUM('0','1') DEFAULT '0',
              ADD `cession_creance` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.60");
    $query = "ALTER TABLE `examigs` 
              ADD `sejour_id` INT (11) UNSIGNED NOT NULL;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `examigs` ADD INDEX (`sejour_id`);";
    $this->addQuery($query);
    
    $query = "UPDATE `examigs`
              SET examigs.sejour_id = (SELECT sejour.sejour_id
                               FROM sejour
                               LEFT JOIN consultation_anesth ON sejour.sejour_id = consultation_anesth.sejour_id
                               WHERE consultation_anesth.consultation_id = examigs.consultation_id);";
    $this->addQuery($query);
    
    $query = "UPDATE `examigs`
              SET sejour_id = (SELECT sejour.sejour_id
                               FROM sejour
                               LEFT JOIN operations ON operations.sejour_id = sejour.sejour_id
                               LEFT JOIN consultation_anesth ON operations.operation_id = consultation_anesth.operation_id
                               WHERE consultation_anesth.consultation_id = examigs.consultation_id)
              WHERE examigs.sejour_id = '0';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `examigs` DROP `consultation_id`;";
    $this->addQuery($query);
    
    $this->makeRevision("1.61");
    
    $query = "UPDATE `plageconsult`
              SET color = 'DDDDDD'
              WHERE color = 'DDD'";
    $this->addQuery($query);
    
    $this->makeRevision("1.62");
    $query = "ALTER TABLE `consultation`
              ADD `derniere` ENUM ('0','1') DEFAULT '0' AFTER `premiere`;";
    $this->addQuery($query);
    
    $this->makeRevision("1.63");
    
    $query = "ALTER TABLE `factureconsult`
              ADD `facture`  ENUM('-1','0','1') DEFAULT '0',
              ADD `assurance`  INT(11) UNSIGNED NULL;";
    $this->addQuery($query);
    $this->makeRevision("1.64");
    
    $query = "ALTER TABLE `factureconsult` 
              ADD `praticien_id` INT (11) UNSIGNED AFTER `patient_id`,
              CHANGE `npq` `npq` ENUM ('0','1') NOT NULL DEFAULT '0',
              CHANGE `cession_creance` `cession_creance` ENUM ('0','1') NOT NULL DEFAULT '0',
              CHANGE `facture` `facture` ENUM ('-1','0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `factureconsult` 
              ADD INDEX (`praticien_id`),
              ADD INDEX (`ouverture`),
              ADD INDEX (`cloture`),
              ADD INDEX (`patient_date_reglement`),
              ADD INDEX (`tiers_date_reglement`),
              ADD INDEX (`assurance`);";
    $this->addQuery($query);
    
    $query = "UPDATE `factureconsult`
              SET `factureconsult`.praticien_id = (SELECT plageconsult.chir_id
                               FROM  plageconsult , consultation
                               WHERE consultation.factureconsult_id = `factureconsult`.factureconsult_id
                               AND plageconsult.plageconsult_id = consultation.plageconsult_id
                               LIMIT 1)
              WHERE factureconsult.praticien_id IS NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("1.65");
    
    $query = "ALTER TABLE `factureconsult`
              ADD `ref_accident` TEXT;";
    $this->addQuery($query);
    
    $this->makeRevision("1.66");
    $query = "ALTER TABLE `consultation`
      ADD `brancardage` TEXT;";
    
    $this->addQuery($query);
    
    $this->makeRevision("1.67");
    
    $query = "ALTER TABLE `consultation_anesth`
      ADD `intub_difficile` ENUM ('0','1');";
    $this->addQuery($query);
    
    $this->makeRevision("1.68");
    $query = "ALTER TABLE `examigs` CHANGE `diurese` `diurese` ENUM('11','12','4','0');";
    $this->addQuery($query);
    
    $this->makeRevision("1.69");
    
    $query = "ALTER TABLE `examigs` 
      ADD `date` DATETIME AFTER examigs_id";
    $this->addQuery($query);
    
    $this->makeRevision("1.70");
    
    $query = "ALTER TABLE `plageconsult` 
                ADD `pct_retrocession` INT (11) DEFAULT '70';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `plageconsult` 
                ADD INDEX (`remplacant_id`);";
    $this->addQuery($query);
    $this->makeRevision("1.71");
    
    $query = "ALTER TABLE `plageconsult` 
                CHANGE `pct_retrocession` `pct_retrocession` FLOAT DEFAULT '70';";
    $this->addQuery($query);
    
    $this->makeRevision("1.72");
    $query = "ALTER TABLE `examigs` 
                CHANGE `glascow` `glasgow` ENUM('26','13','7','5','0');";
    $this->addQuery($query);
    
    $this->makeRevision("1.73");
    $this->addPrefQuery("empty_form_atcd", "0");
    
    $this->makeRevision("1.74");
    $this->addPrefQuery("new_semainier", "0");
    
    $this->makeRevision("1.75");
    $query = "UPDATE consultation, factureconsult
                SET consultation.patient_date_reglement = factureconsult.patient_date_reglement
                WHERE consultation.factureconsult_id = factureconsult.factureconsult_id";
    $this->addQuery($query);
    
    $this->makeRevision("1.76");
    $query = "ALTER TABLE `reglement` 
                ADD `reference` VARCHAR (255)";
    $this->addQuery($query);
        
    $this->makeRevision("1.77");
    $query = "ALTER TABLE `factureconsult` 
              ADD `statut_pro` ENUM ('chomeur','salarie','independant','non_travailleur','sans_emploi','etudiant');";
    $this->addQuery($query);
    $this->makeRevision("1.78");
    
    $query = "ALTER TABLE `plageconsult` 
              ADD `pour_compte_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `plageconsult` 
              ADD INDEX (`pour_compte_id`);";
    $this->addQuery($query);
        
    $this->makeRevision("1.79");
    
    $query = "ALTER TABLE `factureconsult` 
              CHANGE `assurance` `assurance_base` INT (11) UNSIGNED,
              ADD `assurance_complementaire` INT (11) UNSIGNED,
              CHANGE `statut_pro` `statut_pro` ENUM ('chomeur','etudiant','non_travailleur','independant','salarie','sans_emploi');";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `factureconsult` 
              ADD INDEX (`assurance_base`),
              ADD INDEX (`assurance_complementaire`);";
    $this->addQuery($query);
    $this->makeRevision("1.80");
    
    $query = "ALTER TABLE `factureconsult` DROP INDEX `assurance`";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `factureconsult` 
              ADD `send_assur_base` ENUM ('0','1') DEFAULT '0',
              ADD `send_assur_compl` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    $this->makeRevision("1.81");
    
    $query = "ALTER TABLE `factureconsult` 
              ADD `num_reference` VARCHAR (27);";
    $this->addQuery($query);
    
    $this->makeRevision("1.82");
    $this->addPrefQuery("order_mode_grille", "");

    $this->makeRevision("1.83");

    $query = "ALTER TABLE `acte_ngap`
                ADD `facturable` ENUM ('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($query);
    $this->makeRevision("1.84");

    $query = "ALTER TABLE `factureconsult` 
              CHANGE `assurance_base` `assurance_maladie` INT (11) UNSIGNED,
              CHANGE `assurance_complementaire` `assurance_accident` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("1.85");

    $this->addDependency("dPplanningOp", "1.70");
    $query = "ALTER TABLE `consultation_anesth` 
              DROP `position`,
              DROP `ASA`;";
    $this->addQuery($query);
    $this->makeRevision("1.86");

    $query = "ALTER TABLE `factureconsult` 
              ADD `envoi_xml` ENUM ('0','1') DEFAULT '1',
              CHANGE `factureconsult_id` `facture_id` INT (11) UNSIGNED NOT NULL auto_increment,
              ADD `rques_assurance_maladie` TEXT,
              ADD `rques_assurance_accident` TEXT;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `consultation` 
              CHANGE `factureconsult_id` `facture_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `reglement` 
              CHANGE `object_class` `object_class` ENUM ('CConsultation','CFactureConsult','CFactureCabinet','CFactureEtablissement') NOT NULL DEFAULT 'CConsultation';";
    $this->addQuery($query);
    
    $query = "RENAME TABLE `factureconsult` TO `facture_cabinet`;";
    $this->addQuery($query);
    
    $query = "UPDATE reglement
                SET reglement.object_class = 'CFactureCabinet'
                WHERE reglement.object_class = 'CFactureConsult';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `reglement` 
              CHANGE `object_class` `object_class` ENUM ('CConsultation','CFactureCabinet','CFactureEtablissement') NOT NULL DEFAULT 'CConsultation';";
    $this->addQuery($query);
    $this->makeRevision("1.87");
    
    $query = "UPDATE user_log
                SET user_log.object_class = 'CFactureCabinet'
                WHERE user_log.object_class = 'CFactureConsult';";
    $this->addQuery($query);
    $this->makeRevision("1.88");
    
    $query = "UPDATE id_sante400
                SET id_sante400.object_class = 'CFactureCabinet'
                WHERE id_sante400.object_class = 'CFactureConsult';";
    $this->addQuery($query);

    $this->makeRevision("1.89");

    $query = "ALTER TABLE `acte_ngap`
                ADD `lieu` ENUM('C', 'D') DEFAULT 'C' NOT NULL,
                ADD `exoneration` ENUM('N', '13', '15', '17', '19') DEFAULT 'N' NOT NULL;";
    $this->addQuery($query);

    $this->makeRevision("1.90");
    $query = "ALTER TABLE `consultation`
                ADD `num_at` INT (11) AFTER `date_at`,
                ADD `cle_at` INT (11) AFTER `num_at`";
    $this->addQuery($query);

    $this->makeRevision("1.91");
    $query = "ALTER TABLE `consultation`
                ADD `type_assurance` ENUM('classique','at','maternite','smg');";
    $this->addQuery($query);
    $this->makeRevision("1.92");
    
    $query = "ALTER TABLE `facture_cabinet` 
              ADD `consultation_id` INT (11);";
    $this->addQuery($query);
    
    $query = "INSERT INTO `facture_cabinet` (`patient_id` ,`praticien_id`,`ouverture`,`cloture`,`du_patient`,`du_tiers`,`patient_date_reglement`,`tiers_date_reglement`, `consultation_id`)
        SELECT c.patient_id, p.chir_id, p.date, p.date, c.du_patient, c.du_tiers,
          c.patient_date_reglement, c.tiers_date_reglement, c.consultation_id
        FROM consultation c, plageconsult p
        WHERE c.facture_id IS NULL
        AND c.plageconsult_id = p.plageconsult_id
        AND c.valide = '1'
        GROUP BY c.consultation_id;";
    $this->addQuery($query);
    
    $query = "UPDATE consultation c, facture_cabinet f
          SET c.facture_id = f.facture_id
          WHERE c.consultation_id = f.consultation_id;";
    $this->addQuery($query);
    
    $query = "UPDATE reglement r, consultation c, facture_cabinet f
          SET r.object_id = f.facture_id,
              r.object_class = 'CFactureCabinet'
          WHERE r.object_id = c.consultation_id
          AND r.object_class = 'CConsultation'
          AND f.consultation_id = c.consultation_id;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `facture_cabinet` 
              DROP `consultation_id`;";
    $this->addQuery($query);
    $this->makeRevision("1.93");
    
    $query = "ALTER TABLE `reglement` 
              CHANGE `object_class` `object_class` ENUM ('CFactureCabinet','CFactureEtablissement') NOT NULL DEFAULT 'CFactureCabinet';";
    $this->addQuery($query);

    $this->makeRevision("1.94");

    $query = "ALTER TABLE `acte_ngap`
              ADD `ald` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.95");
    $this->addPrefQuery("create_dossier_anesth", "1");

    $this->makeRevision("1.96");
    $this->addDependency("dPfacturation", "0.21");

    $this->makeRevision("1.97");
    
    $query = "UPDATE plageconsult p
              SET p.remplacant_id = NULL
              WHERE p.chir_id = p.remplacant_id;";
    $this->addQuery($query);
    $query = "UPDATE plageconsult p
              SET p.pour_compte_id = NULL
              WHERE p.chir_id = p.pour_compte_id;";
    $this->addQuery($query);
    $this->makeRevision("1.98");
    
    $query = "ALTER TABLE `tarifs` 
                ADD `group_id` INT (11) UNSIGNED";
    $this->addQuery($query);
    $query = "ALTER TABLE `tarifs` 
                    ADD INDEX (`group_id`);";
    $this->addQuery($query);
    $this->makeRevision("1.99");

    $query = "ALTER TABLE `acte_ngap`
                ADD `numero_dent` TINYINT (4) UNSIGNED,
                ADD `comment` VARCHAR (255);";
    $this->addQuery($query);
    $this->makeRevision("2.00");

    $query = "ALTER TABLE `facture_cabinet` 
                CHANGE `statut_pro` `statut_pro` ENUM ('chomeur','etudiant','non_travailleur','independant','invalide','militaire','retraite','salarie_fr','salarie_sw','sans_emploi');";
    $this->addQuery($query);

    $this->makeRevision("2.01");
    $query = "ALTER TABLE `consultation_anesth`
                ADD `plus_de_55_ans` ENUM ('0','1') DEFAULT '0' AFTER `intub_difficile`,
                ADD `imc_sup_26` ENUM ('0','1') DEFAULT '0' AFTER `plus_de_55_ans`,
                ADD `edentation` ENUM ('0','1') DEFAULT '0' AFTER `imc_sup_26`,
                ADD `ronflements` ENUM ('0','1') DEFAULT '0' AFTER `edentation`,
                ADD `barbe` ENUM ('0','1') DEFAULT '0' AFTER `ronflements`;";
    $this->addQuery($query);

    $this->makeRevision("2.02");

    $query = "ALTER TABLE `acte_ngap`
                ADD `execution` DATETIME NOT NULL;";

    $this->addQuery($query);

    $query = "UPDATE `acte_ngap`
                INNER JOIN `consultation` ON (`acte_ngap`.`object_id` = `consultation`.`consultation_id`)
                INNER JOIN `plageconsult` ON (`consultation`.`plageconsult_id` = `plageconsult`.`plageconsult_id`)
                SET `acte_ngap`.`execution` = CONCAT(`plageconsult`.`date`, ' ', `consultation`.`heure`)
                WHERE `acte_ngap`.`object_class` = 'CConsultation';";
    $this->addQuery($query);

    $query = "UPDATE `acte_ngap`
                INNER JOIN `operations` ON (`acte_ngap`.`object_id` = `operations`.`operation_id`)
                INNER JOIN `plagesop` ON (`operations`.`plageop_id` = `plagesop`.`plageop_id`)
                SET `acte_ngap`.`execution` = CONCAT(`plagesop`.`date`, ' ', `operations`.`time_operation`)
                WHERE `acte_ngap`.`object_class` = 'COperation'
                AND `operations`.`date` IS NULL;";
    $this->addQuery($query);

    $query = "UPDATE `acte_ngap`
                INNER JOIN `operations` ON (`acte_ngap`.`object_id` = `operations`.`operation_id`)
                SET `acte_ngap`.`execution` = CONCAT(`operations`.`date`, ' ', `operations`.`time_operation`)
                WHERE `acte_ngap`.`object_class` = 'COperation'
                AND `operations`.`date` IS NOT NULL;";
    $this->addQuery($query);

    $query = "UPDATE `acte_ngap`
                INNER JOIN `sejour` ON (`acte_ngap`.`object_id` = `sejour`.`sejour_id`)
                SET `acte_ngap`.`execution` = `sejour`.`entree`
                WHERE `acte_ngap`.`object_class` = 'CSejour';";
    $this->addQuery($query);

    $this->makeRevision("2.03");
    $query = "UPDATE `plageconsult`
                SET `plageconsult`.`freq` = '00:15:00'
                WHERE `plageconsult`.`freq` < '00:05:00';";
    $this->addQuery($query);

    $this->makeRevision("2.04");
    $this->addPrefQuery("showIntervPlanning", "0");

    $this->makeRevision("2.05");
    $query = "ALTER TABLE `facture_cabinet`
                ADD `annule` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    $this->makeRevision("2.06");

    $query = "ALTER TABLE `plageconsult`
      ADD `pour_tiers` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    $this->makeRevision("2.07");

    $query = "ALTER TABLE `reglement`
                ADD `tireur` VARCHAR (255);";
    $this->addQuery($query);
    $this->makeRevision("2.08");

    $query = "ALTER TABLE `facture_cabinet`
                ADD `definitive` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    $this->makeRevision("2.09");

    $this->addPrefQuery("NbConsultMultiple", 4);
    $this->makeRevision("2.10");

    $query = "ALTER TABLE `facture_cabinet`
                CHANGE `type_facture` `type_facture` ENUM ('maladie','accident','esthetique') NOT NULL DEFAULT 'maladie';";
    $this->addQuery($query);
    $this->makeRevision("2.11");

    $query = "ALTER TABLE `reglement`
                ADD `debiteur_id` INT (11) UNSIGNED,
                ADD `debiteur_desc` VARCHAR (255);";
    $this->addQuery($query);
    $query = "ALTER TABLE `reglement`
                ADD INDEX (`debiteur_id`);";
    $this->addQuery($query);
    $this->makeRevision("2.12");

    $query = "ALTER TABLE `consultation_anesth`
              ADD `result_autre` TEXT AFTER `result_rp`;";
    $this->addQuery($query);
    $this->addPrefQuery("viewAutreResult", "0");
    $this->makeRevision("2.13");

    $this->addPrefQuery("use_acte_date_now", "0");
    $this->makeRevision("2.14");

    $query = "ALTER TABLE `acte_ngap`
                ADD `num_facture` INT (11) UNSIGNED NOT NULL DEFAULT '1';";
    $this->addQuery($query);
    $query = "ALTER TABLE `facture_cabinet`
                ADD `numero` INT (11) UNSIGNED NOT NULL DEFAULT '1';";
    $this->addQuery($query);
    $this->makeRevision("2.15");

    $query = "ALTER TABLE `consultation`
                ADD `secteur3` FLOAT( 6 ) DEFAULT '0' NOT NULL,
                ADD `du_tva` FLOAT( 6 ) DEFAULT '0' NOT NULL,
                ADD `taux_tva` ENUM ('0', '19.6');";
    $this->addQuery($query);

    $query = "ALTER TABLE `tarifs`
                ADD `secteur3` FLOAT( 6 ) DEFAULT '0' NOT NULL,
                ADD `taux_tva` ENUM ('0', '19.6');";
    $this->addQuery($query);

    $query = "ALTER TABLE `facture_cabinet`
                ADD `du_tva` DECIMAL (10,2) DEFAULT '0',
                ADD `taux_tva` ENUM ('0', '19.6') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("2.16");
    $this->addPrefQuery("multi_popups_resume", "1");

    $this->makeRevision("2.17");
    $this->addPrefQuery("allow_plage_holiday", "1");

    $this->makeRevision("2.18");
    $query = "ALTER TABLE `reglement`
                ADD `lock` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("2.19");
    // On supprime les champs inutiles des consultations
    $query = 'ALTER TABLE `consultation`
      DROP `tiers_mode_reglement`,
      DROP `patient_mode_reglement`,
      DROP `banque_id`;';
    $this->addQuery($query);

    // On supprime le champ facture_id de consult
    // !Ne pas d�commenter!
    /*$query = "ALTER TABLE `consultation` DROP `facture_id`;";
    $this->addQuery($query);*/

    $this->makeRevision("2.20");
    // On supprime les champs inutiles des consultations anesth
    $query = "ALTER TABLE `consultation_anesth`
      DROP `poid`,
      DROP `taille`,
      DROP `tasys`,
      DROP `tadias`,
      DROP `pouls`,
      DROP `spo2`;";
    $this->addQuery($query);

    // On supprime les champs inutiles des consultations anesth
    // !Ne pas d�commenter!
    /*$query = "ALTER TABLE `consultation_anesth`
      DROP `groupe`,
      DROP `groupe_ok`,
      DROP `rhesus`;";
    $this->addQuery($query);*/

    $this->makeRevision("2.21");
    $this->addPrefQuery("new_consultation", "0");

    $this->makeRevision("2.22");
    $query = "ALTER TABLE `acte_ngap`
                ADD INDEX (`execution`);";
    $this->addQuery($query);

    $this->makeRevision("2.23");
    $this->addPrefQuery("today_ref_consult_multiple", "1");

    $this->makeRevision("2.24");
    $query = "ALTER TABLE `facture_cabinet`
                CHANGE `taux_tva` `taux_tva` FLOAT DEFAULT '0';";
    $this->addQuery($query);

    $query = "ALTER TABLE `tarifs`
                CHANGE `taux_tva` `taux_tva` FLOAT DEFAULT '0';";
    $this->addQuery($query);

    $query = "ALTER TABLE `consultation`
                CHANGE `taux_tva` `taux_tva` FLOAT DEFAULT '0';";
    $this->addQuery($query);
    $this->makeRevision("2.25");

    $query = "ALTER TABLE `facture_cabinet`
                ADD `group_id` INT (11) UNSIGNED NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `facture_cabinet`
                ADD INDEX (`group_id`);";
    $this->addQuery($query);

    //Facture de cabinet de consultation
    $query = "UPDATE facture_cabinet, users_mediboard, functions_mediboard
          SET facture_cabinet.group_id = functions_mediboard.group_id
          WHERE facture_cabinet.praticien_id = users_mediboard.user_id
          AND functions_mediboard.function_id = users_mediboard.function_id";
    $this->addQuery($query);

    $this->makeRevision("2.26");

    $query = "ALTER TABLE `acte_ngap`
                MODIFY `code` VARCHAR(5) NOT NULL;";
    $this->addQuery($query);

    $this->makeRevision("2.27");
    $query = "ALTER TABLE `consultation`
      CHANGE `duree` `duree` INT (4) UNSIGNED NOT NULL DEFAULT '1'";
    $this->addQuery($query);

    $this->makeRevision("2.28");

    $query = "ALTER TABLE `acte_ngap`
                ADD `major_pct` INT (11),
                ADD `major_coef` FLOAT,
                ADD `minor_pct` INT (11),
                ADD `minor_coef` FLOAT,
                ADD `numero_forfait_technique` INT (11) UNSIGNED,
                ADD `numero_agrement` BIGINT (20) UNSIGNED,
                ADD `rapport_exoneration` ENUM ('4','7','C','R');";

    $this->addQuery($query);

    $this->makeRevision("2.29");
    $this->addDefaultConfig("dPcabinet CPrescription view_prescription");

    $this->makeRevision('2.30');

    $query = "ALTER TABLE `examigs`
                ADD `simplified_igs` INT(11);";
    $this->addQuery($query);
    $this->makeRevision('2.31');

    $query = "UPDATE consultation_anesth, operations
          SET consultation_anesth.sejour_id = operations.sejour_id
          WHERE consultation_anesth.sejour_id IS NULL
          AND consultation_anesth.operation_id IS NOT NULL
          AND operations.operation_id = consultation_anesth.operation_id";
    $this->addQuery($query);

    $this->makeRevision("2.32");
    $query = "ALTER TABLE `consultation`
      ADD `element_prescription_id` INT (11) UNSIGNED";
    $this->addQuery($query);

    if (CModule::getActive("dPprescription")) {
      $query = "UPDATE `consultation`, `sejour_task`, `prescription_line_element`
        SET `consultation`.`element_prescription_id` = `prescription_line_element`.`element_prescription_id`
        WHERE `sejour_task`.`consult_id` = `consultation`.`consultation_id`
        AND   `sejour_task`.`prescription_line_element_id` = `prescription_line_element`.`prescription_line_element_id`";
      $this->addQuery($query);
    }

    $this->mod_version = '2.33';
  }
}
