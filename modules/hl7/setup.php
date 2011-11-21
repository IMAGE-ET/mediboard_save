<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSetuphl7 extends CSetup {
  function insertTableEntry($number, $code_hl7_from, $code_hl7_to, $code_mb_from, $code_mb_to, $description) {
    $description = $this->ds->escape($description);
    
    $query = "INSERT INTO `hl7v2`.`table_entry` (
              `table_entry_id`, `number`, `code_hl7_from`, `code_hl7_to`, `code_mb_from`, `code_mb_to`, `description`, `user`
              ) VALUES (
                NULL , '$number', '$code_hl7_from', '$code_hl7_to', '$code_mb_from', '$code_mb_to', '$description', '1'
              );";
    
    $this->addQuery($query, false, "hl7v2");
  }
  
  function updateTableEntry($number, $update, $where) {
    foreach ($update as $field => $value) {
      $set[] = "`$field` = '$value'";
    }
    
    $and = "";
    foreach ($where as $field => $value) {
      $and .= "AND `$field` = '$value' ";
    }
    $query = "UPDATE `hl7v2`.`table_entry`
              SET ".implode(", ", $set)."
              WHERE `number` = '$number'
              $and;";

    $this->addQuery($query, false, "hl7v2");
    
  }
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "hl7";
    $this->makeRevision("all");
    
    $this->makeRevision("0.01");
       
    function checkHL7v2Tables() {
      $dshl7 = CSQLDataSource::get("hl7v2", true);
    
      if (!$dshl7 || !$dshl7->loadTable("table_entry")) {
        CAppUI::setMsg("CHL7v2Tables-missing", UI_MSG_ERROR);
        return false;
      }
      
      return true;
    }
    $this->addFunction("checkHL7v2Tables");
       
    $this->makeRevision("0.02");
  
    $query = "ALTER TABLE `table_description` 
              ADD `user` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query, false, "hl7v2");
    
    // Gestion du mode de placement en psychiatrie
    $query = "INSERT INTO `hl7v2`.`table_description` (
              `table_description_id`, `number`, `description`, `user`
              ) VALUES (
                NULL , '9000', 'Admit Reason (Psychiatrie)', '1'
              );";
    $this->addQuery($query, false, "hl7v2");
    
    $this->makeRevision("0.03");
    
    $query = "ALTER TABLE `hl7v2`.`table_entry` DROP INDEX `number_code_hl7` ,
                ADD INDEX `number_code_hl7` ( `number` , `code_hl7_from` );";
    $this->addQuery($query, false, "hl7v2");
    
    // Table - 0001
    // F - Female
    $set = array( 
      "code_hl7_to"   => "F",
      "code_mb_from"  => "f",
      "code_mb_to"    => "f"
    );
    $and = array(
      "code_hl7_from" => "F"
    );
    $this->updateTableEntry("1", $set, $and);
    // M - Male
    $set = array( 
      "code_hl7_to"   => "M",
      "code_mb_from"  => "m",
      "code_mb_to"    => "m"
    );
    $and = array(
      "code_hl7_from" => "M"
    );
    $this->updateTableEntry("1", $set, $and);
    // O - Other
    $set = array(
      "code_mb_to"    => "m"
    );
    $and = array(
      "code_hl7_from" => "O"
    );
    $this->updateTableEntry("1", $set, $and);
    // U - Unknown
    $set = array(
      "code_mb_to"    => "m"
    );
    $and = array(
      "code_hl7_from" => "U"
    );
    $this->updateTableEntry("1", $set, $and);
    // A - Ambiguous  
    $set = array(
      "code_mb_to"    => "m"
    );
    $and = array(
      "code_hl7_from" => "A"
    );
    $this->updateTableEntry("1", $set, $and);
    // N - Not applicable
    $set = array(
      "code_mb_to"    => "m"
    );
    $and = array(
      "code_hl7_from" => "N"
    );
    $this->updateTableEntry("1", $set, $and);

    // Table 0004 - Patient Class
    // E - Emergency - Passage aux Urgences - Arriv�e aux urgences
    $set = array(
      "code_hl7_to"   => "E",
      "code_mb_from"  => "urg",
      "code_mb_to"    => "urg"
    );
    $and = array(
      "code_hl7_from" => "E"
    );
    $this->updateTableEntry("4", $set, $and);
    // I - Inpatient - Hospitalisation
    $set = array(
      "code_hl7_to"   => "I",
      "code_mb_from"  => "urg",
      "code_mb_to"    => "urg"
    );
    $and = array(
      "code_hl7_from" => "I"
    );
    $this->updateTableEntry("4", $set, $and);
    $this->insertTableEntry("4", null, "I", "ssr", null, "Inpatient");
    $this->insertTableEntry("4", null, "I", "psy", null, "Inpatient");
    // O - Outpatient - Actes et consultation externe
    $set = array(
      "code_hl7_to"   => "O",
      "code_mb_from"  => "ambu",
      "code_mb_to"    => "ambu"
    );
    $and = array(
      "code_hl7_from" => "O"
    );
    $this->updateTableEntry("4", $set, $and);
    $this->insertTableEntry("4", null, "O", "exte", null, "Outpatient");
    $this->insertTableEntry("4", null, "O", "consult", null, "Outpatient");
    // R - Recurring patient - S�ances
    $set = array(
      "code_hl7_to"   => "R",
      "code_mb_from"  => "seances",
      "code_mb_to"    => "seances"
    );
    $and = array(
      "code_hl7_from" => "R"
    );
    $this->updateTableEntry("4", $set, $and);
    
    // Table 0032 - Charge price indicator
    // 03 - Hospitalisation compl�te
    $set = array(
      "code_hl7_to"   => "03",
      "code_mb_from"  => "comp",
      "code_mb_to"    => "comp"
    );
    $and = array(
      "code_hl7_from" => "03"
    );
    $this->updateTableEntry("32", $set, $and);
    // 07 - Consultations, soins externes
    $set = array(
      "code_hl7_to"   => "07",
      "code_mb_from"  => "consult",
      "code_mb_to"    => "consult"
    );
    $and = array(
      "code_hl7_from" => "07"
    );
    $this->updateTableEntry("32", $set, $and);
    // 10 - Accueil des urgences
    $set = array(
      "code_hl7_to"   => "10",
      "code_mb_from"  => "urg",
      "code_mb_to"    => "urg"
    );
    $and = array(
      "code_hl7_from" => "10"
    );
    $this->updateTableEntry("32", $set, $and);

    // Table - 9000
    // HL  - Hospitalisation libre
    $this->insertTableEntry("9000", "HL", "HL", "libre", "libre", "Hospitalisation libre");
    // HO  - Placement d'office
    $this->insertTableEntry("9000", "HO", "HO", "office", "office", "Placement d'office");
    // HDT - Hospitalisation � la demande d'un tiers
    $this->insertTableEntry("9000", "HDT", "HDT", "tiers", "tiers", "Hospitalisation � la demande d'un tiers");
    
    // Table - 0430
    // 0 - Police
    $this->insertTableEntry("430", "0", "0", "fo", "fo", "Police");
    // 1 - SAMU, SMUR terrestre
    $this->insertTableEntry("430", "1", "1", "smur", "smur", "SAMU, SMUR terrestre");
    // 2 - Ambulance publique
    $this->insertTableEntry("430", "2", "2", "ambu", "ambu", "Ambulance publique");
    // 3 - Ambulance priv�e
    $this->insertTableEntry("430", null, "3", "ambu", null, "Ambulance priv�e");
    // 4 - Taxi
    $this->insertTableEntry("430", "4", "4", "perso_taxi", "perso_taxi", "Taxi");
    // 5 - Moyens personnels
    $this->insertTableEntry("430", "5", "5", "perso", "perso", "Moyens personnels");
    // 6 - SAMU, SMUR h�licopt�re
    $this->insertTableEntry("430", "6", "6", "heli", "heli", "SAMU, SMUR h�licopt�re");
    // 7 - Pompier
    $this->insertTableEntry("430", "7", "7", "vsab", "vsab", "Pompier");
    // 8 - VSL
    $this->insertTableEntry("430", null, "8", "ambu_vsl", null, "VSL");
    // 9 - Autre
    $this->insertTableEntry("430", null, "9", "perso", null, "Autre");

    $this->makeRevision("0.04");
    
    $query = "CREATE TABLE `hl7_config` (
                `hl7_config_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `assigning_authority_namespace_id` VARCHAR (255),
                `assigning_authority_universal_id` VARCHAR (255),
                `assigning_authority_universal_type_id` VARCHAR (255),
                `sender_id` INT (11) UNSIGNED,
                `sender_class` ENUM ('CSenderFTP','CSenderSOAP')
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `hl7_config` 
              ADD INDEX (`sender_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.05");
    
    // Table - 0063
    // Coll�gue
    $set = array(
      "code_hl7_to"   => "ASC",
      "code_mb_from"  => "collegue",
      "code_mb_to"    => "collegue"
    );
    $and = array(
      "code_hl7_from" => "ASC"
    );
    $this->updateTableEntry("63", $set, $and);
    // Fr�re
    $set = array(
      "code_hl7_to"   => "BRO",
      "code_mb_from"  => "frere",
      "code_mb_to"    => "frere"
    );
    $and = array(
      "code_hl7_from" => "BRO"
    );
    $this->updateTableEntry("63", $set, $and);
    // Enfant
    $set = array(
      "code_hl7_to"   => "CHD",
      "code_mb_from"  => "enfant",
      "code_mb_to"    => "enfant"
    );
    $and = array(
      "code_hl7_from" => "CHD"
    );
    $this->updateTableEntry("63", $set, $and);
    // Fr�re
    $set = array(
      "code_hl7_to"   => "DOM",
      "code_mb_from"  => "compagnon",
      "code_mb_to"    => "compagnon"
    );
    $and = array(
      "code_hl7_from" => "DOM"
    );
    $this->updateTableEntry("63", $set, $and);
    // Employ�
    $set = array(
      "code_hl7_to"   => "EME",
      "code_mb_from"  => "employe",
      "code_mb_to"    => "employe"
    );
    $and = array(
      "code_hl7_from" => "EME"
    );
    $this->updateTableEntry("63", $set, $and);
    // Employeur
    $set = array(
      "code_hl7_to"   => "EMR",
      "code_mb_from"  => "employeur",
      "code_mb_to"    => "employeur"
    );
    $and = array(
      "code_hl7_from" => "EMR"
    );
    $this->updateTableEntry("63", $set, $and);
    // Proche
    $set = array(
      "code_hl7_to"   => "EXF",
      "code_mb_from"  => "proche",
      "code_mb_to"    => "proche"
    );
    $and = array(
      "code_hl7_from" => "EXF"
    );
    $this->updateTableEntry("63", $set, $and);
    // Enfant adoptif
    $set = array(
      "code_hl7_to"   => "FCH",
      "code_mb_from"  => "enfant_adoptif",
      "code_mb_to"    => "enfant_adoptif"
    );
    $and = array(
      "code_hl7_from" => "FCH"
    );
    $this->updateTableEntry("63", $set, $and);
    // Ami
    $set = array(
      "code_hl7_to"   => "FND",
      "code_mb_from"  => "ami",
      "code_mb_to"    => "ami"
    );
    $and = array(
      "code_hl7_from" => "FND"
    );
    $this->updateTableEntry("63", $set, $and);
    // P�re
    $set = array(
      "code_hl7_to"   => "FTH",
      "code_mb_from"  => "pere",
      "code_mb_to"    => "pere"
    );
    $and = array(
      "code_hl7_from" => "FTH"
    );
    $this->updateTableEntry("63", $set, $and);
    // Petits-enfants
    $set = array(
      "code_hl7_to"   => "GCH",
      "code_mb_from"  => "petits_enfants",
      "code_mb_to"    => "petits_enfants"
    );
    $and = array(
      "code_hl7_from" => "GCH"
    );
    $this->updateTableEntry("63", $set, $and);
    // Tuteur
    $set = array(
      "code_hl7_to"   => "GRD",
      "code_mb_from"  => "tuteur",
      "code_mb_to"    => "tuteur"
    );
    $and = array(
      "code_hl7_from" => "GRD"
    );
    $this->updateTableEntry("63", $set, $and);
    // M�re
    $set = array(
      "code_hl7_to"   => "MTH",
      "code_mb_from"  => "mere",
      "code_mb_to"    => "mere"
    );
    $and = array(
      "code_hl7_from" => "MTH"
    );
    $this->updateTableEntry("63", $set, $and);
    // Autre
    $set = array(
      "code_hl7_to"   => "OTH",
      "code_mb_from"  => "autre",
      "code_mb_to"    => "autre"
    );
    $and = array(
      "code_hl7_from" => "OTH"
    );
    $this->updateTableEntry("63", $set, $and);
    // Propri�taire
    $set = array(
      "code_hl7_to"   => "OWN",
      "code_mb_from"  => "proprietaire",
      "code_mb_to"    => "proprietaire"
    );
    $and = array(
      "code_hl7_from" => "OWN"
    );
    $this->updateTableEntry("63", $set, $and);
    // Beau-fils
    $set = array(
      "code_hl7_to"   => "SCH",
      "code_mb_from"  => "beau_fils",
      "code_mb_to"    => "beau_fils"
    );
    $and = array(
      "code_hl7_from" => "SCH"
    );
    $this->updateTableEntry("63", $set, $and);
    // Soeur
    $set = array(
      "code_hl7_to"   => "SIS",
      "code_mb_from"  => "soeur",
      "code_mb_to"    => "soeur"
    );
    $and = array(
      "code_hl7_from" => "SIS"
    );
    $this->updateTableEntry("63", $set, $and);
    // �poux
    $set = array(
      "code_hl7_to"   => "SPO",
      "code_mb_from"  => "epoux",
      "code_mb_to"    => "epoux"
    );
    $and = array(
      "code_hl7_from" => "SPO"
    );
    $this->updateTableEntry("63", $set, $and);
    // Entraineur
    $set = array(
      "code_hl7_to"   => "TRA",
      "code_mb_from"  => "entraineur",
      "code_mb_to"    => "entraineur"
    );
    $and = array(
      "code_hl7_from" => "TRA"
    );
    $this->updateTableEntry("63", $set, $and);
    
    // Table - 0131
    // Personne � pr�venir
    $set = array(
      "code_hl7_to"   => "C",
      "code_mb_from"  => "prevenir",
      "code_mb_to"    => "prevenir"
    );
    $and = array(
      "code_hl7_from" => "C"
    );
    $this->updateTableEntry("131", $set, $and);
    // Employeur
    $set = array(
      "code_hl7_to"   => "E",
      "code_mb_from"  => "employeur",
      "code_mb_to"    => "employeur"
    );
    $and = array(
      "code_hl7_from" => "E"
    );
    $this->updateTableEntry("131", $set, $and);
    // Assurance
    $set = array(
      "code_hl7_to"   => "I",
      "code_mb_from"  => "assurance",
      "code_mb_to"    => "assurance"
    );
    $and = array(
      "code_hl7_from" => "I"
    );
    $this->updateTableEntry("131", $set, $and);
    // Autre
    $set = array(
      "code_hl7_to"   => "O",
      "code_mb_from"  => "autre",
      "code_mb_to"    => "autre"
    );
    $and = array(
      "code_hl7_from" => "O"
    );
    $this->updateTableEntry("131", $set, $and);
    // Inconnu
    $set = array(
      "code_hl7_to"   => "U",
      "code_mb_from"  => "inconnu",
      "code_mb_to"    => "inconnu"
    );
    $and = array(
      "code_hl7_from" => "U"
    );
    $this->updateTableEntry("131", $set, $and);
    // Personne de confiance
    $this->insertTableEntry("131", "K", "K", "confiance", "confiance", "Personne de confiance");
    
    $this->mod_version = "0.06";
    
    $query = "SHOW TABLES LIKE 'table_description'";
    $this->addDatasource("hl7v2", $query);
  }
}

?>