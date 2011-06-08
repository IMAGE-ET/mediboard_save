<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSetupdPurgences extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPurgences";
    
    $this->makeRevision("all");
    
    $query = "CREATE TABLE `rpu` (
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
      PRIMARY KEY (`rpu_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.1");
    $query = "ALTER TABLE `rpu` 
			CHANGE `ccmu` `ccmu` ENUM( '1', 'P', '2', '3', '4', '5', 'D' )";
    $this->addQuery($query);
    
    $this->makeRevision("0.11");
    $query = "ALTER TABLE `rpu`
      ADD `radio_debut` DATETIME, 
      ADD `radio_fin` DATETIME;";
    $this->addQuery($query);
    
    $this->makeRevision("0.12");
    $query = "ALTER TABLE `rpu`
      DROP `mode_sortie`,
      DROP `sortie`";
    $this->addQuery($query);
    
    $this->makeRevision("0.13");
    $query = "ALTER TABLE `rpu`
      ADD `mutation_sejour_id` INT(11) UNSIGNED;";
    $this->addQuery($query);

    $this->makeRevision("0.14");
    $query = "ALTER TABLE `rpu`
      ADD `gemsa` ENUM('1','2','3','4','5','6');";
    $this->addQuery($query);
    
    $this->makeRevision("0.15");
    $query = "ALTER TABLE `rpu`
      ADD `type_pathologie` ENUM('C','E','M','P','T');";
    $this->addQuery($query);

    $this->makeRevision("0.16");
    $query = "ALTER TABLE `rpu`
			CHANGE `mode_entree` `mode_entree` ENUM('6','7','8') NOT NULL, 
			CHANGE `transport` `transport` ENUM('perso','perso_taxi','ambu','ambu_vsl','vsab','smur','heli','fo') NOT NULL;";
	  $this->addQuery($query);

    $this->makeRevision("0.17");
    $query = "ALTER TABLE `rpu`
			CHANGE `prise_en_charge` `pec_transport` ENUM('med','paramed','aucun')";
    $this->addQuery($query);

    $this->makeRevision("0.18");
    $query = "ALTER TABLE `rpu`
			ADD `urprov` ENUM('AM','AT','DO','EC','MT','OT','RA','RC','SP','VP'), 
			ADD `urmuta` ENUM('A','D','M','P','X'), 
			ADD `urtrau` ENUM('I','S','T');";
    $this->addQuery($query);

    $this->makeRevision("0.19");
    $query = "ALTER TABLE `rpu`
			ADD `box_id` INT(11) UNSIGNED";
    $this->addQuery($query);
    
    $this->makeRevision("0.20");
    $query = "ALTER TABLE `rpu`
      ADD `sortie_autorisee` ENUM ('0','1') DEFAULT '0',
		  ADD INDEX (`radio_debut`),
		  ADD INDEX (`radio_fin`),
		  ADD INDEX (`mutation_sejour_id`),
		  ADD INDEX (`box_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.21");
    $query = "ALTER TABLE `rpu`
      ADD `accident_travail` DATE,
      ADD INDEX (`accident_travail`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.22");
    $query = "ALTER TABLE `rpu` 
			ADD `bio_depart` DATETIME,
			ADD `bio_retour` DATETIME,
			ADD INDEX (`bio_depart`),
			ADD INDEX (`bio_retour`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.23");
      
    $query = "CREATE TABLE `extract_passages` (
      `extract_passages_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `date_extract` DATETIME NOT NULL,
      `debut_selection` DATETIME NOT NULL,
      `fin_selection` DATETIME NOT NULL,
      `date_echange` DATETIME,
      `message` MEDIUMTEXT NOT NULL,
      `message_valide` ENUM ('0','1'),
      `nb_tentatives` INT (11)
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `extract_passages` 
      ADD INDEX (`date_extract`),
      ADD INDEX (`debut_selection`),
      ADD INDEX (`fin_selection`),
      ADD INDEX (`date_echange`);";
    $this->addQuery($query);
      
    $query = "CREATE TABLE `rpu_passage` (
      `rpu_passage_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `rpu_id` INT (11) UNSIGNED NOT NULL,
      `extract_passages_id` INT (11) UNSIGNED NOT NULL
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `rpu_passage` 
      ADD INDEX (`rpu_id`),
      ADD INDEX (`extract_passages_id`);";
    $this->addQuery($query);
		
		$this->makeRevision("0.24");
      
    $query = "ALTER TABLE `rpu` 
      ADD `specia_att` DATETIME,
      ADD `specia_arr` DATETIME;";
    $this->addQuery($query);
		
		$query = "ALTER TABLE `rpu` 
      ADD INDEX (`specia_att`),
      ADD INDEX (`specia_arr`);";
    $this->addQuery($query);
        
    $this->makeRevision("0.25");
    $this->addPrefQuery("defaultRPUSort", "ccmu");

    $this->makeRevision("0.26");
    $this->addPrefQuery("showMissingRPU", "0");
    
    $this->makeRevision("0.27");
    
    $query = "ALTER TABLE `extract_passages` 
      ADD `type` ENUM ('rpu','urg') DEFAULT 'rpu';";
    $this->addQuery($query);
    
    $this->makeRevision("0.28");
    $query = "ALTER TABLE `rpu`
      ADD `pec_douleur` TEXT";
    $this->addQuery($query);
   
    $this->makeRevision("0.29");
    $query = "ALTER TABLE `extract_passages` 
      ADD `group_id` INT (11) UNSIGNED NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.30");
    $query = "CREATE TABLE `circonstance` (
       `code` VARCHAR (15) NOT NULL,
       `libelle` VARCHAR (100),
       `commentaire` TEXT
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'AVP', 'AVP', 'Accident de transport de toute nature.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'DEFENEST', 'Chute de grande hauteur', 'Chute sup�rieure � 3 m.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'AGRESSION', 'Autres agression, rixe ou morsure ', 'Pour toute agression ou rixe".
      "sans usage d\'arme � feu ou d\'arme blanche. Pour toute morsure ou piqures multiples ou v�n�neuses.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'NOYADE', 'Noyade, plong�e, eau', 'Pour les noyades, ".
      "accident de plong�e ou de d�compression.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'ARMEFEU', 'Arme � feu', 'Pour toute agression, rixe, accident et suicide".
      " ou tentative par agent vuln�rant type arme � feu.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'COUTEAU', 'Objet tranchant ou perforant', 'Pour toute agression, rixe, accident et ".
      "suicide ou tentative par agent vuln�rant type arme blanche.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'SPORT', 'Accident de sport ou de loisir', 'Traumatisme en rapport ".
      "avec une activit� sportive ou de loisir.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'PENDU', 'Pendaison, strangulation', 'Pendaison, strangulation ".
      "sans pr�sag� du caract�re m�dico-l�gal ou non.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'FEU', 'Feu, agent thermique, fum�e', 'Toute source de chaleur intense ".
      "ayant provoqu� des brulures, un coup de chaleur ou une insolation. Y compris incendie, ".
      "fum�e d\'incendie et d�gagement de CO au d�cours d\'un feu.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'EXPLOSIF', 'Explosion', 'Explosion de grande intensit� m�me suivi ".
      "ou pr�c�d� d\'un incendie, m�me si notion d\'�crasement.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'ECRASE', 'Ecrasement', 'Notion d\'�crasement, hors contexe accident ".
      "de circulation, explosion ou incendie.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'TOXIQUE', 'Exposition � produits chimiques ou toxiques', ".
      "'L�sion en rapport avec une exposition � un produit liquide, solide ou gazeux toxique.".
      " Hors contexte NRBC, incendie, intoxication par m�dicament, alcool ou drogues illicites.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'CHUTE', 'Chute, traumatisme b�nin', 'Traumatisme b�nin du ".
      "ou non � une chute de sa hauteur ou de tr�s faible hauteur.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'ELEC', 'Electricit�, foudre', 'Effet du courant �lectrique ".
      "par action directe ou � distance (arc �lectrique, effet de la foudre).');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'PRO', 'Trauma par machine � usage professionnel', 'Toute l�sion ".
      "traumatique provoqu�e par un mat�riel � usage professionnel.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'DOMJEU', 'Trauma par appareillage domestique', 'Toute l�sion ".
      "traumatique provoqu�e par un mat�riel � usage domestique ou un accessoire de jeu ou de loisir.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'SECOND', 'Transfert secondaire', 'Pour tout transfert secondaire.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'AUTRE', 'Autres', 'Autre traumatisme avec circonstance particuli�re non r�pertori�.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'CATA', 'Accident nombreuses victimes', 'Accident catastrophique mettant ".
      "en cause de nombreuses victimes et n�cessitant un plan d\'intervention particulier.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( '00000', 'Pathologie non traumatique, non cironstancielle', ".
     "'Pathologie m�dicale non traumatique ou sans circonstance de survenue particuli�re.');";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `rpu`
      ADD `circonstance` VARCHAR (50);";
    $this->addQuery($query);
    
    $this->makeRevision("0.31");
    $query = "ALTER TABLE `circonstance`
      ADD `circonstance_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY FIRST;";
    $this->addQuery($query);
    
    $this->mod_version = "0.32";
  }  
}

?>