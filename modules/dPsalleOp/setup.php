<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Romain Ollivier
 */

class CSetupdPsalleOp extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPsalleOp";
    
    $this->makeRevision("all");
    
    $this->makeRevision("0.1");
    $query = "CREATE TABLE `acte_ccam` (" .
            "\n`acte_id` INT NOT NULL ," .
            "\n`code_activite` VARCHAR( 2 ) NOT NULL ," .
            "\n`code_phase` VARCHAR( 1 ) NOT NULL ," .
            "\n`execution` DATETIME NOT NULL ," .
            "\n`modificateurs` VARCHAR( 4 ) ," .
            "\n`montant_depassement` FLOAT," .
            "\n`commentaire` TEXT," .
            "\n`operation_id` INT NOT NULL ," .
            "\n`executant_id` INT NOT NULL ," .
            "\nPRIMARY KEY ( `acte_id` )) /*! ENGINE=MyISAM */";
    $this->addQuery($query);
    
    $this->makeRevision("0.11");
    $query = "ALTER TABLE `acte_ccam` ADD `code_acte` CHAR( 7 ) NOT NULL AFTER `acte_id`";
    $this->addQuery($query);
    $query = "ALTER TABLE `acte_ccam` " .
            "ADD UNIQUE (" .
              "`code_acte` ," .
              "`code_activite` ," .
              "`code_phase` ," .
              "`operation_id`)";
    $this->addQuery($query);
    
    $this->makeRevision("0.12");
    $query =  "ALTER TABLE `acte_ccam` CHANGE `acte_id` `acte_id` INT( 11 ) NOT NULL AUTO_INCREMENT";
    $this->addQuery($query);
    
    $this->makeRevision("0.13");
    $query =  "ALTER TABLE `acte_ccam` DROP INDEX `code_acte`";
    $this->addQuery($query);
    
    $this->makeRevision("0.14");
    $query = "ALTER TABLE `acte_ccam` " .
               "\nCHANGE `acte_id` `acte_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `operation_id` `operation_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `executant_id` `executant_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `code_activite` `code_activite` tinyint(2) unsigned zerofill NOT NULL," .
               "\nCHANGE `code_phase` `code_phase` tinyint(1) unsigned zerofill NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.15");
    $query = "ALTER TABLE `acte_ccam` CHANGE `code_acte` `code_acte` varchar(7) NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.16");
    $query = "ALTER TABLE `acte_ccam`" .
        "\nCHANGE `code_activite` `code_activite` TINYINT(4) NOT NULL," .
        "\nCHANGE `code_phase` `code_phase` TINYINT(4) NOT NULL;";
    $this->addQuery($query);

    $this->makeRevision("0.17");
    $query = "ALTER TABLE `acte_ccam`" .
        "\nCHANGE `operation_id` `subject_id` int(11) unsigned NOT NULL DEFAULT '0'," .
        "\nADD `subject_class` VARCHAR(25) NOT NULL;";
    $this->addQuery($query);
    $query = "UPDATE `acte_ccam` SET `subject_class` = 'COperation';";
    $this->addQuery($query); 
    
    $this->makeRevision("0.18");
    $query = "ALTER TABLE `acte_ccam`" .
        "\nCHANGE `subject_id` `object_id` int(11) unsigned NOT NULL DEFAULT '0'," .
        "\nCHANGE `subject_class` `object_class` VARCHAR(25) NOT NULL;";
    $this->addQuery($query); 
    
    $this->makeRevision("0.19");
    $query = "ALTER TABLE `acte_ccam`
            ADD `code_association` TINYINT(4)";
    $this->addQuery($query);
    
    $this->makerevision("0.20");
    $query = "ALTER TABLE `acte_ccam`
            ADD `regle` ENUM('0','1');";
    $this->addQuery($query);
    
    $this->makerevision("0.21");
    $query = "ALTER TABLE `acte_ccam`
              ADD INDEX ( `code_acte` ),
              ADD INDEX ( `code_activite` ),
              ADD INDEX ( `code_phase` ),
              ADD INDEX ( `object_id` ),
              ADD INDEX ( `executant_id` ),
              ADD INDEX ( `object_class` )";
    $this->addQuery($query);
    
    $this->makeRevision("0.22");
    $query = "ALTER TABLE `acte_ccam`
            ADD `montant_base` FLOAT;";
    $this->addQuery($query);
    
    $this->makeRevision("0.23");
    $query = "ALTER TABLE `acte_ccam`
            ADD `signe` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.24");
    $query = "ALTER TABLE `acte_ccam`
						ADD `rembourse` ENUM('0','1'), 
						CHANGE `object_class` `object_class` ENUM('COperation','CSejour','CConsultation') NOT NULL;";
    $this->addQuery($query);
		
		$this->makeRevision("0.25");
		$query = "CREATE TABLE `daily_check_item` (
						  `daily_check_item_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
						  `list_id` INT (11) UNSIGNED NOT NULL,
						  `item_type_id` INT (11) UNSIGNED NOT NULL,
						  `checked` ENUM ('0','1') NOT NULL
						) /*! ENGINE=MyISAM */;";
		$this->addQuery($query);
		$query = "ALTER TABLE `daily_check_item` 
						  ADD INDEX (`list_id`),
						  ADD INDEX (`item_type_id`);";
		$this->addQuery($query);
		
		$query = "CREATE TABLE `daily_check_item_type` (
						  `daily_check_item_type_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
						  `title` VARCHAR (255) NOT NULL,
						  `desc` TEXT,
						  `active` ENUM ('0','1') NOT NULL,
						  `group_id` INT (11) UNSIGNED NOT NULL
						) /*! ENGINE=MyISAM */;";
		$this->addQuery($query);
    $query = "ALTER TABLE `daily_check_item_type` ADD INDEX (`group_id`)";
		$this->addQuery($query);

    $query = "CREATE TABLE `daily_check_list` (
						  `daily_check_list_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
						  `date` DATE NOT NULL,
						  `room_id` INT (11) UNSIGNED NOT NULL,
						  `validator_id` INT (11) UNSIGNED
						) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
		$query = "ALTER TABLE `daily_check_list` 
						  ADD INDEX (`date`),
						  ADD INDEX (`room_id`),
						  ADD INDEX (`validator_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.26");
    $query = "ALTER TABLE `daily_check_item_type` 
			ADD `category_id` INT (11) UNSIGNED NOT NULL, 
			ADD INDEX (`category_id`);";
    $this->addQuery($query);
    $query = "CREATE TABLE `daily_check_item_category` (
              `daily_check_item_category_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `title` VARCHAR (255) NOT NULL,
              `desc` TEXT
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.27");
    $query = "ALTER TABLE `acte_ccam` 
			ADD `charges_sup` ENUM ('0','1')";
    $this->addQuery($query);
    
    $this->makeRevision("0.28");
    $query = "ALTER TABLE `daily_check_list` 
              CHANGE `room_id` `object_id` INT (11) UNSIGNED NOT NULL,
              ADD `object_class` VARCHAR(80) NOT NULL DEFAULT 'CSalle'";
    $this->addQuery($query);
    $query = "ALTER TABLE `daily_check_item_category` 
              ADD `target_class` VARCHAR(80) NOT NULL DEFAULT 'CSalle'";
    $this->addQuery($query);
    
    $this->makeRevision("0.29");
    $query = "ALTER TABLE `daily_check_list` ADD `comments` TEXT";
    $this->addQuery($query);
    
    $this->makerevision("0.30");
    $query = "ALTER TABLE `acte_ccam`
            ADD `regle_dh` ENUM('0','1') DEFAULT '0' AFTER `regle`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.31");
    $query = "ALTER TABLE `daily_check_item_category` 
              ADD `type` ENUM ('preanesth','preop','postop');";
    $this->addQuery($query);
   
    $query = "ALTER TABLE `daily_check_item` 
              CHANGE `checked` `checked` ENUM ('0','1');";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `daily_check_item_type` 
              ADD `attribute` ENUM ('normal','notrecommended','notapplicable'),
              CHANGE `group_id` `group_id` INT(11) UNSIGNED NULL";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `daily_check_list` 
              ADD `type` ENUM ('preanesth','preop','postop'),
              CHANGE `object_class` `object_class` ENUM ('CSalle','CBlocOperatoire','COperation') NOT NULL";
    $this->addQuery($query);
    
    
    
    // Liste des points de check liste sp�cifi�s par la HAS
    $categories = array(
      '01' => array('preanesth', 'Identit� du patient', 
        array(
          array('le patient a d�clin� son nom, sinon, par d�faut, autre moyen de v�rification de son identit�', 'normal'),
        ),
      ),
      
      '02' => array('preanesth', 'L\'intervention et site op�ratoire sont confirm�s', 
        array(
          array('id�alement par le patient et dans tous les cas, par le dossier ou proc�dure sp�cifique', 'normal'),
          array('la documentation clinique et para clinique n�cessaire est disponible en salle', 'normal'),
        ),
      ),
      
      '03' => array('preanesth', null, 
        array(
          array('Le mode d\'installation est connu de l\'�quipe en salle, coh�rent avec le site/intervention et non dangereuse pour le patient', 'notapplicable'),
        ),
      ),
      
      '04' => array('preanesth', 'Le mat�riel n�cessaire pour l\'intervention est v�rifi�', 
        array(
          array('pour la partie chirurgicale', 'normal'),
          array('pour la partie anesth�sique', 'normal'),
        ),
      ),
      
      '05' => array('preanesth', 'V�rification crois�e par l\'�quipe de points critiques et des mesures ad�quates � prendre', 
        array(
          array('allergie du patient', 'normal'),
          array('risque d\'inhalation, de difficult� d\'intubation ou de ventilation au masque', 'normal'),
          array('risque de saignement important', 'normal'),
        ),
      ),
      
      '06' => array('preop', 'V�rification � ultime � crois�e au sein de l\'�quipe', 
        array(
          array('identit� patient correcte', 'normal'),
          array('intervention pr�vue confirm�e', 'normal'),
          array('site op�ratoire correct', 'normal'),
          array('installation correcte', 'normal'),
          array('documents n�cessaires disponibles', 'notapplicable'),
        ),
      ),
      
      '07' => array('preop', 'Partage des informations essentielles dans l\'�quipe sur des �l�ments � risque / points critiques de l\'intervention', 
        array(
          array('sur le plan chirurgical (temps op�ratoire difficile, points sp�cifiques de l\'intervention, etc.)', 'normal'),
          array('sur le plan anesth�sique (risques potentiels li�s au terrain ou � des traitements �ventuellement maintenus)', 'normal'),
        ),
      ),
      
      '08' => array('preop', null, 
        array(
          array('Antibioprophylaxie effectu�e', 'notrecommended'),
        ),
      ),
      
      '09' => array('postop', 'Confirmation orale par le personnel aupr�s de l\'�quipe', 
        array(
          array('de l\'intervention enregistr�e', 'normal'),
          array('du compte final correct des compresses, aiguilles, instruments, etc.', 'notapplicable'),
          array('de l\'�tiquetage des pr�l�vements, pi�ces op�ratoires, etc.', 'notapplicable'),
          array('du signalement de dysfonctionnements mat�riels et des �v�nements ind�sirables', 'normal'),
        ),
      ),
      
      '10' => array('postop', null, 
        array(
          array('Les prescriptions pour les suites op�ratoires imm�diates sont faites de mani�re conjointe', 'notrecommended'),
        ),
      ),
    );
    
    foreach($categories as $title => $cat) {
      $query = $this->ds->prepare("INSERT INTO `daily_check_item_category` (`title`, `desc`, `target_class`, `type`) VALUES 
                               (%1, %2, 'COperation', %3)", $title, $cat[1], $cat[0]);
      $this->addQuery($query);
      
      foreach($cat[2] as $type) {
        $query = $this->ds->prepare("INSERT INTO `daily_check_item_type` (`title`, `active`, `attribute`, `category_id`) VALUES
                    (%1, '1', %2, (SELECT `daily_check_item_category_id` FROM `daily_check_item_category` WHERE `title` = %3 AND `target_class` = 'COperation'))", 
                     $type[0], $type[1], $title);
        $this->addQuery($query);
      }
    }
    
    $this->makeRevision("0.32");
    $query = "ALTER TABLE `acte_ccam` 
              ADD INDEX (`execution`);";
    $this->addQuery($query);

    $this->makeRevision("0.33");
		$query = "CREATE TABLE `anesth_perop` (
              `anesth_perop_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `operation_id` INT (11) UNSIGNED NOT NULL,
              `libelle` VARCHAR (255) NOT NULL,
              `datetime` DATETIME NOT NULL
             ) /*! ENGINE=MyISAM */;";
		$this->addQuery($query);
		
		$query = "ALTER TABLE `anesth_perop` 
              ADD INDEX (`operation_id`),
              ADD INDEX (`datetime`);";
		$this->addQuery($query);
    
    $this->makeRevision("0.34");
    $query = "ALTER TABLE `daily_check_item` CHANGE 
                `checked` `checked` ENUM ('0','1','yes','no','na','nr')";
    $this->addQuery($query);
    
    // yes
    $query = "UPDATE `daily_check_item` SET `checked` = 'yes' WHERE `checked` = '1'";
    $this->addQuery($query);
    
    // no
    $query = "UPDATE `daily_check_item` 
                LEFT JOIN `daily_check_item_type` ON `daily_check_item_type`.`daily_check_item_type_id` = `daily_check_item`.`item_type_id`
                SET `daily_check_item`.`checked` = 'no' 
                WHERE `daily_check_item`.`checked` = '0' AND (
                  `daily_check_item_type`.`attribute` = 'normal' OR 
                  `daily_check_item_type`.`attribute` = 'notrecommended'
                )";
    $this->addQuery($query);
    
    // nr
    $query = "UPDATE `daily_check_item`
                LEFT JOIN `daily_check_item_type` ON `daily_check_item_type`.`daily_check_item_type_id` = `daily_check_item`.`item_type_id`
                SET `checked` = 'nr' 
                WHERE `daily_check_item`.`checked` IS NULL AND 
                  `daily_check_item_type`.`attribute` = 'notrecommended'";
    $this->addQuery($query);
    
    // na
    $query = "UPDATE `daily_check_item`
                LEFT JOIN `daily_check_item_type` ON `daily_check_item_type`.`daily_check_item_type_id` = `daily_check_item`.`item_type_id`
                SET `checked` = 'na' 
                WHERE `daily_check_item`.`checked` = '0' AND
                  `daily_check_item_type`.`attribute` = 'notapplicable'";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `daily_check_item` CHANGE 
                `checked` `checked` ENUM ('yes','no','na','nr') NOT NULL";
    $this->addQuery($query);
    
    $changes = array(
      array('04', 1, "preanesth", "notapplicable"),
      array('05', 1, "preanesth", "notapplicable"),
      array('06', 4, "preop", "normal"),
      array('07', 1, "preop", "notapplicable"),
      array('10', 0, "postop", "normal"),
    );
    
    foreach($changes as $_change) {
      $libelle = $categories[$_change[0]][2][$_change[1]][0];
      $query = "UPDATE `daily_check_item_type` 
      LEFT JOIN `daily_check_item_category` ON `daily_check_item_category`.`daily_check_item_category_id` = `daily_check_item_type`.`category_id`
      SET `daily_check_item_type`.`attribute` = '{$_change[3]}'
      WHERE 
        `daily_check_item_category`.`target_class` = 'COperation' AND 
        `daily_check_item_category`.`type` = '{$_change[2]}' AND
        `daily_check_item_category`.`title` = '{$_change[0]}' AND 
        `daily_check_item_type`.`title` = '".addslashes($libelle)."'"; 
      $this->addQuery($query);
    }
    
    $query = "ALTER TABLE `daily_check_list` 
                CHANGE `type` `type` ENUM ('preanesth','preop','postop','preendoscopie','postendoscopie')";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `daily_check_item_category` 
                CHANGE `type` `type` ENUM ('preanesth','preop','postop','preendoscopie','postendoscopie');";
    $this->addQuery($query);
    
    // Liste des points de check liste d'endoscopie digestive sp�cifi�s par la HAS (au 24/08/2010)
    $category_changes = array(
      '01' => array('preendoscopie', 'Identit� du patient', 
        array(
          array('le patient a d�clin� son nom, sinon, par d�faut, autre moyen de v�rification de son identit�', 'normal'),
        ),
      ),
      
      '02' => array('preendoscopie', null, 
        array(
          array('Le type de l\'endoscopie est confirm� par le patient et dans tous les cas par le dossier', 'normal'),
        ),
      ),
      
      '03' => array('preendoscopie', 'Le mat�riel n�cessaire pour l\'intervention est op�rationnel', 
        array(
          array('pour la partie endoscopique', 'normal'),
          array('pour la partie anesth�sique', 'notapplicable'),
        ),
      ),
      
      '04' => array('preendoscopie', 'V�rification crois�e par l\'�quipe de points critiques et des mesures ad�quates � prendre', 
        array(
          array('allergie du patient', 'normal'),
          array('risque d\'inhalation, de difficult� d\'intubation ou de ventilation au masque', 'normal'),
          array('risque de saignement important', 'normal'),
        ),
      ),
      
      '05' => array('preendoscopie', null, 
        array(
          array('Patient � jeun', 'normal'),
        ),
      ),
      
      '06' => array('preendoscopie', null, 
        array(
          array('La pr�paration ad�quate (coloscopie, gastrostomie) a �t� mise en oeuvre', 'notapplicable'),
        ),
      ),
      
      '07' => array('preendoscopie', null, 
        array(
          array('V�rification crois�e de situations sp�cifiques entre les membres de l\'�quipe m�dico-soignante '.
                'concernant notamment la gestion des antiagr�gants plaquettaires et/ou des anticoagulants', 'notapplicable'),
        ),
      ),
      
      '08' => array('preendoscopie', null, 
        array(
          array('Antibioprophylaxie effectu�e', 'notapplicable'),
        ),
      ),
      
      '09' => array('postendoscopie', null, 
        array(
          array('Confirmation orale par le personnel aupr�s de l\'�quipe de l\'�tiquetage des pr�l�vements, pi�ces op�ratoires, etc.', 'notapplicable'),
        ),
      ),
      
      '10' => array('postendoscopie', null, 
        array(
          array('Les prescriptions pour les suites imm�diates de l\'endoscopie sont faites de mani�re conjointe', 'normal'),
        ),
      ),
    );
    
    foreach($category_changes as $title => $cat) {
      $query = $this->ds->prepare("INSERT INTO `daily_check_item_category` (`title`, `desc`, `target_class`, `type`) VALUES 
                               (%1, %2, 'COperation', %3)", $title, $cat[1], $cat[0]);
      $this->addQuery($query);
      
      foreach($cat[2] as $type) {
        $query = $this->ds->prepare("INSERT INTO `daily_check_item_type` (`title`, `active`, `attribute`, `category_id`) VALUES
                    (%1, '1', %2, (SELECT `daily_check_item_category_id` FROM `daily_check_item_category` WHERE `title` = %3 AND `target_class` = 'COperation' AND `type` = %4))", 
                     $type[0], $type[1], $title, $cat[0]);
        $this->addQuery($query);
      }
    }
    
    $this->makeRevision("0.35");
    $query = "ALTER TABLE `acte_ccam` 
              ADD `sent` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
		
		$this->makeRevision("0.36");
		$query = "ALTER TABLE `daily_check_item_type` 
              ADD `default_value` ENUM ('yes','no','nr','na') NOT NULL DEFAULT 'yes',
              ADD `index` TINYINT (2) UNSIGNED NOT NULL";
    $this->addQuery($query);
		
		// creation des index (pas au sens index SQL, mais pour ordonner les types dans chanque cat�gorie)
		function setup_addCheckItemsIndex($setup){
			$ds = $setup->ds;
			
      $sub_query = "SELECT `daily_check_item_category`.`daily_check_item_category_id` FROM `daily_check_item_category`";
      $categories = $ds->loadList($sub_query);
			
			foreach($categories as $_category) {
				$id = reset($_category);
	      $sub_query = "SELECT `daily_check_item_type`.`daily_check_item_type_id`
	        FROM `daily_check_item_type`
	        WHERE `daily_check_item_type`.`category_id` = '$id'";
	        
	      $types = $ds->loadList($sub_query);
				
				foreach($types as $_index => $_type) {
					$type_id = reset($_type);
					$_index++;
					
	        $update_query = "UPDATE `daily_check_item_type`
	          SET `daily_check_item_type`.`index` = '$_index' 
						WHERE `daily_check_item_type`.`daily_check_item_type_id` = '$type_id'";
          $ds->exec($update_query);
				}
			}
			
			return true;
		}
		
		$this->addFunction("setup_addCheckItemsIndex");
		
    $this->makeRevision("0.37");
		
		$category_changes = array(
      array('COperation', 'preanesth', '01', '01', ""),
      array('COperation', 'preanesth', '04', '05', "L'�quipement / mat�riel n�cessaire pour l'intervention est v�rifi� et ne pr�sente pas de dysfonctionnements"),
      array('COperation', 'preanesth', '05', '06', "V�rification crois�e par l'�quipe de points critiques�et mise en oeuvre des mesures ad�quates : Le patient pr�sente-t-il�un ?"),
      
			array('COperation', 'preop', '06', '07', "V�rification \"ultime\" crois�e au sein de l'�quipe en pr�sence des chirurgiens(s), anesth�siste(s), /IADE-IBODE/IDE"),
      array('COperation', 'preop', '07', '08', "Partage des informations essentielles oralement au sein de l'�quipe  sur les �l�ments � risque / �tapes  critiques de l'intervention (Time out)"),
      array('COperation', 'preop', '08', '09'),
      
			array('COperation', 'postop', '09', '10'),
      array('COperation', 'postop', '10', '11'),
		);
		
		// reverse because of the title changes
		$category_changes = array_reverse($category_changes);
		
		// Category changes
		foreach($category_changes as $_change) {
      $cat_class = $_change[0];
      $cat_type  = $_change[1];
      $cat_title = $_change[2];
      $cat_new_title = $_change[3];
      $cat_new_desc  = CValue::read($_change, 4, null);
      
      $query = "UPDATE `daily_check_item_category` SET 
			`daily_check_item_category`.`title` = '".addslashes($cat_new_title)."' ";
      
      if (isset($cat_new_desc)) {
        $query .= ", `daily_check_item_category`.`desc` = '".addslashes($cat_new_desc)."' ";
      }
      
      $query .= "WHERE 
        `daily_check_item_category`.`target_class` = '$cat_class' AND 
        `daily_check_item_category`.`type` = '$cat_type' AND
        `daily_check_item_category`.`title` = '$cat_title'"; 
      $this->addQuery($query);
    }
		
		// Category addittions
    $category_additions = array(
      array('COperation', 'preanesth', '04'),
    );
		
    foreach($category_additions as $_change) {
      $query = $this->ds->prepare("INSERT INTO `daily_check_item_category` (`target_class`, `type`, `title`, `desc`) 
			  VALUES (%1, %2, %3, %4)", $_change[0], $_change[1], $_change[2], ''); 
      $this->addQuery($query);
    }
		
		// Type changes
    $type_changes = array(
		  //     class         type      title/oldtitle  
      array('COperation', 'preanesth', '01', array(
			  "index"     => 1, 
				"title"     => "L'identit� du patient�est correcte", 
			)),
      array('COperation', 'preanesth', '03', array(
        "index"     => 1, 
        "attribute" => "normal"
      )),
      array('COperation', 'preanesth', '05', array(
        "index"     => 2, 
        "title"     => "pour la partie anesth�sique. \n(N/A: Acte sans prise en charge anesth�sique)", 
      )),
			
			// 06
      array('COperation', 'preanesth', '06', array(
        "index"     => 1, 
        "title"     => "risque d'allergie", 
        "default"   => "no", 
      )),
      array('COperation', 'preanesth', '06', array(
        "index"     => 2, 
        "default"   => "no", 
      )),
      array('COperation', 'preanesth', '06', array(
        "index"     => 3, 
        "default"   => "no", 
      )),
			
      array('COperation', 'preop', '07', array(
        "index"     => 3, 
        "title"     => "site op�ratoire confirm�", 
      )),
      array('COperation', 'preop', '07', array(
        "index"     => 4, 
        "title"     => "installation correcte�confirm�e", 
      )),
      array('COperation', 'preop', '07', array(
        "index"     => 5, 
        "title"     => "documents n�cessaires�disponibles (notamment imagerie)", 
        "attribute" => "notapplicable"
      )),
      array('COperation', 'preop', '08', array(
        "index"     => 1, 
        "title"     => "sur le plan chirurgical (temps op�ratoire difficile, points sp�cifiques de l'intervention, identification des mat�riels n�cessaires, confirmation de leur op�rationnalit�, etc.)", 
      )),
      array('COperation', 'preop', '08', array(
        "index"     => 2, 
        "title"     => "sur le plan anesth�sique (N/A: Acte sans prise en charge anesth�sique) (risques potentiels li�s au terrain ou � des traitements �ventuellement maintenus, etc.)",
      )),
      array('COperation', 'preop', '09', array(
        "index"     => 1, 
        "title"     => "L'antibioprophylaxie a �t� effectu�e selon les recommandations et protocoles en vigueur dans l'�tablissement",
      )),
      array('COperation', 'postop', '10', array(
        "index"     => 4, 
        "title"     => "si des �v�nements ind�sirables ou porteurs de risques m�dicaux sont survenus�: ont-ils fait l'objet d'un signalement / d�claration�? (N/A: aucun �v�nement ind�sirable n'est survenu pendant l'intervention)",
        "attribute" => "notapplicable"
      )),
      array('COperation', 'postop', '11', array(
        "index"     => 1, 
        "title"     => "Les prescriptions pour les suites op�ratoires imm�diates sont faites de mani�re conjointe entre les �quipes chirurgicale et anesth�siste",
      )),
    );
    
    foreach($type_changes as $_change) {
    	$cat_class = $_change[0];
      $cat_type  = $_change[1];
      $cat_title = $_change[2];
			
			$data  = $_change[3];
			$index = $data["index"];
			
      $query = "UPDATE `daily_check_item_type` 
      LEFT JOIN `daily_check_item_category` ON `daily_check_item_category`.`daily_check_item_category_id` = `daily_check_item_type`.`category_id`
			SET ";
			
			if (isset($data["title"])) {
				$query .= " `daily_check_item_type`.`title` = '".addslashes($data["title"])."' ";
			}
      
      if (isset($data["attribute"])) {
      	if (isset($data["title"])) $query .= ",";
        $query .= " `daily_check_item_type`.`attribute` = '".$data["attribute"]."' ";
      }
      
      if (isset($data["default"])) {
        if (isset($data["title"]) || isset($data["attribute"])) $query .= ",";
        $query .= " `daily_check_item_type`.`default_value` = '".$data["default"]."' ";
      }
			
      $query .= "WHERE 
        `daily_check_item_category`.`target_class` = '$cat_class' AND 
        `daily_check_item_category`.`type` = '$cat_type' AND
        `daily_check_item_category`.`title` = '$cat_title' AND 
        `daily_check_item_type`.`index` = '$index'";
      $this->addQuery($query);
    }
		
		// type additions
		$type_additions = array(
      //     class         type      title/oldtitle  
      array('COperation', 'preanesth', '04', array(
        "index"     => 1, 
        "title"     => "La pr�paration cutan�e de l'op�r� est document�e dans la fiche de liaison service / bloc op�ratoire (ou autre proc�dure en oeuvre dans l'�tablissement)", 
        "attribute" => "notapplicable",
        "default"   => "yes", 
			)),
      array('COperation', 'preop', '09', array(
        "index"     => 2, 
        "title"     => "La pr�paration du champ op�ratoire est r�alis�e selon le protocole en vigueur dans l'�tablissement", 
        "attribute" => "notapplicable",
        "default"   => "yes", 
      )),
	  );
		
    foreach($type_additions as $_type) {
      $cat_class = $_type[0];
      $cat_type  = $_type[1];
      $cat_title = $_type[2];
      $data      = $_type[3];
      $index     = $data["index"];
      
      $query = $this->ds->prepare("INSERT INTO `daily_check_item_type` (`title`, `attribute`, `default_value`, `index`, `active`, `category_id`)
			VALUES (%1, %2, %3, %4, '1', (
			  SELECT daily_check_item_category.daily_check_item_category_id
				FROM daily_check_item_category
	      WHERE 
	        `daily_check_item_category`.`target_class` = '$cat_class' AND 
	        `daily_check_item_category`.`type` = '$cat_type' AND
	        `daily_check_item_category`.`title` = '$cat_title'
				LIMIT 1
			))", $data["title"], $data["attribute"], $data["default"], $data["index"]);
      $this->addQuery($query);
    }
		
    // Liste des points de check liste d'endoscopie bronchique sp�cifi�s par la HAS (au 08/02/2011)
    $query = "ALTER TABLE `daily_check_list` 
                CHANGE `type` `type` ENUM ('preanesth','preop','postop','preendoscopie','postendoscopie','preendoscopie_bronchique','postendoscopie_bronchique')";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `daily_check_item_category` 
                CHANGE `type` `type` ENUM ('preanesth','preop','postop','preendoscopie','postendoscopie','preendoscopie_bronchique','postendoscopie_bronchique');";
    $this->addQuery($query);
		
    $new_types = array(
      '01' => array('preendoscopie_bronchique', 'Identit� du patient', 
        array(
          array('le patient a d�clin� son nom, sinon, par d�faut, autre moyen de v�rification de son identit�', 'normal', 'yes'),
        ),
      ),
      
      '02' => array('preendoscopie_bronchique', 'Le mat�riel n�cessaire pour l\'intervention est op�rationnel', 
        array(
          array('pour la partie endoscopique', 'normal', 'yes'),
          array('pour la partie anesth�sique', 'notapplicable', 'yes'),
        ),
      ),
      
      '03' => array('preendoscopie_bronchique', null, 
        array(
          array('Patient � jeun', 'normal', 'yes'),
        ),
      ),
      
      '04' => array('preendoscopie_bronchique', 'V�rification crois�e par l\'�quipe de points critiques et des mesures ad�quates � prendre', 
        array(
          array('allergie du patient', 'normal', 'yes'),
          array('risque de saignement important', 'normal', 'yes'),
        ),
      ),
      
      '05' => array('preendoscopie_bronchique', null, 
        array(
          array('V�rification crois�e de situations sp�cifiques entre les membres de l\'�quipe m�dico-soignante '.
                'concernant notamment la gestion des antiagr�gants plaquettaires et/ou des anticoagulants', 'notapplicable', 'yes'),
        ),
      ),
      
      '06' => array('postendoscopie_bronchique', null, 
        array(
          array('Confirmation orale par le personnel aupr�s de l\'�quipe de l\'�tiquetage des pr�l�vements, pi�ces op�ratoires, etc.', 'notapplicable', 'yes'),
        ),
      ),
      
      '07' => array('postendoscopie_bronchique', null, 
        array(
          array('Les prescriptions pour les suites imm�diates de l\'endoscopie sont faites de mani�re conjointe', 'normal', 'yes'),
        ),
      ),
    );
    
    foreach($new_types as $title => $cat) {
      $query = $this->ds->prepare("INSERT INTO `daily_check_item_category` (`title`, `desc`, `target_class`, `type`) VALUES 
                               (%1, %2, 'COperation', %3)", $title, $cat[1], $cat[0]);
      $this->addQuery($query);
      
      foreach($cat[2] as $i => $type) {
        $query = $this->ds->prepare("INSERT INTO `daily_check_item_type` (`title`, `active`, `attribute`, `category_id`, `index`) VALUES
                    (%1, '1', %2, (SELECT `daily_check_item_category_id` FROM `daily_check_item_category` WHERE `title` = %3 AND `target_class` = 'COperation' AND `type` = %4), %5)", 
                     $type[0], $type[1], $title, $cat[0], $i+1);
        $this->addQuery($query);
      }
    }
    
    $this->mod_version = "0.38";
  }
}
?>