<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CSetupdPsalleOp extends CSetup {
  /**
   * Add a new HAS check list
   *
   * @param array  $check_list   The check list description
   * @param string $object_class The associated object class
   *
   * @return void
   */
  private function addNewCheckList($check_list, $object_class = 'COperation') {
    foreach ($check_list as $title => $cat) {
      // Ajout de la cat�gorie
      $query = "INSERT INTO `daily_check_item_category` (`title`, `desc`, `target_class`, `type`) VALUES
                               (?1, ?2, '$object_class', ?3)";
      $query = $this->ds->prepare($query, $title, $cat[1], $cat[0]);
      $this->addQuery($query);

      // Ajout des �lements
      foreach ($cat[2] as $i => $type) {
        $query = "INSERT INTO `daily_check_item_type` (`title`, `active`, `attribute`, `category_id`, `index`, `default_value`) VALUES
                    (?1, '1', ?2, (
                      SELECT `daily_check_item_category_id`
                      FROM `daily_check_item_category`
                      WHERE `title` = ?3 AND `target_class` = '$object_class' AND `type` = ?4
                    ), ?5, ?6)";
        $query = $this->ds->prepare($query, $type[0], $type[1], $title, $cat[0], $i+1, $type[2]);
        $this->addQuery($query);
      }
    }
  }

  /**
   * Move a check list category
   *
   * @param string $title        Original title
   * @param string $type         Original type
   * @param string $new_title    New title
   * @param string $new_type     New type
   * @param string $object_class Target class
   *
   * @return void
   */
  private function moveCheckListCategory($title, $type, $new_title, $new_type = null, $object_class = "COperation") {
    $update_new_type = "";
    if ($new_type) {
      $update_new_type = ", `type` = '$new_type";
    }

    $query = "UPDATE `daily_check_item_category` SET
                `title` = '$new_title'
                $update_new_type
                WHERE `title` = '$title'
                  AND `target_class` = '$object_class'
                  AND `type` = '$type'";
    $this->addQuery($query);
  }

  /**
   * Changes check list categories
   *
   * @param array $category_changes Categories changes
   *
   * @return void
   */
  private function changeCheckListCategories($category_changes) {
    // reverse because of the title changes
    $category_changes = array_reverse($category_changes);

    // Category changes
    foreach ($category_changes as $_change) {
      $cat_class = $_change[0];
      $cat_type  = $_change[1];
      $cat_title = $_change[2];
      $cat_new_title = addslashes($_change[3]);
      $cat_new_desc  = addslashes(CValue::read($_change, 4, null));

      $query = "UPDATE `daily_check_item_category` SET
        `daily_check_item_category`.`title` = '$cat_new_title' ";

      if (isset($cat_new_desc)) {
        $query .= ", `daily_check_item_category`.`desc` = '$cat_new_desc' ";
      }

      $query .= "WHERE
        `daily_check_item_category`.`target_class` = '$cat_class' AND
        `daily_check_item_category`.`type` = '$cat_type' AND
        `daily_check_item_category`.`title` = '$cat_title'";
      $this->addQuery($query);
    }
  }

  /**
   * Creates check list categories
   *
   * @param array $category_additions A structure containing categories
   *
   * @return void
   */
  private function addCheckListCategories($category_additions) {
    foreach ($category_additions as $_change) {
      $query = "INSERT INTO `daily_check_item_category` (`target_class`, `type`, `title`, `desc`) VALUES (%1, %2, %3, %4)";
      $query = $this->ds->prepare($query, $_change[0], $_change[1], $_change[2], '');
      $this->addQuery($query);
    }
  }

  /**
   * Change check list types
   *
   * @param array $changes A structure containing changes
   *
   * @return void
   */
  private function changeCheckListTypes($changes) {
    foreach ($changes as $_change) {
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
        if (isset($data["title"])) {
          $query .= ",";
        }
        $query .= " `daily_check_item_type`.`attribute` = '".$data["attribute"]."' ";
      }

      if (isset($data["default"])) {
        if (isset($data["title"]) || isset($data["attribute"])) {
          $query .= ",";
        }
        $query .= " `daily_check_item_type`.`default_value` = '".$data["default"]."' ";
      }

      $query .= "WHERE
        `daily_check_item_category`.`target_class` = '$cat_class' AND
        `daily_check_item_category`.`type` = '$cat_type' AND
        `daily_check_item_category`.`title` = '$cat_title' AND
        `daily_check_item_type`.`index` = '$index'";
      $this->addQuery($query);
    }
  }

  /**
   * Creation des index (pas au sens index SQL, mais pour ordonner les types dans chanque cat�gorie)
   *
   * @return bool
   */
  protected function addCheckItemsIndex(){
    $ds = $this->ds;

    $sub_query = "SELECT `daily_check_item_category`.`daily_check_item_category_id` FROM `daily_check_item_category`";
    $categories = $ds->loadList($sub_query);

    foreach ($categories as $_category) {
      $id = reset($_category);
      $sub_query = "SELECT `daily_check_item_type`.`daily_check_item_type_id`
          FROM `daily_check_item_type`
          WHERE `daily_check_item_type`.`category_id` = '$id'";

      $types = $ds->loadList($sub_query);

      foreach ($types as $_index => $_type) {
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

  /**
   * Add a group_id to the check lists
   *
   * @return bool
   */
  protected function listToGroup(){
    $ds = $this->ds;

    $query = "SELECT `daily_check_item_category`.`list_type_id`, `daily_check_item_type`.`group_id`
        FROM `daily_check_item_type`
        LEFT JOIN `daily_check_item_category`
               ON `daily_check_item_category`.`daily_check_item_category_id` = `daily_check_item_type`.`category_id`
        LEFT JOIN `daily_check_list_type`
               ON `daily_check_list_type`.`daily_check_list_type_id` = `daily_check_item_category`.`list_type_id`
        WHERE `daily_check_item_type`.`category_id` = `daily_check_item_category`.`daily_check_item_category_id`
        AND `daily_check_item_category`.`list_type_id` IS NOT NULL
        GROUP BY `daily_check_item_category`.`list_type_id`, `daily_check_item_type`.`group_id`";
    $list_to_group = $ds->loadHashList($query);

    foreach ($list_to_group as $list_type_id => $group_id) {
      $query = "UPDATE `daily_check_list_type` SET
           `group_id` = '$group_id'
           WHERE `daily_check_list_type`.daily_check_list_type_id = '$list_type_id'";
      $ds->exec($query);
    }
    return true;
  }
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPsalleOp";
    
    $this->makeRevision("all");
    
    $this->makeRevision("0.1");
    $query = "CREATE TABLE `acte_ccam` (
                `acte_id` INT NOT NULL ,
                `code_activite` VARCHAR( 2 ) NOT NULL ,
                `code_phase` VARCHAR( 1 ) NOT NULL ,
                `execution` DATETIME NOT NULL ,
                `modificateurs` VARCHAR( 4 ) ,
                `montant_depassement` FLOAT,
                `commentaire` TEXT,
                `operation_id` INT NOT NULL ,
                `executant_id` INT NOT NULL ,
              PRIMARY KEY ( `acte_id` )) /*! ENGINE=MyISAM */";
    $this->addQuery($query);
    
    $this->makeRevision("0.11");
    $query = "ALTER TABLE `acte_ccam`
                ADD `code_acte` CHAR( 7 ) NOT NULL AFTER `acte_id`";
    $this->addQuery($query);
    $query = "ALTER TABLE `acte_ccam`
                ADD UNIQUE (`code_acte`, `code_activite`, `code_phase`, `operation_id`)";
    $this->addQuery($query);
    
    $this->makeRevision("0.12");
    $query = "ALTER TABLE `acte_ccam`
                CHANGE `acte_id` `acte_id` INT( 11 ) NOT NULL AUTO_INCREMENT";
    $this->addQuery($query);
    
    $this->makeRevision("0.13");
    $query = "ALTER TABLE `acte_ccam`
                DROP INDEX `code_acte`";
    $this->addQuery($query);
    
    $this->makeRevision("0.14");
    $query = "ALTER TABLE `acte_ccam`
                CHANGE `acte_id` `acte_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                CHANGE `operation_id` `operation_id` int(11) unsigned NOT NULL DEFAULT '0',
                CHANGE `executant_id` `executant_id` int(11) unsigned NOT NULL DEFAULT '0',
                CHANGE `code_activite` `code_activite` tinyint(2) unsigned zerofill NOT NULL,
                CHANGE `code_phase` `code_phase` tinyint(1) unsigned zerofill NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.15");
    $query = "ALTER TABLE `acte_ccam`
                CHANGE `code_acte` `code_acte` varchar(7) NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.16");
    $query = "ALTER TABLE `acte_ccam`
                CHANGE `code_activite` `code_activite` TINYINT(4) NOT NULL,
                CHANGE `code_phase` `code_phase` TINYINT(4) NOT NULL;";
    $this->addQuery($query);

    $this->makeRevision("0.17");
    $query = "ALTER TABLE `acte_ccam`
                CHANGE `operation_id` `subject_id` int(11) unsigned NOT NULL DEFAULT '0',
                ADD `subject_class` VARCHAR(25) NOT NULL;";
    $this->addQuery($query);
    $query = "UPDATE `acte_ccam` SET `subject_class` = 'COperation';";
    $this->addQuery($query); 
    
    $this->makeRevision("0.18");
    $query = "ALTER TABLE `acte_ccam`
                CHANGE `subject_id` `object_id` int(11) unsigned NOT NULL DEFAULT '0',
                CHANGE `subject_class` `object_class` VARCHAR(25) NOT NULL;";
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
    
    foreach ($categories as $title => $cat) {
      $query = "INSERT INTO `daily_check_item_category` (`title`, `desc`, `target_class`, `type`) VALUES
                               (%1, %2, 'COperation', %3)";
      $query = $this->ds->prepare($query, $title, $cat[1], $cat[0]);
      $this->addQuery($query);
      
      foreach ($cat[2] as $type) {
        $query = "INSERT INTO `daily_check_item_type` (`title`, `active`, `attribute`, `category_id`) VALUES (
                    %1, '1', %2, (
                      SELECT `daily_check_item_category_id`
                      FROM `daily_check_item_category`
                      WHERE `title` = %3
                      AND `target_class` = 'COperation'
                    )
                  )";
        $query = $this->ds->prepare($query, $type[0], $type[1], $title);
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
    
    foreach ($changes as $_change) {
      $libelle = addslashes($categories[$_change[0]][2][$_change[1]][0]);
      $query = "UPDATE `daily_check_item_type` 
      LEFT JOIN `daily_check_item_category`
        ON `daily_check_item_category`.`daily_check_item_category_id` = `daily_check_item_type`.`category_id`
      SET `daily_check_item_type`.`attribute` = '{$_change[3]}'
      WHERE 
        `daily_check_item_category`.`target_class` = 'COperation' AND 
        `daily_check_item_category`.`type` = '{$_change[2]}' AND
        `daily_check_item_category`.`title` = '{$_change[0]}' AND 
        `daily_check_item_type`.`title` = '$libelle'";
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
    
    foreach ($category_changes as $title => $cat) {
      $query = "INSERT INTO `daily_check_item_category` (`title`, `desc`, `target_class`, `type`) VALUES (?1, ?2, 'COperation', ?3)";
      $query = $this->ds->prepare($query, $title, $cat[1], $cat[0]);
      $this->addQuery($query);
      
      foreach ($cat[2] as $type) {
        $query = "INSERT INTO `daily_check_item_type` (`title`, `active`, `attribute`, `category_id`) VALUES (
                    ?1, '1', ?2, (
                      SELECT `daily_check_item_category_id`
                      FROM `daily_check_item_category`
                      WHERE `title` = ?3
                      AND `target_class` = 'COperation'
                      AND `type` = ?4
                    )
                  )";
        $query = $this->ds->prepare($query, $type[0], $type[1], $title, $cat[0]);
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

    $this->addMethod("addCheckItemsIndex");
    
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
    $this->changeCheckListCategories($category_changes);
    
    // Category additions
    $category_additions = array(
      array('COperation', 'preanesth', '04'),
    );
    $this->addCheckListCategories($category_additions);
    
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
    $this->changeCheckListTypes($type_changes);
    
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
    
    foreach ($type_additions as $_type) {
      $cat_class = $_type[0];
      $cat_type  = $_type[1];
      $cat_title = $_type[2];
      $data      = $_type[3];
      
      $query = $this->ds->prepare("INSERT INTO `daily_check_item_type` (`title`, `attribute`, `default_value`, `index`, `active`, `category_id`)
      VALUES (?1, ?2, ?3, ?4, '1', (
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
    
    $check_list = array(
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
    
    $this->addNewCheckList($check_list);
    
    $this->makeRevision("0.38");
    $query = "ALTER TABLE `anesth_perop` 
                CHANGE `libelle` `libelle` TEXT NOT NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `anesth_perop` 
                ADD `incident` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.39");
    $query = "ALTER TABLE `acte_ccam` 
                ADD `motif_depassement` ENUM ('d','e','f','n') AFTER `montant_depassement`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.40");
    // Liste des points de check liste s�curit� du patient en radiologie
    // interventionnelle sp�cifi�s par la HAS, version 2011-01 (au 16/12/2011)
    $query = "ALTER TABLE `daily_check_list` 
                CHANGE `type` `type` ENUM ('preanesth','preop','postop','preendoscopie','postendoscopie','preendoscopie_bronchique','postendoscopie_bronchique','preanesth_radio','preop_radio','postop_radio')";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `daily_check_item_category` 
                CHANGE `type` `type` ENUM ('preanesth','preop','postop','preendoscopie','postendoscopie','preendoscopie_bronchique','postendoscopie_bronchique','preanesth_radio','preop_radio','postop_radio');";
    $this->addQuery($query);
    
    $check_list = array(
      '00' => array('preanesth_radio', null,
        array(
          array('Informations au patient', 'normal', 'yes'),
          array('Tra�abilit� du consentement �clair�', 'normal', 'yes'),
          array('Dossier correspondant au patient', 'normal', 'yes'),
        ),
      ),
      
      '01' => array('preanesth_radio', null,
        array(
          array('L\'identit� du patient�est correcte', 'normal', 'yes'),
        ),
      ),
      
      '02' => array('preanesth_radio', 'L\'intervention et site op�ratoire sont confirm�s', 
        array(
          array('id�alement par le patient et dans tous les cas, par le dossier ou proc�dure sp�cifique', 'normal', 'yes'),
          array('la documentation clinique et para clinique n�cessaire est disponible en salle', 'normal', 'yes'),
        ),
      ),
      
      '03' => array('preanesth_radio', null,
        array(
          array('Le mode d\'installation est connu de l\'�quipe en salle, coh�rent avec le site/intervention et non dangereux pour le patient', 'normal', 'yes'),
        ),
      ),
      
      '04' => array('preanesth_radio', null,
        array(
          array('La pr�paration cutan�e de l\'op�r� est document�e dans la fiche de liaison service', 'notapplicable', 'yes'),
        ),
      ),
      
      '05' => array('preanesth_radio', 'L\'�quipement / mat�riel n�cessaire pour l\'intervention est v�rifi� et ne pr�sente pas de dysfonctionnement', 
        array(
          array('pour la partie chirurgicale', 'normal', 'yes'),
          array('pour la partie anesth�sique', 'normal', 'yes'),
          array('pour la partie imagerie', 'normal', 'yes'),
        ),
      ),
      
      '06' => array('preanesth_radio', 'V�rification crois�e par l\'�quipe de points critiques et mise en oeuvre des mesures ad�quates � prendre. Le patient pr�sente-t-il :', 
        array(
          array('un risque allergique', 'normal', 'no'),
          array('un risque li� au produit de contraste', 'normal', 'no'),
          array('une insuffisance r�nale', 'normal', 'no'),
          array('risque d\'inhalation, de difficult� d\'intubation ou de ventilation au masque', 'normal', 'no'),
          array('un risque de saignement important', 'normal', 'no'),
          array('un risque li� � l\'irradiation (grossesse)', 'normal', 'no'),
        ),
      ),
      
      '07' => array('preop_radio', 'V�rification � ultime � crois�e au sein de l\'�quipe en pr�sence des anesth�sistes, radiologues et manipulateurs �lectroradio.', 
        array(
          array('identit� patient confirm�e', 'normal', 'yes'),
          array('intervention pr�vue confirm�e', 'normal', 'yes'),
          array('site interventionnel confirm�', 'normal', 'yes'),
          array('installation correcte confirm�e', 'normal', 'yes'),
          array('documents n�cessaires disponibles', 'normal', 'yes'),
          array('monitorage du patient v�rifi�', 'normal', 'yes'),
        ),
      ),
      
      '08' => array('preop_radio', 'Partage des informations essentielles oralement au sein de l\'�quipe sur les �l�ments � risque / �tapes critiques de l\'intervention.', 
        array(
          array('sur le plan interventionnel (voie d\'abord d�finie, technique pr�cis�e, DMI disponibles, etc.)', 'normal', 'yes'),
          array('sur le plan anesth�sique (risques potentiels li�s au terrain ou � des traitements �ventuellement maintenus, etc.)', 'notapplicable', 'yes'),
        ),
      ),
      
      '09' => array('preop_radio', 'Prise en compte de situations sp�cifiques concernant', 
        array(
          array('la gestion des antiagr�gants', 'notapplicable', 'yes'),
          array('la gestion des anticoagulants', 'notapplicable', 'yes'),
          array('l\'antibioprophylaxie effectu�e', 'notapplicable', 'yes'),
          array('la pr�paration du champ op�ratoire r�alis� selon protocole en vigueur', 'notapplicable', 'yes'),
        ),
      ),
      
      '10' => array('postop_radio', 'Confirmation orale par le personnel aupr�s de l\'�quipe :', 
        array(
          array('de l\'intervention enregistr�e', 'normal', 'yes'),
          array('de l\'�tiquetage des pr�l�vements, pi�ces op�ratoires, etc.', 'notapplicable', 'yes'),
          array('des m�dications utilis�es', 'normal', 'yes'),
          array('de la quantit� de produit contraste', 'normal', 'yes'),
          array('du recueil de l\'irradiation d�livr�e', 'normal', 'yes'),
          array('de la tra�abilit� du mat�riel et DMI', 'normal', 'yes'),
          array('de l\'enregistrement des images', 'normal', 'yes'),
          array('de la feuille de liaison remplie', 'normal', 'yes'),
          array('si des �v�nements ind�sirables ou porteurs de risques m�dicaux sont survenus : ont-ils fait l\'objet d\'un 
                 signalement / d�claration ? (Si aucun �v�nement ind�sirable n\'est survenu pendant l\'intervention, cochez N/A)', 'notapplicable', 'yes'),
        ),
      ),
      
      '11' => array('postop_radio', null,
        array(
          array('Les prescriptions pour les suites op�ratoires imm�diates sont faites de mani�re conjointe entre les �quipes de radiologie et d\'anesth�sie', 'normal', 'yes'),
        ),
      ),
    );
    
    $this->addNewCheckList($check_list);
    
    $this->makeRevision("0.41");
    
    // Check list pose CVC
    
    $query = "ALTER TABLE `daily_check_list` 
                CHANGE `object_class` `object_class` ENUM ('CSalle','CBlocOperatoire','COperation','CPoseDispositifVasculaire') NOT NULL DEFAULT 'CSalle',
                CHANGE `type` `type` ENUM ('preanesth','preop','postop','preendoscopie','postendoscopie','preendoscopie_bronchique','postendoscopie_bronchique','preanesth_radio','preop_radio','postop_radio','disp_vasc_avant','disp_vasc_pendant','disp_vasc_apres')";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `daily_check_item_category` 
                CHANGE `type` `type` ENUM ('preanesth','preop','postop','preendoscopie','postendoscopie','preendoscopie_bronchique','postendoscopie_bronchique','preanesth_radio','preop_radio','postop_radio','disp_vasc_avant','disp_vasc_pendant','disp_vasc_apres');";
    $this->addQuery($query);
    
    $check_list = array(
      // AVANT
      '01' => array('disp_vasc_avant', null,
        array(
          array('Identit� du patient�v�rifi�e', 'normal', 'yes'),
        ),
      ),
      '02' => array('disp_vasc_avant', null,
        array(
          array('Patient / famille inform�', 'normal', 'yes'),
        ),
      ),
      '03' => array('disp_vasc_avant', "�VALUATION DES RISQUES", 
        array(
          array('Risque h�morragique, allergie, contre-indications anatomique ou pathologique', 'normal', 'yes'),
        ),
      ),
      '04' => array('disp_vasc_avant', null,
        array(
          array('Choix argument� du site d\'insertion', 'normal', 'yes'),
        ),
      ),
      '05' => array('disp_vasc_avant', null,
        array(
          array('Choix concert� du mat�riel', 'normal', 'yes'),
        ),
      ),
      '06' => array('disp_vasc_avant', null,
        array(
          array('Pr�paration cutan�e appropri�e', 'normal', 'yes'),
        ),
      ),
      '07' => array('disp_vasc_avant', null,
        array(
          array('Monitorage appropri�', 'normal', 'yes'),
        ),
      ),
      '08' => array('disp_vasc_avant', "V�rification du mat�riel", 
        array(
          array('Date de p�remption, int�grit� de l\'emballage', 'normal', 'yes'),
        ),
      ),
      '09' => array('disp_vasc_avant', null,
        array(
          array('�chographie', 'normal', 'yes'),
        ),
      ),
      
      // PENDANT
      '10' => array('disp_vasc_pendant', "PROC�DURES D'HYGI�NE", 
        array(
          array('D�tersion/d�sinfection avec antiseptique alcoolique', 'normal', 'yes'),
          array('Conditions d\'asepsie chirurgicale', 'normal', 'yes'),
        ),
      ),
      '11' => array('disp_vasc_pendant', "V�rifications per op�ratoires des mat�riels", 
        array(
          array('M�canique: Solidit� des connexions', 'normal', 'yes'),
          array('Positionnelle: Extr�mit� du cath�ter', 'normal', 'yes'),
          array('FONCTIONNELLE: Reflux sanguin', 'normal', 'yes'),
          array('FONCTIONNELLE: Syst�me perm�able', 'normal', 'yes'),
        ),
      ),
      '12' => array('disp_vasc_pendant', null,
        array(
          array('V�rification de la fixation du dispositif', 'normal', 'yes'),
        ),
      ),
      '13' => array('disp_vasc_pendant', null,
        array(
          array('Pose d\'un pansement occlusif', 'normal', 'yes'),
        ),
      ),
      '14' => array('disp_vasc_pendant', "Si utilisation diff�r�e, fermeture du dispositif",
        array(
          array('En accord avec la proc�dure locale', 'normal', 'yes'),
        ),
      ),
      
      // APRES
      '15' => array('disp_vasc_apres', "CONTR�LE CVC / DV", 
        array(
          array('Position du CVC v�rifi�e', 'normal', 'yes'),
          array('Recherche de complication', 'normal', 'yes'),
        ),
      ),
      '16' => array('disp_vasc_apres', "TRA�ABILIT� / COMPTE RENDU", 
        array(
          array('Mat�riel, technique, nombre de ponctions, incident', 'normal', 'yes'),
        ),
      ),
      '17' => array('disp_vasc_apres', null,
        array(
          array('Prescriptions pour le suivi apr�s pose', 'normal', 'yes'),
        ),
      ),
      '18' => array('disp_vasc_apres', null,
        array(
          array('Documents remis au patient', 'normal', 'yes'),
        ),
      ),
    );
    
    $this->addNewCheckList($check_list, "CPoseDispositifVasculaire");

    $this->makeRevision("0.42");

    $query = "ALTER TABLE `acte_ccam`
                ADD `facturable` ENUM ('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("0.43");
    $query = "ALTER TABLE `daily_check_item_category`
                CHANGE `target_class` `target_class` ENUM ('CSalle','CBlocOperatoire','COperation','CPoseDispositifVasculaire') NOT NULL DEFAULT 'CSalle',
                ADD `target_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    $query = "ALTER TABLE `daily_check_item_category`
                ADD INDEX (`target_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.44");
    // Check list s�curit� du patient en endoscopie digestive, version 2013
    $this->moveCheckListCategory("10", "postendoscopie", "11");
    $this->moveCheckListCategory("09", "postendoscopie", "10");

    // Nouveau point 09
    $check_list = array(
      '09' => array('preendoscopie', 'Patient suspect ou atteint d\'EST',
        array(
          array('(en cas de r�ponse positive, l\'endoscopie doit �tre consid�r�e comme un acte � risque de transmission d\'ATNC et '.
                'il convient de se r�f�rer aux proc�dures en cours dans l\'�tablissement en lien avec l\'Instruction n�DGS/R13/2011'.
                '/449)', 'normal', 'yes'),
        ),
      ),
    );
    $this->addNewCheckList($check_list);

    $this->makeRevision("0.45");

    $query = "ALTER TABLE `acte_ccam`
                ADD `ald` ENUM ('0','1') NOT NULL DEFAULT '0',
                ADD `lieu` ENUM('C', 'D') DEFAULT 'C' NOT NULL,
                ADD `exoneration` ENUM('N', '13', '15', '17', '19') DEFAULT 'N' NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.46");
    $query = "CREATE TABLE `daily_check_list_type` (
                `daily_check_list_type_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `object_class` ENUM ('CSalle','CBlocOperatoire') NOT NULL DEFAULT 'CSalle',
                `object_id` INT (11) UNSIGNED,
                `title` VARCHAR (255) NOT NULL,
                `description` TEXT
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `daily_check_list_type`
                ADD INDEX (`object_id`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `daily_check_item_category`
                ADD `list_type_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    $query = "INSERT INTO `daily_check_list_type` (`object_class`, `object_id`, `title`)
                SELECT `target_class`, `target_id`, 'Check list standard'
                FROM `daily_check_item_category`
                WHERE `target_class` NOT IN ('COperation', 'CPoseDispositifVasculaire')
                GROUP BY `target_class`, `target_id`;";
    $this->addQuery($query);
    $query = "UPDATE `daily_check_item_category` SET
                `list_type_id` = (
                  SELECT `daily_check_list_type_id`
                  FROM `daily_check_list_type`
                  WHERE `daily_check_list_type`.`object_class` = `daily_check_item_category`.`target_class`
                  AND   (
                       `daily_check_list_type`.`object_id`    = `daily_check_item_category`.`target_id`
                    OR `daily_check_list_type`.`object_id` IS NULL AND `daily_check_item_category`.`target_id` IS NULL
                  )
                  LIMIT 1
                )
                WHERE `target_class` NOT IN ('COperation', 'CPoseDispositifVasculaire');";
    $this->addQuery($query);

    $query = "ALTER TABLE `daily_check_list`
                ADD `list_type_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    $query = "ALTER TABLE `daily_check_list`
                ADD INDEX (`list_type_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.47");
    $query = "CREATE TABLE `daily_check_list_type_link` (
                `daily_check_list_type_link_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `object_class` ENUM ('CSalle','CBlocOperatoire') NOT NULL DEFAULT 'CSalle',
                `object_id` INT (11) UNSIGNED,
                `list_type_id` INT (11) UNSIGNED NOT NULL DEFAULT '0'
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `daily_check_list_type_link`
                ADD INDEX (`object_id`),
                ADD INDEX (`object_class`),
                ADD INDEX (`list_type_id`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `daily_check_list_type`
                ADD `group_id` INT ( 11 ) UNSIGNED NOT NULL DEFAULT 0;";
    $this->addQuery($query);

    $this->makeRevision("0.48");

    $this->addMethod("listToGroup");

    $query = "INSERT INTO `daily_check_list_type_link` (`object_class`, `object_id`, `list_type_id`)
                SELECT `object_class`, `object_id`, `daily_check_list_type_id` FROM `daily_check_list_type`";
    $this->addQuery($query);
    
    $this->makeRevision("0.49");
    $query = "ALTER TABLE `acte_ccam`
                ADD `extension_documentaire` ENUM ('1','2','3','4','5','6') AFTER `code_association`;";
    $this->addQuery($query);

    $this->makeRevision("0.50");

    // Check list s�curit� du patient en endoscopie bronchique, version 2013
    $this->moveCheckListCategory("07", "postendoscopie_bronchique", "08");
    $this->moveCheckListCategory("06", "postendoscopie_bronchique", "07");

    // Nouveau point 06
    $check_list = array(
      '06' => array('preendoscopie_bronchique', 'Patient suspect ou atteint d\'EST',
        array(
          array('(en cas de r�ponse positive, l\'endoscopie doit �tre consid�r�e comme un acte � risque de transmission d\'ATNC et '.
          'il convient de se r�f�rer aux proc�dures en cours dans l\'�tablissement en lien avec l\'Instruction n�DGS/R13/2011'.
          '/449)', 'normal', 'yes'),
        ),
      ),
    );
    $this->addNewCheckList($check_list);

    $this->moveCheckListCategory("04",  "preendoscopie_bronchique", "05b");
    $this->moveCheckListCategory("02",  "preendoscopie_bronchique", "04");
    $this->moveCheckListCategory("03",  "preendoscopie_bronchique", "02");
    $this->moveCheckListCategory("05",  "preendoscopie_bronchique", "03");
    $this->moveCheckListCategory("05b", "preendoscopie_bronchique", "05");

    $this->makeRevision("0.51");
    $this->addDependency("dPplanningOp", "1.50");
    $this->addDependency("dPbloc", "0.23");
    $query = "ALTER TABLE `daily_check_list`
                ADD `group_id` INT (11) UNSIGNED NOT NULL,
                ADD INDEX (`group_id`);";
    $this->addQuery($query);

    // Update CSalle
    $query = "UPDATE `daily_check_list` SET
                `daily_check_list`.`group_id` = (
                  SELECT `group_id`
                  FROM `bloc_operatoire`
                  LEFT JOIN `sallesbloc` ON `sallesbloc`.`bloc_id` = `bloc_operatoire`.`bloc_operatoire_id`
                  WHERE `sallesbloc`.`salle_id` = `daily_check_list`.`object_id`
                )
                WHERE `daily_check_list`.`object_class` = 'CSalle'";
    $this->addQuery($query);

    // Update CBlocOperatoire
    $query = "UPDATE `daily_check_list` SET
                `daily_check_list`.`group_id` = (
                  SELECT `group_id`
                  FROM `bloc_operatoire`
                  WHERE `bloc_operatoire_id` = `daily_check_list`.`object_id`
                )
                WHERE `daily_check_list`.`object_class` = 'CBlocOperatoire'";
    $this->addQuery($query);

    // Update COperation
    $query = "UPDATE `daily_check_list` SET
                `daily_check_list`.`group_id` = (
                  SELECT `group_id`
                  FROM `sejour`
                  LEFT JOIN `operations` ON `operations`.`sejour_id` = `sejour`.`sejour_id`
                  WHERE `operations`.`operation_id` = `daily_check_list`.`object_id`
                )
                WHERE `daily_check_list`.`object_class` = 'COperation'";
    $this->addQuery($query);

    // Update CPoseDispositifVasculaire
    $query = "UPDATE `daily_check_list` SET
                `daily_check_list`.`group_id` = (
                  SELECT `group_id`
                  FROM `sejour`
                  LEFT JOIN `pose_dispositif_vasculaire` ON `pose_dispositif_vasculaire`.`sejour_id` = `sejour`.`sejour_id`
                  WHERE `pose_dispositif_vasculaire`.`pose_dispositif_vasculaire_id` = `daily_check_list`.`object_id`
                )
                WHERE `daily_check_list`.`object_class` = 'CPoseDispositifVasculaire'";
    $this->addQuery($query);

    $this->makeRevision("0.52");
    $query = "ALTER TABLE `acte_ccam`
                ADD `num_facture` INT (11) UNSIGNED NOT NULL DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("0.53");
    self::addDefaultConfig("dPsalleOp COperation use_sortie_reveil_reel");

    $this->makeRevision("0.54");
    $query = "ALTER TABLE `daily_check_list_type`
                ADD `type_validateur` TEXT;";
    $this->addQuery($query);

    $this->makeRevision("0.55");
    $query = "UPDATE `daily_check_list_type` SET
                `type_validateur` = 'chir|anesth|op|op_panseuse|iade|sagefemme|manipulateur'
                WHERE `object_class` = 'CSalle'";
    $this->addQuery($query);
    $query = "UPDATE `daily_check_list_type` SET
                `type_validateur` = 'reveil'
                WHERE `object_class` = 'CBlocOperatoire'";
    $this->addQuery($query);
    $this->makeRevision("0.56");

    $query = "ALTER TABLE `daily_check_item_category`
                CHANGE `type` `type` ENUM ('preanesth','preop','postop','preendoscopie','postendoscopie','preendoscopie_bronchique','postendoscopie_bronchique','preanesth_radio','preop_radio','postop_radio','disp_vasc_avant','disp_vasc_pendant','disp_vasc_apres', 'avant_indu_cesar', 'cesarienne_avant', 'cesarienne_apres');";
    $this->addQuery($query);

    $query = "ALTER TABLE `daily_check_list`
                CHANGE `type` `type` ENUM ('preanesth','preop','postop','preendoscopie','postendoscopie','preendoscopie_bronchique','postendoscopie_bronchique','preanesth_radio','preop_radio','postop_radio','disp_vasc_avant','disp_vasc_pendant','disp_vasc_apres', 'avant_indu_cesar', 'cesarienne_avant', 'cesarienne_apres');";
    $this->addQuery($query);

    $check_list = array(
      // AVANT
      '01' => array('avant_indu_cesar', 'Identit� patient',
        array(
          array('Identit� de la patiente est correct', 'normal', 'yes'),
        ),
      ),
      '02' => array('avant_indu_cesar', 'Les �l�ments essentiels � la prise en charge sont connus par l\'�quipe',
        array(
          array('La localisation du placenta', 'normal', 'yes'),
          array('La pr�sentation de l\'enfant', 'normal', 'yes'),
          array('Les bruits du coeur sont v�rifi�s', 'normal', 'yes'),
          array('La documentation clinique et para clinique n�cessaire est disponible en salle:'.
            'Bilan sanguin, carte de groupe, ACI', 'normal', 'yes')
        )
      ),
      '03' => array('avant_indu_cesar', null,
        array(
          array('Le p�diatre est pr�venu', 'notapplicable', 'yes')
        )
      ),
      '04' => array('avant_indu_cesar', null,
        array(
          array('La pr�paration cutan�e de la patiente est document�e dans la fiche de liaison service / bloc op�ratoire (ou autre '.
          'proc�dure en oeuvre dans l\'�tablissement)', 'notapplicable', 'yes')
        )
      ),
      '05' => array('avant_indu_cesar', 'L\'�quipe / mat�riel n�cessaire pour l\'intervention est v�rifi� et fonctionnel:',
        array(
          array('Pour la partie obst�tricale', 'normal', 'yes'),
          array('Pour la partie anesth�sique (m�re)', 'normal', 'yes'),
          array('Pour la partie r�animation (nouveau n�)', 'normal', 'yes'),
        )
      ),
      '06' => array('avant_indu_cesar', 'V�rification crois�e par l\'�quipe de points critiques et mise en oeuvre des mesures ad�quats'
          .'. La patiente pr�sente-t-elle un?',
        array(
          array('risque alergique', 'normal', 'no'),
          array('de la difficult� d\'intubation ou de ventilation au masque', 'normal', 'no'),
          array('risque de saignement sup�rieur � 1000ml', 'normal', 'no'),
          array('L\'administration d\'antiacide  a �t� effectu�e', 'normal', 'yes'),
        )
      ),
      '07' => array('cesarienne_avant', 'V�rification "ultime" crois�e au sein de l\'�quipe',
        array(
          array('identit� patiente confirm�e', 'normal', 'yes'),
          array('installation correcte confirm�e', 'normal', 'yes'),
          array('sondage urinaire efficace', 'normal', 'yes'),
          array('compte initial de textiles et d\'instruments confirm�', 'normal', 'yes'),
          array('electrode de scalp ot�e', 'notapplicable', 'yes'),
        )
      ),
      '08' => array('cesarienne_avant', null,
        array(
          array('Pr�sence du p�diatre', 'notapplicable', 'yes'),
        )
      ),
      '09' => array('cesarienne_avant', 'Partage des informations essentielles oralement au sein de l\'�quipe sur les �l�ments �'.
        ' risque / �tapes critiques de l\'intervention',
        array(
          array('sur le plan obt�trical (temps op�ratoire difficile, localisation du placenta, points sp�cifiques de l\'intervention'.
          ', pr�l�vements cordon placenta, identification des mat�riels n�cessaires, confirmation de leur op�rationnalit�, etc.)',
            'normal', 'yes'),
          array('sur le plan anesth�sique (risque potentiels li�s au terrain ou � des traitements �ventuellement maintenus, etc.)',
            'normal', 'yes'),
        )
      ),
      '10' => array('cesarienne_avant', null,
        array(
          array('La pr�paration du champ op�ratoire est r�alis�e selon le protocole en vigeur dans l\'�tablissement', 'normal', 'yes'),
        )
      ),
      '11' => array('cesarienne_apres', 'Confirmation orale par le personnel aupr�s de l\'�quipe',
        array(
          array('du compte final concordant des textiles, aiguilles, instruments, etc.', 'normal', 'yes'),
          array('de l\'enregistrement des pertes sanguines totales', 'normal', 'yes'),
          array('si des �v�nements ind�sirables ou porteurs de risques m�dicaux sont survenus: ont-ils fait l\'objet d\'un '.
          'signalement / d�claration', 'notapplicable', 'yes'),
        )
      ),
      '12' => array('cesarienne_apres', null,
        array(
          array('L\'antibioprophylaxie a �t� effectu�e selon les recommandations et protocoles en vigueur dans l\'�tablissement',
            'normal', 'yes'),
          array('Les prescriptions pour les suites op�ratoires imm�diates sont faites de mani�re conjointe entre les �quipes '.
          'obst�tricale et anesth�siste', 'normal', 'yes'),
        )
      ),
      '13' => array('cesarienne_apres', null,
        array(
          array('Le ou les nouveaux n�s sont identifi�s selon les protocoles en vigueur dans l\'�tablissement','normal', 'yes'),
        )
      ),
    );
    $this->addNewCheckList($check_list);
    $this->makeRevision("0.57");

    $query = "UPDATE `daily_check_item_type` SET `title` = 'Identit� de la patiente est correcte'
            WHERE `title` = 'Identit� de la patiente est correct'";
    $this->addQuery($query);

    $query = "UPDATE `daily_check_item_type` SET `title` = 'un risque alergique'
            WHERE `title` = 'risque alergique'";
    $this->addQuery($query);

    $query = "UPDATE `daily_check_item_type` SET `title` = 'un risque de saignement sup�rieur � 1000ml'
            WHERE `title` = 'risque de saignement sup�rieur � 1000ml'";
    $this->addQuery($query);

    $query = "UPDATE `daily_check_item_type` SET `title` = '�lectrode de scalp �t�e'
            WHERE `title` = 'electrode de scalp ot�e'";
    $this->addQuery($query);

    $query = "UPDATE `daily_check_item_category`
      SET `desc` = 'V�rification crois�e par l''�quipe de points critiques et mise en oeuvre des mesures ad�quates. La patiente pr�sente-t-elle  ? '
      WHERE `target_class` = 'COperation'
      AND `type` = 'avant_indu_cesar'
      AND `title` =  '06'";
    $this->addQuery($query);

    $this->makeRevision("0.58");

    $query = "ALTER TABLE `daily_check_item_category`
                ADD `index` INT (11) UNSIGNED NOT NULL DEFAULT '1';";
    $this->addQuery($query);

    $query = "ALTER TABLE `daily_check_item_category`
                ADD INDEX (`list_type_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.59");

    $query = "UPDATE `daily_check_item_type` SET `title` = 'un risque allergique'
            WHERE `title` = 'un risque alergique'";
    $this->addQuery($query);

    $query = "UPDATE `daily_check_item_type`
    SET `title` = 'sur le plan anesth�sique (risques potentiels li�s au terrain ou � des traitements �ventuellement maintenus, etc.)'
    WHERE `title` = 'sur le plan anesth�sique (risque potentiels li�s au terrain ou � des traitements �ventuellement maintenus, etc.)'";
    $this->addQuery($query);

    $query = "UPDATE `daily_check_item_type`
    SET `title` = 'La documentation clinique et para clinique n�cessaire est disponible en salle: Bilan sanguin, carte de groupe, ACI'
    WHERE `title` = 'La documentation clinique et para clinique n�cessaire est disponible en salle:Bilan sanguin, carte de groupe, ACI'";
    $this->addQuery($query);

    $this->makeRevision("0.60");
    $this->addPrefQuery("autosigne_sortie", "1");

    $this->mod_version = "0.61";
  }
}
