<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

global $AppUI;
 
// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPprescription";
$config["mod_version"]     = "0.28";
$config["mod_type"]        = "user";


class CSetupdPprescription extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPprescription";
       
    $this->makeRevision("all");
    
    $sql = "CREATE TABLE `prescription` (
          `prescription_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `object_class` ENUM('CSejour','CConsultation') NOT NULL DEFAULT 'CSejour',
          `object_id` INT(11) UNSIGNED,
          PRIMARY KEY (`prescription_id`)
          ) TYPE=MyISAM COMMENT='Table des prescriptions';";
    $this->addQuery($sql);
    
    $sql = "CREATE TABLE `prescription_line` (
          `prescription_line_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `prescription_id` INT(11) UNSIGNED NOT NULL,
          `code_cip` VARCHAR(7) NOT NULL,
          PRIMARY KEY (`prescription_line_id`)
          ) TYPE=MyISAM COMMENT='Table des lignes de m�dicament des prescriptions';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.10");
    
    $sql = "ALTER TABLE `prescription_line` ADD `no_poso` SMALLINT(6) ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `prescription_line` ADD INDEX (`no_poso`) ;" ;
    $this->addQuery($sql);

    $sql = "ALTER TABLE `prescription` ADD `praticien_id` INT(11) NOT NULL AFTER `prescription_id` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `prescription` ADD INDEX (`praticien_id`) ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.11");
    $sql = "CREATE TABLE `category_prescription` (
           `category_prescription_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `chapitre` ENUM('dmi','anapath','biologie','imagerie','consult','kine','soin') NOT NULL, 
           `nom` VARCHAR(255) NOT NULL, 
           `description` TEXT,  
            PRIMARY KEY (`category_prescription_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $sql = "CREATE TABLE `element_prescription` (
           `element_prescription_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `category_prescription_id` INT(11) UNSIGNED NOT NULL, 
           `libelle` VARCHAR(255) NOT NULL, 
           `description` TEXT, 
           PRIMARY KEY (`element_prescription_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $sql = "CREATE TABLE `prescription_line_element` (
           `prescription_line_element_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `element_prescription_id` INT(11) UNSIGNED NOT NULL, 
           `prescription_id` INT(11) UNSIGNED NOT NULL,
           `commentaire` VARCHAR(255),
           PRIMARY KEY (`prescription_line_element_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.12");
    $sql = "ALTER TABLE `prescription_line`
            ADD `commentaire` VARCHAR(255);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.13");
    $sql = "ALTER TABLE `prescription`
            ADD `libelle` VARCHAR(255);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.14");
    $sql = "CREATE TABLE `prescription_line_comment` (
           `prescription_line_comment_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `prescription_id` INT(11) UNSIGNED NOT NULL, 
           `commentaire` TEXT, 
           `chapitre` ENUM('medicament','dmi','anapath','biologie','imagerie','consult','kine','soin') NOT NULL, 
            PRIMARY KEY (`prescription_line_comment_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.15");
    $sql = "ALTER TABLE `prescription_line_comment`
            ADD `ald` ENUM('0','1');";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_element`
            ADD `ald` ENUM('0','1');";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line`
            ADD `ald` ENUM('0','1');";
    $this->addQuery($sql);
    
    $this->makeRevision("0.16");
    $sql = "CREATE TABLE `moment_unitaire` (
           `moment_unitaire_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `libelle` VARCHAR(255) NOT NULL, 
           `heure_min` TIME, 
           `heure_max` TIME, 
           `type_moment` ENUM('matin','midi','apres_midi','soir','horaire','autre') NOT NULL,
            PRIMARY KEY (`moment_unitaire_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    // Creation d'un tableau de moments => array("libelle","type_moment")
    $moments = array();
    
    $moments[] = array("le matin", "matin");
    $moments[] = array("au lever", "matin");
    $moments[] = array("en prise matinale unique","matin");
    $moments[] = array("au cours de la matin�e","matin");
    $moments[] = array("le matin � jeun","matin");
    $moments[] = array("1 heure avant le petit-d�jeuner","matin");
    $moments[] = array("2 heures avant le d�jeuner","matin");
    $moments[] = array("30 � 60 minutes avant le petit-d�jeuner","matin");
    $moments[] = array("30 minutes avant le petit-d�jeuner","matin");
    $moments[] = array("15 � 30 minutes avant le petit-d�jeuner","matin");
    $moments[] = array("1/4 d\'heure avant le petit-d�jeuner","matin");
    $moments[] = array("15 � 20 minutes avant le petit-d�jeuner","matin");
    $moments[] = array("imm�diatement avant le petit-d�jeuner","matin");
    $moments[] = array("en d�but de petit-d�jeuner","matin");
    $moments[] = array("avant le petit-d�jeuner","matin");
    $moments[] = array("au petit-d�jeuner","matin");
    $moments[] = array("au cours du petit-d�jeuner","matin");
    $moments[] = array("apr�s le petit-d�jeuner","matin");
    $moments[] = array("15 � 30 minutes apr�s le petit-d�jeuner","matin");
    $moments[] = array("matin apr�s la selle","matin");
    $moments[] = array("� la fin du petit-d�jeuner","matin");
    $moments[] = array("le matin � jeun de pr�f�rence","matin");
    $moments[] = array("� jeun d\'alcool, le matin au petit-d�jeuner","matin");
    $moments[] = array("le matin dans chaque narine","matin");
    $moments[] = array("le matin � jeun, au moins 1/2 avant le repas","matin");
    $moments[] = array("5 minutes au moins avant le petit-d�jeuner","matin");
    $moments[] = array("au lever, au moins 1/2 heure avant toute prise orale","matin");
 
    $moments[] = array("le midi", "midi");
    $moments[] = array("au d�jeuner","midi");
    $moments[] = array("1 heure avant le d�jeuner","midi");
    $moments[] = array("15 � 30 minutes avant le d�jeuner","midi");
    $moments[] = array("30 minutes avant le d�jeuner","midi");
    $moments[] = array("30 � 60 minutes avant le d�jeuner","midi");
    $moments[] = array("1/4 d\'heure avant le d�jeuner","midi");
    $moments[] = array("avant le d�jeuner","midi");
    $moments[] = array("en d�but de d�jeuner","midi");
    $moments[] = array("au cours du d�jeuner","midi");
    $moments[] = array("� la fin du d�jeuner","midi");
    $moments[] = array("apr�s le d�jeuner","midi");
    $moments[] = array("15 � 30 minutes apr�s le d�jeuner","midi");
   
    $moments[] = array("l\'apr�s-midi", "apres_midi");
    $moments[] = array("en debut d\'apr�s-midi","apres_midi");
    $moments[] = array("en fin d\'apr�s-midi","apres_midi");
    
    $moments[] = array("le soir", "soir");
    $moments[] = array("avant le d�ner","soir");
    $moments[] = array("au coucher", "soir");
    $moments[] = array("au d�ner","soir");
    $moments[] = array("en fin de journ�e","soir");
    $moments[] = array("le soir avant le coucher","soir");
    $moments[] = array("1h apr�s le d�ner","soir");
    $moments[] = array("2h apr�s le d�ner","soir");
    $moments[] = array("au cours du d�ner","soir");
    $moments[] = array("apr�s le d�ner","soir");
    $moments[] = array("3/4 d\'heure avant le coucher","soir");
    $moments[] = array("avant le coucher","soir");
    $moments[] = array("imm�diatement avant le coucher","soir");
    $moments[] = array("au moment m�me du coucher","soir");
    $moments[] = array("1 heure avant le coucher","soir");
    $moments[] = array("15 � 30 minutes avant le d�ner","soir");
    $moments[] = array("15 � 30 minutes avant le coucher","soir");
    $moments[] = array("15 � 30 minutes apr�s le d�ner","soir");
    $moments[] = array("15 � 30 minutes apr�s le coucher","soir");
    $moments[] = array("30 minutes avant le coucher","soir");
    $moments[] = array("1/4 d\'heure avant le d�ner","soir");
    $moments[] = array("30 minutes avant le d�ner","soir");
    $moments[] = array("30 � 60 minutes avant le d�ner","soir");
    $moments[] = array("1 heure avant le d�ner","soir");
    $moments[] = array("1 heure avant le coucher","soir");
    $moments[] = array("au d�but du d�ner","soir");
    $moments[] = array("le soir apr�s le brossage des dents","soir");
    $moments[] = array("le soir apr�s la toilette","soir");
    $moments[] = array("� la fin du d�ner","soir");
    $moments[] = array("2 heures apr�s le d�ner","soir");
    $moments[] = array("le soir apr�s la toilette sur peau bien s�che","soir");
    $moments[] = array("le soir 1/4 d\'heure apr�s la toilette","soir");
    $moments[] = array("un soir sur deux","soir");
    $moments[] = array("un soir sur trois","soir");
    $moments[] = array("imm�diatement apr�s le d�ner","soir");
    $moments[] = array("1/2 heure � 1 heure avant le coucher","soir");
    $moments[] = array("2 heures apr�s le d�ner","soir");
    $moments[] = array("le soir dans chaque narine","soir");
    $moments[] = array("de pr�f�rence le soir au coucher","soir");
    $moments[] = array("la veille au soir","soir");
    $moments[] = array("dans chaque narine le soir","soir");
    $moments[] = array("� jeun au coucher","soir");
    $moments[] = array("au coucher et 2h30 � 4h plus tard","soir");
    $moments[] = array("au coucher, au moins 2 heures apr�s le d�ner","soir");
    $moments[] = array("2 � 3 heures avant le coucher","soir");
      
    $moments[] = array("� distance d\'un repas","autre");
    $moments[] = array("dans la journ�e","autre");
    $moments[] = array("� l\'induction anesth�sique","autre");
    $moments[] = array("� l\'induction anesth�sique et 2 heures apr�s","autre");
    $moments[] = array("au moment des troubles","autre");
    $moments[] = array("4 fois par jour dans chaque narine","autre");
    $moments[] = array("1 heure avant un repas","autre");
    $moments[] = array("2 heures apr�s un repas","autre");
    $moments[] = array("2 fois par jour dans chaque narine","autre");
    $moments[] = array("3 fois par jour dans chaque narine","autre");
    $moments[] = array("5 fois par jour dans chaque narine","autre");
    $moments[] = array("avant les repas","autre");
    $moments[] = array("dans une narine le matin, dans l\'autre le soir","autre");
    $moments[] = array("dans chaque narine","autre");
    $moments[] = array("matin et soir (� 8 heures d\'intervalle)","autre");
    $moments[] = array("matin et soir (� 12 heures d\'intervalle)","autre");
    
    for($i=0; $i<24; $i++){
      $moments[] = array($i."h","horaire");
    }
    
    foreach($moments as &$moment){
      $sql = " INSERT INTO `moment_unitaire` ( `moment_unitaire_id` , `libelle` , `heure_min`, `heure_max`, `type_moment` ) VALUES ( '' , '".$moment[0]."', NULL, NULL, '".$moment[1]."');";
      $this->addQuery($sql);
    }
    
    $this->makeRevision("0.17");
    $sql = "ALTER TABLE `prescription_line` 
            ADD `debut` DATE, 
            ADD `duree` INT(11);";
    $this->addQuery($sql);

    $this->makeRevision("0.18");
    $sql = "CREATE TABLE `prise_posologie` (
           `prise_posologie_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `prescription_line_id` INT(11) UNSIGNED NOT NULL, 
           `moment_unitaire_id` INT(11) UNSIGNED NOT NULL, 
           `quantite` INT(11), 
            PRIMARY KEY (`prise_posologie_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.19");
    $sql = "CREATE TABLE `association_moment` (
           `association_moment_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `code_moment_id` INT(11) UNSIGNED NOT NULL, 
           `moment_unitaire_id` INT(11) UNSIGNED NOT NULL, 
           `OR` ENUM('0','1') DEFAULT '0', 
            PRIMARY KEY (`association_moment_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.20");
    $sql = "ALTER TABLE `prescription_line`
            ADD `unite_duree` ENUM('minute','heure','demi_journee','jour','semaine','quinzaine','mois','trimestre','semestre','an');";
    $this->addQuery($sql);
    
    $this->makeRevision("0.21");
    $sql = "ALTER TABLE `prise_posologie`
            CHANGE `quantite` `quantite` FLOAT;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.22");
    $sql = "ALTER TABLE `prise_posologie`
            CHANGE `moment_unitaire_id` `moment_unitaire_id` INT(11) UNSIGNED DEFAULT NULL, 
            ADD `nb_fois` INT(11), 
            ADD `unite_fois` ENUM('minute','heure','demi_journee','jour','semaine','quinzaine','mois','trimestre','semestre','an'), 
            ADD `nb_tous_les` INT(11), 
            ADD `unite_tous_les` ENUM('minute','heure','demi_journee','jour','semaine','quinzaine','mois','trimestre','semestre','an');";
    $this->addQuery($sql);

    
    $this->makeRevision("0.23"); 
    
    $moments = array();
    $moments[] = array("1 heure apr�s les repas", "autre");
    $moments[] = array("2 heures avant le d�jeuner", "midi");
    $moments[] = array("15 minutes avant le coucher", "soir");
    $moments[] = array("1 heure apr�s le petit-d�jeuner", "matin");
    $moments[] = array("1 fois par jour dans chaque narine", "autre");
    $moments[] = array("30 minute avant le repas", "autre");
    
    foreach($moments as &$moment){
      $sql = " INSERT INTO `moment_unitaire` ( `moment_unitaire_id` , `libelle` , `heure_min`, `heure_max`, `type_moment` ) VALUES ( '' , '".$moment[0]."', NULL, NULL, '".$moment[1]."');";
      $this->addQuery($sql);
    }
   
    $this->makeRevision("0.24");
    $sql = "CREATE TABLE `executant_prescription_line` (
            `executant_prescription_line_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,  
            `category_prescription_id` INT(11) UNSIGNED NOT NULL, 
            `nom` VARCHAR(255) NOT NULL, 
            `description` TEXT, 
            PRIMARY KEY (`executant_prescription_line_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.25");
    $sql = "ALTER TABLE `prescription_line_element`
            ADD `executant_prescription_line_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.26");
    $sql = "ALTER TABLE `prescription_line_comment`
            ADD `category_prescription_id` INT(11) UNSIGNED, 
            ADD `executant_prescription_line_id` INT(11) UNSIGNED, 
            DROP `chapitre`;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.27");
    $sql = "ALTER TABLE `prescription`
            ADD `type` ENUM('externe','pre_admission','sejour','sortie','traitement') NOT NULL;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_comment`
            ADD `praticien_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_element`
            ADD `praticien_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line`
            ADD `stoppe` ENUM('0','1'), 
            ADD `praticien_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);

    $this->mod_version = "0.28";
  }  
}

?>